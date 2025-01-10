<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{




    public function index()
    {
        $permissions = Permission::all();
        return view('pages.permissions.permissions', compact('permissions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web', // Ganti guard jika diperlukan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully.',
                'data' => $permission,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $permission = Permission::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $permission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        try {
            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            $permission->save();

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.',
                'data' => $permission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating permission: ' . $e->getMessage(),
            ], 500);
        }
    }
}
