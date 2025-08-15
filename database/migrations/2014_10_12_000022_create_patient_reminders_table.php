<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('reminder_type'); // appointment, medication, followup, birthday
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('reminder_date');
            $table->time('reminder_time')->nullable();
            $table->enum('status', ['pending', 'sent', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_reminders');
    }
};