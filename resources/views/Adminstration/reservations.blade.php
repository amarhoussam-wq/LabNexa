@extends('Adminstration.layout')

@section('title', __('messages.manage_reservations'))

@section('content')
<div class="section-header d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-calendar-check"></i> {{ __('messages.manage_reservations') }}</h2>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-success shadow-sm px-4 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
            <i class="fas fa-qrcode fs-5"></i> 
            <span class="fw-bold">{{ __('messages.scan_qr') }}</span>
        </button>
        <div id="toast-container" class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;"></div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary" id="qrScannerModalLabel">
                    <i class="fas fa-camera me-2"></i> {{ __('messages.scan_qr') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="text-muted small mb-3">{{ __('messages.scan_qr_help') }}</p>
                <div id="qr-reader" class="rounded-3 border bg-light overflow-hidden" style="width: 100%; min-height: 300px;"></div>
                <div id="qr-reader-results" class="mt-3"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-container">
    <h3><i class="fas fa-filter"></i> {{ __('messages.filter_reservations') }}</h3>
    <form method="GET" action="{{ route('filter.reservations') }}">
        <div class="filter-row">
            <div class="form-group">
                <label>{{ __('messages.start_date') }}</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>{{ __('messages.status') }}</label>
                <select name="status" class="form-control">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('messages.confirmed') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('messages.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('messages.search') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> {{ __('messages.apply_filter') }}
        </button>
        <a href="{{ route('reservations') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
        </a>
    </form>
</div>

<!-- Letter Filter -->
<div class="letter-filter mb-4">
    <div class="d-flex flex-wrap gap-1 justify-content-center">
        <a href="{{ request()->fullUrlWithQuery(['letter' => null]) }}" class="btn btn-sm {{ !request('letter') ? 'btn-primary' : 'btn-outline-secondary' }}">ALL</a>
        @foreach(range('A', 'Z') as $char)
            <a href="{{ request()->fullUrlWithQuery(['letter' => $char]) }}" 
               class="btn btn-sm {{ request('letter') == $char ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="width: 35px;">{{ $char }}</a>
        @endforeach
        <a href="{{ request()->fullUrlWithQuery(['letter' => '#']) }}" class="btn btn-sm {{ request('letter') == '#' ? 'btn-primary' : 'btn-outline-secondary' }}">#</a>
    </div>
</div>

<!-- Results Table -->
<div class="table-container container-fluid px-0 mb-4">
    <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    @if($bookings->count() > 0)
        <div class="table-info mx-0 mt-3 px-3 d-flex justify-content-between align-items-center">
            <p class="mb-0">{{ __('messages.showing_results', ['count' => $bookings->count(), 'total' => $bookings->total()]) }}</p>
            @if(request('letter'))
                <span class="badge bg-primary px-3 py-2">{{ __('messages.filters') }}: "{{ request('letter') }}"</span>
            @endif
        </div>
        
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order', 'asc') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                            {{ __('messages.customer') }}
                            <i class="fas fa-sort{{ request('sort_by') == 'name' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }} ms-1 opacity-50"></i>
                        </a>
                    </th>
                    <th>{{ __('messages.phone') }}</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'analysis_date', 'sort_order' => request('sort_by') == 'analysis_date' && request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                            {{ __('messages.date') }}
                            <i class="fas fa-sort{{ request('sort_by') == 'analysis_date' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }} ms-1 opacity-50"></i>
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'time', 'sort_order' => request('sort_by') == 'time' && request('sort_order', 'asc') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center">
                            {{ __('messages.time') }}
                            <i class="fas fa-sort{{ request('sort_by') == 'time' ? (request('sort_order') == 'asc' ? '-up' : '-down') : '' }} ms-1 opacity-50"></i>
                        </a>
                    </th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $reservation)
                <tr>
                    <td>{{ $loop->iteration + ($bookings->perPage() * ($bookings->currentPage() - 1)) }}</td>
                    <td class="fw-bold">{{ $reservation->patient?->name }}</td>
                    <td>{{ $reservation->patient?->phone }}</td>
                    <td>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($reservation->analysis_date)->format('Y-m-d') }}</div>
                        <div style="font-size: 0.75rem; margin-top: 4px;">
                            @php
                                try {
                                    $dt = \Carbon\Carbon::parse($reservation->analysis_date . ' ' . $reservation->time);
                                    if ($dt->isPast()) {
                                        echo '<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> متأخر: ' . $dt->locale('ar')->diffForHumans(null, true) . '</span>';
                                    } else {
                                        echo '<span class="text-primary fw-bold"><i class="fas fa-clock"></i> متبقي: ' . $dt->locale('ar')->diffForHumans(null, true) . '</span>';
                                    }
                                } catch(\Exception $e) { }
                            @endphp
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold">{{ date('H:i', strtotime($reservation->time)) }}</div>
                        <div style="font-size: 0.75rem; margin-top: 4px;">
                            @if($reservation->email_sent === 1)
                                <span class="text-success" title="تم إرسال إيميل التأكيد بنجاح"><i class="fas fa-check-circle"></i> إيميل مُرسَل</span>
                            @elseif($reservation->email_sent === 0 && $reservation->email_error != null)
                                <span class="text-danger" title="خطأ: {{ $reservation->email_error }}"><i class="fas fa-times-circle"></i> إيميل فشل</span>
                            @else
                                <span class="text-muted" title="لم يتم إرسال إيميل"><i class="fas fa-envelope"></i> بلا إيميل</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $reservation->status }}">
                            @if($reservation->status == 'booked') {{ __('messages.booked') }}
                            @elseif($reservation->status == 'ready') {{ __('messages.ready') }}
                            @elseif($reservation->status == 'blocked') {{ __('messages.blocked') }}
                            @elseif($reservation->status == 'warning') {{ __('messages.warning') }}
                            @elseif($reservation->status == 'pending_approval') {{ __('messages.pending_approval') }}
                            @elseif($reservation->status == 'completed') {{ __('messages.completed') }}
                            @else {{ $reservation->status }} @endif
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="{{ route('admin.bookings.show', $reservation->id) }}" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-eye me-1"></i> {{ __('messages.view_manage') ?? 'View / Manage' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        
        <!-- Pagination -->
        <div class="pagination-container">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-calendar-times"></i>
            <p>{{ __('messages.no_reservations_found') }}</p>
            <a href="{{ route('reservations') }}" class="btn btn-primary">{{ __('messages.view_all_reservations') }}</a>
        </div>
    @endif
