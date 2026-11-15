@extends('Adminstration.layout')

@section('title', __('messages.reservation_details') ?? 'Reservation Details')

@section('content')
<div class="section-header d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-circle"></i> {{ __('messages.reservation_details') ?? 'Patient File' }}: {{ $reservation->patient->name }}</h2>
    <a href="{{ route('reservations') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') ?? 'Back to List' }}
    </a>
</div>

<div id="toast-container" class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;"></div>

<div class="row">
    <!-- Patient & Appointment Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-lg mb-4">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <img src="{{ $reservation->barcode }}" width="150" class="img-thumbnail border-0 shadow-sm" alt="QR Code">
                </div>
                <h4 class="fw-bold mb-1">{{ $reservation->patient->name }}</h4>
                <p class="text-muted small mb-3"><i class="fas fa-phone-alt me-1"></i> {{ $reservation->patient->phone }}</p>
                
                <hr class="my-3 opacity-25">
                
                <div class="text-start">
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">{{ __('messages.date') }}:</span>
                        <span class="fw-bold">{{ $reservation->analysis_date }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted small">{{ __('messages.time') }}:</span>
                        <span class="fw-bold">{{ $reservation->time }}</span>
                    </div>
                    <div class="mb-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">{{ __('messages.status') }}:</span>
                        <span class="status-badge status-{{ $reservation->status }}">
                            @if($reservation->status == 'booked') {{ __('messages.booked') }}
                            @elseif($reservation->status == 'ready') {{ __('messages.ready') }}
                            @elseif($reservation->status == 'blocked') {{ __('messages.blocked') }}
                            @elseif($reservation->status == 'warning') {{ __('messages.warning') }}
                            @elseif($reservation->status == 'pending_approval') {{ __('messages.pending_approval') }}
                            @elseif($reservation->status == 'completed') {{ __('messages.completed') }}
                            @else {{ $reservation->status }} @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-tools me-2 text-primary"></i> {{ __('messages.quick_actions') ?? 'Actions' }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <form class="ajax-status-form" action="{{ route('admin.bookings.update', $reservation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <label class="small text-muted mb-1">{{ __('messages.change_main_status') ?? 'Main Status' }}</label>
                        <select name="status" class="form-select status-select status-{{ $reservation->status }} mb-3">
                            <option value="booked" {{ $reservation->status == 'booked' ? 'selected' : '' }}>{{ __('messages.booked') }}</option>
                            <option value="ready" {{ $reservation->status == 'ready' ? 'selected' : '' }}>{{ __('messages.ready') }}</option>
                            <option value="blocked" {{ $reservation->status == 'blocked' ? 'selected' : '' }}>{{ __('messages.blocked') }}</option>
                            <option value="warning" {{ $reservation->status == 'warning' ? 'selected' : '' }}>{{ __('messages.warning') }}</option>
                            <option value="pending_approval" {{ $reservation->status == 'pending_approval' ? 'selected' : '' }}>{{ __('messages.pending_approval') }}</option>
                            <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                        </select>
                    </form>

                    <a href="{{ route('admin.bookings.full-eligibility.form', $reservation->id) }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-stethoscope me-2"></i> {{ __('messages.eligibility_check') }}
                    </a>
                    
                    @php
                        $diagStatuses = $reservation->reservationAnalyses->pluck('status');
                        $diagHasBlocked = $diagStatuses->contains('blocked');
                        $diagHasWarning = $diagStatuses->contains('warning');
                        $diagIsChecked = $diagStatuses->contains('ready') || $diagHasBlocked || $diagHasWarning;
                    @endphp

                    @if($diagIsChecked)
                        <a href="{{ route('admin.bookings.eligibility.results', $reservation->id) }}" class="btn btn-outline-primary text-start">
                            <i class="fas {{ $diagHasBlocked ? 'fa-times-circle' : ($diagHasWarning ? 'fa-exclamation-triangle' : 'fa-check-circle') }} me-2"></i>
                            {{ __('messages.eligibility_results') }}
                        </a>
                    @endif

                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-info text-start dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell me-2"></i> {{ __('messages.notify') ?? 'Send Notification' }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item notify-btn" href="#" data-url="{{ route('admin.bookings.notify', $reservation->id) }}" data-target="patient"><i class="fas fa-user me-2"></i> {{ __('messages.notify_patient') ?? 'To Patient' }}</a></li>
                            <li><a class="dropdown-item notify-btn" href="#" data-url="{{ route('admin.bookings.notify', $reservation->id) }}" data-target="doctor"><i class="fas fa-user-md me-2"></i> {{ __('messages.notify_doctor') ?? 'To Physician' }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item notify-btn" href="#" data-url="{{ route('admin.bookings.notify', $reservation->id) }}" data-target="both"><i class="fas fa-users me-2"></i> {{ __('messages.notify_both') ?? 'To Both' }}</a></li>
                        </ul>
                    </div>

                    <a href="{{ route('reservation.label', $reservation->id) }}" target="_blank" class="btn btn-outline-success text-start">
                        <i class="fas fa-tag me-2"></i> {{ __('messages.print_tube_label') ?? 'Print Tube Label' }}
                    </a>
                    
                    <a href="{{ route('admin.bookings.results.form', $reservation->id) }}" class="btn btn-primary text-start mt-2 py-2">
                        <i class="fas fa-file-medical me-2"></i> {{ __('messages.fill_results') ?? 'Enter Lab Results' }}
                    </a>

                    <a href="{{ route('report.view', $reservation->id) }}" target="_blank" class="btn btn-outline-secondary text-start mt-2">
                        <i class="fas fa-print me-2"></i> {{ __('messages.download_preparation_pdf') ?? 'Print Preparation' }}
                    </a>

                    <button data-bs-toggle="modal" data-bs-target="#mobileAccessModal" class="btn btn-outline-dark text-start mt-2">
                        <i class="fas fa-mobile-alt me-2 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'وصول التطبيق' : 'Mobile App Access' }}
                        @if($reservation->patient->password)
                            <span class="badge bg-success ms-1 small" style="font-size: 8px;">ON</span>
                        @endif
                    </button>

                    <button id="generate-ai-btn" class="btn btn-dark text-start mt-3 py-2 shadow-sm border-0" style="background: linear-gradient(135deg, #2D3436 0%, #000000 100%);">
                        <i class="fas fa-robot me-2 text-info"></i> {{ __('messages.generate_ai_insight') ?? 'Smart AI Insight' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- AI Insight Card -->
        <div id="ai-insight-card" class="card shadow-sm border-0 rounded-lg mt-4 {{ $reservation->ai_insight ? '' : 'd-none' }}" style="background: #f8fafc; border-left: 4px solid #000 !important;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-brain me-2 text-primary"></i> {{ __('messages.ai_clinical_report') ?? 'AI Clinical Insight' }}</h5>
                <span class="badge bg-dark text-white shadow-sm" style="font-size: 10px;">{{ __('messages.preliminary') ?? 'BETA' }}</span>
            </div>
            <div class="card-body p-4">
                <div id="ai-insight-content" class="markdown-content small">
                    {!! nl2br(e($reservation->ai_insight)) !!}
                </div>
                <hr class="my-3 opacity-25">
                <div class="text-muted" style="font-size: 10px; line-height: 1.4;">
                    <i class="fas fa-exclamation-triangle me-1"></i> 
                    {{ __('messages.ai_disclaimer') ?? 'This is a preliminary analysis generated by AI. It is NOT a final diagnosis and MUST be reviewed and validated by a qualified medical professional before clinical use.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Analyses List -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-microscope me-2 text-primary"></i> {{ __('messages.requested_analyses') ?? 'Analyses & Tests' }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.analysis') ?? 'Test Name' }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.physician') ?? 'Assigned Doctor' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservation->reservationAnalyses as $resAnalysis)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $resAnalysis->analyse->name }}</div>
                                    <div class="small text-muted">{{ $resAnalysis->analyse->code ?? '' }}</div>
                                </td>
                                <td>
                                    <form class="ajax-status-form" action="{{ route('admin.bookings.analysis.status.update', $resAnalysis->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="mini-status-select status-{{ $resAnalysis->status }}">
                                            <option value="booked" {{ $resAnalysis->status == 'booked' ? 'selected' : '' }}>{{ __('messages.booked') }}</option>
                                            <option value="ready" {{ $resAnalysis->status == 'ready' ? 'selected' : '' }}>{{ __('messages.ready') }}</option>
                                            <option value="blocked" {{ $resAnalysis->status == 'blocked' ? 'selected' : '' }}>{{ __('messages.blocked') }}</option>
                                            <option value="warning" {{ $resAnalysis->status == 'warning' ? 'selected' : '' }}>{{ __('messages.warning') }}</option>
                                            <option value="pending_approval" {{ $resAnalysis->status == 'pending_approval' ? 'selected' : '' }}>{{ __('messages.pending_approval') }}</option>
                                            <option value="completed" {{ $resAnalysis->status == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($loop->first)
                                        <div class="text-muted small">
                                            <i class="fas fa-user-md me-1"></i>
                                            <select class="referral-select-custom ajax-referral-update" data-url="{{ route('admin.bookings.referral.update', $reservation->id) }}" style="border:none; background:transparent; font-size:12px; cursor:pointer;">
                                                <option value="">— {{ __('messages.referred_by') ?? 'Assign Physician' }} —</option>
                                                @foreach($doctors as $doc)
                                                    <option value="{{ $doc->id }}" {{ $reservation->doctor_id == $doc->id ? 'selected' : '' }}>
                                                        {{ $doc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <span class="text-muted small opacity-50">— Same as above —</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared toast function
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} shadow-sm border-0 mb-2 py-2 px-3 fade show`;
        toast.style.minWidth = '250px';
        toast.style.pointerEvents = 'none';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                <span class="small">${message}</span>
            </div>
        `;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 200);
        }, 3000);
    }

    // Status AJAX
    const statusForms = document.querySelectorAll('.ajax-status-form');
    statusForms.forEach(form => {
        const select = form.querySelector('select');
        select.addEventListener('change', function() {
            const formData = new FormData(form);
            select.disabled = true;
            select.style.opacity = '0.5';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                select.disabled = false;
                select.style.opacity = '1';
                if (data.success) {
                    const baseClass = select.classList.contains('mini-status-select') ? 'mini-status-select' : 'form-select status-select';
                    select.className = `${baseClass} status-${data.status}`;
                    
                    // Specific to details page: if it's main status, update the badge in info card
                    if (select.closest('.card-body')?.querySelector('.status-badge')) {
                        const badge = select.closest('.row').querySelector('.status-badge');
                        badge.className = `status-badge status-${data.status}`;
                    }
                    
                    showToast(data.message || 'Updated successfully', 'success');
                } else {
                    showToast(data.message || 'Error', 'danger');
                }
            })
            .catch(error => {
                select.disabled = false;
                select.style.opacity = '1';
                showToast('Server connection error', 'danger');
            });
        });
    });

    // Physician Update
    const referralSelects = document.querySelectorAll('.ajax-referral-update');
    referralSelects.forEach(select => {
        select.addEventListener('change', function() {
            const doctorId = this.value;
            const url = this.dataset.url;
            this.disabled = true;
            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ doctor_id: doctorId })
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Error', 'danger');
                }
            })
            .catch(error => {
                this.disabled = false;
                showToast('Connection error', 'danger');
            });
        });
    });

    // Notifications
    const notifyBtns = document.querySelectorAll('.notify-btn');
    notifyBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const target = this.dataset.target;
            btn.classList.add('disabled');
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ target: target })
            })
            .then(response => response.json())
            .then(data => {
                btn.classList.remove('disabled');
                showToast(data.message, data.success ? 'success' : 'danger');
            })
            .catch(error => {
                btn.classList.remove('disabled');
                showToast('Error', 'danger');
            });
        });
    });

    // AI Insight Generation
    const aiBtn = document.getElementById('generate-ai-btn');
    const aiCard = document.getElementById('ai-insight-card');
    const aiContent = document.getElementById('ai-insight-content');

    if (aiBtn) {
        aiBtn.addEventListener('click', function() {
            if (!confirm('{{ __("messages.confirm_ai_analysis") ?? "Do you want to run Smart AI Clinical Analysis on these results?" }}')) return;

            aiBtn.disabled = true;
            aiBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> {{ __("messages.analyzing") ?? "Analyzing..." }}';
            
            fetch('{{ route("admin.bookings.ai-insight", $reservation->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                aiBtn.disabled = false;
                aiBtn.innerHTML = '<i class="fas fa-robot me-2 text-info"></i> {{ __("messages.generate_ai_insight") ?? "Smart AI Insight" }}';
                
                if (data.success) {
                    aiCard.classList.remove('d-none');
                    aiContent.innerHTML = data.insight.replace(/\n/g, '<br>');
                    showToast('AI Clinical Insight generated successfully', 'success');
                    
                    // Smooth scroll to the card
                    aiCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    showToast(data.message || 'AI interpretation failed', 'danger');
                }
            })
            .catch(error => {
                aiBtn.disabled = false;
                aiBtn.innerHTML = '<i class="fas fa-robot me-2 text-info"></i> {{ __("messages.generate_ai_insight") ?? "Smart AI Insight" }}';
                showToast('AI Connection error', 'danger');
            });
        });
    }
});

