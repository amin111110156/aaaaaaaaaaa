<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacySaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'medication_id',
        'quantity',
        'unit_price',
        'total_price',
        'instructions',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(PharmacySale::class, 'sale_id');
    }

    public function medication()
    {
        return $this->belongsTo(PharmacyMedication::class);
    }
}