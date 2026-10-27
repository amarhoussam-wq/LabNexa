<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Doctor::updateOrCreate(
            ['email' => 'doctor1@gmail.com'],
            [
                'name' => 'Dr. Labo',
                'password' => Hash::make('password'),
                'specialty' => 'Medical Analysis',
                'phone' => '0550000000',
                'address' => 'Labo.dz',
            ]
        );
    }
}
