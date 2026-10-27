<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ResearchDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Algerian patient names (unique, realistic) ───────────────────────────────
        $maleNames = [
            'بلحاج رضوان',   'كريم مزياني',    'عمر بلقاسم',    'محمد دحماني',   'أنيس حمزة',
            'رضا سبتي',       'سامي بوطالب',    'إلياس بلال',    'رياض شريفي',    'خالد حمدان',
            'فارس قاسمي',     'نور الدين بوديسة','بلال عمراني',   'أيمن بوزيد',    'هشام خلوف',
            'علي قروج',       'ماهر تواتي',     'رمزي بن علي',   'سفيان مغربي',   'عدنان بلعيد',
          'لحاج قويدر وليد ',     'منير بودالي',    'أسامة بن يوسف', 'توفيق مرابط',   'شريف بوخالفة',
            'نضال بن حسين',   'هيثم بوقرة',     'زكريا حمادي',   'مروان طيار',    'لطفي بومدين',
            'بكر بوحجر',      'عثمان ملاح',     'حمزة زغيب',     'صلاح دريدي',    'جمال بوسعادة',
            'إبراهيم حلوشي',  'عزيز معزة',      'فاروق بلحسن',   'عبد الكريم نور','نسيم مخلوفي',
            'عمر حسام ',      'راشد جلول',      'كمال بوسايح',   'عبدالله فارح',  'مصطفى بن ناصر',
        ];
        $femaleNames = [
            'سارة بوزيان',    'أسماء دحماني',   'نور بلحسن',     'فاطمة الزهراء عيسى','ريم خلوف',
            'أميرة بوقرة',    'لينة سبتي',      'هناء معزوز',    'ياسمين طيبي',   'نجوى شريف',
            'لمياء قاسمي',    'رنا بوطالب',     'منال بلال',     'سلمى حمزة',     'وردة زروق',
            'حفيظة عمراني',   'زهرة بن علي',    'إيمان بوديسة',  'وسيلة كرام',    'حنان مقران',
            'ندى بوسعادة',    'أمينة حلوشي',    'رحمة سريدي',    'فريدة مرابط',   'عائشة بن عامر',
            'خديجة بوخضرة',   'مريم سالمي',     'صبرينة مزهودي', 'شيماء قريشي',   'نادية تيزي',
            'لبنى بلحروف',    'بسمة قطاف',      'ريحانة طاهر',   'أروى جبالي',    'حياة بوناب',
            'فوزية عمر',      'رفيقة سعدي',     'ثريا بشير',     'وفاء منصور',    'جهان سعيداني',
            'صونيا بلعباس',   'كريمة حرزالله',  'نوال بن غريبة', 'زينب كبير',     'فايزة أوعيش',
        ];

        // ─── Analysis dates (past 6 weeks to look like a real study) ─────────
        $startDate = Carbon::now()->subWeeks(6)->startOfDay();
        $times = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30'];

        // ─── Helpers ──────────────────────────────────────────────────────────
        $makePatient = function (string $name, string $gender, int $idx) use ($startDate): array {
            $birth = Carbon::now()->subYears(rand(20, 70))->subDays(rand(0, 300));
            // Unique phone: prefix varies per group, index ensures uniqueness
            $phone = '077' . str_pad((string)(1000 + $idx), 7, '0', STR_PAD_LEFT);
            return [
                'name'       => $name,
                'email'      => 'patient' . (1000 + $idx) . '@labodz-etude.dz',
                'phone'      => $phone,
                'gender'     => $gender,
                'birth_date' => $birth->toDateString(),
                'password'   => Hash::make('Labo@1234'),
                'created_at' => now()->subWeeks(8),
                'updated_at' => now()->subWeeks(8),
            ];
        };

        DB::beginTransaction();
        try {

            // ═══════════════════════════════════════════════════════════
            // 1. INSERT 80 PATIENTS – all unique names
            // ═══════════════════════════════════════════════════════════
            $patientIds = [];

            for ($i = 1; $i <= 80; $i++) {
                if ($i <= 40) {
                    // Groupe A: alternate gender
                    $gender = ($i % 2 === 1) ? 'male' : 'female';
                    $nameIdx = intdiv($i - 1, 2); // 0..19
                    $name = $gender === 'male' ? $maleNames[$nameIdx] : $femaleNames[$nameIdx];
                } else {
                    // Groupe B: alternate gender (reverse order to avoid same names)
                    $gender = ($i % 2 === 1) ? 'female' : 'male';
                    $nameIdx = intdiv($i - 41, 2) + 20; // 20..39
                    $name = $gender === 'female' ? $femaleNames[$nameIdx] : $maleNames[$nameIdx];
                }
                $data = $makePatient($name, $gender, $i);
                $id = DB::table('patients')->insertGetId($data);
                $patientIds[] = $id;
            }

            // First 40 = Groupe A, Last 40 = Groupe B
            $groupA_ids = array_slice($patientIds, 0, 40);
            $groupB_ids = array_slice($patientIds, 40, 40);

            // ═══════════════════════════════════════════════════════════
            // 2. INSERT RESERVATIONS
            // ═══════════════════════════════════════════════════════════
            // Migration may not have run — check for quality columns
            $hasQualityCols = Schema::hasColumn('reservations', 'is_no_show');

            $makeDate = function (int $weekOffset, int $dayOfWeek) use ($startDate): string {
                return $startDate->copy()->addWeeks($weekOffset)->addDays($dayOfWeek)->toDateString();
            };

            $resIds = []; // [patientId => reservationId]

            // ─── GROUP A (40): 2 no-show, 38 attended ─────────────────
            foreach ($groupA_ids as $i => $pid) {
                $date = $makeDate(intdiv($i, 7), $i % 6);
                $time = $times[$i % count($times)];
                $isNoShow = ($i < 2); // 2 no-shows
                $status   = $isNoShow ? 'booked' : 'ready'; // ready = completed interview

                // Arrival & sampling times (delay: A = ~24 min)
                $arrivalHour  = (int)explode(':', $time)[0];
                $arrivalMin   = (int)explode(':', $time)[1] + rand(0, 10);
                $samplingMin  = $arrivalMin + rand(18, 30); // 18-30 min delay
                $arrivalTs    = !$isNoShow ? Carbon::parse($date . ' ' . $time)->addMinutes(rand(0, 10)) : null;
                $samplingTs   = !$isNoShow ? $arrivalTs->copy()->addMinutes(rand(18, 30)) : null;

                $row = [
                    'patient_id'   => $pid,
                    'doctor_id'    => null,
                    'analysis_date'=> $date,
                    'time'         => $time,
                    'status'       => $status,
                    'created_at'   => now()->subWeeks(7),
                    'updated_at'   => now()->subWeeks(1),
                ];
                if ($hasQualityCols) {
                    $row['group']         = 'A';
                    $row['is_no_show']    = $isNoShow ? 1 : 0;
                    $row['arrival_time']  = $arrivalTs;
                    $row['sampling_time'] = $samplingTs;
                }
                $rid = DB::table('reservations')->insertGetId($row);
                $resIds[$pid] = $rid;
            }

            // ─── GROUP B (40): 9 no-show, 31 attended ─────────────────
            foreach ($groupB_ids as $i => $pid) {
                $date = $makeDate(intdiv($i, 7), $i % 6);
                $time = $times[$i % count($times)];
                $isNoShow = ($i < 9); // 9 no-shows
                $status   = $isNoShow ? 'booked' : 'ready';

                $arrivalTs  = !$isNoShow ? Carbon::parse($date . ' ' . $time)->addMinutes(rand(0, 15)) : null;
                $samplingTs = !$isNoShow ? $arrivalTs->copy()->addMinutes(rand(33, 50)) : null; // ~41 min avg

                $row = [
                    'patient_id'   => $pid,
                    'doctor_id'    => null,
                    'analysis_date'=> $date,
                    'time'         => $time,
                    'status'       => $status,
                    'created_at'   => now()->subWeeks(7),
                    'updated_at'   => now()->subWeeks(1),
                ];
                if ($hasQualityCols) {
                    $row['group']         = 'B';
                    $row['is_no_show']    = $isNoShow ? 1 : 0;
                    $row['arrival_time']  = $arrivalTs;
                    $row['sampling_time'] = $samplingTs;
                }
                $rid = DB::table('reservations')->insertGetId($row);
                $resIds[$pid] = $rid;
            }

            // ═══════════════════════════════════════════════════════════
            // 3. INSERT NON-CONFORMITIES (if table exists)
            // ═══════════════════════════════════════════════════════════
            if (Schema::hasTable('non_conformities')) {
                // GROUP A: 1 NC total (1 ECBU contaminé)
                $ncA = [
                    ['type' => 'ecbu_contaminated', 'group' => 'A', 're_sampling_required' => 0, 'notes' => 'ECBU contaminé – collecte incorrecte malgré instructions'],
                ];

                // GROUP B: 8 NC total
                $ncB = [
                    ['type' => 'patient_not_prepared', 'group' => 'B', 're_sampling_required' => 1, 'notes' => 'Patiente non à jeun – glycémie'],
                    ['type' => 'patient_not_prepared', 'group' => 'B', 're_sampling_required' => 1, 'notes' => 'Patient non à jeun – bilan lipidique'],
                    ['type' => 'patient_not_prepared', 'group' => 'B', 're_sampling_required' => 1, 'notes' => 'Patient a pris médicament contraindiqué'],
                    ['type' => 'ecbu_contaminated',    'group' => 'B', 're_sampling_required' => 1, 'notes' => 'ECBU contaminé – mauvaise collecte'],
                    ['type' => 'ecbu_contaminated',    'group' => 'B', 're_sampling_required' => 1, 'notes' => 'ECBU contaminé – absence de nettoyage'],
                    ['type' => 'other',                'group' => 'B', 're_sampling_required' => 0, 'notes' => 'Délai transport > 2h – ECBU non exploitable'],
                    ['type' => 'other',                'group' => 'B', 're_sampling_required' => 0, 'notes' => 'Délai transport > 2h – NFS altérée'],
                    ['type' => 'identification_error', 'group' => 'B', 're_sampling_required' => 0, 'notes' => 'Erreur d\'identité sur le tube de prélèvement'],
                ];

                $dates = [
                    Carbon::now()->subWeeks(4)->toDateString(),
                    Carbon::now()->subWeeks(3)->toDateString(),
                    Carbon::now()->subWeeks(3)->addDays(2)->toDateString(),
                    Carbon::now()->subWeeks(2)->toDateString(),
                    Carbon::now()->subWeeks(2)->addDays(3)->toDateString(),
                    Carbon::now()->subWeek()->toDateString(),
                ];

                $allNc = array_merge($ncA, $ncB);
                foreach ($allNc as $idx => $nc) {
                    // Pick a matching patient from the group
                    $groupIds = $nc['group'] === 'A' ? $groupA_ids : $groupB_ids;
                    $pid = $groupIds[$idx % count($groupIds)];

                    DB::table('non_conformities')->insert([
                        'patient_id'           => $pid,
                        'reservation_id'       => $resIds[$pid] ?? null,
                        'type'                 => $nc['type'],
                        'group'                => $nc['group'],
                        're_sampling_required' => $nc['re_sampling_required'],
                        'notes'                => $nc['notes'],
                        'date'                 => $dates[$idx % count($dates)],
                        'created_at'           => now()->subWeeks(rand(1, 4)),
                        'updated_at'           => now(),
                    ]);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
