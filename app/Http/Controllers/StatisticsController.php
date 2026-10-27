<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Patient;
use App\Models\ReservationAnalysis;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        // 1. Overview Metrics
        $totalReservations = Reservation::count();
        $reservationsThisMonth = Reservation::whereMonth('analysis_date', Carbon::now()->month)->count();
        $completedReservations = Reservation::where('status', 'completed')->count();
        $completionRate = $totalReservations > 0 ? round(($completedReservations / $totalReservations) * 100) : 0;
        
        $totalPatients = Patient::count();

        // 2. Status Distribution
        $statusDistributionQuery = Reservation::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
            
        $statusDistribution = [
            'labels' => [],
            'data' => [],
            'colors' => [],
        ];

        $statusColors = [
            'booked' => '#3b82f6', // Blue
            'ready' => '#10b981', // Green
            'pending_approval' => '#f59e0b', // Yellow
            'warning' => '#ea580c', // Orange
            'blocked' => '#ef4444', // Red
            'completed' => '#6366f1', // Indigo
        ];

        foreach ($statusDistributionQuery as $stat) {
            $statusDistribution['labels'][] = __('messages.' . $stat->status) ?? $stat->status;
            $statusDistribution['data'][] = $stat->total;
            $statusDistribution['colors'][] = $statusColors[$stat->status] ?? '#cbd5e1';
        }

        // 3. Gender Distribution
        $genderDistributionQuery = Patient::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get();

        $genderDistribution = [
            'labels' => [],
            'data' => []
        ];

        foreach ($genderDistributionQuery as $gender) {
            $genderDistribution['labels'][] = $gender->gender == 'M' ? __('messages.male') ?? 'Male' : __('messages.female') ?? 'Female';
            $genderDistribution['data'][] = $gender->total;
        }

        // 4. Daily Trend (Last 30 Days)
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateString();
        $dailyTrendsQuery = Reservation::select(DB::raw('DATE(analysis_date) as date'), DB::raw('count(*) as total'))
            ->where('analysis_date', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $dailyTrends = [
            'labels' => [],
            'data' => []
        ];

        // Fill missing days with 0
        $currentDate = Carbon::parse($thirtyDaysAgo);
        $endDate = Carbon::now();
        $trendsDataRaw = $dailyTrendsQuery->keyBy('date')->toArray();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->toDateString();
            $dailyTrends['labels'][] = $currentDate->format('M d'); // e.g. Mar 01
            $dailyTrends['data'][] = isset($trendsDataRaw[$dateStr]) ? $trendsDataRaw[$dateStr]['total'] : 0;
            $currentDate->addDay();
        }

        // 5. Top Requested Analyses
        $topAnalysesQuery = ReservationAnalysis::select('analysis_id', DB::raw('count(*) as total'))
            ->with('analyse') // Eager load the relation
            ->groupBy('analysis_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topAnalyses = [
            'labels' => [],
            'data' => []
        ];

        foreach ($topAnalysesQuery as $ta) {
            $name = $ta->analyse ? $ta->analyse->name : 'Unknown';
            // Truncate long names for chart readability
            if (mb_strlen($name) > 20) {
                $name = mb_substr($name, 0, 20) . '...';
            }
            $topAnalyses['labels'][] = $name;
            $topAnalyses['data'][] = $ta->total;
        }

        return view('Adminstration.statistics', compact(
            'totalReservations',
            'reservationsThisMonth',
            'completionRate',
            'totalPatients',
            'statusDistribution',
            'genderDistribution',
            'dailyTrends',
            'topAnalyses'
        ));
    }
}
