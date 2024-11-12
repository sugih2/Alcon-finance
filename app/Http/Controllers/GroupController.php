<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('pages.group.group', compact('groups'));
    }

    public function create()
    {
        return view('pages.group.create');
    }

    public function edit($id)
    {
        $groups = Group::find($id);
        $html = view('pages.group.edit', compact('groups'))->render();

        return response()->json([
            'html' => $html,
            'project_id' => $groups->project_id,
            'leader_id' => $groups->leader_id,
        ]);
    }

    public function store(Request $request)
    {
        // Log input request
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25',
            'code' => 'required|max:10|unique:groups,code',
            'project' => 'required|integer',
            'leader' => 'required|integer'
        ]);

        // Jika validasi gagal, kirim response error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Buat dan simpan data ke model
            $groups = Group::create([
                'name' => $request->name,
                'code' => $request->code,
                'project_id' => $request->project,
                'leader_id' => $request->leader,
            ]);

            // Log data yang berhasil disimpan
            Log::info("Berhasil Menyimpan: " . json_encode($groups));

            // Kirim response sukses
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $groups
            ], 201);

        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error("Error: " . $e->getMessage());

            // Kirim response gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ], 500);
        }
    }
}
