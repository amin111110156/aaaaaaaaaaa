<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'early_leave'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_attendances');
    }
};