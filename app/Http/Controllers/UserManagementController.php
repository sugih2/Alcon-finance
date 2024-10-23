<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;


class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('pages.admin.user-management', compact('users', 'roles'));
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
            'password' => bcrypt($request->password),
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
            'password' => 'nullable|string|min:8', // Tambahkan ini untuk password
        ]);

        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        // Jika password diisi, hash dan tambahkan ke data
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
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
}
