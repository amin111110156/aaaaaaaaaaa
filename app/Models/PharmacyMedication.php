<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyMedication extends Model
{
    // الحقول القابلة للتعبئة
    protected $fillable = [
        'pharmacy_id',
        'medication_name',
        'description',
        'price',          // حقل مطلوب
        'stock_quantity', // حقل مطلوب
        'expiry_date',
        'batch_number',
    ];

    // تحديد أنواع البيانات
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقة مع الصيدلية
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    // العلاقة مع المبيعات
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
