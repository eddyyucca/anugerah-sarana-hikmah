<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'link',
        'reference_type', 'reference_id', 'is_read',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
