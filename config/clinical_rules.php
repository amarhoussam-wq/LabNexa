<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Global Clinical Rules Mapping
     |--------------------------------------------------------------------------
     |
     | This configuration file defines the medical rules applied to bookings
     | automatically by the system.
     |
     */

    // 1. Tests requiring strict fasting (8 to 12 hours)
    // The system will reject bookings made less than 8 hours before the appointment
    'fasting' => [
        'تحليل السكر في الدم',
        'تحليل الكوليسترول والدهون',
        'Glycémie à jeun',
        'Bilan Lipidique',
        'Insulin', // example
        'Triglycerides'
    ],

    // 2. Tests requiring early morning collection due to diurnal variation
    // The system will reject bookings after 10:30 AM for these
    'morning_only' => [
        'Cortisol',
        'Prolactin',
        'ACTH',
        'Testosterone',
        'كورتيزول',
        'برولاكتين'
    ],

    // 3. Tests requiring specific urine collection instructions
    'urine_culture' => [
        'تحليل البول الكامل',
        'Examen des Urines (ECBU)',
        'ECBU',
        'ecbu'
    ],
];
