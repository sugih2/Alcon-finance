<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use App\Models\RolePermission;
use App\Models\MenuPermission;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        $menus = Menu::all();
        return view('pages.admin.user-management', compact('users', 'roles', 'menus'));
    }

    public function roles_index()
    {
        $roles = Role::all();
        return view('pages.admin.roles-management', compact('roles'));
    }

    public function getDataUser()
    {
        $users = User::with('role')->select('users.*');

        return DataTables::of($users)
            ->addColumn('name', function ($user) {
                return $user->firstname . ' ' . $user->lastname;
            })
            ->addColumn('delete_url', function ($user) {
                return route('users.destroy', $user->id);
            })
            ->make(true);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
            'status' => 'active',
        ]);

        Alert::success('Success', 'User berhasil ditambahkan!');

        return redirect()->back();
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        Alert::success('Success', 'User berhasil diperbarui!');

        return redirect()->back();
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        Alert::success('Success', 'User berhasil dihapus!');
        return redirect()->back();
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create([
            'name' => $request->name,
        ]);

        Alert::success('Success', 'Role berhasil ditambahkan!');

        return redirect()->back();
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        Alert::success('Success', 'Role berhasil diperbarui!');
        return redirect()->back();
    }

    public function destroyRole(Role $role)
    {
        $role->delete();
        Alert::success('Success', 'Role berhasil dihapus!');
        return redirect()->back();
    }

    public function getDataMenu()
    {
        $menus = Menu::all();

        return response()->json($menus);
    }

    public function showDataMenu($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        return response()->json($menu);
    }

    public function storeDataMenu(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'urutan' => 'nullable|integer',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $menu = Menu::create($request->all());

        return response()->json($menu);
    }
    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'urutan' => 'nullable|integer',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $menu = Menu::findOrFail($id); // Temukan menu berdasarkan ID
        $menu->update($request->all()); // Perbarui data menu

        return response()->json($menu);
    }
    public function destroyMenu($id)
    {
        $menu = Menu::findOrFail($id); // Temukan menu berdasarkan ID
        $menu->delete(); // Hapus menu

        return response()->json(['message' => 'Menu deleted successfully.']);
    }
    public function getPermissions(Role $role)
    {
        $menus = Menu::with(['menuPermissions.rolePermissions' => function ($query) use ($role) {
            $query->where('role_id', $role->id);
        }])->get();

        return view('pages.admin.permissions-partial', compact('menus', 'role'))->render();
    }
    public function savePermissions(Request $request, $roleId)
    {
        $menus = Menu::all();

        $menuPermissionsData = [];
        $rolePermissionsData = [];

        foreach ($menus as $menu) {
            $menuPermissionsData[] = [
                'menu_id' => $menu->id,
                'c' => false,
                'r' => false,
                'u' => false,
                'd' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Cek apakah menu_permission sudah ada
            $menuPermission = MenuPermission::where('menu_id', $menu->id)->first();

            if ($menuPermission) {
                $rolePermissionsData[] = [
                    'menu_permission_id' => $menuPermission->id,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Buat atau perbarui menu_permissions
        MenuPermission::upsert($menuPermissionsData, ['menu_id'], ['updated_at']);

        // Ambil semua menu_permissions terbaru
        $menuPermissions = MenuPermission::whereIn('menu_id', $menus->pluck('id'))->get();

        foreach ($menuPermissions as $menuPermission) {
            $rolePermissionsData[] = [
                'menu_permission_id' => $menuPermission->id,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Simpan role_permissions
        RolePermission::upsert($rolePermissionsData, ['menu_permission_id', 'role_id'], ['updated_at']);

        return response()->json(['message' => 'Permissions saved successfully']);
    }
}
