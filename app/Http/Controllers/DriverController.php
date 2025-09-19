<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AddDriverRequest;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers.
     */
    public function index()
    {
        try {
            $drivers = Driver::with(['user', 'company'])->get();
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
            $driver->load(['user', 'company']);
            return response()->json([
                'success' => true,
                'data' => []
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
     * Store a newly created driver with user.
     */
    public function store(AddDriverRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create user first (without name field)
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'driver',
            ]);

            // Create driver with all required fields
            $driver = Driver::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'city' => $request->city,
                'country' => $request->country,
                'phone' => $request->phone,
                'is_created_by_typing' => $request->is_created_by_typing ?? false,
                'user_id' => $user->id,
                'company_id' => $request->company_id,
            ]);

            DB::commit();

            $driver->load(['user', 'company']);

            return response()->json([
                'success' => true,
                'message' => 'Driver and user created successfully.',
                'data' => []
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create driver and user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified driver.
     */
    public function update(AddDriverRequest $request, Driver $driver)
    {
        DB::beginTransaction();

        try {
            // Update driver fields
            $driver->update($request->only([
                'first_name',
                'last_name',
                'date_of_birth',
                'address',
                'postal_code',
                'city',
                'country',
                'phone',
                'is_created_by_typing',
                'company_id'
            ]));

            // Update user if email or password is provided
            if ($request->has('email') || $request->has('password')) {
                $userData = [];

                if ($request->has('email')) {
                    $userData['email'] = $request->email;
                }

                if ($request->has('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $driver->user->update($userData);
            }

            DB::commit();

            $driver->load(['user', 'company']);

            return response()->json([
                'success' => true,
                'message' => 'Driver updated successfully.',
                'data' => $driver
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update driver.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified driver and user.
     */
    public function destroy(Driver $driver)
    {
        DB::beginTransaction();

        try {
            $user = $driver->user;
            $driver->delete();
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Driver and user deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete driver and user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get drivers by company.
     */
    public function getByCompany($companyId)
    {
        try {
            $drivers = Driver::with(['user', 'company'])
                ->where('company_id', $companyId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $drivers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve drivers for company.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function profile()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Check if user is authenticated and is a driver
            if (!$user || $user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Driver access only.'
                ], 401);
            }

            // Load the driver profile with relationships
            $driver = Driver::with(['user', 'company'])
                ->where('user_id', $user->id)
                ->first();

            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver profile not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $driver
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve driver profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function details($id = null)
{
    try {
        // If no ID is provided, get the current authenticated driver
        if ($id === null) {
            $user = Auth::user();
            
            // Check if user is authenticated and is a driver
            if (!$user || $user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Driver access only.'
                ], 401);
            }
            
            $driver = Driver::with(['user', 'company', 'grayCard', 'Insurancess', 'lessons'])
                ->where('user_id', $user->id)
                ->first();
        } else {
            // Get specific driver by ID
            $driver = Driver::with(['user', 'company', 'grayCard', 'Insurancess', 'lessons'])
                ->find($id);
        }

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $driver
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve driver details.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
