<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonConformity extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'patient_id',
        'type',
        'notes',
        're_sampling_required',
        'group',
        'date',
    ];

    protected $casts = [
        're_sampling_required' => 'boolean',
        'date' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Human-readable labels for NC types.
     */
    public static function typeLabels(): array
    {
        return [
            'patient_not_prepared' => 'مريض غير محضَّر',
            'identification_error'  => 'خطأ تعريف',
            'hemolysis'             => 'حمرة الدم (Hémolyse)',
            'ecbu_contaminated'     => 'ECBU ملوث',
            'ecbu_non_conformant'   => 'ECBU غير مطابق',
            're_sampling'           => 'إعادة السحب',
            'other'                 => 'أخرى',
        ];
    }
}
