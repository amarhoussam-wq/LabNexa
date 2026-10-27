<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class MasterAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Master Administrator
        Administrator::updateOrCreate(
            ['name' => 'admin'],
            [
                'email'    => 'admin@labnexa.com',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Master Doctor
        Doctor::updateOrCreate(
            ['email' => 'admin'],
            [
                'name'      => 'Dr. LabNexa Admin',
                'password'  => Hash::make('admin123'),
                'specialty' => 'System Administration',
                'phone'     => '0500000000',
                'address'   => 'LabNexa HQ',
            ]
        );

        // 3. Master Patient
        Patient::firstOrCreate(
            ['email' => 'admin'],
            [
                'name'       => 'Patient Admin Account',
                'password'   => Hash::make('admin123'),
                'phone'      => '0600000000',
                'gender'     => 'male',
                'birth_date' => '1990-01-01',
            ]
        );
    }
}
