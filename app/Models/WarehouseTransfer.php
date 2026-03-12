<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    protected $fillable = [
        'transfer_number', 'from_location_id', 'to_location_id',
        'transfer_date', 'status', 'remarks',
        'created_by', 'posted_by', 'posted_at', 'received_by', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
            'posted_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function items() { return $this->hasMany(WarehouseTransferItem::class); }
    public function fromLocation() { return $this->belongsTo(WarehouseLocation::class, 'from_location_id'); }
    public function toLocation() { return $this->belongsTo(WarehouseLocation::class, 'to_location_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function poster() { return $this->belongsTo(User::class, 'posted_by'); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }
}
