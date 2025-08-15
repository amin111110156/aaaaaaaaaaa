<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'manufacturer',
        'price',
        'stock_quantity',
        'expiry_date',
        'batch_number',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'prescription_medications')
            ->withPivot(['dosage', 'frequency', 'duration_days', 'instructions']);
    }
}