<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class P2hCheck extends Model
{
    protected $fillable = [
        'p2h_number', 'unit_id', 'operator_id', 'check_date', 'shift',
        'hour_meter_start', 'km_start', 'overall_status',
        'general_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'check_date' => 'date',
            'hour_meter_start' => 'decimal:2',
            'km_start' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items()
    {
        return $this->hasMany(P2hCheckItem::class);
    }

    public function getGoodCountAttribute(): int
    {
        return $this->items->where('condition', 'good')->count();
    }

    public function getWarningCountAttribute(): int
    {
        return $this->items->where('condition', 'warning')->count();
    }

    public function getBadCountAttribute(): int
    {
        return $this->items->where('condition', 'bad')->count();
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('overall_status', $status);
    }
}
