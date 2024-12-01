<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
