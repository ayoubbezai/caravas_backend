<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDriverRequest;
use App\Models\Driver;
use App\Models\User;
use App\Models\Companies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
}
