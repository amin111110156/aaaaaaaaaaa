<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization');
            $table->string('license_number');
            $table->string('phone');
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
};