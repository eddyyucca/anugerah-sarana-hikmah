<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitToWork extends Model
{
    protected $table = 'fit_to_work_checks';

    protected $fillable = [
        'ftw_number', 'operator_id', 'check_date', 'shift',
        'siap_bekerja', 'kondisi_sehat', 'is_fit', 'notes', 'checked_by',
    ];

    protected function casts(): array
    {
        return [
            'check_date'    => 'date',
            'siap_bekerja'  => 'boolean',
            'kondisi_sehat' => 'boolean',
            'is_fit'        => 'boolean',
        ];
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_fit ? 'Fit' : 'Unfit';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_fit ? 'success' : 'danger';
    }
}
