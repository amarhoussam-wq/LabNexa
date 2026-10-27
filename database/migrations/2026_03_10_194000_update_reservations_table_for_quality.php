<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('arrival_time')->nullable()->after('time');
            $table->timestamp('sampling_time')->nullable()->after('arrival_time');
            $table->boolean('is_no_show')->default(false)->after('sampling_time');
            $table->string('group')->nullable()->after('is_no_show'); // A or B
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['arrival_time', 'sampling_time', 'is_no_show', 'group']);
        });
    }
};
