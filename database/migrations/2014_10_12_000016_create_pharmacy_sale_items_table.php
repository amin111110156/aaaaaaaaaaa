<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('pharmacy_sales')->onDelete('cascade');
            $table->foreignId('medication_id')->constrained('pharmacy_medications')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('instructions')->nullable();
            $table->timestamps();
            
            $table->index(['sale_id', 'medication_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacy_sale_items');
    }
};