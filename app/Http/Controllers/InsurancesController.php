<?php

namespace App\Http\Controllers;

use App\Models\Insurances;
use Illuminate\Http\Request;

class InsurancesController extends Controller
{
    /**
     * Display a list of all insurances.
     */
    public function index()
    {
        try {
            $insurances = Insurances::with(['driver', 'company'])->get();
            return response()->json([
                'success' => true,
                'data' => $insurances
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve insurances.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific insurance.
     */
    public function show(Insurances $insurance)
    {
        try {
            $insurance->load(['driver', 'company']);
            return response()->json([
                'success' => true,
                'data' => $insurance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve insurance.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new insurance.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'policy_number' => 'required|string',
                'company_name' => 'required|string',
                'valid_from' => 'nullable|date',
                'valid_until' => 'nullable|date|after_or_equal:valid_from',
                'driver_id' => 'required|exists:drivers,id',
                'company_id' => 'required|exists:companies,id',
                'agency_name' => 'nullable|string',
                'agency_address' => 'nullable|string',
                'agency_phone' => 'nullable|string',
                'is_created_by_typing' => 'boolean',
            ]);

            $insurance = Insurances::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Insurance created successfully.',
                'data' => $insurance
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create insurance.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing insurance.
     */
    public function update(Request $request, Insurances $insurance)
    {
        try {
            $validated = $request->validate([
                'policy_number' => 'sometimes|string',
                'company_name' => 'sometimes|string',
                'valid_from' => 'nullable|date',
                'valid_until' => 'nullable|date|after_or_equal:valid_from',
                'driver_id' => 'sometimes|exists:drivers,id',
                'company_id' => 'sometimes|exists:companies,id',
                'agency_name' => 'nullable|string',
                'agency_address' => 'nullable|string',
                'agency_phone' => 'nullable|string',
                'is_created_by_typing' => 'boolean',
            ]);

            $insurance->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Insurance updated successfully.',
                'data' => $insurance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update insurance.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an insurance.
     */
    public function destroy(Insurances $insurance)
    {
        try {
            $insurance->delete();
            return response()->json([
                'success' => true,
                'message' => 'Insurance deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete insurance.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
