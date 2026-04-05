<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operator_code', 'operator_name', 'nik', 'phone',
        'license_type', 'license_expiry', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'license_expiry' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function p2hChecks()
    {
        return $this->hasMany(P2hCheck::class);
    }

    public function isLicenseExpired(): bool
    {
        return $this->license_expiry && $this->license_expiry->isPast();
    }

    public function isLicenseExpiringSoon(): bool
    {
        return $this->license_expiry && $this->license_expiry->between(now(), now()->addDays(30));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
