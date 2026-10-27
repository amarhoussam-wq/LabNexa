<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

/**
 * @property int $question_id
 * @property int $option_id
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\Option $option
 */
class PatientAnswer extends Model
{
    use HasFactory, Auditable;

    protected $fillable = ['patient_id', 'question_id', 'option_id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
