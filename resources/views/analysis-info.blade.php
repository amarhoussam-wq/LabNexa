<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.analysis_info_title') }} - {{ __('messages.lab_name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Global Dynamic Background -->
    <div class="global-bg">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Navigation -->
    <nav>
        <ul class="nav-links">
            <li><a href="/"><i class="fas fa-home"></i> {{ __('messages.home') }}</a></li>
            <li><a href="/#analysis"><i class="fas fa-flask"></i> {{ __('messages.analysis') }}</a></li>
            <li><a href="/#booking"><i class="fas fa-calendar-check"></i> {{ __('messages.booking') }}</a></li>
            <li><a href="/#contact"><i class="fas fa-envelope"></i> {{ __('messages.contact') }}</a></li>
        {{-- Floating Language Toggle Pill --}}
        <div class="position-fixed top-0 end-0 p-4" style="z-index: 1050;">
            <div class="d-flex bg-white rounded-pill p-1 border shadow" style="min-width: 120px;">
                <a href="{{ route('lang.switch', 'ar') }}" 
                   class="flex-fill text-center rounded-pill px-3 py-1 text-decoration-none {{ app()->getLocale() == 'ar' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-muted' }}"
                   style="font-size: 13px; transition: all 0.3s;">
                    العربية
                </a>
                <a href="{{ route('lang.switch', 'fr') }}" 
                   class="flex-fill text-center rounded-pill px-3 py-1 text-decoration-none {{ app()->getLocale() == 'fr' ? 'bg-primary text-white fw-bold shadow-sm' : 'text-muted' }}"
                   style="font-size: 13px; transition: all 0.3s;">
                    FR
                </a>
            </div>
        </div>
    </nav>

    <!-- Analysis Info Section -->
    <section class="section">
        <div class="container animate-fade-in">
            <h1><i class="fas fa-flask"></i> {{ __('messages.analysis_info_title') ?? 'معلومات التحاليل ونصائح الإعداد' }}</h1>
            <p>{{ __('messages.analysis_info_subtitle') ?? 'فيما يلي معلومات مفصلة حول التحاليل المختلفة ونصائح الإعداد لكل منها' }}</p>

            <div class="search-input-wrapper mb-4" style="max-width: 600px;">
                <i class="fas fa-search"></i>
                <input type="text" id="tableSearch" class="form-control" placeholder="{{ __('messages.search_analysis_placeholder') ?? 'ابحث عن تحليل باسمه أو كوده...' }}">
            </div>

            <div class="filter-tabs" id="statusFilters">
                <button class="filter-tab active" data-filter="all">{{ __('messages.all') ?? 'الكل' }}</button>
                <button class="filter-tab" data-filter="available">{{ __('messages.available') ?? 'متوفر' }}</button>
                <button class="filter-tab" data-filter="unavailable">{{ __('messages.unavailable') ?? 'غير متوفر' }}</button>
            </div>

            <div class="table-responsive">
                <table class="modern-table" id="analysisTable">
                    <thead>
                        <tr>
                            <th>{{ __('messages.analysis_name') ?? 'التحليل' }}</th>
                            <th>{{ __('messages.status') ?? 'الحالة' }}</th>
                            <th>{{ __('messages.duration') ?? 'المدة' }}</th>
                            <th>{{ __('messages.price') ?? 'السعر' }}</th>
                            <th>{{ __('messages.actions') ?? 'إجراءات' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analyses as $analyse)
                        <tr class="analysis-row" data-status="{{ $analyse->availability ? 'available' : 'unavailable' }}">
                            <td>
                                <div class="fw-bold">{{ $analyse->name }}</div>
                                <small class="text-muted">{{ $analyse->code }}</small>
                            </td>
                            <td>
                                @if($analyse->availability)
                                <span class="status-badge available-status">{{ __('messages.available') ?? 'متوفر' }}</span>
                                @else
                                <span class="status-badge unavailable-status">{{ __('messages.unavailable') ?? 'غير متوفر' }}</span>
                                @endif
                            </td>
                            <td><i class="fas fa-clock text-muted"></i> {{ $analyse->duration }}</td>
                            <td><span class="price-tag">{{ number_format($analyse->price, 2) }}</span> <small>دج</small></td>
                            <td>
                                <button class="action-btn view-details" 
                                        data-name="{{ $analyse->name }}"
                                        data-prep="{{ $analyse->preparation_instructions ?? 'لا توجد نصائح إعدادية' }}"
                                        data-desc="{{ $analyse->description ?? 'لا توجد معلومات إضافية' }}"
                                        data-video="{{ $analyse->normalized_video_url ?? '' }}">
                                    <i class="fas fa-info-circle"></i> {{ __('messages.details') ?? 'التفاصيل' }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ __('messages.no_analyses_found') ?? 'لا توجد تحاليل متاحة حالياً' }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Details Modal (Using simpler overlay for speed) -->
    <div id="detailsModal" class="modal-overlay" style="display:none;">
        <div class="prep-modal animate-fade-in">
            <div class="prep-modal-header">
                <h2 id="modalTitle"></h2>
            </div>
            <div class="prep-modal-body">
                <div class="prep-item">
                    <h4><i class="fas fa-info-circle"></i> {{ __('messages.description') ?? 'الوصف' }}</h4>
                    <p id="modalDesc"></p>
                </div>
                <div class="prep-item">
                    <h4><i class="fas fa-clipboard-check"></i> {{ __('messages.preparation_instructions') ?? 'تعليمات التحضير' }}</h4>
                    <p id="modalPrep"></p>
                </div>
                <div class="prep-item" id="videoContainer" style="display: none;">
                    <h4><i class="fab fa-youtube text-danger"></i> {{ __('messages.instruction_video') != 'messages.instruction_video' ? __('messages.instruction_video') : 'فيديو توضيحي للتحضير' }}</h4>
                    <div class="video-wrapper mt-3" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: #000;">
                        <iframe id="modalVideoIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
                    </div>
                </div>
            </div>
            <div class="prep-modal-footer">
                <button class="close-prep-btn" id="closeDetailsModal">{{ __('messages.close') ?? 'إغلاق' }}</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search Functionality
            const searchInput = document.getElementById('tableSearch');
            const tableRows = document.querySelectorAll('.analysis-row');
            const statusFilters = document.querySelectorAll('.filter-tab');
            let currentFilter = 'all';

            function filterTable() {
                const query = searchInput.value.toLowerCase();
                tableRows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    const status = row.getAttribute('data-status');
                    const matchesSearch = text.includes(query);
                    const matchesFilter = currentFilter === 'all' || status === currentFilter;
                    
                    row.style.display = (matchesSearch && matchesFilter) ? 'table-row' : 'none';
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }

            statusFilters.forEach(tab => {
                tab.addEventListener('click', function() {
                    statusFilters.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter');
                    filterTable();
                });
            });

            // Modal Logic
            const modal = document.getElementById('detailsModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalPrep = document.getElementById('modalPrep');
            const modalDesc = document.getElementById('modalDesc');
            const videoContainer = document.getElementById('videoContainer');
            const modalVideoIframe = document.getElementById('modalVideoIframe');
            const closeBtn = document.getElementById('closeDetailsModal');

            document.querySelectorAll('.view-details').forEach(btn => {
                btn.addEventListener('click', function() {
                    modalTitle.textContent = this.getAttribute('data-name');
                    modalPrep.textContent = this.getAttribute('data-prep');
                    modalDesc.textContent = this.getAttribute('data-desc');
                    
                    const videoUrl = this.getAttribute('data-video');
                    if (videoUrl && videoUrl.trim() !== '') {
                        modalVideoIframe.src = videoUrl;
                        videoContainer.style.display = 'block';
                    } else {
                        modalVideoIframe.src = '';
                        videoContainer.style.display = 'none';
                    }
                    
                    modal.style.display = 'flex';
                });
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    modal.style.display = 'none';
                    modalVideoIframe.src = ''; // Stop video when closing
                });
            }
            
            modal.addEventListener('click', (e) => {
                if(e.target === modal) {
                    modal.style.display = 'none';
                    modalVideoIframe.src = ''; // Stop video when clicking outside
                }
            });
        });
    </script>

    <!-- Back to Home Button -->
    <section class="section">
        <div class="container text-center">
            <a href="/" class="cta-button"><i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i> {{ __('messages.back_to_home') }}</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>{{ __('messages.lab_name') }}</h3>
                <p>{{ __('messages.footer_about') }}</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>{{ __('messages.quick_links') }}</h3>
                <a href="/">{{ __('messages.home') }}</a>
                <a href="/#analysis">{{ __('messages.analysis') }}</a>
                <a href="/#booking">{{ __('messages.booking') }}</a>
                <a href="/#contact">{{ __('messages.contact') }}</a>
            </div>
            <div class="footer-section">
                <h3>{{ __('messages.contact_info') }}</h3>
                <p><i class="fas fa-map-marker-alt"></i> {{ __('messages.address') }}</p>
                <p><i class="fas fa-phone"></i> {{ __('messages.phone_val') }}</p>
                <p><i class="fas fa-envelope"></i> {{ __('messages.email_val') }}</p>
                <p><i class="fas fa-clock"></i> {{ __('messages.work_hours') }}</p>
            </div>
        </div>
        <div class="copyright">
            <p>{!! __('messages.all_rights_reserved') !!}</p>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>