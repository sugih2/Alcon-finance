<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\User;
//use App\Models\Role;
use App\Models\Menu;
use App\Models\RolePermission;
use App\Models\MenuPermission;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;



class UserManagementController extends Controller
{
    // public function index()
    // {

    //     // $users = User::with('roles.permissions')->get()->map(function ($user) {
    //     //     return [
    //     //         'id' => $user->id,
    //     //         'name' => $user->name,
    //     //         'email' => $user->email,
    //     //         'role' => $user->roles->map(function ($role) {
    //     //             return [
    //     //                 'role_name' => $role->name,
    //     //                 'permissions' => $role->permissions->pluck('name')->toArray(),  // Mendapatkan semua permissions dari role
    //     //             ];
    //     //         }),
    //     //     ];
    //     // });

    //     // dd($users);


    //     $users = User::with('role')->get();
    //     $roles = Role::all();
    //     $menus = Menu::all();

    //     return view('pages.admin.user-management', compact('users', 'roles', 'menus'));
    // }
    public function index(Request $request)
    {
        // $users = User::with('roles.permissions')->get();
        //  dd($users);
        // $users = User::with('roles.permissions')->get()->map(function ($user) {
        //     return [
        //         'id' => $user->id,
        //         'name' => $user->username,
        //         'email' => $user->email,
        //         'created_at' => Carbon::parse($user->created_at)->format('d/m/Y'), // Format dd/mm/yyyy
        //         'roles' => $user->roles->map(function ($role) {
        //             return [
        //                 'name' => $role->name, // Ambil nama role langsung
        //             ];
        //         })->toArray(), // Konversi roles menjadi array
        //     ];
        // });
        $role = auth()->user()->getRoleNames();

        if ($role[0] == 'super_admin') {

            $users = User::when(request()->q, function ($query) {
                $query->where('name', 'like', '%' . request()->q . '%');
            })->with('roles')->latest()->get();
        } else {

            $users = User::when(request()->q, function ($query) {
                $query->where('name', 'like', '%' . request()->q . '%');
            })->where('id', auth()->user()->id)->with('roles')->latest()->get();
        }
        dd($users);


        // Jika bukan request ajax, ambil data untuk view
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
        // Muat data user beserta relasi roles
        $users = User::with('roles')->select('users.*');

        return DataTables::of($users)
            ->addColumn('name', function ($user) {
                return $user->firstname . ' ' . $user->lastname;
            })
            ->addColumn('role_name', function ($user) {
                // Ambil nama role pertama atau default jika tidak ada role
                return $user->roles->pluck('name')->first() ?? 'No Role Assigned';
            })
            ->addColumn('delete_url', function ($user) {
                return route('users.destroy', $user->id);
            })
            ->make(true);
    }

    public function storeUser(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name', // Validasi peran (harus ada di tabel roles)
        ]);
        $role_n = 0;
        // Membuat user baru
        $user = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'role_id' => $role_n,
            'password' => bcrypt($request->password), // Enkripsi password
            'status' => 'active',
        ]);

        // Menambahkan role ke user
        $user->assignRole($request->role);

        // Notifikasi berhasil
        Alert::success('Success', 'User berhasil ditambahkan dengan role!');

        // Redirect kembali ke halaman sebelumnya
        return redirect()->back();
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name', // Validasi role
        ]);

        // Data untuk diperbarui
        $role_n = 0;
        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'role_id' => $role_n,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Update data pengguna
        $user->update($data);

        // Update role pengguna
        $user->syncRoles($request->role); // Hapus role lama dan tetapkan role baru

        Alert::success('Success', 'User berhasil diperbarui!');

        return redirect()->back();
    }




    // public function storeUser(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string|max:255',
    //         'firstname' => 'required|string|max:255',
    //         'lastname' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:8',
    //         //'role_id' => 'required|exists:roles,id',
    //     ]);
    //     $role_n = 0;
    //     User::create([
    //         'username' => $request->username,
    //         'firstname' => $request->firstname,
    //         'lastname' => $request->lastname,
    //         'email' => $request->email,
    //         'password' => $request->password,
    //         'role_id' => $role_n,
    //         'status' => 'active',
    //     ]);
    //     $user->assignRole($request->role);
    //     Alert::success('Success', 'User berhasil ditambahkan!');

    //     return redirect()->back();
    // }

    // public function updateUser(Request $request, User $user)
    // {
    //     $request->validate([
    //         'firstname' => 'required|string|max:255',
    //         'lastname' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'role_id' => 'required|exists:roles,id',
    //         'password' => 'nullable|string|min:8',
    //     ]);

    //     $data = [
    //         'firstname' => $request->firstname,
    //         'lastname' => $request->lastname,
    //         'email' => $request->email,
    //         'role_id' => $request->role_id,
    //     ];

    //     if ($request->filled('password')) {
    //         $data['password'] = $request->password;
    //     }

    //     $user->update($data);

    //     Alert::success('Success', 'User berhasil diperbarui!');

    //     return redirect()->back();
    // }


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
        $guard_name = 'web';
        Role::create([
            'name' => $request->name,
            'guard_name' => $guard_name,
        ]);

        Alert::success('Success', 'Role berhasil ditambahkan!');

        return redirect()->back();
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $guard_name = 'web';
        $role->update([
            'name' => $request->name,
            'guard_name' => $guard_name,
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


        MenuPermission::upsert($menuPermissionsData, ['menu_id'], ['updated_at']);


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
