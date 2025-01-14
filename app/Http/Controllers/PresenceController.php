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
        $presences = Presence::with('employee')->get();
        // log::info('CEK DATA : ' . json_encode($presences, JSON_PRETTY_PRINT));

        $uniqueEmployees = $presences->pluck('employee')->unique('name');

        // log::info('CEK DATA : ' . json_encode($uniqueEmployees, JSON_PRETTY_PRINT));
        return view('pages.presence.presence', compact('presences', 'uniqueEmployees'));
    }
    public function detailPresence($id)
    {
        $presences = Presence::with('employee')->where('employed_id', $id)->get();
        $uniqueEmployees = $presences->pluck('employee')->unique('name');
        log::info('CEK DATA employee : ' . json_encode($presences, JSON_PRETTY_PRINT));

        return view('pages.presence.detailpresence', compact('presences', 'uniqueEmployees'));
    }
    public function filterPresences(Request $request)
    {
        log::info('cek tanggal :' . json_encode($request, JSON_PRETTY_PRINT));
        $startDate = $request->query('start');
        $endDate = $request->query('end');

        $presences = Presence::whereBetween('tanggal', [$startDate, $endDate])->get();

        return response()->json($presences);
    }
    public function create()
    {
        return view('pages.presence.create');
    }

    public function edit($id)
    {
        $presence = Presence::find($id);
        $presence->tanggal = Carbon::parse($presence->tanggal)->format('Y-m-d');
        $html = view('pages.presence.edit', compact('presence'))->render();

        return response()->json([
            'html' => $html,
            'presence_id' => $presence->id,
        ]);
    }

    public function store(Request $request)
    {
        // log::info('Cik Nempo Data:', ['cek' => $request->employed_id]);
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employed_id' => 'required|integer', // Validasi karyawan
            // 'date' => 'required|date',
            // 'status' => 'required|string|in:Present,Absent,Sick,Leave', // Contoh status presensi
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
            $status = ""; // Default ke status sebelumnya
            if ($request->jam_masuk) {
                $jamMasuk = Carbon::createFromFormat('H:i:s', $request->jam_masuk);
                $jamDelapan = Carbon::createFromTime(8, 0, 0); // Jam 08:00:00

                if ($jamMasuk->greaterThan($jamDelapan)) {
                    $status = 'Late';
                } else {
                    $status = 'EarlyIn';
                }
            } elseif ($request->jam_masuk === null && $request->jam_pulang !== null) {
                $status = 'MissingIn';
            }
            $exists = Presence::where('employed_id', $request->employed_id)
                ->where('tanggal', $request->tanggal)
                ->exists();
            $employes = Employee::where('id', $request->employed_id)->first();

            if ($exists) {
                Log::warning("Presensi untuk employed_id {$request->employed_id} pada tanggal {$request->tanggal} sudah ada.");
                return response()->json([
                    'error' => "Presensi Karyawan Dengan Nama : {$employes->name} 
                    Pada Tanggal {$request->tanggal} sudah ada"
                ], 400);
            } else {
                $presence = Presence::create([
                    'employed_id' => $request->employed_id,
                    'code_upload' => "UP001",
                    'jam_masuk' => $request->jam_masuk,
                    'jam_pulang' => $request->jam_pulang,
                    'tanggal_scan' => $request->tanggal_scan,
                    'tanggal' => $request->tanggal,
                    'presensi_status' => $status,
                    'sn' => "-"
                    // 'date' => $request->date,
                    // 'status' => $request->status,
                    // 'remarks' => $request->remarks,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data presensi berhasil disimpan',
                    'data' => $presence,
                ], 201);
            }
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
            // 'employed_id' => 'required|integer|exists:employees,id',
            'tanggal_scan' => 'required|date',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i:s',
            'jam_pulang' => 'nullable|date_format:H:i:s',
            'status' => 'required|string',
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
            $status = $presence->presensi_status; // Default ke status sebelumnya
            if ($request->jam_masuk) {
                $jamMasuk = Carbon::createFromFormat('H:i:s', $request->jam_masuk);
                $jamDelapan = Carbon::createFromTime(8, 0, 0); // Jam 08:00:00

                if ($jamMasuk->greaterThan($jamDelapan)) {
                    $status = 'Late';
                } else {
                    $status = 'EarlyIn';
                }
            } elseif ($request->jam_masuk === null && $request->jam_pulang !== null) {
                $status = 'MissingIn';
            }

            $presence->update([
                // 'employed_id' => $request->employed_id,
                'tanggal_scan' => $request->tanggal_scan,
                'tanggal' => $request->tanggal,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'presensi_status' => $status,
                // 'remarks' => $request->remarks,
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
        $presences = Employee::select('id', 'name', 'nip')->get();
        return response()->json($presences);
    }

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
        $errors = [];
        $importedCount = 0;
        $validasiData = [];
        try {
            // Log::info('Request All: ', $request->all());
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
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
                    $importedCounts = [];
                    foreach ($xml->ROWS->ROW as $row) {
                        $nip = (string) $row['dbg_scanlogpegawai_nip'];
                        $tanggal = Carbon::parse((string) $row['dbg_scanlogtgl']);
                        $exists = [];
                        if ($tanggal->between($startDate, $endDate)) {
                            // Mengecek apakah NIP ada di database Employee
                            $isEmployee = Employee::where('nip', $nip)->exists();

                            if ($isEmployee) {
                                $employee = Employee::where('nip', $nip)->first();
                                $employee_id = $employee->id;

                                $exists = Presence::where('employed_id', $employee_id)
                                    ->whereBetween('tanggal', [$startDate, $endDate])
                                    ->exists();
                                // log::info("CEK BROUUU : ", ['ceck data' => $employee]);
                            } else {
                                $error[] = "cek";
                            }
                            $tanggalss[] = $tanggal;
                            $validateDate = array_filter($tanggalss, function ($item) use ($request) {
                                return $item >= $request->start_date && $item <= $request->end_date;
                            });
                            $existss = Presence::where('employed_id', $employee_id)
                                ->whereBetween('tanggal', [$startDate, $endDate])
                                ->get();
                            log::info("CEK REQUEST" . json_encode($existss, JSON_PRETTY_PRINT));
                            // log::info("CEK REQUEST", $existss);
                            // Mengecek apakah NIP dan tanggal sudah pernah diimpor sebelumnya

                            // Array untuk menampung error
                            log::info("CEK validate : ", $importedCounts);
                            // Jika data sudah ada, tambahkan pesan error
                            if ($exists) {
                                // Tambahkan counter untuk NIP terkait
                                if (!isset($importedCounts[$nip])) {
                                    $importedCounts[$nip] = 0;
                                }

                                $importedCounts[$nip]++;
                                $errors[] = "Data dengan NIP {$nip} dan tanggal {$tanggal} sudah pernah diimport.";
                            }

                            $data[] = [
                                'tanggal_scan' => (string) $row['dbg_scanlogscan_date'] ?? null,
                                'tanggal' => (string) $row['dbg_scanlogtgl'] ?? null,
                                'jam' => (string) $row['dbg_scanlogjam'] ?? null,
                                'nip' => (string) $row['dbg_scanlogpegawai_nip'] ?? null,
                                'nama' => (string) $row['dbg_scanlogpegawai_nama'] ?? null,
                                'sn' => (string) $row['dbg_scanlogsn'] ?? null,
                                'status_karyawan' => $isEmployee ? 'Karyawan' : 'Bukan Karyawan',
                                'validasi_tanggal' => $errors ? implode(', ', $errors) : null, // Menambahkan pesan error jika ada
                            ];
                            // Menambahkan status karyawan ke dalam array $getStatus
                            $getStatus[] = [
                                'status_karyawan' => $isEmployee ? 'Karyawan' : 'Bukan Karyawan'
                            ];
                        }
                    }
                } else {
                    Log::warning('Invalid XML structure');
                    return response()->json(['error' => 'Invalid XML structure'], 400);
                }
            } else {
                $data = Excel::toArray([], $file)[0];
            }
            foreach ($importedCounts as $nip => $count) {
                if ($count > 0) {
                    $validasiData[] = "Ada {$count} data dengan NIP {$nip} yang sudah diimport sebelumnya.";
                }
            };

            foreach ($data as &$rows) {
                $rows['validasi_data'] = $validasiData;
            }






            $filteredData = array_filter($data, function ($item) use ($request) {
                return $item['tanggal'] >= $request->start_date && $item['tanggal'] <= $request->end_date;
            });

            // Log::info('Filtered Data: ' . json_encode($filteredData));
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
            $cekCek = array_map(function ($item) {
                return $item['nip'];
            }, $uniqueData);
            $idEmploye = Employee::whereIn('nip', $cekCek)->get();
            $cek_id_employee = $idEmploye->pluck('id')->toArray();

            // Output data unik
            // log::info("CEKKKK SOUND : " . json_encode($statuses, JSON_PRETTY_PRINT));

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

                // Tambahkan ke final
                if ($jamMasuk || $jamPulang) {
                    $errorMessage = null;
                    foreach ($errors as $error) {
                        if (strpos($error, $tanggal) !== false) {
                            $errorMessage = $error;  // Menyimpan pesan error
                            break;
                        }
                    }
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
                        )) ?? null, // Ambil elemen pertama, jika ada
                        'validasi_error' => $errorMessage,
                        'validasi_data' =>  array_values(array_filter($validasiData, function ($error) use ($nip) {
                            return str_contains($error, "NIP {$nip}"); // Filter pesan berdasarkan NIP
                        })),
                    ];
                }
            }
            $tanggall = Presence::get();
            // Log::info('Final Data: ' . json_encode($finalData, JSON_PRETTY_PRINT));

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
        // Log::info('Request Data: ' . json_encode($request->all(), JSON_PRETTY_PRINT));
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

                $employee_id = $employee->id;
                $tanggal = $row['tanggal'];


                if (!$employee) {
                    // Skip jika employee tidak ditemukan
                    Log::warning("Employee dengan NIP {$row['nip']} tidak ditemukan.");
                    return response()->json(['error' => "Employee dengan NIP {$row['nip']} tidak ditemukan."], 404);
                }
                $exists = Presence::where('employed_id', $employee_id)
                    ->where('tanggal', $tanggal)
                    ->exists();

                if ($exists) {
                    // Jika data sudah ada, kembalikan respons error
                    Log::warning("Presensi untuk employed_id {$employee_id} pada tanggal {$tanggal} sudah ada.");
                    return response()->json([
                        'error' => "Ada Data Yang Sudah Pernah Di Import"
                    ], 400); // Gunakan HTTP status code 400 untuk bad request
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
                // Log::info("Data presensi untuk NIP {$row['nip']} pada tanggal {$row['tanggal']} berhasil disimpan.");
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
