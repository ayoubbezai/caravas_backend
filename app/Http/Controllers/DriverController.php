<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDriverRequest;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers.
     */
    public function index()
    {
        try {
            $drivers = Driver::all();
            return response()->json([
                'success' => true,
                'data' => $drivers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve drivers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified driver.
     */
    public function show(Driver $driver)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve driver.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created driver.
     */
    public function store(AddDriverRequest $request)
    {
        try {
            $driver = Driver::create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Driver created successfully.',
                'data' => $driver
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create driver.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified driver.
     */
    public function update(AddDriverRequest $request, Driver $driver)
    {
        try {
            $driver->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Driver updated successfully.',
                'data' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update driver.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified driver.
     */
    public function destroy(Driver $driver)
    {
        try {
            $driver->delete();
            return response()->json([
                'success' => true,
                'message' => 'Driver deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete driver.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
