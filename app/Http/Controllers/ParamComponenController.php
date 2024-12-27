<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParamComponen;
use App\Models\Regency;
use App\Models\Position;
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
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'type' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->componen === 'Allowance') {
            $existingCategory = ParamComponen::where('id_regency', $request->id_regency)
                                        ->where('category', $request->category)
                                        ->first();

            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'message' => "Kategori '{$request->category}' untuk Regency '{$existingCategory->regency->name}' sudah ada. Silakan gunakan kategori lain."
                ], 422);
            }
        }

        if ($request->componen === 'Salary') {
            $existingParam = ParamComponen::where('id_position', $request->id_position)
                                        ->where('componen', 'Salary')
                                        ->first();
        
            if ($existingParam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komponen "Salary" untuk posisi ini sudah ada. Silakan pilih posisi atau komponen lain.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $param_componen = new ParamComponen();
            $param_componen->name = $request->name;
            $param_componen->id_position = $request->id_position;
            $param_componen->id_regency = $request->id_regency;
            $param_componen->componen = $request->componen;
            $param_componen->category = $request->category;
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

    public function getform($componentType)
    {
        if ($componentType == 'allowance') {
            $regency = Regency::select('id', 'name')->get();
        } elseif ($componentType == 'benefit') {
            $level_jabatan = ParLevelJabatan::select('id', 'nama')->get();
            $cabang = Cabang::select('id', 'nama')
                ->orderBy('nama', 'asc')
                ->get();
        } elseif ($componentType == 'deduction') {
            
        } elseif ($componentType == 'phl'){
            //$phl = PhlLevel::select('id', 'level')->get();
            $kota = Kota::select('id', 'name')->get();
        } else {
            $position = Position::select('id', 'name')->get();
        }

        if ($componentType == 'allowance') {
            return view('pages.param_componen.allowance', compact('regency'));
        } elseif ($componentType == 'benefit') {
            return view('parcom.benefit_form', compact('level_jabatan', 'cabang'));
        } elseif ($componentType == 'deduction') {
            return view('pages.param_componen.deduction');
        } elseif ($componentType == 'phl') {
            return view('parcom.phl_form', compact('kota'));
        } else {
            return view('pages.param_componen.salary', compact('position'));
        }
    }
}
