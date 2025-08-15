<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacySale extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'patient_id',
        'total_amount',
        'discount',
        'tax',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function items()
    {
        return $this->hasMany(PharmacySaleItem::class, 'sale_id');
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_price');
    }
}