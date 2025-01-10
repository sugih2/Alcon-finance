<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    //public function __construct()
    // {
    //     // Pastikan hanya user dengan role 'admin' yang dapat mengakses controller ini
    //     $this->middleware('permission:create-roles', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:edit-roles', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:delete-roles', ['only' => ['destroy']]);
    // }

    // public function index(Request $request)
    // {
    //     $roles = Role::orderBy('id', 'DESC')->paginate(5);
    //     return view('pages.roles.index', compact('roles'))
    //         ->with('i', ($request->input('page', 1) - 1) * 5);
    // }
    public function index(Request $request)
    {

        $roles = Role::orderBy('id', 'DESC')->paginate(5);


        $permissions = Permission::all();

        if ($roles->isEmpty()) {
            return view('pages.roles.index', ['roles' => $roles, 'permissions' => $permissions])
                ->with('message', 'No roles available.');
        }

        return view('pages.roles.index', compact('roles', 'permissions'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $permissions = Permission::all();

        return view('pages.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);


        $existingRole = Role::where('name', $request->name)->first();

        if ($existingRole) {
            alert()->error('Role Already Exists', 'The role with this name already exists.');
            return redirect()->route('roles.index');
        }

        $guard_name = 'web';


        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $guard_name
        ]);


        $permissions = Permission::whereIn('id', $request->permissions)
            ->where('guard_name', 'web')
            ->get();

        $role->givePermissionTo($permissions);


        alert()->success('Role Created', 'The role has been successfully created.');
        return redirect()->route('roles.index');
    }





    public function show($id)
    {

        $role = Role::findOrFail($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->get();

        return view('pages.roles.show', compact('role', 'rolePermissions'));
    }

    // public function edit($id)
    // {
    //     $role = Role::with('permissions')->findOrFail($id);
    //     $permissions = Permission::all();

    //     return response()->json([
    //         'role' => $role,
    //         'permissions' => $permissions
    //     ]);
    // }
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();

        return response()->json([
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);

        // Menetapkan nama role yang diperbarui
        $role->name = $validated['name'];
        $role->save(); // Simpan perubahan nama role

        // Mendapatkan permission berdasarkan ID yang diberikan dan memastikan menggunakan nama
        $permissions = Permission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();

        // Sinkronisasi permission dengan nama yang sesuai
        $role->syncPermissions($permissions);

        return response()->json(['message' => 'Role updated successfully.']);
    }


    public function destroy($id)
    {
        // Menggunakan findOrFail dan destroy untuk menangani role yang tidak ditemukan
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
