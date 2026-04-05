<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $fillable = [
        'document_type', 'document_id', 'approval_setting_id',
        'level_name', 'level_order', 'action', 'acted_by', 'acted_at', 'remarks',
    ];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }

    public function setting()
    {
        return $this->belongsTo(ApprovalSetting::class, 'approval_setting_id');
    }
}
