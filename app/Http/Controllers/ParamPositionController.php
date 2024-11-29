<?php

namespace App\Http\Controllers;

use App\Models\ParamPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;



class ParamPositionController extends Controller
{
    public function index()
    {
        $parpositions = ParamPosition::all();
        return view('pages.param_position.param-position', compact('parpositions'));
    }

    public function create()
    {
        return view('pages.param_position.create'); 
    }

    public function edit($id)
    {
        $paramPosition = ParamPosition::find($id);
        return view('pages.param_position.edit', compact('paramPosition'));
    }

    public function storeEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:param_positions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $paramPosition = ParamPosition::findOrFail($id);
            $paramPosition->update ([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $paramPosition
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:param_positions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $paramPosition = ParamPosition::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $paramPosition
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ], 500);
        }
    }
    
    public function list()
    {
        $parampositions = ParamPosition::select('id', 'name')->get();
        return response()->json($parampositions);
    }
}
