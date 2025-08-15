<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'license_number',
        'manager_name',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ← العلاقة المُصلحة
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function medications()
    {
        return $this->hasMany(PharmacyMedication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}