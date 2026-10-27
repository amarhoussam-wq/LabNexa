<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Patient extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'password',
    ];

    protected $hidden = ['password'];

    public function histories(){
        return $this->hasMany(History::class);
    }

    /**
     * Get the answers provided by the patient.
     */
    public function patientAnswers()
    {
        return $this->hasMany(PatientAnswer::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}