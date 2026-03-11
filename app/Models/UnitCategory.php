<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function units()
    {
        return $this->hasMany(Unit::class, 'category_id');
    }
}
