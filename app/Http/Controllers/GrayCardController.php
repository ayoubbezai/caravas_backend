<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\GrayCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GrayCardController extends Controller
{
    /**
     * Display all gray cards with driver information.
     */
    public function index()
    {
        try {
            $grayCards = GrayCard::with('driver')->get();

            return response()->json([
                'success' => true,
                'data' => $grayCards
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gray cards.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the gray card for a specific driver.
     */
    public function show($driverId)
    {
        try {
            $grayCard = GrayCard::where('driver_id', $driverId)->first();

            return response()->json([
                'success' => true,
                'data' => $grayCard
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gray card.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store or update a gray card for a driver.
     */
    public function storeOrUpdate(Request $request, $driverId)
    {
        try {
            // Validate the driver exists
            Driver::findOrFail($driverId);

            $validated = $request->validate([
                'card_number' => 'nullable|string|max:50',
                'car_name' => 'required|string|max:255',
                'car_type' => 'required|string|max:255'
            ]);

            // Add driver_id to validated data
            $validated['driver_id'] = $driverId;

            // Update or create the gray card
            $grayCard = GrayCard::updateOrCreate(
                ['driver_id' => $driverId],
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Gray card ' . ($grayCard->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.',
                'data' => $grayCard
            ], Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], Response::HTTP_NOT_FOUND);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process gray card.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the gray card for a driver.
     */
    public function destroy($driverId)
    {
        try {
            $grayCard = GrayCard::where('driver_id', $driverId)->first();

            if (!$grayCard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gray card not found for this driver.',
                ], Response::HTTP_NOT_FOUND);
            }

            $grayCard->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gray card deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete gray card.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
