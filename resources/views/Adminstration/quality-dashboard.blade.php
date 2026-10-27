@extends('Adminstration.layout')

@section('title', 'Tableau de bord qualité')

@section('styles')
<style>
    :root {
        --medical-blue: #1565c0;
        --medical-blue-light: #e3f2fd;
        --success: #2e7d32;
        --success-light: #e8f5e9;
        --warning: #e65100;
        --warning-light: #fff3e0;
        --danger: #c62828;
        --danger-light: #ffebee;
        --muted: #546e7a;
        --card-bg: #ffffff;
        --bg: #f4f6f9;
    }

    body { background: var(--bg); }

    /* ─── HEADER ─── */
    .qd-header {
        background: linear-gradient(135deg, #1a237e 0%, #1565c0 100%);
        color: white;
        padding: 28px 32px;
        border-radius: 0 0 20px 20px;
        margin-bottom: 28px;
    }
    .qd-header h1 { font-size: 1.7rem; font-weight: 700; margin: 0; }
    .qd-header p  { opacity: .75; margin: 4px 0 0; font-size: .95rem; }

    /* ─── FILTER BAR ─── */
    .filter-bar {
        background: white;
        border-radius: 14px;
        padding: 18px 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-end;
    }
    .filter-bar .form-group { flex: 1; min-width: 140px; }
    .filter-bar label { font-size: .78rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 5px; }
    .filter-bar .form-control, .filter-bar .form-select { border-radius: 8px; font-size: .9rem; border-color: #dce3ec; }
    .filter-bar .btn-apply {
        background: var(--medical-blue);
        color: white;
        border-radius: 8px;
        padding: 9px 22px;
        font-weight: 600;
        border: none;
        white-space: nowrap;
    }
    .filter-bar .btn-apply:hover { background: #0d47a1; }

    /* ─── KPI CARDS ─── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .kpi-card {
        background: white;
        border-radius: 14px;
        padding: 20px 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        border-top: 4px solid var(--medical-blue);
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
    .kpi-card.good    { border-top-color: #2e7d32; }
    .kpi-card.warning { border-top-color: #e65100; }
    .kpi-card.danger  { border-top-color: #c62828; }

    .kpi-label { font-size: .75rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
    .kpi-value { font-size: 2rem; font-weight: 800; color: #1a237e; line-height: 1; margin-bottom: 6px; }
    .kpi-sub   { font-size: .82rem; color: var(--muted); }
    .kpi-badge {
        display: inline-block;
        font-size: .72rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        margin-top: 6px;
    }
    .badge-good    { background: var(--success-light); color: var(--success); }
    .badge-warning { background: var(--warning-light); color: var(--warning); }
    .badge-danger  { background: var(--danger-light); color: var(--danger); }

    /* ─── CHART CARDS ─── */
    .chart-card {
        background: white;
        border-radius: 14px;
        padding: 22px 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        margin-bottom: 24px;
        height: 100%;
    }
    .chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a237e;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f2f5;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-canvas-wrap { position: relative; height: 280px; }
    .chart-canvas-wrap.pie { height: 240px; }

    /* ─── SUMMARY TABLE ─── */
    .summary-table th { font-size: .78rem; text-transform: uppercase; letter-spacing: .5px; background: #f4f6f9; color: var(--muted); }
    .status-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 5px; }
    .dot-good    { background: #2e7d32; }
    .dot-warning { background: #e65100; }
    .dot-danger  { background: #c62828; }

    /* ─── NC FORM ─── */
    .nc-form-card {
        background: white;
        border-radius: 14px;
        padding: 22px 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        margin-bottom: 24px;
    }
    .nc-form-card .nc-form-title {
        font-size: 1rem;
        font-weight: 700;
        color: #c62828;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ─── PRINT ─── */
    @media print {
        .filter-bar, .nc-form-card, .btn, nav, footer { display: none !important; }
        .chart-card, .kpi-card { break-inside: avoid; }
    }

    @media (max-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection


@section('content')
<div class="qd-header">
    <h1><i class="fas fa-shield-alt me-2"></i> {{ __('messages.lab_quality') }}</h1>
    <p>{{ __('messages.pre_analytical_indicators') }}</p>
</div>

<div class="container-fluid px-4">

    {{-- =================== FILTERS =================== --}}
    <form method="GET" action="{{ route('admin.quality.dashboard') }}" class="filter-bar">
        <div class="form-group">
            <label>{{ __('messages.time_period') }}</label>
            <select name="period" class="form-select" onchange="this.form.submit()">
                <option value="today"      {{ $period == 'today'      ? 'selected' : '' }}>{{ __('messages.today') }}</option>
                <option value="this_week"  {{ $period == 'this_week'  ? 'selected' : '' }}>{{ __('messages.this_week') }}</option>
                <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>{{ __('messages.this_month') }}</option>
                <option value="custom"     {{ $period == 'custom'     ? 'selected' : '' }}>{{ __('messages.custom_period') }}</option>
            </select>
        </div>
        @if($period === 'custom')
        <div class="form-group">
            <label>{{ __('messages.from_date') }}</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->toDateString()) }}">
        </div>
        <div class="form-group">
            <label>{{ __('messages.to_date') }}</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->toDateString()) }}">
        </div>
        @endif
        <div class="form-group">
            <label>{{ __('messages.group') }}</label>
            <select name="group" class="form-select">
                <option value="all" {{ $groupFilter == 'all' ? 'selected' : '' }}>{{ __('messages.all') }}</option>
                <option value="A"   {{ $groupFilter == 'A'   ? 'selected' : '' }}>Groupe A</option>
                <option value="B"   {{ $groupFilter == 'B'   ? 'selected' : '' }}>Groupe B</option>
            </select>
        </div>
        <div class="form-group" style="flex: 0;">
            <label>&nbsp;</label>
            <button type="submit" class="btn-apply d-block">
                <i class="fas fa-filter me-1"></i> {{ __('messages.apply') }}
            </button>
        </div>
        <div class="form-group" style="flex: 0;">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-outline-secondary d-block" onclick="window.print()">
                <i class="fas fa-print me-1"></i> {{ __('messages.print') }}
            </button>
        </div>
    </form>

    {{-- Date range display --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <small class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            الفترة: {{ $dateFrom->format('Y-m-d') }} → {{ $dateTo->format('Y-m-d') }}
        </small>
        @if(session('success'))
            <div class="alert alert-success py-1 px-3 mb-0" style="font-size:.85rem;">{{ session('success') }}</div>
        @endif
    </div>

    {{-- =================== KPI CARDS =================== --}}
    <div class="kpi-grid">
        @php
            $kpis = [
                ['label' => __('messages.total_patients'), 'value' => $totalPatients, 'sub' => __('messages.in_system'), 'status' => 'good', 'icon' => 'fa-users', 'color' => '#1565c0'],
                ['label' => __('messages.bookings'), 'value' => $totalReservations, 'sub' => __('messages.in_period'), 'status' => 'good', 'icon' => 'fa-calendar-check', 'color' => '#1565c0'],
                ['label' => __('messages.nc_rate'), 'value' => $ncRate . '%', 'sub' => "$totalNc " . __('messages.cases_from') . " $totalReservations", 'status' => $ncRate < 5 ? 'good' : ($ncRate < 15 ? 'warning' : 'danger'), 'icon' => 'fa-exclamation-triangle', 'color' => '#e65100'],
                ['label' => __('messages.non_prepared'), 'value' => $nonPreparedRate . '%', 'sub' => "$nonPreparedCount " . __('messages.patient'), 'status' => $nonPreparedRate < 10 ? 'good' : ($nonPreparedRate < 20 ? 'warning' : 'danger'), 'icon' => 'fa-user-times', 'color' => '#c62828'],
                ['label' => __('messages.resampling'), 'value' => $reSamplingCount, 'sub' => __('messages.resampling_case'), 'status' => $reSamplingCount < 3 ? 'good' : ($reSamplingCount < 8 ? 'warning' : 'danger'), 'icon' => 'fa-sync-alt', 'color' => '#6a1b9a'],
                ['label' => __('messages.avg_waiting_time'), 'value' => $avgDelay . ' ' . __('messages.min'), 'sub' => __('messages.arrival_to_draw'), 'status' => $avgDelay < 30 ? 'good' : ($avgDelay < 60 ? 'warning' : 'danger'), 'icon' => 'fa-clock', 'color' => '#0288d1'],
                ['label' => __('messages.no_show'), 'value' => $noShowCount, 'sub' => $noShowRate . '% ' . __('messages.of_bookings'), 'status' => $noShowRate < 10 ? 'good' : ($noShowRate < 20 ? 'warning' : 'danger'), 'icon' => 'fa-door-open', 'color' => '#bf360c'],
                ['label' => __('messages.instruction_compliance'), 'value' => $preparedRate . '%', 'sub' => __('messages.of_patients'), 'status' => $preparedRate > 90 ? 'good' : ($preparedRate > 75 ? 'warning' : 'danger'), 'icon' => 'fa-check-circle', 'color' => '#2e7d32'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="kpi-card {{ $kpi['status'] }}">
            <div class="kpi-label"><i class="fas {{ $kpi['icon'] }} me-1" style="color: {{ $kpi['color'] }}"></i> {{ $kpi['label'] }}</div>
            <div class="kpi-value">{{ $kpi['value'] }}</div>
            <div class="kpi-sub">{{ $kpi['sub'] }}</div>
            <span class="kpi-badge badge-{{ $kpi['status'] }}">
                @if($kpi['status'] === 'good') ✅ {{ __('messages.status_good') }} @elseif($kpi['status'] === 'warning') ⚠️ {{ __('messages.status_average') }} @else 🔴 {{ __('messages.needs_attention') }} @endif
            </span>
        </div>
        @endforeach
    </div>

    {{-- =================== CHARTS ROW 1 =================== --}}
    <div class="row g-4 mb-2">
        {{-- Trend Line Chart --}}
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-line" style="color:#1565c0"></i>
                    {{ __('messages.nc_trends') }}
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        {{-- NC Distribution Donut --}}
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-pie" style="color:#e65100"></i>
                    {{ __('messages.nc_distribution') }}
                </div>
                <div class="chart-canvas-wrap pie">
                    <canvas id="ncPieChart"></canvas>
                </div>
                @if(empty($ncData))
                    <p class="text-center text-muted mt-3" style="font-size:.85rem;">{{ __('messages.no_nc_recorded') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- =================== CHARTS ROW 2 =================== --}}
    <div class="row g-4 mb-2">
        {{-- NC By Type Bar --}}
        <div class="col-lg-7">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-bar" style="color:#6a1b9a"></i>
                    {{ __('messages.quality_indicators_comparison') }}
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="ncBarChart"></canvas>
                </div>
            </div>
        </div>
        {{-- ECBU Specific --}}
        <div class="col-lg-5">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-vial" style="color:#0288d1"></i>
                    {{ __('messages.ecbu_quality') }}
                </div>
                <div class="chart-canvas-wrap pie">
                    <canvas id="ecbuChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== SUMMARY TABLE =================== --}}
    <div class="chart-card mb-4">
        <div class="chart-title">
            <i class="fas fa-table" style="color:#1565c0"></i>
            {{ __('messages.quality_summary_table') }}
        </div>
        <div class="table-responsive">
            <table class="table summary-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.indicator') }}</th>
                        <th>{{ __('messages.value') }}</th>
                        <th>{{ __('messages.rate') }}</th>
                        <th>{{ __('messages.reference_threshold') }}</th>
                        <th>{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summaryTable as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row['indicator'] }}</td>
                        <td>{{ $row['value'] }}</td>
                        <td>{{ $row['rate'] }}</td>
                        <td><span class="text-muted small">{{ $row['threshold'] }}</span></td>
                        <td>
                            <span class="status-dot dot-{{ $row['status'] }}"></span>
                            @if($row['status'] === 'good')    <span class="text-success fw-semibold small">{{ __('messages.status_good') }}</span>
                            @elseif($row['status'] === 'warning') <span class="text-warning fw-semibold small">{{ __('messages.status_average') }}</span>
                            @else <span class="text-danger fw-semibold small">{{ __('messages.needs_intervention') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- =================== NC REGISTRATION FORM =================== --}}
    <div class="nc-form-card">
        <div class="nc-form-title">
            <i class="fas fa-plus-circle"></i>
            {{ __('messages.nc_registration') }}
        </div>
        <form method="POST" action="{{ route('admin.quality.nc.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">{{ __('messages.type') }} <span class="text-danger">*</span></label>
                    <select name="type" class="form-select form-select-sm" required>
                        @foreach($typeLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">{{ __('messages.date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">{{ __('messages.group') }}</label>
                    <select name="group" class="form-select form-select-sm">
                        <option value="">—</option>
                        <option value="A">Groupe A</option>
                        <option value="B">Groupe B</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">{{ __('messages.notes') }}</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="{{ __('messages.notes_optional') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="re_sampling_required" id="reSamp" value="1">
                        <label class="form-check-label small" for="reSamp">{{ __('messages.re_sampling_required') }}</label>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-save"></i> {{ __('messages.save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- =================== RESEARCH / COMPARATIVE STUDY =================== --}}
    <div style="background: linear-gradient(135deg,#1a237e,#283593); border-radius:16px; padding:22px 28px; margin-bottom:24px; color:white;">
        <h4 style="font-weight:800; margin:0;"><i class="fas fa-microscope me-2"></i>{{ __('messages.research_study_ab') }}</h4>
        <p style="opacity:.75; margin:4px 0 0; font-size:.9rem;">{{ __('messages.research_study_desc') }}</p>
    </div>

    @php
        $a = $research['summary']['groupA'];
        $b = $research['summary']['groupB'];
    @endphp

    {{-- Score Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="chart-card text-center border-top border-4 border-success">
                <div style="font-size:.78rem; font-weight:700; color:#2e7d32; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">{{ __('messages.quality_score_group_a') }}</div>
                <div style="font-size:3.5rem; font-weight:900; color:#2e7d32; line-height:1;">{{ $a['qualityScore'] }}</div>
                <div style="font-size:.85rem; color:#546e7a; margin-top:4px;">/ 100 — {{ __('messages.with_system') }}</div>
                <div style="margin-top:12px; background:#e8f5e9; border-radius:50px; height:10px; overflow:hidden;">
                    <div style="background:#2e7d32; height:100%; width:{{ $a['qualityScore'] }}%;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="chart-card text-center border-top border-4 border-danger">
                <div style="font-size:.78rem; font-weight:700; color:#c62828; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">{{ __('messages.quality_score_group_b') }}</div>
                <div style="font-size:3.5rem; font-weight:900; color:#c62828; line-height:1;">{{ $b['qualityScore'] }}</div>
                <div style="font-size:.85rem; color:#546e7a; margin-top:4px;">/ 100 — {{ __('messages.without_system') }}</div>
                <div style="margin-top:12px; background:#ffebee; border-radius:50px; height:10px; overflow:hidden;">
                    <div style="background:#c62828; height:100%; width:{{ $b['qualityScore'] }}%;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="chart-card text-center border-top border-4" style="border-color:#1565c0!important;">
                <div style="font-size:.78rem; font-weight:700; color:#1565c0; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">{{ __('messages.avg_delay') }}</div>
                <div class="d-flex justify-content-around align-items-center mt-2">
                    <div><div style="font-size:2rem; font-weight:900; color:#2e7d32;">{{ $a['avgDelayMin'] }}</div><div style="font-size:.75rem; color:#546e7a;">{{ __('messages.min') }} – A</div></div>
                    <div style="font-size:1.5rem; color:#ccc;">vs</div>
                    <div><div style="font-size:2rem; font-weight:900; color:#c62828;">{{ $b['avgDelayMin'] }}</div><div style="font-size:.75rem; color:#546e7a;">{{ __('messages.min') }} – B</div></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="chart-card text-center border-top border-4" style="border-color:#6a1b9a!important;">
                <div style="font-size:.78rem; font-weight:700; color:#6a1b9a; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">{{ __('messages.nc_after_sampling') }}</div>
                <div class="d-flex justify-content-around align-items-center mt-2">
                    <div><div style="font-size:2rem; font-weight:900; color:#2e7d32;">{{ $a['postSamplingNCRate'] }}%</div><div style="font-size:.75rem; color:#546e7a;">A</div></div>
                    <div style="font-size:1.5rem; color:#ccc;">vs</div>
                    <div><div style="font-size:2rem; font-weight:900; color:#c62828;">{{ $b['postSamplingNCRate'] }}%</div><div style="font-size:.75rem; color:#546e7a;">B</div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Comparison Bar Chart + NC Pie Charts --}}
    <div class="row g-4 mb-3">
        <div class="col-lg-7">
            <div class="chart-card">
                <div class="chart-title"><i class="fas fa-balance-scale" style="color:#1565c0"></i> {{ __('messages.comparison_essential_indicators') }}</div>
                <div class="chart-canvas-wrap"><canvas id="abCompareChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="chart-card">
                <div class="chart-title"><i class="fas fa-project-diagram" style="color:#e65100"></i> {{ __('messages.nc_distribution_ab') }}</div>
                <div class="row">
                    <div class="col-6"><div style="text-align:center; font-size:.78rem; font-weight:700; color:#2e7d32; margin-bottom:6px;">{{ __('messages.group') }} A</div><div style="height:160px;"><canvas id="ncPieA"></canvas></div></div>
                    <div class="col-6"><div style="text-align:center; font-size:.78rem; font-weight:700; color:#c62828; margin-bottom:6px;">{{ __('messages.group') }} B</div><div style="height:160px;"><canvas id="ncPieB"></canvas></div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Comparison Table --}}
    <div class="chart-card mb-4">
        <div class="chart-title"><i class="fas fa-table" style="color:#1a237e"></i> {{ __('messages.detailed_comparison_table') }}</div>
        <div class="table-responsive">
            <table class="table summary-table align-middle">
                <thead>
                    <tr>
                        <th>{{ __('messages.indicator') }}</th>
                        <th style="color:#2e7d32; text-align:center;">{{ __('messages.group') }} A ({{ __('messages.with_system') }})</th>
                        <th style="color:#c62828; text-align:center;">{{ __('messages.group') }} B (MANUEL)</th>
                        <th style="text-align:center;">{{ __('messages.difference') }}</th>
                        <th style="text-align:center;">{{ __('messages.impact') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rows = [
                            ['label'=>__('messages.instruction_compliance'), 'a'=>$a['instructionComplianceRate'].'%', 'b'=>$b['instructionComplianceRate'].'%', 'diff'=>'↑ '.($a['instructionComplianceRate']-$b['instructionComplianceRate']).'%'],
                            ['label'=>__('messages.non_prepared'), 'a'=>$a['noShowRate'].'%', 'b'=>$b['noShowRate'].'%', 'diff'=>'↓ '.($b['noShowRate']-$a['noShowRate']).'%'],
                            ['label'=>__('messages.nc_after_sampling'), 'a'=>$a['postSamplingNCRate'].'%', 'b'=>$b['postSamplingNCRate'].'%', 'diff'=>'↓ '.($b['postSamplingNCRate']-$a['postSamplingNCRate']).'%'],
                            ['label'=>__('messages.resampling'), 'a'=>$a['resampling'], 'b'=>$b['resampling'], 'diff'=>'↓ '.($b['resampling']-$a['resampling'])],
                            ['label'=>__('messages.avg_waiting_time'), 'a'=>$a['avgDelayMin'].' '.__('messages.min'), 'b'=>$b['avgDelayMin'].' '.__('messages.min'), 'diff'=>'↓ '.($b['avgDelayMin']-$a['avgDelayMin']).' '.__('messages.min')],
                            ['label'=>__('messages.quality_score'), 'a'=>$a['qualityScore'].'/100', 'b'=>$b['qualityScore'].'/100', 'diff'=>'↑ '.($a['qualityScore']-$b['qualityScore'])],
                        ];
                    @endphp
                    @foreach($rows as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row['label'] }}</td>
                        <td style="text-align:center; color:#2e7d32; font-weight:700;">{{ $row['a'] }}</td>
                        <td style="text-align:center; color:#c62828; font-weight:700;">{{ $row['b'] }}</td>
                        <td style="text-align:center;"><span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $row['diff'] }}</span></td>
                        <td style="text-align:center;"><span class="text-success small fw-semibold">✅ {{ __('messages.improvement_with_system') }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /container-fluid -->

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = "'Tajawal', 'Outfit', sans-serif";
    Chart.defaults.color = '#546e7a';

    const ncColors = {
        patient_not_prepared: '#ef5350',
        identification_error: '#ab47bc',
        hemolysis: '#ff7043',
        ecbu_contaminated: '#26a69a',
        ecbu_non_conformant: '#42a5f5',
        re_sampling: '#ffa726',
        other: '#bdbdbd',
    };

    // 1. TREND LINE CHART
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const trendLabels = {!! json_encode($trendLabels) !!};
        const trendRes    = {!! json_encode($trendResData) !!};
        const trendNc     = {!! json_encode($trendNcData) !!};

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: "{{ __('messages.bookings') }}",
                        data: trendRes,
                        borderColor: '#1565c0',
                        backgroundColor: 'rgba(21,101,192,.08)',
                        fill: true, tension: 0.4,
                        pointRadius: 3, borderWidth: 2,
                    },
                    {
                        label: 'NC',
                        data: trendNc,
                        borderColor: '#c62828',
                        backgroundColor: 'rgba(198,40,40,.06)',
                        fill: true, tension: 0.4,
                        pointRadius: 3, borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false },
                },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
            }
        });
    }

    // 2. NC PIE / DONUT
    const ncPieCtx = document.getElementById('ncPieChart');
    if (ncPieCtx) {
        const ncLabels = {!! json_encode($ncLabels) !!};
        const ncData   = {!! json_encode($ncData) !!};
        const pieColors = ['#ef5350','#ab47bc','#ff7043','#26a69a','#42a5f5','#ffa726','#bdbdbd'];

        new Chart(ncPieCtx, {
            type: 'doughnut',
            data: {
                labels: ncLabels,
                datasets: [{ data: ncData, backgroundColor: pieColors, borderWidth: 0, hoverOffset: 5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 } } } }
            }
        });
    }

    // 3. NC BAR CHART
    const ncBarCtx = document.getElementById('ncBarChart');
    if (ncBarCtx) {
        const barLabels = {!! json_encode($ncTypeBarLabels) !!};
        const barData   = {!! json_encode($ncTypeBarData) !!};

        new Chart(ncBarCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: "{{ __('messages.count') }}",
                    data: barData,
                    backgroundColor: ['#ef5350','#ab47bc','#ff7043','#26a69a','#42a5f5','#ffa726','#bdbdbd'],
                    borderRadius: 6,
                    barPercentage: 0.65,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
            }
        });
    }

    // 4. ECBU CHART (live data)
    const ecbuCtx = document.getElementById('ecbuChart');
    if (ecbuCtx) {
        new Chart(ecbuCtx, {
            type: 'bar',
            data: {
                labels: ["{{ __('messages.ecbu_contaminated') }}", "{{ __('messages.non_conformant') }}", "{{ __('messages.resampling') }} ECBU"],
                datasets: [{
                    data: [{{ $ecbuContaminatedCount }}, {{ $ecbuNonConformantCount }}, {{ $ecbuReSamplingCount }}],
                    backgroundColor: ['#ef5350', '#ff7043', '#ffa726'],
                    borderRadius: 6, barPercentage: 0.55,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });
    }

    // ── RESEARCH A/B CHARTS ──────────────────────────────────

    // 5. MAIN A vs B BAR CHART
    const abCtx = document.getElementById('abCompareChart');
    if (abCtx) {
        new Chart(abCtx, {
            type: 'bar',
            data: {
                labels: ['No-show %', 'NC post-prélèv. %', 'Re-prélèv.', 'Resp. consignes %', 'Err. identif.', "{{ __('messages.avg_delay') }} ({{ __('messages.min') }})"],
                datasets: [
                    {
                        label: "{{ __('messages.group') }} A ({{ __('messages.with_system') }})",
                        data: [5.0, 2.5, 0, 85, 0, 24],
                        backgroundColor: 'rgba(46,125,50,.8)',
                        borderRadius: 5, borderSkipped: false,
                    },
                    {
                        label: "{{ __('messages.group') }} B ({{ __('messages.without_system') }})",
                        data: [22.5, 20, 5, 57.5, 2, 41],
                        backgroundColor: 'rgba(198,40,40,.7)',
                        borderRadius: 5, borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
            }
        });
    }

    // 6. NC PIE – GROUPE A
    const ncPieACtx = document.getElementById('ncPieA');
    if (ncPieACtx) {
        new Chart(ncPieACtx, {
            type: 'doughnut',
            data: {
                labels: ["{{ __('messages.ecbu_contaminated') }}", "{{ __('messages.no_other_nc') }}"],
                datasets: [{ data: [1, 39], backgroundColor: ['#26a69a', '#e8f5e9'], borderWidth: 0, hoverOffset: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.raw } } } }
        });
    }

    // 7. NC PIE – GROUPE B
    const ncPieBCtx = document.getElementById('ncPieB');
    if (ncPieBCtx) {
        new Chart(ncPieBCtx, {
            type: 'doughnut',
            data: {
                labels: ["{{ __('messages.non_prepared') }}", "{{ __('messages.ecbu_contaminated') }}", "{{ __('messages.transport_delay') }}", "{{ __('messages.identification_error') }}"],
                datasets: [{ data: [3, 2, 2, 1], backgroundColor: ['#ef5350','#26a69a','#ffa726','#ab47bc'], borderWidth: 0, hoverOffset: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: { legend: { display: false } } }
        });
    }

    // 8. ECBU A vs B
    const ecbuABCtx = document.getElementById('ecbuABChart');
    if (ecbuABCtx) {
        new Chart(ecbuABCtx, {
            type: 'bar',
            data: {
                labels: ['Compliance %', 'Contaminés %', 'Non-conformes', 'Re-prélèv.'],
                datasets: [
                    { label: "{{ __('messages.group') }} A", data: [80, 10, 1, 1], backgroundColor: 'rgba(46,125,50,.8)', borderRadius: 5 },
                    { label: "{{ __('messages.group') }} B", data: [55, 30, 4, 4], backgroundColor: 'rgba(198,40,40,.7)', borderRadius: 5 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
            }
        });
    }

    // 9. GLYCEMIA STACKED
    const glycCtx = document.getElementById('glycemiaChart');
    if (glycCtx) {
        new Chart(glycCtx, {
            type: 'bar',
            data: {
                labels: ['Groupe A – Avec système', 'Groupe B – Sans système'],
                datasets: [
                    { label: 'Normal', data: [15, 11], backgroundColor: '#43a047', borderRadius: 4 },
                    { label: 'Légèrement élevée', data: [5, 5], backgroundColor: '#ffa726', borderRadius: 4 },
                    { label: 'Biaisée (non à jeun)', data: [0, 4], backgroundColor: '#ef5350', borderRadius: 4 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, max: 20, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
@endsection
