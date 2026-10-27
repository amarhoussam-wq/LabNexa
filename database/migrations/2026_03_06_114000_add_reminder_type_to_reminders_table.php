<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->string('reminder_type')->default('12h')->after('is_sent');
            $table->unsignedBigInteger('reservation_id')->nullable()->change();
            // Ensure we can have multiple reminders for the same reservation
            $table->dropIndex(['scheduled_for', 'is_sent']);
            $table->index(['reservation_id', 'reminder_type', 'is_sent'], 'idx_res_type_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropIndex('idx_res_type_sent');
            $table->dropColumn('reminder_type');
            $table->index(['scheduled_for', 'is_sent']);
        });
    }
};