</script>

<style>
    .rounded-lg { border-radius: 12px !important; }
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-select {
        height: 45px;
        font-weight: 600;
        border-radius: 8px;
    }
    .mini-status-select {
        border-radius: 6px;
        font-size: 11px;
        padding: 4px 8px;
        font-weight: 600;
        width: 100%;
        max-width: 130px;
    }
    /* Status Colors */
    .status-booked { background-color: #edf2f7 !important; color: #4a5568 !important; border: 1px solid #cbd5e0; }
    .status-ready { background-color: #c6f6d5 !important; color: #22543d !important; border: 1px solid #9ae6b4; }
    .status-blocked { background-color: #fed7d7 !important; color: #822727 !important; border: 1px solid #feb2b2; }
    .status-warning { background-color: #feebc8 !important; color: #744210 !important; border: 1px solid #fbd38d; }
    .status-pending_approval { background-color: #e9d8fd !important; color: #44337a !important; border: 1px solid #d6bcfa; }
    .status-completed { background-color: #bee3f8 !important; color: #2c5282 !important; border: 1px solid #90cdf4; }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const whatsappBtn = document.getElementById('whatsapp-info-btn');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', function() {
            const modal = document.getElementById('mobileAccessModal');
            const email = modal.querySelector('input[name="email"]').value;
            const password = modal.querySelector('input[name="password"]').value;
            const phone = "{{ $reservation->patient->phone }}";
            const patientName = "{{ $reservation->patient->name }}";

            if (!email || !password) {
                alert('يرجى كتابة الإيميل وكلمة المرور أولاً.');
                return;
            }

            const text = `أهلاً بك سيد/ة ${patientName}،\n\nبيانات دخولك لتطبيق مختبر Labo.dz هي:\n\nالبريد: ${email}\nكلمة المرور: ${password}\n\nيمكنك تحميل التطبيق ومتابعة نتائجك مباشرة.\nرابط الموقع: {{ config('app.url') }}`;
            const encodedText = encodeURIComponent(text);
            const whatsappUrl = `https://wa.me/${phone}?text=${encodedText}`;
            window.open(whatsappUrl, '_blank');
        });
    }
});
</script>
@endsection

{{-- Mobile Access Modal --}}
<div class="modal fade" id="mobileAccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-mobile-alt me-2"></i> إدارة وصول التطبيق</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.patients.set-password', $reservation->patient->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">البريد الإلكتروني (اسم المستخدم):</label>
                        <input type="email" name="email" class="form-control" value="{{ $reservation->patient->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">كلمة المرور الجديدة:</label>
                        <input type="password" name="password" class="form-control" placeholder="6 أحرف على الأقل" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تأكيد كلمة المرور:</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    
                    @if($reservation->patient->password)
                        <div class="alert alert-success d-flex align-items-center mb-0">
                            <i class="fas fa-check-circle me-3 h4 mb-0"></i>
                            <div>هذا المريض لديه وصول مفعل للتطبيق حالياً.</div>
                        </div>
                    @else
                        <div class="alert alert-warning d-flex align-items-center mb-0">
                            <i class="fas fa-exclamation-triangle me-3 h4 mb-0"></i>
                            <div>هذا المريض ليس لديه وصول للتطبيق بعد.</div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    @if($reservation->patient->password)
                        <button type="button" class="btn btn-outline-danger me-auto" onclick="confirm('إلغاء الوصول؟') ? document.getElementById('revoke-form').submit() : ''">إلغاء الوصول</button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" id="whatsapp-info-btn" class="btn btn-outline-success">
                        <i class="fab fa-whatsapp me-1"></i> إرسال عبر واتساب
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ البيانات ✅</button>
                </div>
            </form>
            @if($reservation->patient->password)
                <form id="revoke-form" action="{{ route('admin.patients.revoke-password', $reservation->patient->id) }}" method="POST" style="display:none;">
                    @csrf @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>
