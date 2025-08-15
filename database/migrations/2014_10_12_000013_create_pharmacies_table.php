<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->string('license_number');
            $table->string('manager_name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacies');
    }
};