<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->string('medication_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamps();
            
            $table->index('pharmacy_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacy_medications');
    }
};