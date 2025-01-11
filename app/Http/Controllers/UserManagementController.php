<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;




class UserManagementController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:users.index', ['only' => ['index_user', 'getDataUser']]);
        $this->middleware('permission:users.create', ['only' => ['storeUser', 'getDataUser']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'updateUser']]);
        $this->middleware('permission:users.delete', ['only' => ['destroyUser']]);
    }

    public function index_user()
    {

        $isSuperAdmin = auth()->user()->hasRole('super_admin');


        $users = User::when(request()->q, function ($query) {
            $query->where('name', 'like', '%' . request()->q . '%');
        })
            ->when(!$isSuperAdmin, function ($query) {
                $query->where('id', auth()->user()->id);
            })
            ->with('roles')
            ->latest()
            ->get();

        $roles = Role::all();
        return view('pages.admin.index-user', compact('users', 'roles'));
    }

    public function getDataUser()
    {

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

    public function showUser(User $user)
    {
        return response()->json($user);
    }


    public function storeUser(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);


        $user = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active',
        ]);


        $user->assignRole($request->role);

        Alert::success('Success', 'User berhasil ditambahkan dengan role!');

        return redirect()->back();
    }

    public function updateUser(Request $request, User $user)
    {

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);


        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }


        $user->update($data);


        $user->syncRoles($request->role);

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
