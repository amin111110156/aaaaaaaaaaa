<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dateTime('followup_date')->nullable()->after('notes');
            $table->text('followup_reason')->nullable()->after('followup_date');
        });
    }

    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['followup_date', 'followup_reason']);
        });
    }
};