</div>


<style>
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mini-status-select {
        border: none;
        background: transparent;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 12px;
        cursor: pointer;
        outline: none;
        width: 85px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .analysis-status-item {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 15px;
        padding: 4px 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: transform 0.2s;
    }
    
    .analysis-status-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .analysis-name {
        display: inline-block;
        font-size: 11px;
        color: #4a5568;
        width: 110px;
        margin-left: 8px;
        font-weight: 600;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .compact-status-group {
        width: 100%;
        justify-content: space-between;
    }

    /* Unified Status Colors */
    .status-booked { background-color: #edf2f7 !important; color: #4a5568 !important; }
    .status-ready { background-color: #c6f6d5 !important; color: #22543d !important; }
    .status-blocked { background-color: #fed7d7 !important; color: #822727 !important; }
    .status-warning { background-color: #feebc8 !important; color: #744210 !important; }
    .status-pending_approval { background-color: #e9d8fd !important; color: #44337a !important; }
    .status-completed { background-color: #bee3f8 !important; color: #2c5282 !important; }

    .eligibility-result-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .status-select {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .status-select:hover {
        border-color: #cbd5e0;
    }

    .actions-cell .btn {
        height: 32px;
        display: inline-flex;
        align-items: center;
        padding: 0 12px;
        font-weight: 600;
        font-size: 12px;
        white-space: nowrap;
    }
    
    .no-data {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        color: #a0aec0;
    }
    
    .no-data i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
    }
    
    .table-info {
        background-color: #ebf8ff;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-right: 4px solid #3182ce;
        color: #2b6cb0;
        font-weight: 500;
        min-width: 1240px;
        display: block;
    }

    .toast-container .alert {
        border-right: 4px solid rgba(0,0,0,0.1);
        pointer-events: none;
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .analysis-name { width: 90px; }
        .mini-status-select { width: 75px; }
    }

    @media (max-width: 992px) {
        .analysis-name {
            width: 100px; /* Reset width for better readability when scrolling */
        }
        .mini-status-select {
            width: 80px;
        }
        .data-table th, .data-table td {
            padding: 12px 10px;
            font-size: 13px;
        }
    }

    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            text-align: center;
        }
        .section-header h2 {
            font-size: 1.4rem;
            margin-bottom: 10px;
        }
        .analysis-status-item {
            padding: 4px;
        }
        .analysis-name {
            width: auto;
            max-width: 120px;
            font-size: 11px;
        }
    }

    /* Force table width to prevent compression */
    .data-table {
        min-width: 900px;
    }

    .table-container {
        overflow: visible !important; /* Allow the inner responsive div to handle scrolling */
        box-shadow: none;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─────────────── QR SCANNER LOGIC ───────────────
    const qrModal = document.getElementById('qrScannerModal');
    let html5QrCode = null;

    qrModal.addEventListener('shown.bs.modal', function () {
        html5QrCode = new Html5Qrcode("qr-reader");
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            console.log(`Scan Result: ${decodedText}`);
            showToast(`تم التعرف على الكود: ${decodedText}`, 'info');
            
            let targetUrl = null;
            
            // 1. If it's a full URL
            if (decodedText.startsWith('http')) {
                if (decodedText.includes(window.location.host)) {
                    targetUrl = decodedText;
                } else {
                    showToast('تنبيه: الكود لا يخص هذا النظام', 'warning');
                    return;
                }
            } 
            // 2. If it's the legacy VIST- format
            else if (decodedText.startsWith('VIST-')) {
                const parts = decodedText.split('|');
                const idPart = parts[0].replace('VIST-', '').trim();
                if (idPart) {
                    targetUrl = `${window.location.pathname}?rid=${idPart}&t=${Date.now()}`;
                }
            }
            // 3. Fallback: treat as raw numeric search
            else if (!isNaN(decodedText.trim()) && decodedText.trim() !== "") {
                targetUrl = `${window.location.pathname}?search=${decodedText.trim()}&t=${Date.now()}`;
            }

            if (targetUrl) {
                html5QrCode.stop().then(() => {
                    showToast('تم تمييز الرمز، جاري الانتقال...', 'success');
                    window.location.href = targetUrl;
                }).catch(err => console.error(err));
            } else {
                showToast('عذراً، لم يتم التعرف على صيغة الكود', 'danger');
            }
        };

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
            .catch(err => {
                console.error(err);
                let errorMsg = '{{ __("messages.camera_permission_required") }}';
                
                // Add specific HTTPS warning if not in secure context
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                    errorMsg = '{{ app()->getLocale() == "ar" ? "تنبيه: الكاميرا تتطلب رابط HTTPS آمن للعمل (أنت تستخدم حالياً HTTP)" : "Alerte : La caméra nécessite une connexion HTTPS sécurisée (vous utilisez actuellement HTTP)" }}';
                }
                
                showToast(errorMsg, 'danger');
            });
    });

    qrModal.addEventListener('hidden.bs.modal', function () {
        if (html5QrCode) {
            html5QrCode.stop().catch(err => console.error(err));
        }
    });

    // ─────────────── RE-IMPLEMENTED EXISTING LOGIC ───────────────
    const statusForms = document.querySelectorAll('.ajax-status-form');
    statusForms.forEach(form => {
        const select = form.querySelector('select');
        select.addEventListener('change', function() {
            const formData = new FormData(form);
            select.disabled = true;
            select.style.opacity = '0.5';
            fetch(form.action, {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                select.disabled = false; select.style.opacity = '1';
                if (data.success) {
                    const baseClass = select.classList.contains('mini-status-select') ? 'mini-status-select' : 'status-select';
                    select.className = `${baseClass} status-${data.status}`;
                    showToast(data.message || 'Updated successfully', 'success');
                    const row = select.closest('tr');
                    const mainBadge = row.querySelector('.status-badge');
                    if (mainBadge && select.classList.contains('status-select')) {
                        mainBadge.className = `status-badge status-${data.status}`;
                    }
                } else { showToast(data.message || 'Error', 'danger'); }
            }).catch(error => {
                select.disabled = false; select.style.opacity = '1';
                showToast('Server error', 'danger');
            });
        });
    });

    const referralSelects = document.querySelectorAll('.ajax-referral-update');
    referralSelects.forEach(select => {
        select.addEventListener('change', function() {
            const doctorId = this.value;
            const url = this.dataset.url;
            this.disabled = true;
            fetch(url, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ doctor_id: doctorId })
            }).then(response => response.json()).then(data => {
                this.disabled = false;
                if (data.success) showToast(data.message, 'success');
                else showToast(data.message || 'Error', 'danger');
            }).catch(error => { this.disabled = false; showToast('Connection error', 'danger'); });
        });
    });

    const notifyBtns = document.querySelectorAll('.notify-btn');
    notifyBtns.forEach(btn => {
        btn.addEventListener('click', function(e) { e.preventDefault();
            const url = this.dataset.url; const target = this.dataset.target; btn.classList.add('disabled');
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ target: target })
            }).then(response => response.json()).then(data => {
                btn.classList.remove('disabled'); showToast(data.message, data.success ? 'success' : 'danger');
            }).catch(error => { btn.classList.remove('disabled'); showToast('Error', 'danger'); });
        });
    });

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if(!container) return;
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} shadow-sm border-0 mb-2 py-2 px-3 fade show`;
        toast.style.minWidth = '250px';
        toast.innerHTML = `<div class="d-flex align-items-center"><i class="fas ${type==='success'?'fa-check-circle':'fa-exclamation-circle'} me-2"></i><span class="small">${message}</span></div>`;
        container.appendChild(toast);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 200); }, 3000);
    }
});
</script>
@endsection
