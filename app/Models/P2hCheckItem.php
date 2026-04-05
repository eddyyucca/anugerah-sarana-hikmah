<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class P2hCheckItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'p2h_check_id', 'category', 'check_item', 'condition', 'notes',
    ];

    public function p2hCheck()
    {
        return $this->belongsTo(P2hCheck::class);
    }
}
