<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\ParamPosition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('paramposition')->get();
        
        return view('pages.position.position', compact('positions'));
    }

    public function create()
    {
        $paramPositions = ParamPosition::all();
        return view('pages.position.create', compact('paramPositions'));
    }

    public function edit($id)
    {
        $position = Position::with('paramposition')->find($id);
        $paramPositions = ParamPosition::all();
        $html = view('pages.position.edit', compact('position', 'paramPositions'))->render();

        return response()->json([
            'html' => $html,
            'parent_id' => $position->parent_id,
        ]);
    }

    public function getPositionName(Request $request)
    {
        $position = Position::find($request->parent_id);
        return response()->json(['name' => $position->name]);
    }

    public function storeEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:positions,name,' . $id,
            'code' => 'required|max:20|unique:positions,code,' . $id,
            'param_position_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $position = Position::findOrFail($id);

            $position->update([
                'name' => $request->name,
                'code' => $request->code,
                'parent_id' => $request->parent_id,
                'fk_parposition' => $request->param_position_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data'    => $position
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        // Log input request
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:positions,name',
            'code' => 'required|max:20|unique:positions,code',
            'param_position_id' => 'required|integer'
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
            $Position = Position::create([
                'name' => $request->name,
                'code' => $request->code,
                'fk_parposition' => $request->param_position_id,
            ]);

            // Log data yang berhasil disimpan
            Log::info("Berhasil Menyimpan: " . json_encode($Position));

            // Kirim response sukses
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $Position
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

    public function list()
    {
        $positions = Position::select('id', 'name')->get();
        return response()->json($positions);
    }
}
