<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['purchase_request_id', 'sparepart_id', 'qty', 'notes'];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
