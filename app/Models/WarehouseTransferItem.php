<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransferItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['warehouse_transfer_id', 'sparepart_id', 'qty'];

    public function warehouseTransfer() { return $this->belongsTo(WarehouseTransfer::class); }
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
}
