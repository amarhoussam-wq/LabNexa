<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.lab_name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* Reverting to classic health-check transparent styles if needed, 
           but mostly relying on style.css for the old look */
        .preparation-box {
            background: #fff;
            border: 2px solid var(--secondary);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .ocr-status {
            margin-top: 10px;
            font-weight: bold;
            color: var(--secondary);
        }
        .fa-spinner {
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            left: 20px; /* Full width on mobile by default */
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 99999;
            display: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
        }
        @media (min-width: 768px) {
            .notification {
                left: auto;
                width: auto;
                max-width: 400px;
            }
        }
        .notification.success { background: #2ecc71; }
        .notification.error { background: #e74c3c; border: 2px solid white; }
        .notification.info { background: #3498db; }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        .prep-modal {
            background: #fff;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
    </style>
</head>
<script src="{{ asset('js/app.js') }}"></script>

<body>
    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <!-- Preparation Instructions Modal -->
    <div id="prepModalOverlay" class="modal-overlay">
        <div class="prep-modal">
            <div class="prep-modal-header" style="background: var(--primary); color: #fff; padding: 20px; text-align: center;">
                <h2>{{ __('messages.important_preparation_instructions') }}</h2>
            </div>
            <div class="prep-modal-body" style="padding: 20px;">
                <div id="prepContainer">
                    @if(session('preparations'))
                        @foreach(session('preparations') as $prep)
                            <div class="prep-item" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                <h4 style="color: var(--secondary);">{{ $prep['name'] }}</h4>
                                <p>{{ $prep['instructions'] }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="prep-modal-footer" style="padding: 15px; text-align: center;">
                <button class="cta-button" id="closePrepModal">{{ __('messages.i_understand') }}</button>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">
                <a href="#home">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px; vertical-align: middle; border-radius: 5px;">
                    {{ __('messages.lab_name') }}
                </a>
            </div>
            
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-links" id="navLinks">
                <li><a href="#home">{{ __('messages.home') }}</a></li>
                <li><a href="#features">{{ __('messages.features') }}</a></li>
                <li><a href="#analysis">{{ __('messages.analysis') }}</a></li>
                <li><a href="#booking">{{ __('messages.booking') }}</a></li>
                <li><a href="{{ url('/labnexa_v1.1.4.apk') }}" style="color: #2ecc71; font-weight: bold;"><i class="fab fa-android"></i> {{ app()->getLocale() == 'ar' ? 'تحميل التطبيق' : 'Télécharger l\'App' }}</a></li>
                <li><a href="{{ route('access') }}" style="color: #f1c40f; font-weight: bold;"><i class="fas fa-lock"></i> {{ __('messages.patient_portal') }}</a></li>
                <li><a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'fr' : 'ar') }}" class="btn-lang">
                    {{ app()->getLocale() == 'ar' ? 'Français' : 'العربية' }}
                </a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <h1>{{ __('messages.hero_title') }}</h1>
        <p>{{ __('messages.hero_subtitle') }}</p>
        <a href="#booking" class="cta-button">{{ __('messages.book_now') }} <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i></a>
    </section>

    <!-- Features Section -->
    <section id="features" class="section">
        <div class="container">
            <h2><i class="fas fa-star"></i> {{ __('messages.our_features') }}</h2>
            <p>{{ __('messages.features_desc') }}</p>

            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-microscope"></i>
                    <h3>{{ __('messages.modern_equipment') }}</h3>
                    <p>{{ __('messages.modern_equipment_desc') }}</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-user-md"></i>
                    <h3>{{ __('messages.medical_expertise') }}</h3>
                    <p>{{ __('messages.medical_expertise_desc') }}</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-bolt"></i>
                    <h3>{{ __('messages.fast_results') }}</h3>
                    <p>{{ __('messages.fast_results_desc') }}</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>{{ __('messages.continuous_support') }}</h3>
                    <p>{{ __('messages.continuous_support_desc') }}</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Analysis Section -->
    <section id="analysis" class="section">
        <div class="container">
            <h2><i class="fas fa-flask"></i> {{ __('messages.available_analyses_list') }}</h2>
            <div class="analysis-list" id="mainAnalysisList">
                @foreach($analyses as $analysis)
                <div class="analysis-item">
                    <span>{{ $analysis->name }}</span>
                    @if($analysis->availability == 1)
                    <button class='status-btn available'>{{ __('messages.available') }}</button>
                    @else
                    <button class='status-btn unavailable'>{{ __('messages.unavailable') }}</button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Analysis Info Button Section -->
    <section id="tips" class="section">
        <div class="container">
            <h2><i class="fas fa-lightbulb"></i> {{ __('messages.tips_title') }}</h2>
            <p>{{ __('messages.tips_desc') }}</p>
            <a href="{{ route('analysis.info') }}" class="cta-button">{{ __('messages.view_analysis_info') }} <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i></a>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="section">
        <div class="container">
            <h2><i class="fas fa-calendar-check"></i> {{ __('messages.booking') }}</h2>
            <p>{{ __('messages.booking_desc') }}</p>
            <form id="bookingForm" action="{{ route('booking') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="form-group">
                    <label for="name">{{ __('messages.full_name') }}</label>
                    <input type="text" id="name" name="name" placeholder="{{ __('messages.full_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="phone">{{ __('messages.phone_number') }}</label>
                    <input type="tel" id="phone" name="phone" placeholder="{{ __('messages.phone_number') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input type="email" id="email" name="email" placeholder="{{ __('messages.email') }}" required>
                </div>
                <div class="form-group">
                    <label for="gender">{{ __('messages.gender') }}</label>
                    <select id="gender" name="gender" required>
                        <option value="">{{ __('messages.select_gender') }}</option>
                        <option value="male">{{ __('messages.male') }}</option>
                        <option value="female">{{ __('messages.female') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.birth_date') }} <span class="required">*</span></label>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <select id="birth_day" style="flex:1; min-width:70px;" required>
                            <option value="">{{ app()->getLocale() == 'ar' ? 'اليوم' : 'Jour' }}</option>
                            @for($d=1; $d<=31; $d++)
                                <option value="{{ str_pad($d,2,'0',STR_PAD_LEFT) }}">{{ $d }}</option>
                            @endfor
                        </select>
                        <select id="birth_month" style="flex:1; min-width:90px;" required>
                            <option value="">{{ app()->getLocale() == 'ar' ? 'الشهر' : 'Mois' }}</option>
                            @php $months = app()->getLocale() == 'ar'
                                ? ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
                                : ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];
                            @endphp
                            @foreach($months as $mi => $mn)
                                <option value="{{ str_pad($mi+1,2,'0',STR_PAD_LEFT) }}">{{ $mn }}</option>
                            @endforeach
                        </select>
                        <select id="birth_year" style="flex:2; min-width:90px;" required>
                            <option value="">{{ app()->getLocale() == 'ar' ? 'السنة' : 'Année' }}</option>
                            @for($y=date('Y'); $y>=1930; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <input type="hidden" id="birth_date" name="birth_date">
                </div>
                
                <div class="form-group ocr-section">
                    <label for="prescription"><i class="fas fa-file-medical"></i> {{ __('تحميل الوصفة الطبية (اختياري)') }}</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="prescription" name="prescription" accept="image/*" class="file-input">
                        <div class="ocr-status" id="ocrStatus" style="display:none;">
                            <span class="status-text">{{ __('messages.scanning_prescription') }}</span>
                        </div>
                        <div id="ocrFeedback" class="ocr-feedback-box" style="display:none; margin-top:10px; padding:12px; border-radius:8px; border:1px solid #e2e8f0; background:#f7fafc; font-size:0.9em;">
                            <p id="ocrFeedbackText" style="margin:0; color:#2d3748; line-height:1.5;"></p>
                        </div>
                    </div>
                </div>

                <div id="preparationBox" class="preparation-box" style="display:none;">
                    <h3><i class="fas fa-info-circle"></i> {{ __('messages.important_preparation_instructions') }}:</h3>
                    <ul id="preparationList"></ul>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.analysis_types') }} <span class="required">*</span></label>
                    <div class="checkbox-grid">
                        @foreach($analyses as $analysis)
                            @if($analysis->availability == 1)
                            <div class="checkbox-item">
                                <input type="checkbox" name="analysisTypes[]" value="{{ $analysis->id }}" id="analysis_{{ $analysis->id }}" 
                                       data-name="{{ $analysis->name }}" data-code="{{ $analysis->code }}" 
                                       data-preparation="{{ $analysis->preparation_instructions }}">
                                <label for="analysis_{{ $analysis->id }}">{{ $analysis->name }}</label>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <small class="form-text text-muted">يمكنك اختيار واحد أو أكثر</small>
                </div>
                <div class="form-group">
                    <label for="date">{{ __('messages.date') }}</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="time">{{ __('messages.time') }}</label>
                    <input type="time" id="time" name="time" required>
                </div>
                <button type="submit" style="width: 100%;"><i class="fas fa-check-circle"></i> {{ __('messages.confirm_booking') }}</button>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="analysisTypes[]"]');
        const preparationBox = document.getElementById('preparationBox');
        const preparationList = document.getElementById('preparationList');

        // Update Birth Date Logic
        const birthDay = document.getElementById('birth_day');
        const birthMonth = document.getElementById('birth_month');
        const birthYear = document.getElementById('birth_year');
        const birthDateInput = document.getElementById('birth_date');

        function updateBirthDate() {
            if (birthDay && birthMonth && birthYear && birthDateInput) {
                const d = birthDay.value;
                const m = birthMonth.value;
                const y = birthYear.value;
                birthDateInput.value = (d && m && y) ? `${y}-${m}-${d}` : '';
            }
        }

        if (birthDay) birthDay.addEventListener('change', updateBirthDate);
        if (birthMonth) birthMonth.addEventListener('change', updateBirthDate);
        if (birthYear) birthYear.addEventListener('change', updateBirthDate);
        
        // Initial call to catch any pre-selected values
        updateBirthDate();

        // Update Preparations Logic (Box + Modal)
        function updatePreparations() {
            if (!preparationList || !preparationBox) return;
            preparationList.innerHTML = '';
            let hasPrep = false;
            let prepText = '';
            
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const prep = cb.getAttribute('data-preparation');
                    if (prep && prep.trim() !== '') {
                        const li = document.createElement('li');
                        li.innerHTML = `<strong>${cb.getAttribute('data-name')}:</strong> ${prep}`;
                        preparationList.appendChild(li);
                        
                        prepText += `<div class="prep-item" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                        <h4 style="color: var(--secondary);">${cb.getAttribute('data-name')}</h4>
                                        <p>${prep}</p>
                                     </div>`;
                        hasPrep = true;
                    }
                }
            });
            preparationBox.style.display = hasPrep ? 'block' : 'none';

            /* 
            // Removed automatic popup per user request
            if (hasPrep) {
                const prepContainer = document.getElementById('prepContainer');
                const prepModalOverlay = document.getElementById('prepModalOverlay');
                if (prepContainer && prepModalOverlay) {
                    prepContainer.innerHTML = prepText;
                    prepModalOverlay.style.display = 'flex';
                }
            }
            */
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updatePreparations));

        // OCR Logic
        const prescriptionInput = document.getElementById('prescription');
        const ocrStatus = document.getElementById('ocrStatus');

        if (prescriptionInput) {
            prescriptionInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const ocrFeedback = document.getElementById('ocrFeedback');
                const ocrFeedbackText = document.getElementById('ocrFeedbackText');

                ocrStatus.style.display = 'block';
                if (ocrFeedback) ocrFeedback.style.display = 'none';

                Tesseract.recognize(file, 'ara+fra+eng').then(({ data: { text } }) => {
                    ocrStatus.style.display = 'none';
                    const lowerText = text.toLowerCase();
                    let foundCount = 0;
                    let detectedNames = [];

                    checkboxes.forEach(cb => {
                        const name = (cb.getAttribute('data-name') || '').toLowerCase();
                        const code = (cb.getAttribute('data-code') || '').toLowerCase();
                        
                        if (name && (lowerText.includes(name) || (code && lowerText.includes(code)))) {
                            if (!cb.checked) {
                                cb.checked = true;
                                foundCount++;
                            }
                            detectedNames.push(cb.getAttribute('data-name'));
                        }
                    });

                    if (ocrFeedback && ocrFeedbackText) {
                        ocrFeedback.style.display = 'block';
                        if (foundCount > 0) {
                            const uniqueNames = [...new Set(detectedNames)];
                            ocrFeedbackText.innerHTML = `<strong>{{ __('messages.prescription_analyzed_success') }}</strong><br><span style="color:#2c5282;">&bull; ${uniqueNames.join('<br>&bull; ')}</span><br><br><small>{{ __('messages.download_prep_instructions') }}</small>`;
                            showNotification('{{ __("messages.ocr_success_detected") }}'.replace(':count', foundCount), 'success');
                        } else {
                            ocrFeedbackText.innerHTML = `<i class="fas fa-info-circle" style="color:#3182ce;"></i> {{ __('messages.prescription_manual_verify_notice') }}`;
                            showNotification('{{ __("messages.ocr_no_matches_found") }}', 'info');
                        }
                        updatePreparations();
                    }
                }).catch(err => {
                    console.error("OCR Error:", err);
                    ocrStatus.style.display = 'none';
                    showNotification('{{ __("messages.ocr_processing_error") }}', 'error');
                });
            });
        }

        // Modal Close Logic
        const closePrepModal = document.getElementById('closePrepModal');
        const prepModalOverlay = document.getElementById('prepModalOverlay');
        if (closePrepModal && prepModalOverlay) {
            closePrepModal.addEventListener('click', () => {
                prepModalOverlay.style.display = 'none';
            });
        }

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
            // Close menu when a link is clicked
            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                });
            });
        }
    });

    // Global Notification Function with Mobile Alert Fallback
    window.showNotification = function(message, type = 'info') {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.textContent = message;
            notification.className = 'notification ' + type;
            notification.style.display = 'block';
            
            // Failsafe: Log to console and use alert for critical errors on mobile
            console.warn("Notification (" + type + "): " + message);
            if (type === 'error') {
                // Secondary check: if it's an error, also use native alert to be 100% sure the user sees it
                setTimeout(() => {
                    if (notification.style.display !== 'block') {
                        alert(message);
                    }
                }, 100);
            }

            setTimeout(() => {
                notification.style.display = 'none';
            }, 6000);
        } else {
            // Disaster fallback: if the element doesn't exist
            alert(message);
        }
    };
    </script>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <h2><i class="fas fa-envelope"></i> {{ __('messages.contact') }}</h2>
            <p>{{ __('messages.contact_desc') }}</p>
            <form id="contactForm" action="{{ route('message') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <label for="contact_name">{{ __('messages.name') }}</label>
                    <input type="text" name="name" id="contact_name" placeholder="{{ __('messages.name') }}" required>
                </div>
                <div class="form-group">
                    <label for="contact_email">{{ __('messages.email') }}</label>
                    <input type="text" id="contact_email" name="email" placeholder="{{ __('messages.email') }}" required>
                </div>
                <div class="form-group">
                    <label for="message">{{ __('messages.message') }}</label>
                    <textarea id="message" name="message" rows="5" placeholder="{{ __('messages.message') }}" required></textarea>
                </div>
                <button type="submit"><i class="fas fa-paper-plane"></i> {{ __('messages.send_message') }}</button>
            </form>
        </div>
    </section>

    <!-- Map Section -->
    <section class="section">
        <div class="container">
            <h2><i class="fas fa-map-marker-alt"></i> {{ __('messages.our_location') }}</h2>
            <p>{{ __('messages.location_desc') }}</p>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5693.052425601013!2d5.262623025838582!3d31.957933404038677!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x125d69d1688915f9%3A0xc65def288f0e9a57!2sLaboratoire%20Bela%C3%AFd%20d&#39;analyse%20m%C3%A9dical!5e0!3m2!1sen!2sdz!4v1761573361428!5m2!1sen!2sdz" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>{{ __('messages.admin_panel') }}</h3>
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
                <a href="#home">{{ __('messages.home') }}</a>
                <a href="#features">{{ __('messages.features') }}</a>
                <a href="#analysis">{{ __('messages.analysis') }}</a>
                <a href="#booking">{{ __('messages.booking') }}</a>
                <a href="{{ route('access') }}">{{ __('messages.patient_portal') }} / {{ __('messages.physician_portal') }}</a>
                <a href="#contact">{{ __('messages.contact') }}</a>
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

    {{-- Session Notifications & Validation --}}
    <script>
        window.addEventListener('load', function() {
            @if(session('success'))
                showNotification(@json(session('success')), 'success');
            @endif

            @if(session('error'))
                showNotification(@json(session('error')), 'error');
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    showNotification(@json($error), 'error');
                @endforeach
            @endif

            // Booking Form Validation
            const bookingForm = document.getElementById('bookingForm');
            const prescriptionInput = document.getElementById('prescription');
            const phoneInput = document.getElementById('phone');
            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('time');
            const submitBtn = bookingForm ? bookingForm.querySelector('button[type="submit"]') : null;

            if (bookingForm) {
                const today = new Date().toISOString().split('T')[0];
                if (dateInput) dateInput.setAttribute('min', today);

                // Helper to combine birth date selects into hidden field
                function updateBirthDate() {
                    const day = document.getElementById('birth_day');
                    const month = document.getElementById('birth_month');
                    const year = document.getElementById('birth_year');
                    const hidden = document.getElementById('birth_date');
                    
                    if (day && month && year && hidden && day.value && month.value && year.value) {
                        hidden.value = year.value + '-' + month.value + '-' + day.value;
                        return true;
                    }
                    return false;
                }

                // Robust Booking Form Submission Handler
                bookingForm.addEventListener('submit', function(e) {
                    try {
                        let errorMessage = null;

                        // FIRST: Update the combined birth date field
                        updateBirthDate();

                        // 1. Full Name Validation
                        const nameInput = document.getElementById('name');
                        if (!errorMessage && (!nameInput || !nameInput.value.trim())) {
                            errorMessage = '{{ __('messages.name_required') }}';
                        }

                        // 2. Gender Validation
                        const genderSelect = document.getElementById('gender');
                        if (!errorMessage && (!genderSelect || !genderSelect.value)) {
                            errorMessage = '{{ __('messages.gender_required') }}';
                        }

                        // 3. Birth Date Validation (Complete Selection)
                        const birthDateHidden = document.getElementById('birth_date');
                        if (!errorMessage && (!birthDateHidden || !birthDateHidden.value)) {
                            errorMessage = '{{ __('messages.birth_date_required') }}';
                        }

                        // 4. Contact Information Validation
                        const phoneRegex = /^(05|06|07)[0-9]{8}$/;
                        if (!errorMessage && (!phoneInput || !phoneInput.value.trim())) {
                            errorMessage = '{{ __('messages.phone_required') }}';
                        } else if (!errorMessage && !phoneRegex.test(phoneInput.value)) {
                            errorMessage = '{{ __('messages.invalid_phone_format') }}';
                        }

                        const emailInput = document.getElementById('email');
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!errorMessage) {
                            if (!emailInput || !emailInput.value.trim()) {
                                errorMessage = '{{ __('messages.email_required') }}';
                            } else {
                                // Clean invisible chars (Zero-width, NBSP, etc.) often added by Arabic mobile keyboards
                                const cleanEmail = emailInput.value.replace(/[\u00A0\u200B\u200C\u200D\u200E\u200F\uFEFF]/g, '').trim();
                                if (!emailRegex.test(cleanEmail)) {
                                    errorMessage = '{{ __('messages.invalid_email_format') }}';
                                } else {
                                    emailInput.value = cleanEmail; // update input to pass clean value to backend
                                }
                            }
                        }

                        // 5. Analysis/Prescription Requirement
                        const checkboxes = this.querySelectorAll('input[name="analysisTypes[]"]:checked');
                        const hasFile = prescriptionInput && prescriptionInput.files.length > 0;
                        if (!errorMessage && checkboxes.length === 0 && !hasFile) {
                            errorMessage = '{{ __('messages.at_least_one_analysis_or_prescription') }}';
                        }

                        // 6. Appointment Date & Time Validation
                        if (!errorMessage && (!dateInput || !dateInput.value)) {
                            errorMessage = '{{ __('messages.date_required') }}';
                        }
                        if (!errorMessage && (!timeInput || !timeInput.value)) {
                            errorMessage = '{{ __('messages.time_required') }}';
                        }

                        // 7. Lead Time Check (14 Hours) - Robust Implementation
                        if (!errorMessage && dateInput.value && timeInput.value) {
                            const dateParts = dateInput.value.split('-'); // [YYYY, MM, DD]
                            const timeParts = timeInput.value.split(':'); // [HH, mm]
                            
                            if (dateParts.length === 3 && timeParts.length >= 2) {
                                const selectedDate = new Date(
                                    parseInt(dateParts[0]), 
                                    parseInt(dateParts[1]) - 1, 
                                    parseInt(dateParts[2]), 
                                    parseInt(timeParts[0]), 
                                    parseInt(timeParts[1])
                                );
                                const now = new Date();
                                const diffInHours = (selectedDate - now) / (1000 * 60 * 60);

                                if (diffInHours < 14) {
                                    errorMessage = '{{ __('messages.booking_too_soon') }}';
                                }
                            }
                        }

                        // Final Action
                        if (errorMessage) {
                            e.preventDefault();
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> {{ __("messages.confirm_booking") }}';
                            }
                            window.showNotification(errorMessage, 'error');
                            alert(errorMessage);
                            return;
                        }

                        // Success Case: Attempt submission
                        if (submitBtn) {
                            // Small delay for disabling to ensure form data is captured
                            setTimeout(() => {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("messages.processing") }}';
                            }, 50);
                        }
                        
                    } catch (err) {
                        console.error("Critical Validation Error:", err);
                        // Failsafe: Continue with standard submission if JS logic fails
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("messages.processing") }}...';
                        }
                    }
                });
            }

            // Auto-open HTML Report if session exists (to fix Arabic distortion)
            @if(session('download_pdf'))
                try {
                    const viewUrl = "{{ route('report.view', ['id' => session('download_res_id'), 'type' => 'request']) }}";
                    window.open(viewUrl, '_blank');
                    showNotification("{{ __('messages.viewing_prep_instructions') }}", 'success');
                } catch(e) {
                    console.error('Report View Error:', e);
                }
            @endif
        });
    </script>
</body>
</html>