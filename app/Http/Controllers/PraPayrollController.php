<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\ParamComponen;
use App\Models\MasterPayroll;
use App\Models\DetailPayroll;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PraPayrollController extends Controller
{
    public function index()
    {
        $masterPayrolls = MasterPayroll::all();
        $detailPayrolls = DetailPayroll::all();
        return view('pages.pra_payroll.index', compact('masterPayrolls', 'detailPayrolls'));
    }

    public function adjusment()
    {
        return view('pages.adjusment.index');
    }

    public function employee()
    {
        return view('pages.adjusment.employee');
    }

    public function component()
    {
        return view('pages.adjusment.component');
    }

    public function storeselectkar(Request $request)
    {
        $employeeData = $request->all();
        $finalEmployeeData = [];
        foreach ($employeeData as $employee) {
            $employeeId = $employee['id'];
    
            $karyawan = Employee::findOrFail($employeeId);
    
            $finalEmployeeData[] = [
                'id' => $karyawan->id,
                'nama_lengkap' => $karyawan->name,
                'nomor_induk_karyawan' => $karyawan->nip,
                'jabatan' => $karyawan->position->name
            ];
        }
        session()->put('selectedEmployees', $finalEmployeeData);

        return response()->json($finalEmployeeData);
    }

    public function storeselectcom(Request $request)
    {
        $components = $request->all();
        $employeeData = session()->get('selectedEmployees');

        if (!$employeeData) {
            return response()->json([
                'success' => false,
                'message' => 'No employees selected.'
            ], 400);
        }

        foreach ($components as $componentReq) {
            if (isset($componentReq['id_com'])) {
                $componentId = $componentReq['id_com'];

                $componentData = ParamComponen::select('name', 'type', 'amount')
                    ->findOrFail($componentId);

                foreach ($employeeData as &$employee) {
                    $employee['componentData'][] = [
                        'id_com' => $componentId,
                        'nama' => $componentData->name,
                        'komponen' => $componentData->type,
                        'nilai' => $componentData->amount,
                    ];
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing id_com in request data',
                    'errorData' => $componentReq
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Components successfully stored.',
            'employees' => $employeeData,
        ]);
    }

    public function storeadjusment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'efektif_date' => 'required|date',
            'description' => 'nullable|string',
            'employeedata' => 'required|array',
            'employeedata.*.id' => 'required|integer',
            'employeedata.*.nomor_induk_karyawan' => 'required|string',
            'employeedata.*.nama_lengkap' => 'required|string',
            'employeedata.*.jabatan' => 'required|string',
            'employeedata.*.components' => 'required|array',
            'employeedata.*.components.*.id_com' => 'required|integer',
            'employeedata.*.components.*.component_name' => 'required|string',
            'employeedata.*.components.*.last_amount' => 'required|numeric',
            'employeedata.*.components.*.new_amount' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessages = [];

            foreach ($errors as $field => $errorMessagesForField) {
                $errorMessages[$field] = $errorMessagesForField[0];
            }

            return response()->json(['error' => $errorMessages], 400);
        }
        Log::info("Request Data Input: " . json_encode($request->all(), JSON_PRETTY_PRINT));
        try {
            DB::beginTransaction();

            $prefix = 'PP';
            $id_transaksi = MasterPayroll::generateIdTransaksi($prefix);

            // $total_karyawan = count($request->employeedata);
            // $uniqueComponents = [];
            // $total_transaksi = 0;
            // $total_payment = 0;

            // foreach ($request->employeedata as $employee) {
            //     foreach ($employee['components'] as $component) {
            //         if (!in_array($component['id_com'], $uniqueComponents)) {
            //             $uniqueComponents[] = $component['id_com'];
            //         }
            //         $total_transaksi++;

            //         $amount = isset($component['new_amount']) && !empty($component['new_amount']) 
            //             ? $component['new_amount'] 
            //             : $component['last_amount'];

            //         $total_payment += $amount;
            //     }
            // }

            // $total_component = count($uniqueComponents);

            $settingTunjangan = MasterPayroll::create([
                'id_transaksi' => $id_transaksi,
                'type' => $request->type,
                'efektif_date' => $request->efektif_date,
                'description' => $request->description,
                'end_date' => $request->end_date,
            ]);

            foreach ($request->employeedata as $employee) {
                foreach ($employee['components'] as $component) {
                    $amount = isset($component['new_amount']) && !empty($component['new_amount']) 
                        ? $component['new_amount'] 
                        : $component['last_amount'];

                    DetailPayroll::create([
                        'id_employee' => $employee['id'],
                        'id_component' => $component['id_com'],
                        'id_transaksi' => $settingTunjangan->id_transaksi,
                        'amount' => $amount,
                    ]);
                }
            }

            DB::commit();
            session()->forget('selectedEmployees');
            return response()->json(['message' => 'Data Param Payroll berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
