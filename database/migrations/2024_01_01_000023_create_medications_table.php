<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->string('medication_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->integer('duration_days');
            $table->text('instructions')->nullable();
            $table->timestamps();
            
            $table->index('prescription_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medications');
    }
};