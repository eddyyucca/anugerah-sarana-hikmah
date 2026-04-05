<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintType extends Model
{
    protected $fillable = ['name', 'description', 'color', 'order', 'is_active'];

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public static function active()
    {
        return self::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc');
    }
}
