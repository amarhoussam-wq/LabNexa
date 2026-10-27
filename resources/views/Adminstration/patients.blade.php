@extends('Adminstration.layout')

@section('title', __('messages.patients_management') . ' - ' . __('messages.lab_name'))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">📱 {{ __('messages.app_access_management') }}</h3>
            <p class="text-muted small">{{ __('messages.app_access_management_desc') }}</p>
        </div>
    </div>

    {{-- Success / Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.patients.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_placeholder_patient') }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">{{ __('messages.search') }}</button>
                @if(request('search'))
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.patient') }}</th>
                            <th>{{ __('messages.username') }}</th>
                            <th>{{ __('messages.phone_number') }}</th>
                            <th class="text-center">{{ __('messages.total_bookings') }}</th>
                            <th class="text-center">{{ __('messages.app_status') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        <tr>
                            <td class="text-muted small">{{ $patient->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:38px;height:38px;font-size:14px;flex-shrink:0;">
                                        {{ mb_substr($patient->name, 0, 1) }}
                                    </div>
                                    <span class="fw-semibold">{{ $patient->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $patient->email ?? '—' }}</td>
                            <td class="text-muted small">{{ $patient->phone ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $patient->reservations_count }}</span>
                            </td>
                            <td class="text-center">
                                @if($patient->password)
                                    <span class="badge bg-success">✅ {{ __('messages.completed') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('messages.pending_approval') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#passwordModal{{ $patient->id }}">
                                    <i class="fas fa-key"></i> {{ __('messages.set_password') }}
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#profileModal{{ $patient->id }}">
                                    <i class="fas fa-user-edit"></i> {{ __('messages.update_profile') }}
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#mergeModal{{ $patient->id }}">
                                    <i class="fas fa-object-group"></i> {{ __('messages.merge_patient') }}
                                </button>

                                {{-- Merge Patient Modal --}}
                                <div class="modal fade" id="mergeModal{{ $patient->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content text-start">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">🔗 {{ __('messages.merge_patient') }} - {{ $patient->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.patients.merge') }}">
                                                @csrf
                                                <input type="hidden" name="source_id" value="{{ $patient->id }}">
                                                <div class="modal-body">
                                                    <div class="alert alert-danger small py-2">
                                                        ⚠️ {!! __('messages.merge_patient_warning') !!}
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.choose_target_patient') }}</label>
                                                        <select name="target_id" class="form-select" required>
                                                            <option value="">-- {{ __('messages.select_gender') }} --</option>
                                                            @foreach($patients as $targetPatient)
                                                                @if($targetPatient->id != $patient->id)
                                                                    <option value="{{ $targetPatient->id }}">#{{ $targetPatient->id }} - {{ $targetPatient->name }} ({{ $targetPatient->phone }})</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.confirm_merge_delete') }}')">{{ __('messages.confirm_merge_delete') }} ⚠️</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Update Profile Modal --}}
                                <div class="modal fade" id="profileModal{{ $patient->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary text-white">
                                                <h5 class="modal-title">👤 {{ __('messages.update_profile') }} - {{ $patient->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.patients.update-profile', $patient->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.username') }}:</label>
                                                        <input type="text" name="email" class="form-control" value="{{ $patient->email }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.phone_number') }}:</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $patient->phone }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }} ✅</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Password Modal --}}
                                <div class="modal fade" id="passwordModal{{ $patient->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">🔐 {{ __('messages.set_password') }} - {{ $patient->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <form method="POST" action="{{ route('admin.patients.set-password', $patient->id) }}" id="setPasswordForm{{ $patient->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.username') }}: <span class="text-danger">*</span></label>
                                                        <input type="text" name="email" class="form-control" value="{{ $patient->email }}" required placeholder="admin أو email">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.password') }} <span class="text-danger">*</span></label>
                                                        <input type="password" name="password" class="form-control" placeholder="6 أحرف أو أكثر" required minlength="6">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.confirm') }} {{ __('messages.password') }} <span class="text-danger">*</span></label>
                                                        <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('messages.save') }}" required>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                @if($patient->password)
                                                <form method="POST" action="{{ route('admin.patients.revoke-password', $patient->id) }}" class="me-auto">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.revoke_access_confirm') }}')">
                                                        {{ __('messages.revoke_access') }}
                                                    </button>
                                                </form>
                                                @endif
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                                                <button type="button" class="btn btn-outline-success whatsapp-btn" 
                                                    data-patient-id="{{ $patient->id }}"
                                                    data-phone="{{ $patient->phone }}">
                                                    <i class="fab fa-whatsapp"></i> {{ __('messages.send_via_whatsapp') }}
                                                </button>
                                                <button type="submit" form="setPasswordForm{{ $patient->id }}" class="btn btn-primary">{{ __('messages.save') }} ✅</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                {{ __('messages.no_patients_found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($patients->hasPages())
        <div class="card-footer">
            {{ $patients->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // WhatsApp notification logic
    document.querySelectorAll('.whatsapp-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            const email = modal.querySelector('input[name="email"]').value;
            const password = modal.querySelector('input[name="password"]').value;
            const phone = this.getAttribute('data-phone');

            if (!email || !password) {
                alert("{{ __('messages.whatsapp_error_credentials') }}");
                return;
            }

            let text = `{!! __('messages.whatsapp_message_template') !!}`;
            text = text.replace(':username', email).replace(':password', password);
            
            const encodedText = encodeURIComponent(text);
            const whatsappUrl = `https://wa.me/${phone}?text=${encodedText}`;
            window.open(whatsappUrl, '_blank');
        });
    });
});
</script>
@endsection
