<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('whatsapp_enabled')->default(false);
            $table->string('whatsapp_phone')->nullable();
            $table->boolean('telegram_enabled')->default(false);
            $table->string('telegram_chat_id')->nullable();
            $table->time('daily_report_time')->nullable();
            $table->time('analysis_reminder_time')->nullable();
            $table->time('followup_reminder_time')->nullable();
            $table->boolean('financial_report_enabled')->default(false);
            $table->boolean('doctor_report_enabled')->default(false);
            $table->boolean('patient_reminder_enabled')->default(false);
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_settings');
    }
};