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

    public static function calculateAttendanceAndDeductions($employeeId, $startDate, $endDate, $nilaiPerHari)
    {
        $presences = self::where('employed_id', $employeeId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $attendanceDetails = [];
        $totalEarnings = 0;
        $totalDeductions = 0;

        $shift = SettingShift::where('kode', 'S001')->first();
        $defaultShiftTime = '08:00:00';
        $shiftJamMasuk = $shift ? trim($shift->jam_masuk) : $defaultShiftTime;

        foreach ($presences as $presence) {
            Log::info('Memproses presensi', [
                'tanggal' => $presence->tanggal,
                'jam_masuk' => $presence->jam_masuk,
                'jam_pulang' => $presence->jam_pulang,
            ]);

            $dailyDeductions = 0;
            $dailyEarnings = $nilaiPerHari;

            // Cek kehadiran
            if (is_null($presence->jam_masuk) || is_null($presence->jam_pulang)) {
                $dailyDeductions += $nilaiPerHari * 0.5;
                Log::info('Tidak hadir atau jam masuk/pulang kosong', ['dailyDeductions' => $dailyDeductions]);
            } else {
                // Validasi dan perhitungan keterlambatan
                $lateMinutes = self::calculateLateMinutes($presence->tanggal, $presence->jam_masuk, $shiftJamMasuk);
                if ($lateMinutes !== null) {
                    $dailyDeductions += self::calculateDeductionsForLateness($lateMinutes, $nilaiPerHari);
                    Log::info('Menghitung keterlambatan', [
                        'scheduledTime' => $shiftJamMasuk,
                        'actualTime' => $presence->jam_masuk,
                        'lateMinutes' => $lateMinutes,
                        'dailyDeductions' => $dailyDeductions,
                    ]);
                } else {
                    Log::warning('Format waktu tidak valid untuk perhitungan keterlambatan', [
                        'shiftJamMasuk' => $shiftJamMasuk,
                        'presenceJamMasuk' => $presence->jam_masuk,
                    ]);
                }
            }

            $dailyEarnings -= $dailyDeductions;

            // Akumulasi total
            $totalEarnings += max(0, $dailyEarnings); // Pastikan penghasilan tidak negatif
            $totalDeductions += $dailyDeductions;

            // Simpan detail harian
            $attendanceDetails[] = [
                'tanggal' => $presence->tanggal,
                'earnings' => $dailyEarnings,
                'deductions' => $dailyDeductions,
            ];
        }

        Log::info('Akumulasi total', [
            'totalEarnings' => $totalEarnings,
            'totalDeductions' => $totalDeductions,
        ]);

        return [
            'attendance_details' => $attendanceDetails,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
        ];
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
