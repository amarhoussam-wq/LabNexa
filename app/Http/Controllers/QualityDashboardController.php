<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Patient;
use App\Models\ReservationAnalysis;

class QualityDashboardController extends Controller
{
    public function index(Request $request)
    {
        // === DATE FILTERING ===
        $period      = $request->input('period', 'this_month');
        $groupFilter = $request->input('group', 'all');

        [$dateFrom, $dateTo] = $this->getDateRange($period, $request);

        // === FEATURE FLAGS (depends on migrations being run) ===
        $hasNcTable        = Schema::hasTable('non_conformities');
        $hasQualityCols    = Schema::hasColumn('reservations', 'is_no_show');
        $hasGroupCol       = Schema::hasColumn('reservations', 'group');

        // === BASE QUERIES ===
        $resQuery = Reservation::query()
            ->whereBetween('analysis_date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        if ($groupFilter !== 'all' && $hasGroupCol) {
            $resQuery->where('group', $groupFilter);
        }

        // === KPI CALCULATIONS ===
        $totalPatients     = Patient::count();
        $totalReservations = $resQuery->count();

        // No-show (only if column exists)
        $noShowCount = ($hasQualityCols)
            ? (clone $resQuery)->where('is_no_show', true)->count()
            : 0;
        $noShowRate = $totalReservations > 0 ? round(($noShowCount / $totalReservations) * 100, 1) : 0;

        // Average delay (only if columns exist)
        $avgDelay = 0;
        if ($hasQualityCols && Schema::hasColumn('reservations', 'arrival_time')) {
            $avgDelayData = (clone $resQuery)
                ->whereNotNull('arrival_time')
                ->whereNotNull('sampling_time')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, arrival_time, sampling_time)) as avg_delay')
                ->first();
            $avgDelay = $avgDelayData ? round($avgDelayData->avg_delay ?? 0) : 0;
        }

        // === NC DATA (only if table exists) ===
        $totalNc            = 0;
        $ncRate             = 0;
        $ncRateDelta        = 0;
        $reSamplingCount    = 0;
        $nonPreparedCount   = 0;
        $nonPreparedRate    = 0;
        $identificationErrors       = 0;
        $ecbuContaminatedCount      = 0;
        $ecbuNonConformantCount     = 0;
        $ecbuReSamplingCount        = 0;
        $ncLabels           = [];
        $ncData             = [];
        $trendNcData        = array_fill(0, 30, 0);
        $ncTypeBarData      = array_fill(0, 7, 0);
        $ncByType           = [];
        $typeLabels         = $this->getTypeLabels();

        if ($hasNcTable) {
            // Use NonConformity via DB to avoid model auto-load errors
            $ncQuery = DB::table('non_conformities')
                ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

            if ($groupFilter !== 'all') {
                $ncQuery->where('group', $groupFilter);
            }

            $totalNc = (clone $ncQuery)->count();
            $ncRate  = $totalReservations > 0 ? round(($totalNc / $totalReservations) * 100, 1) : 0;

            $reSamplingCount = DB::table('non_conformities')
                ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                ->where('re_sampling_required', true)->count();

            $ncByType = DB::table('non_conformities')
                ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                ->select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->pluck('total', 'type')
                ->toArray();

            $nonPreparedCount     = $ncByType['patient_not_prepared'] ?? 0;
            $identificationErrors = $ncByType['identification_error'] ?? 0;
            $ecbuContaminatedCount  = $ncByType['ecbu_contaminated'] ?? 0;
            $ecbuNonConformantCount = $ncByType['ecbu_non_conformant'] ?? 0;
            $ecbuReSamplingCount    = DB::table('non_conformities')
                ->whereBetween('date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                ->whereIn('type', ['ecbu_contaminated', 'ecbu_non_conformant'])
                ->where('re_sampling_required', true)->count();

            // NC Pie Chart
            foreach ($typeLabels as $key => $label) {
                if (isset($ncByType[$key]) && $ncByType[$key] > 0) {
                    $ncLabels[] = $label;
                    $ncData[]   = $ncByType[$key];
                }
            }

            // NC Bar Chart
            foreach (array_keys($typeLabels) as $key) {
                $ncTypeBarData[] = $ncByType[$key] ?? 0;
            }
            $ncTypeBarData = array_slice($ncTypeBarData, 7); // remove fill

            // Previous period comparison
            $prevDateFrom = (clone $dateFrom)->sub($dateFrom->diff($dateTo))->subDay();
            $prevDateTo   = (clone $dateFrom)->subDay();
            $prevNc  = DB::table('non_conformities')->whereBetween('date', [$prevDateFrom->toDateString(), $prevDateTo->toDateString()])->count();
            $prevRes = Reservation::whereBetween('analysis_date', [$prevDateFrom->toDateString(), $prevDateTo->toDateString()])->count();
            $prevNcRate  = $prevRes > 0 ? round(($prevNc / $prevRes) * 100, 1) : 0;
            $ncRateDelta = $ncRate - $prevNcRate;
        }

        $nonPreparedRate = $totalReservations > 0 ? round(($nonPreparedCount / $totalReservations) * 100, 1) : 0;
        $preparedCount   = $totalReservations - $nonPreparedCount;
        $preparedRate    = $totalReservations > 0 ? round(($preparedCount / $totalReservations) * 100, 1) : 0;
        $ncTypeBarLabels = array_values($typeLabels);

        // === TREND DATA (last 30 days) ===
        $trendDateFrom = Carbon::now()->subDays(29)->startOfDay();
        $trendDateTo   = Carbon::now()->endOfDay();

        $trendRawRes = Reservation::whereBetween('analysis_date', [$trendDateFrom->toDateString(), $trendDateTo->toDateString()])
            ->select(DB::raw('DATE(analysis_date) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')->pluck('total', 'date')->toArray();

        $trendRawNcArr = [];
        if ($hasNcTable) {
            $trendRawNcArr = DB::table('non_conformities')
                ->whereBetween('date', [$trendDateFrom->toDateString(), $trendDateTo->toDateString()])
                ->select(DB::raw('DATE(date) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')->pluck('total', 'date')->toArray();
        }

        $trendLabels  = [];
        $trendResData = [];
        $trendNcData  = [];
        $cur = clone $trendDateFrom;
        while ($cur <= $trendDateTo) {
            $ds = $cur->toDateString();
            $trendLabels[]  = $cur->format('d M');
            $trendResData[] = $trendRawRes[$ds] ?? 0;
            $trendNcData[]  = $trendRawNcArr[$ds] ?? 0;
            $cur->addDay();
        }

        // === TOTAL ANALYSES ===
        $totalAnalyses = ReservationAnalysis::whereHas('reservation', function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('analysis_date', [$dateFrom->toDateString(), $dateTo->toDateString()]);
        })->count();

        // === SUMMARY TABLE ===
        $summaryTable = [
            ['indicator' => 'مرضى غير محضَّرين',    'value' => $nonPreparedCount,   'rate' => $nonPreparedRate.'%',  'threshold' => '< 10%', 'status' => $nonPreparedRate  < 10 ? 'good' : ($nonPreparedRate  < 20 ? 'warning' : 'danger')],
            ['indicator' => 'عدم المطابقات (NC)',     'value' => $totalNc,            'rate' => $ncRate.'%',          'threshold' => '< 5%',  'status' => $ncRate           < 5  ? 'good' : ($ncRate           < 15 ? 'warning' : 'danger')],
            ['indicator' => 'إعادة السحب',            'value' => $reSamplingCount,    'rate' => '-',                  'threshold' => '< 3',   'status' => $reSamplingCount  < 3  ? 'good' : ($reSamplingCount  < 8  ? 'warning' : 'danger')],
            ['indicator' => 'غيابات (No-show)',       'value' => $noShowCount,        'rate' => $noShowRate.'%',      'threshold' => '< 10%', 'status' => $noShowRate       < 10 ? 'good' : ($noShowRate       < 20 ? 'warning' : 'danger')],
            ['indicator' => 'أخطاء التعريف',          'value' => $identificationErrors,'rate' => '-',                 'threshold' => '0',     'status' => $identificationErrors === 0 ? 'good' : 'danger'],
            ['indicator' => 'متوسط الانتظار',         'value' => $avgDelay.' دق',     'rate' => '-',                  'threshold' => '< 30',  'status' => $avgDelay         < 30 ? 'good' : ($avgDelay         < 60 ? 'warning' : 'danger')],
            ['indicator' => 'احترام التعليمات',       'value' => $preparedCount,      'rate' => $preparedRate.'%',    'threshold' => '> 90%', 'status' => $preparedRate     > 90 ? 'good' : ($preparedRate     > 75 ? 'warning' : 'danger')],
            ['indicator' => 'ECBU ملوثة',             'value' => $ecbuContaminatedCount,'rate' => '-',                'threshold' => '-',     'status' => $ecbuContaminatedCount === 0 ? 'good' : 'warning'],
        ];

        // === RESEARCH DATA (Static A/B study: 40+40 patients) ===
        $research = $this->getResearchData();

        return view('Adminstration.quality-dashboard', compact(
            'dateFrom', 'dateTo', 'period', 'groupFilter',
            'totalPatients', 'totalReservations', 'totalAnalyses',
            'totalNc', 'ncRate', 'ncRateDelta',
            'nonPreparedCount', 'nonPreparedRate',
            'reSamplingCount', 'noShowCount', 'noShowRate',
            'avgDelay', 'preparedRate', 'identificationErrors',
            'ecbuContaminatedCount', 'ecbuNonConformantCount', 'ecbuReSamplingCount',
            'ncLabels', 'ncData',
            'trendLabels', 'trendResData', 'trendNcData',
            'ncTypeBarLabels', 'ncTypeBarData',
            'summaryTable', 'typeLabels',
            'hasNcTable', 'research'
        ));
    }

    private function getDateRange(string $period, Request $request): array
    {
        return match ($period) {
            'today'      => [Carbon::today(), Carbon::today()],
            'this_week'  => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'custom'     => [
                Carbon::parse($request->input('date_from', Carbon::now()->startOfMonth())),
                Carbon::parse($request->input('date_to', Carbon::now())),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    private function getResearchData(): array
    {
        return [
            'summary' => [
                'groupA' => [
                    'label' => 'Groupe A – Avec système',
                    'totalPatients' => 40, 'appointmentsHonored' => 38,
                    'noShow' => 2, 'noShowRate' => 5.0,
                    'firstVisitConformRate' => 77.5, 'samplesCollected' => 33,
                    'postSamplingNC' => 1, 'postSamplingNCRate' => 2.5,
                    'resampling' => 0, 'resamplingRate' => 0.0,
                    'identificationErrors' => 0,
                    'avgDelayMin' => 24,
                    'instructionComplianceRate' => 85.0,
                    'qualityScore' => 88,
                    'nonPreparedDetectedBefore' => 7, 'nonPreparedDetectedBeforeRate' => 17.5,
                ],
                'groupB' => [
                    'label' => 'Groupe B – Sans système',
                    'totalPatients' => 40, 'appointmentsHonored' => 31,
                    'noShow' => 9, 'noShowRate' => 22.5,
                    'firstVisitConformRate' => 55.0, 'samplesCollected' => 38,
                    'postSamplingNC' => 8, 'postSamplingNCRate' => 20.0,
                    'resampling' => 5, 'resamplingRate' => 12.5,
                    'identificationErrors' => 2,
                    'avgDelayMin' => 41,
                    'instructionComplianceRate' => 57.5,
                    'qualityScore' => 61,
                    'nonPreparedDetectedBefore' => 2, 'nonPreparedDetectedBeforeRate' => 5.0,
                ],
            ],
            'glycemia' => [
                'groupA' => ['total' => 20, 'fastingConformRate' => 90.0, 'biasedResults' => 0, 'meanGlycemia' => 0.97, 'normal' => 15, 'slightlyHigh' => 5, 'biased' => 0],
                'groupB' => ['total' => 20, 'fastingConformRate' => 60.0, 'biasedResults' => 4, 'meanGlycemia' => 1.08, 'normal' => 11, 'slightlyHigh' => 5, 'biased' => 4],
            ],
            'ecbu' => [
                'groupA' => ['total' => 20, 'instructionComplianceRate' => 80.0, 'contaminated' => 2, 'contaminatedRate' => 10.0, 'nonConform' => 1, 'resampling' => 1, 'resamplingRate' => 5.0],
                'groupB' => ['total' => 20, 'instructionComplianceRate' => 55.0, 'contaminated' => 6, 'contaminatedRate' => 30.0, 'nonConform' => 4, 'resampling' => 4, 'resamplingRate' => 20.0],
            ],
            'ncDistribution' => [
                'groupA' => ['patient_not_prepared' => 0, 'ecbu_contaminated' => 1, 'transport_delay' => 0, 'identification_error' => 0, 'other' => 0],
                'groupB' => ['patient_not_prepared' => 3, 'ecbu_contaminated' => 2, 'transport_delay' => 2, 'identification_error' => 1, 'other' => 0],
            ],
        ];
    }

    private function getTypeLabels(): array
    {
        return [
            'patient_not_prepared' => 'مريض غير محضَّر',
            'identification_error'  => 'خطأ تعريف',
            'hemolysis'             => 'Hémolyse',
            'ecbu_contaminated'     => 'ECBU ملوث',
            'ecbu_non_conformant'   => 'ECBU غير مطابق',
            're_sampling'           => 'إعادة السحب',
            'other'                 => 'أخرى',
        ];
    }

    public function storeNc(Request $request)
    {
        if (!Schema::hasTable('non_conformities')) {
            return back()->withErrors(['يجب تشغيل الـ Migration أولاً. افتح: /run-migrate']);
        }

        $request->validate([
            'type'  => 'required|in:patient_not_prepared,identification_error,hemolysis,ecbu_contaminated,ecbu_non_conformant,re_sampling,other',
            'date'  => 'required|date',
            'notes' => 'nullable|string|max:500',
            'group' => 'nullable|in:A,B',
        ]);

        DB::table('non_conformities')->insert([
            'type'                 => $request->type,
            'date'                 => $request->date,
            'reservation_id'       => $request->reservation_id,
            'patient_id'           => $request->patient_id,
            'notes'                => $request->notes,
            're_sampling_required' => $request->boolean('re_sampling_required') ? 1 : 0,
            'group'                => $request->group,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return back()->with('success', __('messages.non_conformity_recorded'));
    }
}
