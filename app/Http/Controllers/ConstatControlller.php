<?php

namespace App\Http\Controllers;

use App\Models\Constat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ConstatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constats = Constat::with(['driverA', 'driverB', 'companyA', 'companyB'])->get();
        return response()->json($constats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'driver_a_id' => 'required|exists:drivers,id',
            'driver_b_id' => 'nullable|exists:drivers,id',
            'company_a_id' => 'required|exists:companies,id',
            'company_b_id' => 'nullable|exists:companies,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120', // max 5MB per attachment
        ]);

        // Store PDF
        $pdfPath = $request->file('pdf')->store('constats_pdfs', 'public');
        $pdfHash = hash_file('sha256', storage_path("app/public/{$pdfPath}"));

        // Compute HMAC using server secret
        $hmac = hash_hmac('sha256', $pdfHash, env('PDF_SECRET_KEY'));

        // Store attachments if any
        $attachmentsUrls = [];
        if ($request->has('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $attachmentsUrls[] = $attachment->store('constat_attachments', 'public');
            }
        }

        $constat = Constat::create([
            'pdf_url' => $pdfPath,
            'pdf_hash' => $pdfHash,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'driver_a_id' => $validated['driver_a_id'],
            'driver_b_id' => $validated['driver_b_id'] ?? null,
            'company_a_id' => $validated['company_a_id'],
            'company_b_id' => $validated['company_b_id'] ?? null,
            'attachments_urls' => $attachmentsUrls,
        ]);

        // Store HMAC somewhere secure (e.g., Redis or file)
        // Here we recompute HMAC for verification, assuming secret key is server-only

        return response()->json($constat, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $constat = Constat::with(['driverA', 'driverB', 'companyA', 'companyB'])->findOrFail($id);
        return response()->json($constat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $constat = Constat::findOrFail($id);

        $validated = $request->validate([
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'driver_a_id' => 'nullable|exists:drivers,id',
            'driver_b_id' => 'nullable|exists:drivers,id',
            'company_a_id' => 'nullable|exists:companies,id',
            'company_b_id' => 'nullable|exists:companies,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);

        // Update PDF
        if ($request->hasFile('pdf')) {
            if ($constat->pdf_url) {
                Storage::disk('public')->delete($constat->pdf_url);
            }
            $pdfPath = $request->file('pdf')->store('constats_pdfs', 'public');
            $pdfHash = hash_file('sha256', storage_path("app/public/{$pdfPath}"));
            $constat->pdf_url = $pdfPath;
            $constat->pdf_hash = $pdfHash;

            // Compute HMAC with server secret
            $hmac = hash_hmac('sha256', $pdfHash, env('PDF_SECRET_KEY'));
            // Store HMAC securely if needed
        }

        // Update attachments
        if ($request->has('attachments')) {
            $attachmentsUrls = $constat->attachments_urls ?? [];
            foreach ($request->file('attachments') as $attachment) {
                $attachmentsUrls[] = $attachment->store('constat_attachments', 'public');
            }
            $constat->attachments_urls = $attachmentsUrls;
        }

        // Update other fields
        $constat->latitude = $validated['latitude'] ?? $constat->latitude;
        $constat->longitude = $validated['longitude'] ?? $constat->longitude;
        $constat->driver_a_id = $validated['driver_a_id'] ?? $constat->driver_a_id;
        $constat->driver_b_id = $validated['driver_b_id'] ?? $constat->driver_b_id;
        $constat->company_a_id = $validated['company_a_id'] ?? $constat->company_a_id;
        $constat->company_b_id = $validated['company_b_id'] ?? $constat->company_b_id;

        $constat->save();

        return response()->json($constat);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $constat = Constat::findOrFail($id);

        if ($constat->pdf_url) {
            Storage::disk('public')->delete($constat->pdf_url);
        }

        // Delete attachments
        if ($constat->attachments_urls) {
            foreach ($constat->attachments_urls as $url) {
                Storage::disk('public')->delete($url);
            }
        }

        $constat->delete();

        return response()->json(['message' => 'Constat deleted successfully']);
    }

    /**
     * Get all constats related to a specific driver
     */
    public function constatsForDriver($driverId)
    {
        $constats = Constat::with(['driverA', 'driverB', 'companyA', 'companyB'])
                           ->forDriver($driverId)
                           ->get();

        return response()->json($constats);
    }

    /**
     * Verify if the PDF has been modified using HMAC
     */
    public function verifyPdf(string $id)
    {
        $constat = Constat::findOrFail($id);

        if (!Storage::disk('public')->exists($constat->pdf_url)) {
            return response()->json([
                'status' => 'error',
                'message' => 'PDF file not found'
            ], 404);
        }

        // Compute current hash
        $currentHash = hash_file('sha256', storage_path("app/public/{$constat->pdf_url}"));

        // Compute HMAC using server secret
        $currentHmac = hash_hmac('sha256', $currentHash, env('PDF_SECRET_KEY'));

        // Compute original HMAC based on stored pdf_hash
        $originalHmac = hash_hmac('sha256', $constat->pdf_hash, env('PDF_SECRET_KEY'));

        if (hash_equals($originalHmac, $currentHmac)) {
            return response()->json([
                'status' => 'ok',
                'message' => 'PDF is unmodified'
            ]);
        } else {
            return response()->json([
                'status' => 'tampered',
                'message' => 'PDF has been modified!'
            ]);
        }
    }
     public function createJoinableConstat(Request $request)
    {
        $validated = $request->validate([
            'driver_a_id' => 'required|exists:drivers,id',
        ]);

        // Generate a 6-digit code
        $code = mt_rand(100000, 999999);

        // Store in cache temporarily (e.g., 10 minutes)
        Cache::put("constat_join_code:{$code}", $validated['driver_a_id'], now()->addMinutes(10));

        return response()->json([
            'message' => 'Constat creation initialized. Share this code with the other driver.',
            'code' => $code,
        ]);
    }

    /**
     * Join an existing constat using the 6-digit code.
     */
    public function joinConstat(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'code' => 'required|digits:6',
        ]);

        $driverAId = Cache::get("constat_join_code:{$validated['code']}");

        if (!$driverAId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired code.'
            ], 404);
        }

        // Create the constat in DB with driverA and driverB
        $constat = Constat::create([
            'driver_a_id' => $driverAId,
            'driver_b_id' => $validated['driver_id'],
            // Other fields can be null for now
            'pdf_url' => null,
            'pdf_hash' => null,
            'company_a_id' => null,
            'company_b_id' => null,
            'latitude' => null,
            'longitude' => null,
            'attachments_urls' => [],
        ]);

        // Delete cache so the code cannot be reused
        Cache::forget("constat_join_code:{$validated['code']}");

        return response()->json([
            'status' => 'ok',
            'message' => 'Constat successfully joined!',
            'constat_id' => $constat->id,
        ], 201);
    }
}
