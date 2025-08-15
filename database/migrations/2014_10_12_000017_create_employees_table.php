<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('department');
            $table->string('position');
            $table->decimal('salary', 10, 2);
            $table->date('hire_date');
            $table->date('birth_date');
            $table->text('address');
            $table->string('emergency_contact')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};