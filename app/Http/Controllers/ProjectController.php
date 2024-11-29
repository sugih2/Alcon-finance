<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Regency;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('regency')->get();
        return view('pages.project.project', compact('projects'));
    }

    public function create()
    {
        return view('pages.project.create');
    }

    public function edit($id)
    {
        $projects = Project::find($id);
        $html = view('pages.project.edit', compact('projects'))->render();
    
        return response()->json([
            'html' => $html,
            'regency_id' => $projects->regency_id,
        ]);
    }

    public function store(Request $request)
    {
        // Log input request
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:projects,name',
            'code' => 'required|max:20|unique:projects,code',
            'regency' => 'required|integer'
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
            $projects = Project::create([
                'name' => $request->name,
                'code' => $request->code,
                'jenis' => $request->jenis,
                'description' => $request->description,
                'regency_id' => $request->regency,
            ]);

            // Log data yang berhasil disimpan
            Log::info("Berhasil Menyimpan: " . json_encode($projects));

            // Kirim response sukses
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $projects
            ], 201);

        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error("Error: " . $e->getMessage());

            // Kirim response gagal
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:projects,name,' . $id,
            'code' => 'required|max:20|unique:projects,code,' . $id,
            'regency' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $project = Project::findOrFail($id);

            $project->update([
                'name' => $request->name,
                'code' => $request->code,
                'jenis' => $request->jenis,
                'description' => $request->description,
                'regency_id' => $request->regency,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    => $project
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data'
            ], 500);
        }
    }

    public function list()
    {
        $projects = Project::select('id', 'name')->get();
        return response()->json($projects);
    }

    public function getProjectName(Request $request)
    {
        $projects = Project::find($request->project_id);
        return response()->json(['name' => $projects->name]);
    }
}
