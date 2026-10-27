@extends('Adminstration.layout')

@section('title', __('messages.statistics') ?? 'Statistics')

@section('styles')
<style>
    :root {
        --primary-color: #3b82f6; /* Modern Blue */
        --primary-light: #eff6ff;
        --success-color: #10b981;
        --success-light: #ecfdf5;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-main: #334155;
        --text-muted: #64748b;
        --bg-body: #f8fafc;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
    }

    body {
        background-color: var(--bg-body);
        color: var(--text-main);
    }

    .statistics-container {
        padding: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: var(--shadow-sm);
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 25px;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .icon-primary { background: var(--primary-light); color: var(--primary-color); }
    .icon-success { background: var(--success-light); color: var(--success-color); }
    .icon-warning { background: #fffbeb; color: var(--warning-color); }
    .icon-purple { background: #faf5ff; color: #a855f7; }

    .stat-details h3 {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-details h2 {
        font-size: 28px;
        font-weight: 800;
        margin: 0;
        color: var(--text-main);
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: var(--shadow-sm);
        padding: 25px;
        margin-bottom: 25px;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .chart-card:hover {
        box-shadow: var(--shadow);
    }

    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 15px;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .chart-container.doughnut-container {
        height: 250px;
    }
</style>
@endsection

@section('content')
<div class="statistics-container container-fluid">
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-pie me-2 text-primary"></i> {{ __('messages.statistics_analytics') }}</h2>
    </div>

    <!-- Summary Metrics Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon icon-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <h3>{{ __('messages.total_bookings') }}</h3>
                    <h2>{{ number_format($totalReservations) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>{{ __('messages.completion_rate') }}</h3>
                    <h2>{{ $completionRate }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3>{{ __('messages.total_patients') }}</h3>
                    <h2>{{ number_format($totalPatients) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon icon-warning">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-details">
                    <h3>{{ __('messages.bookings_this_month') }}</h3>
                    <h2>{{ number_format($reservationsThisMonth) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Daily Trends Line Chart -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-line text-primary"></i>
                    {{ __('messages.booking_trends') }}
                </div>
                <div class="chart-container">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution Doughnut Chart -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-pie text-success"></i>
                    {{ __('messages.status_distribution') }}
                </div>
                <div class="chart-container doughnut-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Top Analyses Bar Chart -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-flask text-purple" style="color: #a855f7;"></i>
                    {{ __('messages.top_analyses') }}
                </div>
                <div class="chart-container">
                    <canvas id="topAnalysesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gender Distribution Doughnut Chart -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-venus-mars text-warning"></i>
                    {{ __('messages.gender_distribution') }}
                </div>
                <div class="chart-container doughnut-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared Chart.js options for modern styling
    Chart.defaults.font.family = "'Outfit', 'Tajawal', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.scale.grid.color = '#f1f5f9';

    // 1. Daily Trends Line Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    
    // Create gradient
    let gradient = trendsCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');   
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyTrends['labels']) !!},
            datasets: [{
                label: '{{ __('messages.booking_count') }}',
                data: {!! json_encode($dailyTrends['data']) !!},
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Status Distribution Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusDistribution['labels']) !!},
            datasets: [{
                data: {!! json_encode($statusDistribution['data']) !!},
                backgroundColor: {!! json_encode($statusDistribution['colors']) !!},
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                }
            }
        }
    });

    // 3. Top Analyses Bar Chart
    const topAnalysesCtx = document.getElementById('topAnalysesChart').getContext('2d');
    new Chart(topAnalysesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topAnalyses['labels']) !!},
            datasets: [{
                label: '{{ __('messages.times_requested') }}',
                data: {!! json_encode($topAnalyses['data']) !!},
                backgroundColor: '#a855f7',
                borderRadius: 6,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 4. Gender Distribution Doughnut Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genderDistribution['labels']) !!},
            datasets: [{
                data: {!! json_encode($genderDistribution['data']) !!},
                backgroundColor: ['#60a5fa', '#f472b6'], // Blue for Male, Pink for Female
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                }
            }
        }
    });
});
</script>
@endsection
