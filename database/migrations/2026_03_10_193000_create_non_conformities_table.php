<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_conformities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('set null');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->enum('type', [
                'patient_not_prepared', // لم يحترم التحضير
                'identification_error',  // خطأ تعريف
                'hemolysis',             // تحلل الدم
                'ecbu_contaminated',     // ECBU ملوث
                'ecbu_non_conformant',   // ECBU غير مطابق
                're_sampling',           // إعادة السحب
                'other',                 // أخرى
            ])->default('other');
            $table->text('notes')->nullable();
            $table->boolean('re_sampling_required')->default(false);
            $table->string('group')->nullable(); // A or B (for before/after study)
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_conformities');
    }
};
