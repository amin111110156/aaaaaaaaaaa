<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'partial', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['pharmacy_id', 'patient_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacy_sales');
    }
};