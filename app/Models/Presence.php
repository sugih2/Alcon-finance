<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DateTime;
use Exception;


class Presence extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'presences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employed_id',
        'code_upload',
        'tanggal_scan',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'presensi_status',
        'sn',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_scan' => 'datetime:Y-m-d H:i:s',
        'tanggal' => 'date',
        'jam' => 'H:i:s',
    ];

    /**
     * Example of a relationship: Presence belongs to an Employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employed_id', 'id');
    }

    /**
     * Menghitung jumlah hari kerja berdasarkan employee_id dan tanggal
     *
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public static function getTotalHadir($employeeId, $startDate, $endDate)
    {
        return self::where('employed_id', $employeeId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('jam_masuk')
            ->whereNotNull('jam_pulang')
            ->count();
    }

    public static function calculateAttendanceAndDeductions($employeeId, $startDate, $endDate, $nilaiPerHari, $componentType)
    {   try {
            $presences = self::where('employed_id', $employeeId)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $attendanceDetails = [];
            $totalEarnings = 0;
            $totalDeductions = 0;
            $totalOvertimeEarnings = 0;

            $shift = SettingShift::where('kode', 'S001')->first();
            $defaultShiftTime = '08:00:00';
            $shiftJamMasuk = $shift ? trim($shift->jam_masuk) : $defaultShiftTime;

            // Ambil nilai allowance TLK dari DetailPayroll
            $allowanceTLK = DetailPayroll::where('id_employee', $employeeId)
                ->whereHas('component', function ($query) {
                    $query->where('category', 'TLK');
                })
                ->sum('amount');

            foreach ($presences as $presence) {
                $dailyEarnings = $nilaiPerHari;
                $dailyDeductions = 0;
                $overtimeEarnings = 0;
                $overtimeTime = "00:00:00";
                $deductionReason = null;

                if ($componentType === 'Allowance') {
                    // Component Type Allowance
                    if (!is_null($presence->jam_masuk) && !is_null($presence->jam_pulang)) {
                        $totalEarnings += $dailyEarnings;
                        $attendanceDetails[] = [
                            'tanggal' => $presence->tanggal,
                            'earnings' => [
                                $componentType => $dailyEarnings,
                            ],
                            'deductions' => [
                                'attendance' => 0,
                            ],
                            'overtime' => [
                                'overtime_hours' => "00:00:00",
                                'overtime_earnings' => 0,
                            ],
                            'deduction_reason' => null,
                        ];
                    }
                    continue;
                }

                // Cek kehadiran
                if (is_null($presence->jam_masuk) || is_null($presence->jam_pulang)) {
                    $dailyDeductions += $nilaiPerHari * 0.5;
                    $deductionReason = 'Tidak hadir atau jam masuk/pulang kosong';
                } else {
                    // Validasi dan perhitungan keterlambatan
                    $lateMinutes = self::calculateLateMinutes($presence->tanggal, $presence->jam_masuk, $shiftJamMasuk);
                    if ($lateMinutes !== null) {
                        $deductionAmount = self::calculateDeductionsForLateness($lateMinutes, $nilaiPerHari);
                        if ($deductionAmount > 0) {
                            $dailyDeductions += $deductionAmount;
                            $deductionReason = "Terlambat $lateMinutes menit";
                        }
                    }

                    // Perhitungan lembur
                    $overtimeResult = self::calculateOvertimeHours($presence->jam_pulang, $shift->jam_pulang);
                    if (!empty($overtimeResult['overtimeHours']) && $overtimeResult['overtimeHours'] > 0) {
                        $hourlyRate = $allowanceTLK > 0
                            ? ($nilaiPerHari + $allowanceTLK) / 8
                            : $nilaiPerHari / 8;
        
                        $overtimeEarnings = $hourlyRate * $overtimeResult['overtimeHours'];
                        $overtimeTime = $overtimeResult['overtimeTime']; // Gunakan overtimeTime di sini
                        $totalOvertimeEarnings += $overtimeEarnings;
                    }
                }

                // Hitung Earnings dan Deductions
                $dailyEarnings -= $dailyDeductions;
                $totalEarnings += max(0, $dailyEarnings);
                $totalDeductions += $dailyDeductions;

                // Menyusun attendanceDetails
                $attendanceDetails[] = [
                    'tanggal' => $presence->tanggal,
                    'earnings' => [
                        $componentType => max(0, $dailyEarnings),
                    ],
                    'deductions' => [
                        'attendance' => $dailyDeductions,
                    ],
                    'overtime' => [
                        'overtime_hours' => $overtimeTime,
                        'overtime_earnings' => $overtimeEarnings,
                    ],
                    'deduction_reason' => $deductionReason,
                ];

                // Log::info("Attendance Details: ", [
                //     'data' => json_encode($attendanceDetails, JSON_PRETTY_PRINT)
                // ]);
            }

            // Log::info("
            //     Total Earnings: $totalEarnings,
            //     Total Overtime Earnings: $totalOvertimeEarnings,
            //     Total Deductions: $totalDeductions");
            // Mengembalikan hasil akhir
            return [
                'attendance_details' => $attendanceDetails,
                'total_earnings' => $totalEarnings,
                'total_overtime_earnings' => $totalOvertimeEarnings,
                'total_deductions' => $totalDeductions,
            ];
        } catch (\Exception $e) {
            // Log the error
            Log::error("Error calculating attendance and deductions: {$e->getMessage()} in file {$e->getFile()} on line {$e->getLine()}");
    
            // Rethrow the exception to notify the caller
            throw new \Exception("Terjadi kesalahan saat menghitung kehadiran dan pemotongan: " . $e->getMessage());
        }
    }



    private static function calculateOvertimeHours($actualTimeOut, $shiftTimeOut)
    {
        try {
            // Validasi apakah format waktu valid
            if (empty($actualTimeOut) || empty($shiftTimeOut)) {
                return [
                    'overtimeHours' => 0,
                    'overtimeTime' => '00:00:00',
                ];
            }

            // Konversi ke objek DateTime
            $actualTime = new \DateTime($actualTimeOut);
            $scheduledTime = new \DateTime($shiftTimeOut);

            // Pastikan waktu aktual melebihi jadwal untuk perhitungan lembur
            if ($actualTime <= $scheduledTime) {
                Log::info('Jam pulang tidak melebihi jadwal, tidak ada lembur', [
                    'actualTimeOut' => $actualTimeOut,
                    'shiftTimeOut' => $shiftTimeOut,
                ]);
                return [
                    'overtimeHours' => 0,
                    'overtimeTime' => '00:00:00',
                ];
            }

            // Hitung interval waktu lembur
            $interval = $scheduledTime->diff($actualTime);
            $overtimeHours = floor($interval->h + ($interval->i / 60)); // Dalam bentuk desimal
            $overtimeTime = $interval->format('%H:%I:%S'); // Waktu lembur dalam format HH:MM:SS

            // Log sukses dengan tambahan overtimeTime
            Log::info('Berhasil menghitung lembur', [
                'actualTimeOut' => $actualTimeOut,
                'shiftTimeOut' => $shiftTimeOut,
                'overtimeHours' => $overtimeHours,
                'overtimeTime' => $overtimeTime, // Tambahan informasi waktu lembur
            ]);

            return [
                'overtimeHours' => $overtimeHours,
                'overtimeTime' => $overtimeTime,
            ];

        } catch (\Exception $e) {
            // Log kesalahan dan kembalikan default 0
            Log::error('Kesalahan saat menghitung lembur', [
                'error' => $e->getMessage(),
                'actualTimeOut' => $actualTimeOut,
                'shiftTimeOut' => $shiftTimeOut,
            ]);
            return [
                'overtimeHours' => 0,
                'overtimeTime' => '00:00:00',
            ];
        }
    }





    /**
     * Menghitung menit keterlambatan.
     */
    private static function calculateLateMinutes($date, $actualTime, $scheduledTime)
    {
        try {
            // Pastikan hanya mengambil bagian tanggal
            $dateOnly = substr($date, 0, 10); // Ambil 10 karakter pertama (YYYY-MM-DD)
            
            // Gabungkan tanggal dengan waktu yang dijadwalkan dan aktual
            $scheduledDateTime = new DateTime("$dateOnly $scheduledTime");
            $actualDateTime = new DateTime("$dateOnly $actualTime");

            if ($actualDateTime > $scheduledDateTime) {
                return ceil(($actualDateTime->getTimestamp() - $scheduledDateTime->getTimestamp()) / 60);
            }

            return 0; // Tidak terlambat
        } catch (Exception $e) {
            Log::error('Error saat menghitung keterlambatan', ['message' => $e->getMessage()]);
            return null; // Waktu tidak valid
        }
    }


    /**
     * Menghitung potongan berdasarkan menit keterlambatan.
     */
    private static function calculateDeductionsForLateness($lateMinutes, $nilaiPerHari)
    {
        if ($lateMinutes > 0 && $lateMinutes <= 15) {
            return $lateMinutes * 500;
        } elseif ($lateMinutes > 15 && $lateMinutes <= 60) {
            return 10000;
        } elseif ($lateMinutes > 60) {
            return $nilaiPerHari * 0.5;
        }
        return 0;
    }


}
