<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('manufacturer')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drugs');
    }
};