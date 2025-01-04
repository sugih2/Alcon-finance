<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function __invoke(Request $request)
    {
        $permissions = Permission::when($request->q, function ($query) {
            return $query->where('name', 'like', '%' . request()->q . '%');
        })
            ->orderBy('id', 'asc')
            ->get();

        return view('permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $guard_name = "api";
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $guard_name,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil dibuat.');
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')->with('error', 'Terjadi kesalahan saat menghapus permission: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        try {
            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            $permission->save();

            return redirect()->route('permissions.index')->with('success', 'Permission berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')->with('error', 'Terjadi kesalahan saat memperbarui permission: ' . $e->getMessage());
        }
    }
}
