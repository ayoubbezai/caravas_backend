<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCompanyRequest;
use App\Models\User;
use App\Models\Companies;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Companies::with('user')->get();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddCompanyRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create user first
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'company_admin',
            ]);

            // Create company linked to the user
            $company = Companies::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'name_of_company' => $request->name_of_company,
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Company and user created successfully',
                'company' => $company,
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create company and user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Companies::with('user')->findOrFail($id);
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $company = Companies::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20',
            'name_of_company' => 'sometimes|string|max:255|unique:companies,name_of_company,' . $id,
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $company = Companies::findOrFail($id);
            $user = $company->user;

            $company->delete();
            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'Company and user deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete company and user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
