<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_crms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_visit')->nullable();
            $table->timestamp('next_appointment')->nullable();
            $table->integer('total_visits')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->foreignId('preferred_doctor')->nullable()->constrained('doctors');
            $table->string('preferred_time')->nullable();
            $table->json('communication_preferences')->nullable();
            $table->json('allergies')->nullable();
            $table->json('chronic_conditions')->nullable();
            $table->json('family_history')->nullable();
            $table->text('insurance_info')->nullable();
            $table->text('emergency_contact')->nullable();
            $table->timestamps();
            
            $table->unique('patient_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_crms');
    }
};