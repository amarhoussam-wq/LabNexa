<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

/**
 * @property int $id
 * @property int $patient_id
 * @property int|null $doctor_id
 * @property string $analysis_date
 * @property string $time
 * @property string $status
 * @property-read \App\Models\Patient $patient
 * @property-read \App\Models\Doctor|null $doctor
 */
class Reservation extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'analysis_date',
        'time',
        'status',
        'result_notes',
        'result_file_path',
        'prescription_path',
        'email_sent',
        'email_error',
        // Quality tracking
        'arrival_time',
        'sampling_time',
        'is_no_show',
        'group',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function reservationAnalyses()
    {
        return $this->hasMany(ReservationAnalysis::class);
    }

    public function analyses()
    {
        return $this->belongsToMany(Analyse::class, 'reservation_analyses', 'reservation_id', 'analysis_id')
            ->withPivot(['result_value', 'unit', 'reference_range', 'status']);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
