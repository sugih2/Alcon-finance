<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Employee;
use App\Models\SettingShift;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    public function index()
    {
        $presences = Presence::with('employee')->get(); // Asumsikan ada relasi dengan tabel karyawan
        return view('pages.presence.presence', compact('presences'));
    }

    public function create()
    {
        return view('pages.presence.create');
    }

    public function edit($id)
    {
        $presence = Presence::find($id);
        $html = view('pages.presence.edit', compact('presence'))->render();

        return response()->json([
            'html' => $html,
            'presence_id' => $presence->id,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id', // Validasi karyawan
            'date' => 'required|date',
            'status' => 'required|string|in:Present,Absent,Sick,Leave', // Contoh status presensi
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Simpan data presensi
            $presence = Presence::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil disimpan',
                'data' => $presence,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data presensi',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|string|in:Present,Absent,Sick,Leave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $presence = Presence::findOrFail($id);
            $presence->update([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil diupdate',
                'data' => $presence,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data presensi',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $presence = Presence::findOrFail($id);
            $presence->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data presensi',
            ], 500);
        }
    }

    public function list()
    {
        $presences = Presence::select('id', 'employee_id', 'date', 'status')->get();
        return response()->json($presences);
    }

    // public function processImport(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xls,xlsx,xml',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);
    //     Log::info('Request All: ', $request->all());
    //     $file = $request->file('file');
    //     $data = [];

    //     if ($file->getClientOriginalExtension() === 'xml') {
    //         $xmlContent = file_get_contents($file);
    //         $xml = simplexml_load_string($xmlContent);
    //         foreach ($xml->presence as $row) {
    //             $data[] = [
    //                 'tanggal_scan' => (string)$row->tanggal_scan,
    //                 'tanggal' => (string)$row->tanggal,
    //                 'jam' => (string)$row->jam,
    //                 'nip' => (string)$row->nip,
    //                 'nama' => (string)$row->nama,
    //                 'sn' => (string)$row->sn,
    //             ];
    //         }
    //         Log::info('Data Presen:' . json_encode($data));
    //     } else {
    //         $data = Excel::toArray([], $file)[0];
    //     }

    //     // Filter data by date range
    //     $filteredData = array_filter($data, function ($row) use ($request) {
    //         $date = isset($row['tanggal']) ? $row['tanggal'] : $row['tanggal_scan'];
    //         return $date >= $request->start_date && $date <= $request->end_date;
    //     });

    //     // Validasi Data
    //     $validatedData = $this->validatePresenceData($filteredData);

    //     return response()->json([
    //         'data' => $validatedData['data'],
    //         'invalidData' => $validatedData['invalid'],
    //     ]);
    // }


    public function processImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx,xml',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessages = [];

            foreach ($errors as $field => $errorMessagesForField) {
                $errorMessages[$field] = $errorMessagesForField[0];
            }

            return response()->json(['error' => $errorMessages], 400);
        }

        try {
            Log::info('Request All: ', $request->all());
            $file = $request->file('file');
            $data = [];
            $getStatus = [];

            if ($file->getClientOriginalExtension() === 'xml') {
                $fileContent = file_get_contents($file);
                $fileContent = preg_replace('/<\?xml:stylesheet(.*?)\?>/', '<?xml-stylesheet\1?>', $fileContent);
                $xml = simplexml_load_string($fileContent);

                if ($xml === false) {
                    Log::error('Error parsing XML: ' . implode(", ", libxml_get_errors()));
                    return response()->json(['error' => 'Failed to parse XML'], 400);
                }

                if (isset($xml->ROWS->ROW)) {

                    // Mengecek apakah NIP ada di database Employee

                    foreach ($xml->ROWS->ROW as $row) {
                        $nip = (string) $row['dbg_scanlogpegawai_nip'];
                        $isEmployee = Employee::where('nip', $nip)->exists();
                        $data[] = [
                            'tanggal_scan' => (string) $row['dbg_scanlogscan_date'] ?? null,
                            'tanggal' => (string) $row['dbg_scanlogtgl'] ?? null,
                            'jam' => (string) $row['dbg_scanlogjam'] ?? null,
                            'nip' => (string) $row['dbg_scanlogpegawai_nip'] ?? null,
                            'nama' => (string) $row['dbg_scanlogpegawai_nama'] ?? null,
                            'sn' => (string) $row['dbg_scanlogsn'] ?? null,
                            'status_karyawan' => $isEmployee ? 'Karyawan' : 'Bukan Karyawan',
                        ];

                        $getStatus[] = [
                            'status_karyawan' => $isEmployee ? 'Karyawan' : 'Bukan Karyawan'
                        ];
                        Log::info('Data Presensi:' . json_encode($nip, $isEmployee));
                    }
                } else {
                    Log::warning('Invalid XML structure');
                    return response()->json(['error' => 'Invalid XML structure'], 400);
                }
            } else {
                $data = Excel::toArray([], $file)[0];
            }





            $filteredData = array_filter($data, function ($item) use ($request) {
                return $item['tanggal'] >= $request->start_date && $item['tanggal'] <= $request->end_date;
            });

            Log::info('Filtered Data: ' . json_encode($filteredData));
            $uniqueData = [];

            foreach ($filteredData as $itemssss) {
                $key = $itemssss['nip']; // Gunakan NIP sebagai kunci unik

                // Jika kombinasi nip belum ada di hasil, tambahkan
                if (!isset($uniqueData[$key])) {
                    $uniqueData[$key] = $itemssss;
                }
            }

            $uniqueData = array_values($uniqueData);
            $statuses = array_map(function ($item) {
                return $item['status_karyawan'];
            }, $uniqueData);

            // Output data unik
            log::info("CEKKKK SOUND : " . json_encode($statuses, JSON_PRETTY_PRINT));

            // Grupkan Data Berdasarkan NIP dan Tanggal
            $groupedData = [];
            foreach ($filteredData as $item) {
                $key = $item['nip'] . '|' . $item['tanggal'];
                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = [];
                }
                $groupedData[$key][] = $item;
            }

            $finalData = [];
            foreach ($groupedData as $key => $items) {
                [$nip, $tanggal] = explode('|', $key);
                $shift = SettingShift::where('kode', 'S001')->first(); // Contoh shift, sesuaikan kode shift

                if (!$shift) {
                    Log::warning("No shift found for NIP: $nip on $tanggal");
                    continue;
                }

                // Filter jam masuk
                $jamMasukData = array_filter($items, function ($item) use ($shift) {
                    return $item['jam'] >= $shift->awal_masuk && $item['jam'] <= $shift->maks_late;
                });

                $jamMasuk = null;
                $presensiStatus = null;




                // Filter jam pulang
                $jamPulangData = array_filter($items, function ($item) use ($shift) {
                    return $item['jam'] > $shift->jam_pulang && $item['jam'] <= '23:59:59';
                });

                $jamPulang = null;
                if (!empty($jamPulangData)) {
                    $jamPulang = max(array_column($jamPulangData, 'jam'));
                }

                if (!empty($jamMasukData)) {
                    $jamMasuk = min(array_column($jamMasukData, 'jam'));

                    if ($jamMasuk < $shift->jam_masuk) {
                        $presensiStatus = 'EarlyIn';
                    } elseif ($jamMasuk >= $shift->jam_masuk && $jamMasuk <= date('H:i:s', strtotime($shift->jam_masuk) + 59)) {
                        $presensiStatus = 'OnTime';
                    } else {
                        $presensiStatus = 'Late';
                    }
                } elseif ($jamPulang) {
                    $presensiStatus = 'MissingIn';
                }
                // foreach ($data as &$rows) {
                //     $employee = Employee::where('nip', $rows['nip'])->first();
                //     $row['status_karyawan'] = $employee ? 'karyawan' : 'bukan karyawan';
                //     Log::info('Data Presensi:', ['nip' => $rows['nip']]);
                // }

                // Tambahkan ke final
                if ($jamMasuk || $jamPulang) {
                    $finalData[] = [
                        'nip' => $nip,
                        'nama' => $items[0]['nama'],
                        'tanggal' => $tanggal,
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'presensi_status' => $presensiStatus,
                        'sn' => $items[0]['sn'],
                        'status_karyawan' => array_values(array_column(
                            array_filter($uniqueData, function ($item) use ($nip) {
                                return $item['nip'] === $nip; // Filter berdasarkan NIP
                            }),
                            'status_karyawan' // Ambil kolom `status_karyawan`
                        )) ?? null // Ambil elemen pertama, jika ada
                    ];
                }
            }

            Log::info('Final Data: ' . json_encode($finalData, JSON_PRETTY_PRINT));

            return response()->json(['data' => $finalData]);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process import'], 500);
        }
    }


    private function validatePresenceData(array $filteredData)
    {
        $validated = [];
        $invalid = [];

        // Group data by nip
        $grouped = collect($filteredData)->groupBy('nip');

        foreach ($grouped as $nip => $entries) {
            $entries = collect($entries)->sortBy('jam')->values(); // Reset keys after sorting

            // Group further by date
            $dateGroups = $entries->groupBy('tanggal');

            foreach ($dateGroups as $date => $dailyEntries) {
                $dailyEntries = $dailyEntries->sortBy('jam')->values();

                // Filter for valid jam masuk (06:00 - 07:00) and jam pulang (16:00 - 23:59)
                $jamMasuk = $dailyEntries->first(function ($entry) {
                    $time = Carbon::createFromFormat('H:i', $entry['jam']);
                    return $time->between(Carbon::createFromTime(6, 0), Carbon::createFromTime(7, 0));
                });

                $jamPulang = $dailyEntries->last(function ($entry) {
                    $time = Carbon::createFromFormat('H:i', $entry['jam']);
                    return $time->between(Carbon::createFromTime(16, 0), Carbon::createFromTime(23, 59));
                });

                if ($jamMasuk && $jamPulang) {
                    $validated[] = [
                        'nip' => $nip,
                        'tanggal' => $date,
                        'jam_masuk' => $jamMasuk['jam'],
                        'jam_keluar' => $jamPulang['jam'],
                    ];
                } else {
                    $invalid[] = [
                        'nip' => $nip,
                        'tanggal' => $date,
                        'jam_masuk' => $jamMasuk['jam'] ?? null,
                        'jam_keluar' => $jamPulang['jam'] ?? null,
                    ];
                }
            }
        }

        return ['data' => $validated, 'invalid' => $invalid];
    }



    // private function validatePresenceData(array $filteredData)
    // {
    //     $validated = [];
    //     $invalid = [];
    //     Log::info('Group:'. json_encode($filteredData));

    //     $grouped = collect($filteredData)->groupBy('tanggal');

    //     Log::info('Group:'. json_encode($grouped));

    //     foreach ($grouped as $date => $entries) {
    //         $entries = $entries->sortBy('jam');

    //         if ($entries->count() === 1) {
    //             // Invalid: Hanya ada satu data untuk tanggal ini
    //             $invalid[] = $entries->first();
    //         } elseif ($entries->count() > 2) {
    //             // Ambil jam terkecil dan terbesar
    //             $validated[] = [
    //                 'tanggal' => $date,
    //                 'jam_masuk' => $entries->first()['jam'],
    //                 'jam_keluar' => $entries->last()['jam'],
    //             ];
    //         } else {
    //             // Valid: Hanya ada 2 data
    //             $validated[] = [
    //                 'tanggal' => $date,
    //                 'jam_masuk' => $entries->first()['jam'],
    //                 'jam_keluar' => $entries->last()['jam'],
    //             ];
    //         }
    //     }

    //     return ['data' => $validated, 'invalid' => $invalid];
    // }

    public function storeImport(Request $request)
    {
        Log::info('Request Data: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
        // $request->validate(['data' => 'required|array']);

        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.nip' => 'required|string',
            'data.*.tanggal' => 'required|date',
            'data.*.presensi_status' => 'required|string|in:EarlyIn,Late,OnTime,MissingIn',
            'data.*.sn' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessages = [];

            foreach ($errors as $field => $errorMessagesForField) {
                $errorMessages[$field] = $errorMessagesForField[0];
            }

            return response()->json(['error' => $errorMessages], 400);
        }
        try {
            DB::beginTransaction();

            foreach ($request->data as $row) {
                // Cari id karyawan berdasarkan nip
                $employee = Employee::where('nip', $row['nip'])->first();

                if (!$employee) {
                    // Skip jika employee tidak ditemukan
                    Log::warning("Employee dengan NIP {$row['nip']} tidak ditemukan.");
                    return response()->json(['error' => "Employee dengan NIP {$row['nip']} tidak ditemukan."], 404);
                }


                // Simpan data presensi
                $presemsi = Presence::create([
                    'employed_id' => $employee->id, // ID karyawan
                    'code_upload' => 'UP001',    // Contoh, sesuaikan jika ada kode khusus
                    'tanggal_scan' => now(),        // Anda dapat menyesuaikan sumber tanggal scan
                    'tanggal' => $row['tanggal'],
                    'jam_masuk' => $row['jam_masuk'],
                    'jam_pulang' => $row['jam_pulang'],
                    'presensi_status' => $row['presensi_status'],
                    'sn' => $row['sn'], // Serial number
                ]);
                Log::info("Data presensi untuk NIP {$row['nip']} pada tanggal {$row['tanggal']} berhasil disimpan.");
            }

            DB::commit();
            return response()->json(['message' => 'Data berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data presensi'], 500);
        }
    }
}
