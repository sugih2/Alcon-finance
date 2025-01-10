<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;




class UserManagementController extends Controller
{
    //     public function index_user()
    //     {

    //         $role = auth()->user()->getRoleNames()->first();
    //         $isSuperAdmin = $role === 'super_admin';

    //         $users = User::when(request()->q, function ($query) {
    //             $query->where('name', 'like', '%' . request()->q . '%');
    //         })
    //             ->when(!$isSuperAdmin, function ($query) {
    //                 $query->where('id', auth()->user()->id);
    //             })
    //             ->with('roles')
    //             ->latest()
    //             ->get();
    //         //  dd($users->toArray());
    //         $roles = Role::all();
    //         return view('pages.admin.index-user', compact('users', 'roles'));
    //     }



    //     public function getDataUser()
    //     {
    //         // Muat data user beserta relasi roles
    //         $users = User::with('roles')->select('users.*');

    //         return DataTables::of($users)
    //             ->addColumn('name', function ($user) {
    //                 return $user->firstname . ' ' . $user->lastname;
    //             })
    //             ->addColumn('role_name', function ($user) {
    //                 // Ambil nama role pertama atau default jika tidak ada role
    //                 return $user->roles->pluck('name')->first() ?? 'No Role Assigned';
    //             })
    //             ->addColumn('delete_url', function ($user) {
    //                 return route('users.destroy', $user->id);
    //             })
    //             ->make(true);
    //     }

    //     public function storeUser(Request $request)
    //     {
    //         // Validasi input
    //         $request->validate([
    //             'username' => 'required|string|max:255',
    //             'firstname' => 'required|string|max:255',
    //             'lastname' => 'required|string|max:255',
    //             'email' => 'required|email|unique:users,email',
    //             'password' => 'required|string|min:8',
    //             'role' => 'required|exists:roles,name', // Validasi peran (harus ada di tabel roles)
    //         ]);
    //         $role_n = 0;
    //         // Membuat user baru
    //         $user = User::create([
    //             'username' => $request->username,
    //             'firstname' => $request->firstname,
    //             'lastname' => $request->lastname,
    //             'email' => $request->email,
    //             'password' => $request->password,
    //             'status' => 'active',
    //         ]);

    //         // Menambahkan role ke user
    //         $user->assignRole($request->role);

    //         // Notifikasi berhasil
    //         Alert::success('Success', 'User berhasil ditambahkan dengan role!');

    //         // Redirect kembali ke halaman sebelumnya
    //         return redirect()->back();
    //     }

    //     public function updateUser(Request $request, User $user)
    //     {
    //         $request->validate([
    //             'firstname' => 'required|string|max:255',
    //             'lastname' => 'required|string|max:255',
    //             'email' => 'required|email|unique:users,email,' . $user->id,
    //             'password' => 'nullable|string|min:8',
    //             'role' => 'required|exists:roles,name', // Validasi role
    //         ]);

    //         // Data untuk diperbarui
    //         $role_n = 0;
    //         $data = [
    //             'firstname' => $request->firstname,
    //             'lastname' => $request->lastname,
    //             'email' => $request->email,

    //         ];

    //         if ($request->filled('password')) {
    //             $data['password'] = bcrypt($request->password);
    //         }

    //         // Update data pengguna
    //         $user->update($data);

    //         // Update role pengguna
    //         $user->syncRoles($request->role); // Hapus role lama dan tetapkan role baru

    //         Alert::success('Success', 'User berhasil diperbarui!');

    //         return redirect()->back();
    //     }



    //     public function destroyUser(User $user)
    //     {
    //         $user->delete();
    //         Alert::success('Success', 'User berhasil dihapus!');
    //         return redirect()->back();
    //     }
    public function index_user()
    {
        // Memeriksa apakah pengguna memiliki role 'super_admin'
        $isSuperAdmin = auth()->user()->hasRole('super_admin');  // Pemeriksaan role yang lebih mudah dibaca

        // Mengambil data pengguna dengan filter pencarian dan role
        $users = User::when(request()->q, function ($query) {
            $query->where('name', 'like', '%' . request()->q . '%');
        })
            ->when(!$isSuperAdmin, function ($query) {
                $query->where('id', auth()->user()->id); // Jika bukan super admin, hanya tampilkan data diri sendiri
            })
            ->with('roles')
            ->latest()
            ->get();

        $roles = Role::all();
        return view('pages.admin.index-user', compact('users', 'roles'));
    }

    public function getDataUser()
    {
        // Muat data pengguna beserta relasi roles
        $users = User::with('roles')->select('users.*');

        return DataTables::of($users)
            ->addColumn('name', function ($user) {
                return $user->firstname . ' ' . $user->lastname;
            })
            ->addColumn('role_name', function ($user) {
                return $user->roles->pluck('name')->first() ?? 'No Role Assigned';
            })
            ->addColumn('delete_url', function ($user) {
                return route('users.destroy', $user->id);
            })
            ->make(true);
    }

    public function storeUser(Request $request)
    {
        // Validasi input pengguna
        $request->validate([
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        // Membuat pengguna baru dengan password yang di-hash
        $user = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,  // Pastikan password di-hash
            'status' => 'active',
        ]);

        // Menambahkan role ke pengguna
        $user->assignRole($request->role);

        Alert::success('Success', 'User berhasil ditambahkan dengan role!');

        return redirect()->back();
    }

    public function updateUser(Request $request, User $user)
    {
        // Validasi input untuk pembaruan
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        // Data yang akan diperbarui
        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password); // Pastikan password di-hash
        }

        // Memperbarui data pengguna
        $user->update($data);

        // Memperbarui role pengguna
        $user->syncRoles($request->role); // Menghapus role lama dan menambahkan role baru

        Alert::success('Success', 'User berhasil diperbarui!');

        return redirect()->back();
    }

    public function destroyUser(User $user)
    {
        // Menghapus pengguna
        $user->delete();
        Alert::success('Success', 'User berhasil dihapus!');
        return redirect()->back();
    }
}
