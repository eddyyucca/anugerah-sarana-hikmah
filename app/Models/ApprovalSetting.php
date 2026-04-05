<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalSetting extends Model
{
    protected $fillable = [
        'document_type', 'level_name', 'level_order',
        'min_budget', 'max_budget', 'approver_user_id', 'approver_role', 'is_active',
    ];

    protected function casts(): array
    {
        return ['min_budget' => 'decimal:2', 'max_budget' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function approverUser()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function scopeForDocument($query, string $type)
    {
        return $query->where('document_type', $type)->where('is_active', true)->orderBy('level_order');
    }

    public function scopeForBudget($query, string $type, float $amount)
    {
        return $query->where('document_type', $type)
            ->where('is_active', true)
            ->where('min_budget', '<=', $amount)
            ->where(fn($q) => $q->whereNull('max_budget')->orWhere('max_budget', '>=', $amount))
            ->orderBy('level_order');
    }
}
