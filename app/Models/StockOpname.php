<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    // Status: in_progress → pending_approval → completed | rejected
    protected $fillable = [
        'opname_number', 'opname_date', 'status', 'remarks',
        'conducted_by', 'submitted_by', 'submitted_at', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function conductor()
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getProgressAttribute(): array
    {
        $total = $this->items->count();
        $counted = $this->items->where('is_counted', true)->count();
        return [
            'total' => $total,
            'counted' => $counted,
            'remaining' => $total - $counted,
            'percent' => $total > 0 ? round(($counted / $total) * 100) : 0,
        ];
    }
}
