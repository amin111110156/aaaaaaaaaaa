<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // ربط الصيدلية
            $table->foreignId('pharmacy_id')
                ->constrained()
                ->cascadeOnDelete();

            // ربط الدواء مع جدول pharmacy_medications
            $table->foreignId('pharmacy_medication_id')
                ->constrained('pharmacy_medications')
                ->restrictOnDelete();

            // تفاصيل البيع
            $table->unsignedInteger('quantity');
            $table->decimal('sale_price', 10, 2);
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->decimal('total_amount', 10, 2);

            // حالة الفاتورة
            $table->enum('status', ['pending', 'completed', 'cancelled'])
                ->default('pending');

            $table->timestamps();

            // الفهارس لتحسين الأداء
            $table->index(['pharmacy_id', 'pharmacy_medication_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
