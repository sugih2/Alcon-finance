<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParamComponen;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ParamComponenController extends Controller
{
    public function index()
    {
        $componens = ParamComponen::all();
        return view('pages.param_componen.index', compact('componens'));
    }

    public function create()
    {
        return view('pages.param_componen.create');
    }

    public function edit($id)
    {
        $param_componen = ParamComponen::find($id);
        $html = view('pages.param_componen.edit', compact('param_componen'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    public function store(Request $request)
    {
        // Log input request
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'type' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Jika validasi gagal, kirim response error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $param_componen = new ParamComponen();
            $param_componen->name = $request->name;
            $param_componen->type = $request->type;
            $param_componen->amount = $request->amount;
            $param_componen->status = "Active";
            $param_componen->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
            ]);
        
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getComponentList(Request $request)
    {
        $searchTerm = $request->input('q');
        $querycomponent = ParamComponen::where('status', '=', 'Active')
        ->where('name', 'like', "%$searchTerm%")
        ->select('id', 'name', 'type')
        ->orderBy('name', 'asc')
        ->distinct()
        ->get();

        $component = $querycomponent->map(function($component) {
            return [
                'id' => $component->id,
                'nama' => $component->name,
                'komponen' => $component->type,
            ];
        });
        
        return response()->json($component);
    }
}
