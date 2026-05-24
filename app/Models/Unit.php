<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'unit_code', 'unit_model', 'unit_type', 'category_id',
        'department', 'current_status', 'hour_meter', 'is_active',
        'monthly_budget_limit', 'current_odometer', 'wheel_count',
    ];

    protected function casts(): array
    {
        return [
            'hour_meter'          => 'decimal:2',
            'is_active'           => 'boolean',
            'monthly_budget_limit'=> 'decimal:2',
            'current_odometer'    => 'decimal:2',
            'wheel_count'         => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(UnitCategory::class, 'category_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function availabilityLogs()
    {
        return $this->hasMany(UnitAvailabilityLog::class);
    }

    public function repairCosts()
    {
        return $this->hasMany(RepairCostSummary::class);
    }

    public function monthlyCosts()
    {
        return $this->hasMany(UnitMonthlyCost::class);
    }

    public function odometerReadings()
    {
        return $this->hasMany(UnitOdometerReading::class)->latest('reading_date');
    }

    public function tires()
    {
        return $this->hasMany(UnitTire::class)->orderBy('position_number');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class)->latest('service_date');
    }

    public function getWheelPositionLabelsAttribute(): array
    {
        $count = $this->wheel_count ?? 8;
        return match($count) {
            8  => [1=>'Steer Kiri',2=>'Steer Kanan',3=>'Drive Kiri Luar',4=>'Drive Kiri Dalam',5=>'Drive Kanan Dalam',6=>'Drive Kanan Luar',7=>'Tag Kiri',8=>'Tag Kanan'],
            12 => [1=>'Steer Kiri',2=>'Steer Kanan',3=>'Drive1 Kiri Luar',4=>'Drive1 Kiri Dalam',5=>'Drive1 Kanan Dalam',6=>'Drive1 Kanan Luar',7=>'Drive2 Kiri Luar',8=>'Drive2 Kiri Dalam',9=>'Drive2 Kanan Dalam',10=>'Drive2 Kanan Luar',11=>'Tag Kiri',12=>'Tag Kanan'],
            default => array_combine(range(1,$count), array_map(fn($i)=>"Posisi $i", range(1,$count))),
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('current_status', $status);
    }
}
