<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\DriverLesson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DriverLessonsController extends Controller
{
    /**
     * Display a listing of lessons for a specific driver.
     */
    public function index($driverId)
    {
        try {
            $lessons = DriverLesson::where('driver_id', $driverId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $lessons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve driver lessons.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created lesson for a driver.
     */
    public function store(Request $request, $driverId)
    {
        try {
            // Validate the driver exists
            $driver = Driver::findOrFail($driverId);

            // Validate the request data
            $validated = $request->validate([
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before:today',
                'address' => 'required|string|max:500',
                'postal_code' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'phone_email' => 'required|string|max:255',
                'license_number' => 'required|string|max:50',
                'license_category' => 'required|string|max:10|in:A,B,C,D,BE,CE,DE',
                'license_valid_until' => 'required|date|after:today',
            ]);

            // Add driver_id to validated data
            $validated['driver_id'] = $driverId;

            $lesson = DriverLesson::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Driver lesson created successfully.',
                'data' => $lesson
            ], Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
                'error' => $e->getMessage()
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
                'message' => 'Failed to create driver lesson.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified lesson.
     */
    public function show($driverId, $lessonId)
    {
        try {
            $lesson = DriverLesson::where('driver_id', $driverId)
                ->findOrFail($lessonId);

            return response()->json([
                'success' => true,
                'data' => $lesson
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Driver lesson not found.',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve driver lesson.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified lesson.
     */
    public function update(Request $request, $driverId, $lessonId)
    {
        try {
            $lesson = DriverLesson::where('driver_id', $driverId)
                ->findOrFail($lessonId);

            $validated = $request->validate([
                'last_name' => 'sometimes|required|string|max:255',
                'first_name' => 'sometimes|required|string|max:255',
                'date_of_birth' => 'sometimes|required|date|before:today',
                'address' => 'sometimes|required|string|max:500',
                'postal_code' => 'sometimes|required|string|max:20',
                'city' => 'sometimes|required|string|max:255',
                'country' => 'sometimes|required|string|max:255',
                'phone_email' => 'sometimes|required|string|max:255',
                'license_number' => 'sometimes|required|string|max:50',
                'license_category' => 'sometimes|required|string|max:10|in:A,B,C,D,BE,CE,DE',
                'license_valid_until' => 'sometimes|required|date|after:today',
            ]);

            $lesson->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Driver lesson updated successfully.',
                'data' => $lesson
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Driver lesson not found.',
                'error' => $e->getMessage()
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
                'message' => 'Failed to update driver lesson.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy($driverId, $lessonId)
    {
        try {
            $lesson = DriverLesson::where('driver_id', $driverId)
                ->findOrFail($lessonId);

            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Driver lesson deleted successfully.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Driver lesson not found.',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete driver lesson.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all lessons (across all drivers) - optional
     */
    public function getAllLessons()
    {
        try {
            $lessons = DriverLesson::with('driver')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $lessons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all lessons.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
