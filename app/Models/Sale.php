<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'medication_id',
        'quantity',
        'sale_price',
        'customer_name',
        'customer_phone',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sale_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medication()
    {
        return $this->belongsTo(PharmacyMedication::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}