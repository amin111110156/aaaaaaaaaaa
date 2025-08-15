<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->string('test_type'); // lab, xray, mri, ct_scan
            $table->string('test_name');
            $table->text('description')->nullable();
            $table->enum('status', ['requested', 'in_progress', 'completed', 'cancelled'])->default('requested');
            $table->text('result')->nullable();
            $table->string('result_file')->nullable();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_tests');
    }
};