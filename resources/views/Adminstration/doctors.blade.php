@extends('Adminstration.layout')

@section('title', __('messages.doctors_management') . ' - ' . __('messages.lab_name'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">👨‍⚕️ {{ __('messages.doctors_management') }}</h3>
            <p class="text-muted small">{{ __('messages.doctors_management_desc') }}</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
            <i class="fas fa-plus"></i> {{ __('messages.add_new_doctor') }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.doctors.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_placeholder_doctor') }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">{{ __('messages.search') }}</button>
                @if(request('search'))
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-start">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.doctor') }}</th>
                            <th>{{ __('messages.username') }}</th>
                            <th>{{ __('messages.specialty') }}</th>
                            <th>{{ __('messages.phone_number') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doctors as $doctor)
                        <tr>
                            <td class="text-muted small">{{ $doctor->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white fw-bold" style="width:38px;height:38px;font-size:14px;flex-shrink:0;">
                                        {{ mb_substr($doctor->name, 0, 1) }}
                                    </div>
                                    <span class="fw-semibold">{{ $doctor->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $doctor->email }}</td>
                            <td class="text-muted small">{{ $doctor->specialty ?? '—' }}</td>
                            <td class="text-muted small">{{ $doctor->phone ?? '—' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDoctorModal{{ $doctor->id }}">
                                    <i class="fas fa-edit"></i> {{ __('messages.update_profile') }}
                                </button>
                                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.confirm_delete_account') }}')">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>

                                {{-- Edit Doctor Modal --}}
                                <div class="modal fade" id="editDoctorModal{{ $doctor->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">📝 {{ __('messages.update_profile') }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.doctors.update', $doctor->id) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.full_name') }}:</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $doctor->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.username') }}:</label>
                                                        <input type="text" name="email" class="form-control" value="{{ $doctor->email }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.new_password_placeholder') }}:</label>
                                                        <input type="password" name="password" class="form-control" minlength="6">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.specialty') }}:</label>
                                                        <input type="text" name="specialty" class="form-control" value="{{ $doctor->specialty }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">{{ __('messages.phone_number') }}:</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $doctor->phone }}">
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">{{ __('messages.no_doctors_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($doctors->hasPages())
        <div class="card-footer">
            {{ $doctors->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Add Doctor Modal --}}
<div class="modal fade" id="addDoctorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-start">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">➕ {{ __('messages.add_new_doctor') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.doctors.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.full_name') }}:</label>
                        <input type="text" name="name" class="form-control" required placeholder="د. فلان الفلاني">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.username') }}:</label>
                        <input type="text" name="email" class="form-control" required placeholder="admin أو doctor_name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.password') }}:</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="******">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.specialty') }}:</label>
                        <input type="text" name="specialty" class="form-control" placeholder="تحاليل طبية">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.phone_number') }}:</label>
                        <input type="text" name="phone" class="form-control" placeholder="0500000000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('messages.add_doctor') }} ✅</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
