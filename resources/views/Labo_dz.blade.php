<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.lab_name') }} — {{ __('messages.portal_welcome_subtitle') ?? 'Système de gestion du laboratoire médical' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — World-Class HealthTech Landing Page & Medical Portal
           Precision Health UI • Glassmorphism • Micro-Interactions
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --primary: #0066FF;
            --primary-hover: #0052CC;
            --primary-light: #EBF3FF;
            --primary-glow: rgba(0, 102, 255, 0.25);
            
            --teal: #0D9488;
            --teal-light: #F0FDFA;
            --emerald: #10B981;
            --emerald-light: #ECFDF5;
            
            --slate-900: #0B1120;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-400: #94A3B8;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;
            --slate-50:  #F8FAFC;
            
            --surface-glass: rgba(255, 255, 255, 0.85);
            --surface-glass-dark: rgba(15, 23, 42, 0.75);
            --border-glass: rgba(226, 232, 240, 0.8);
            
            --shadow-subtle: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-card: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            --shadow-elevated: 0 20px 40px -15px rgba(0, 102, 255, 0.15), 0 0 15px rgba(0,0,0,0.03);
            --shadow-glow: 0 0 30px rgba(0, 102, 255, 0.3);
            
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 30px;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Inter'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            overflow-x: hidden;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Typography & Links ── */
        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.25s ease;
        }

        /* ── Floating Notification ── */
        .notification {
            position: fixed;
            top: 28px;
            right: 28px;
            left: 28px;
            padding: 16px 24px;
            border-radius: var(--radius-md);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            z-index: 999999;
            display: none;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.25);
            backdrop-filter: blur(12px);
            animation: slideInDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 450px;
            margin-inline: auto;
        }
        @keyframes slideInDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .notification.success { background: linear-gradient(135deg, #059669, #10B981); }
        .notification.error   { background: linear-gradient(135deg, #DC2626, #EF4444); }
        .notification.info    { background: linear-gradient(135deg, #0284C7, #0EA5E9); }

        /* ── Glass Navbar ── */
        .navbar-main {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }

        .navbar-main.scrolled {
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
            background: rgba(255, 255, 255, 0.95);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--slate-900);
            letter-spacing: -0.5px;
        }

        .brand-logo img {
            height: 44px;
            width: auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 102, 255, 0.15);
        }

        .brand-logo span.gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, #00C6FF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-links a {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--slate-600);
            position: relative;
            padding: 6px 0;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
            transform: scaleX(0);
            transition: transform 0.25s ease;
        }
        .nav-links a:hover::after {
            transform: scaleX(1);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 700;
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(0, 102, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-portal:hover {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 6px 20px var(--primary-glow);
            transform: translateY(-2px);
        }

        .btn-app-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 700;
            background: var(--emerald-light);
            color: var(--emerald);
            border: 1px solid rgba(16, 185, 129, 0.2);
            transition: all 0.25s;
        }

        .btn-app-download:hover {
            background: var(--emerald);
            color: #fff;
            transform: translateY(-2px);
        }

        .lang-switch-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 100px;
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--slate-700);
        }

        .lang-switch-pill:hover {
            background: var(--slate-200);
            color: var(--slate-900);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--slate-800);
            cursor: pointer;
            padding: 8px;
        }

        /* ── Hero Section ── */
        .hero-section {
            position: relative;
            padding: 160px 24px 100px;
            background: radial-gradient(circle at 80% 20%, rgba(0, 102, 255, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 20% 80%, rgba(13, 148, 136, 0.06) 0%, transparent 40%),
                        linear-gradient(180deg, #FFFFFF 0%, var(--slate-50) 100%);
            overflow: hidden;
        }

        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 60px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 100px;
            background: var(--primary-light);
            border: 1px solid rgba(0, 102, 255, 0.15);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 24px;
        }

        .hero-badge i {
            color: var(--primary);
            font-size: 0.9rem;
        }

        .hero-title {
            font-size: 3.4rem;
            font-weight: 900;
            line-height: 1.18;
            color: var(--slate-900);
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--primary) 0%, #00B4D8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.18rem;
            color: var(--slate-600);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 600px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 48px;
        }

        .btn-primary-glow {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 32px;
            border-radius: var(--radius-md);
            font-size: 1.05rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, #0052CC 100%);
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 25px var(--primary-glow);
            transition: all 0.3s ease;
        }

        .btn-primary-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 102, 255, 0.4);
            color: #fff;
        }

        .btn-secondary-outline {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 28px;
            border-radius: var(--radius-md);
            font-size: 1.05rem;
            font-weight: 700;
            background: #fff;
            color: var(--slate-800);
            border: 1.5px solid var(--slate-200);
            box-shadow: var(--shadow-subtle);
            transition: all 0.25s ease;
        }

        .btn-secondary-outline:hover {
            border-color: var(--slate-400);
            background: var(--slate-50);
            transform: translateY(-2px);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            padding-top: 32px;
            border-top: 1px solid var(--slate-200);
        }

        .stat-item h4 {
            font-size: 2rem;
            font-weight: 900;
            color: var(--slate-900);
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .stat-item p {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--slate-500);
        }

        /* ── Hero Visual Card ── */
        .hero-visual {
            position: relative;
        }

        .medical-card-preview {
            background: #fff;
            border-radius: var(--radius-xl);
            padding: 32px;
            box-shadow: var(--shadow-elevated);
            border: 1px solid rgba(226, 232, 240, 0.9);
            position: relative;
            z-index: 2;
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--slate-100);
        }

        .pulse-live {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--emerald);
            background: var(--emerald-light);
            padding: 4px 12px;
            border-radius: 100px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--emerald);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse 1.6s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .preview-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            background: var(--slate-50);
            margin-bottom: 12px;
            border: 1px solid var(--slate-100);
        }

        .preview-item:last-child {
            margin-bottom: 0;
        }

        .preview-item-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .preview-icon.blue { background: var(--primary-light); color: var(--primary); }
        .preview-icon.teal { background: var(--teal-light); color: var(--teal); }
        .preview-icon.green { background: var(--emerald-light); color: var(--emerald); }

        .floating-badge {
            position: absolute;
            background: #fff;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            box-shadow: 0 15px 35px -5px rgba(0,0,0,0.12);
            border: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 3;
            animation: float 4s ease-in-out infinite;
        }

        .floating-badge.badge-top {
            top: -20px;
            right: -20px;
        }
        [dir="rtl"] .floating-badge.badge-top {
            right: auto;
            left: -20px;
        }

        .floating-badge.badge-bottom {
            bottom: -20px;
            left: -20px;
            animation-delay: -2s;
        }
        [dir="rtl"] .floating-badge.badge-bottom {
            left: auto;
            right: -20px;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* ── Section Standard Styles ── */
        .section-padding {
            padding: 100px 24px;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }

        .section-tag {
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 12px;
            background: var(--primary-light);
            padding: 6px 16px;
            border-radius: 100px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--slate-600);
            line-height: 1.6;
        }

        /* ── Features Grid ── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }

        .feature-card {
            background: #fff;
            padding: 36px 28px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-subtle);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #00C6FF);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(0, 102, 255, 0.3);
            box-shadow: var(--shadow-card);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 24px;
            transition: transform 0.3s;
        }

        .feature-card:hover .feature-icon-wrap {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 12px;
        }

        .feature-card p {
            color: var(--slate-600);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* ── Analysis Catalog Section ── */
        .analysis-section {
            background: #fff;
            border-top: 1px solid var(--slate-200);
            border-bottom: 1px solid var(--slate-200);
        }

        .catalog-search-bar {
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
        }

        .catalog-search-bar input {
            width: 100%;
            padding: 16px 24px 16px 54px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--slate-200);
            font-size: 1rem;
            font-family: inherit;
            background: var(--slate-50);
            color: var(--slate-800);
            outline: none;
            transition: all 0.25s;
        }

        [dir="rtl"] .catalog-search-bar input {
            padding: 16px 54px 16px 24px;
        }

        .catalog-search-bar input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .catalog-search-bar i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
            font-size: 1.15rem;
        }

        [dir="rtl"] .catalog-search-bar i {
            left: auto;
            right: 20px;
        }

        .analysis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            max-height: 480px;
            overflow-y: auto;
            padding: 8px;
        }

        .analysis-grid::-webkit-scrollbar {
            width: 6px;
        }
        .analysis-grid::-webkit-scrollbar-thumb {
            background: var(--slate-300);
            border-radius: 4px;
        }

        .analysis-item-card {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: all 0.2s;
        }

        .analysis-item-card:hover {
            background: #fff;
            border-color: var(--primary);
            box-shadow: var(--shadow-subtle);
            transform: translateY(-2px);
        }

        .analysis-item-title {
            font-weight: 700;
            color: var(--slate-800);
            font-size: 0.95rem;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-badge.available {
            background: var(--emerald-light);
            color: var(--emerald);
        }

        .status-badge.unavailable {
            background: #FEE2E2;
            color: #DC2626;
        }

        /* ── Modern Booking Section ── */
        .booking-section {
            background: linear-gradient(180deg, var(--slate-50) 0%, #FFFFFF 100%);
        }

        .booking-card {
            background: #fff;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-elevated);
            border: 1px solid var(--slate-200);
            padding: 48px;
            max-width: 900px;
            margin: 0 auto;
        }

        .booking-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .form-group-modern {
            margin-bottom: 4px;
        }

        .form-group-modern label {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 8px;
        }

        .input-wrap-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap-modern i {
            position: absolute;
            left: 16px;
            color: var(--slate-400);
            font-size: 1rem;
        }

        [dir="rtl"] .input-wrap-modern i {
            left: auto;
            right: 16px;
        }

        .input-wrap-modern input,
        .input-wrap-modern select,
        .input-wrap-modern textarea {
            width: 100%;
            padding: 14px 16px 14px 44px;
            font-family: inherit;
            font-size: 0.95rem;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--slate-200);
            background: var(--slate-50);
            color: var(--slate-800);
            outline: none;
            transition: all 0.2s;
        }

        [dir="rtl"] .input-wrap-modern input,
        [dir="rtl"] .input-wrap-modern select,
        [dir="rtl"] .input-wrap-modern textarea {
            padding: 14px 44px 14px 16px;
        }

        .input-wrap-modern input:focus,
        .input-wrap-modern select:focus,
        .input-wrap-modern textarea:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        /* ── OCR Prescription Uploader ── */
        .prescription-uploader {
            border: 2px dashed rgba(0, 102, 255, 0.35);
            background: rgba(0, 102, 255, 0.02);
            border-radius: var(--radius-md);
            padding: 28px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .prescription-uploader:hover {
            border-color: var(--primary);
            background: rgba(0, 102, 255, 0.05);
        }

        .prescription-uploader input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .uploader-content i {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .uploader-content h4 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--slate-800);
            margin-bottom: 4px;
        }

        .uploader-content p {
            font-size: 0.85rem;
            color: var(--slate-500);
        }

        /* OCR status & feedback */
        .ocr-status {
            display: none;
            margin-top: 14px;
            font-weight: 700;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .ocr-feedback-box {
            display: none;
            margin-top: 14px;
            padding: 16px;
            border-radius: var(--radius-md);
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            font-size: 0.9rem;
            text-align: right;
        }
        [dir="ltr"] .ocr-feedback-box { text-align: left; }

        /* ── Analysis Checkbox Grid ── */
        .checkbox-modern-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 10px;
            max-height: 280px;
            overflow-y: auto;
            padding: 12px;
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-md);
            background: var(--slate-50);
        }

        .checkbox-modern-grid::-webkit-scrollbar {
            width: 6px;
        }
        .checkbox-modern-grid::-webkit-scrollbar-thumb {
            background: var(--slate-300);
            border-radius: 4px;
        }

        .checkbox-pill-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-200);
            cursor: pointer;
            transition: all 0.2s;
        }

        .checkbox-pill-item:hover {
            border-color: var(--primary);
        }

        .checkbox-pill-item input[type="checkbox"] {
            accent-color: var(--primary);
            width: 17px;
            height: 17px;
            cursor: pointer;
        }

        .checkbox-pill-item label {
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--slate-800);
            margin-bottom: 0 !important;
            flex-grow: 1;
        }

        /* Preparation box */
        .preparation-box {
            background: #FEF3C7;
            border: 1.5px solid #F59E0B;
            border-radius: var(--radius-md);
            padding: 18px 22px;
            margin-top: 16px;
        }

        .preparation-box h3 {
            color: #92400E;
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preparation-box ul {
            padding-inline-start: 20px;
            color: #78350F;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* ── Submit Booking Button ── */
        .btn-confirm-booking {
            width: 100%;
            padding: 18px;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, #0052CC 100%);
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 25px var(--primary-glow);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-confirm-booking:hover {
            box-shadow: 0 15px 35px rgba(0, 102, 255, 0.4);
            transform: translateY(-2px);
        }

        /* ── Contact & Location Section ── */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 36px;
        }

        .contact-form-card {
            background: #fff;
            border-radius: var(--radius-xl);
            padding: 40px;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-card);
        }

        .map-card {
            background: #fff;
            border-radius: var(--radius-xl);
            overflow: hidden;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-card);
            height: 100%;
            min-height: 420px;
        }

        .map-card iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* ── Modal Preparation Overlay ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .prep-modal {
            background: #fff;
            border-radius: var(--radius-xl);
            max-width: 560px;
            width: 100%;
            overflow: hidden;
            box-shadow: var(--shadow-elevated);
            animation: modalPop 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalPop {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .prep-modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, #0052CC 100%);
            color: #fff;
            padding: 24px;
            text-align: center;
        }

        .prep-modal-header h2 {
            font-size: 1.3rem;
            font-weight: 800;
        }

        .prep-modal-body {
            padding: 28px;
            max-height: 400px;
            overflow-y: auto;
        }

        .prep-modal-footer {
            padding: 20px 28px;
            background: var(--slate-50);
            border-top: 1px solid var(--slate-100);
            text-align: center;
        }

        /* ── Footer ── */
        footer {
            background: var(--slate-900);
            color: #fff;
            padding: 80px 24px 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 48px;
            margin-bottom: 60px;
        }

        .footer-brand h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 16px;
        }

        .footer-brand p {
            color: var(--slate-400);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .footer-social-links {
            display: flex;
            gap: 12px;
        }

        .footer-social-links a {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all 0.25s;
        }

        .footer-social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-col h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul li a {
            color: var(--slate-400);
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .footer-col ul li a:hover {
            color: #fff;
            padding-inline-start: 4px;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--slate-400);
            font-size: 0.85rem;
        }

        /* ── Responsive Rules ── */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            .nav-links {
                position: fixed;
                top: 72px;
                left: 0;
                right: 0;
                background: #fff;
                flex-direction: column;
                align-items: flex-start;
                padding: 24px;
                gap: 16px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
                display: none;
            }
            .nav-links.active {
                display: flex;
            }
            .nav-actions {
                display: none;
            }
            .nav-links .nav-actions-mobile {
                display: flex;
                flex-direction: column;
                gap: 12px;
                width: 100%;
                margin-top: 16px;
                padding-top: 16px;
                border-top: 1px solid var(--slate-200);
            }
            .hero-title {
                font-size: 2.3rem;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .booking-form-grid {
                grid-template-columns: 1fr;
            }
            .booking-card {
                padding: 24px;
            }
            .contact-grid {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .hero-stats {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>
</head>

<body>

    <!-- Notification Alert -->
    <div id="notification" class="notification"></div>

    <!-- Preparation Instructions Modal -->
    <div id="prepModalOverlay" class="modal-overlay">
        <div class="prep-modal">
            <div class="prep-modal-header">
                <h2><i class="fas fa-notes-medical"></i> {{ __('messages.important_preparation_instructions') }}</h2>
            </div>
            <div class="prep-modal-body">
                <div id="prepContainer">
                    @if(session('preparations'))
                        @foreach(session('preparations') as $prep)
                            <div class="prep-item" style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
                                <h4 style="color: var(--primary); font-weight: 800; margin-bottom: 4px;">{{ $prep['name'] }}</h4>
                                <p style="color: var(--slate-600); font-size: 0.95rem;">{{ $prep['instructions'] }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="prep-modal-footer">
                <button class="btn-primary-glow" id="closePrepModal" style="padding: 10px 28px; font-size: 0.95rem;">
                    {{ __('messages.i_understand') }}
                </button>
            </div>
        </div>
    </div>

    <!-- ══════════════════ Navigation ══════════════════ -->
    <nav class="navbar-main" id="mainNav">
        <div class="nav-container">
            <a href="#home" class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                <span>{{ __('messages.lab_name') }}</span>
            </a>

            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-links" id="navLinks">
                <li><a href="#home">{{ __('messages.home') }}</a></li>
                <li><a href="#features">{{ __('messages.features') }}</a></li>
                <li><a href="#analysis">{{ __('messages.analysis') }}</a></li>
                <li><a href="#booking">{{ __('messages.booking') }}</a></li>
                <li><a href="{{ route('analysis.info') }}">{{ __('messages.tips_title') ?? 'Conseils' }}</a></li>
                <li><a href="#contact">{{ __('messages.contact') }}</a></li>
                
                <li class="nav-actions-mobile" style="display: none;">
                    <a href="{{ route('access') }}" class="btn-portal">
                        <i class="fas fa-user-circle"></i> {{ __('messages.patient_portal') }}
                    </a>
                    <a href="{{ url('/labnexa_v1.1.4.apk') }}" class="btn-app-download">
                        <i class="fab fa-android"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق الموبايل' : 'App Android' }}
                    </a>
                    <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'fr' : 'ar') }}" class="lang-switch-pill">
                        <i class="fas fa-globe"></i> {{ app()->getLocale() == 'ar' ? 'Français' : 'العربية' }}
                    </a>
                </li>
            </ul>

            <div class="nav-actions">
                <a href="{{ url('/labnexa_v1.1.4.apk') }}" class="btn-app-download">
                    <i class="fab fa-android"></i> <span>{{ app()->getLocale() == 'ar' ? 'تحميل التطبيق' : 'App' }}</span>
                </a>
                <a href="{{ route('access') }}" class="btn-portal">
                    <i class="fas fa-lock"></i> <span>{{ __('messages.patient_portal') }}</span>
                </a>
                <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'fr' : 'ar') }}" class="lang-switch-pill">
                    {{ app()->getLocale() == 'ar' ? 'FR' : 'AR' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- ══════════════════ Hero Section ══════════════════ -->
    <section id="home" class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-dna"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'منظومة مخبرية ذكية رائدة' : 'Plateforme Médicale Intelligente de Pointe' }}</span>
                </div>
                
                <h1 class="hero-title">
                    {{ __('messages.hero_title') }}
                </h1>
                
                <p class="hero-subtitle">
                    {{ __('messages.hero_subtitle') }}
                </p>

                <div class="hero-cta-group">
                    <a href="#booking" class="btn-primary-glow">
                        <span>{{ __('messages.book_now') }}</span>
                        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                    </a>
                    <a href="{{ route('access') }}" class="btn-secondary-outline">
                        <i class="fas fa-fingerprint"></i>
                        <span>{{ __('messages.patient_portal') }} / {{ __('messages.admin') }}</span>
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <h4>99.8%</h4>
                        <p>{{ app()->getLocale() == 'ar' ? 'دقة التحاليل المخبرية' : 'Précision Analytique' }}</p>
                    </div>
                    <div class="stat-item">
                        <h4>24h</h4>
                        <p>{{ app()->getLocale() == 'ar' ? 'إصدار فوري للنتائج' : 'Délai d\'analyse rapide' }}</p>
                    </div>
                    <div class="stat-item">
                        <h4>100%</h4>
                        <p>{{ app()->getLocale() == 'ar' ? 'سرية طبية مشفرة' : 'Confidentialité Sécurisée' }}</p>
                    </div>
                </div>
            </div>

            <!-- Hero Visual Preview Card -->
            <div class="hero-visual">
                <div class="floating-badge badge-top">
                    <i class="fas fa-certificate" style="color: #F59E0B; font-size: 1.4rem;"></i>
                    <div>
                        <strong style="display:block; font-size:0.85rem; color:var(--slate-800);">{{ app()->getLocale() == 'ar' ? 'معتمد رسمياً' : 'Certifié & Homologué' }}</strong>
                        <span style="font-size:0.75rem; color:var(--slate-500);">ISO 15189 Quality</span>
                    </div>
                </div>

                <div class="medical-card-preview">
                    <div class="preview-header">
                        <div>
                            <h3 style="font-size:1.15rem; font-weight:800; color:var(--slate-900);">{{ __('messages.lab_name') }}</h3>
                            <span style="font-size:0.8rem; color:var(--slate-500);">{{ app()->getLocale() == 'ar' ? 'نظام التحاليل المباشر' : 'Système d\'analyse en direct' }}</span>
                        </div>
                        <div class="pulse-live">
                            <div class="pulse-dot"></div>
                            <span>{{ app()->getLocale() == 'ar' ? 'متاح الآن' : 'En ligne' }}</span>
                        </div>
                    </div>

                    <div class="preview-item">
                        <div class="preview-item-info">
                            <div class="preview-icon blue"><i class="fas fa-vial"></i></div>
                            <div>
                                <strong style="display:block; font-size:0.9rem; color:var(--slate-800);">FNS / Hemogramme</strong>
                                <span style="font-size:0.75rem; color:var(--slate-500);">Automated Hematology</span>
                            </div>
                        </div>
                        <span class="status-badge available">{{ __('messages.available') }}</span>
                    </div>

                    <div class="preview-item">
                        <div class="preview-item-info">
                            <div class="preview-icon teal"><i class="fas fa-microscope"></i></div>
                            <div>
                                <strong style="display:block; font-size:0.9rem; color:var(--slate-800);">Biochimie Clinique</strong>
                                <span style="font-size:0.75rem; color:var(--slate-500);">Glycémie, Bilan Rénal</span>
                            </div>
                        </div>
                        <span class="status-badge available">{{ __('messages.available') }}</span>
                    </div>

                    <div class="preview-item">
                        <div class="preview-item-info">
                            <div class="preview-icon green"><i class="fas fa-brain"></i></div>
                            <div>
                                <strong style="display:block; font-size:0.9rem; color:var(--slate-800);">AI Health Insights</strong>
                                <span style="font-size:0.75rem; color:var(--slate-500);">Clinical Intelligence</span>
                            </div>
                        </div>
                        <span class="status-badge" style="background:#E0E7FF; color:#4338CA;">Smart</span>
                    </div>
                </div>

                <div class="floating-badge badge-bottom">
                    <i class="fas fa-shield-halved" style="color: var(--primary); font-size: 1.4rem;"></i>
                    <div>
                        <strong style="display:block; font-size:0.85rem; color:var(--slate-800);">{{ app()->getLocale() == 'ar' ? 'تحميل مباشر للنتائج' : 'Téléchargement Direct' }}</strong>
                        <span style="font-size:0.75rem; color:var(--slate-500);">Rapports PDF Sécurisés</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ Features Section ══════════════════ -->
    <section id="features" class="section-padding">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.our_features') }}</span>
                <h2 class="section-title">{{ __('messages.features_desc') }}</h2>
                <p class="section-subtitle">
                    {{ app()->getLocale() == 'ar' ? 'نجمع بين أحدث التقنيات المخبرية والخبرة الطبية العالية لضمان أدق النتائج وأسرعها.' : 'L\'excellence médicale et technologique au service de votre santé au quotidien.' }}
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fas fa-microscope"></i></div>
                    <h3>{{ __('messages.modern_equipment') }}</h3>
                    <p>{{ __('messages.modern_equipment_desc') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background:var(--teal-light); color:var(--teal);"><i class="fas fa-user-md"></i></div>
                    <h3>{{ __('messages.medical_expertise') }}</h3>
                    <p>{{ __('messages.medical_expertise_desc') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background:var(--emerald-light); color:var(--emerald);"><i class="fas fa-bolt"></i></div>
                    <h3>{{ __('messages.fast_results') }}</h3>
                    <p>{{ __('messages.fast_results_desc') }}</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap" style="background:#FFFBEB; color:#D97706;"><i class="fas fa-headset"></i></div>
                    <h3>{{ __('messages.continuous_support') }}</h3>
                    <p>{{ __('messages.continuous_support_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ Analysis Catalog Section ══════════════════ -->
    <section id="analysis" class="section-padding analysis-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.analysis') }}</span>
                <h2 class="section-title">{{ __('messages.available_analyses_list') }}</h2>
                <p class="section-subtitle">{{ app()->getLocale() == 'ar' ? 'ابحث واستكشف كافة الفحوصات والتحاليل المخبرية المتاحة لدينا مع تعليمات الصيام والتحضير' : 'Explorez notre catalogue d\'analyses médicales et conditions de préparation' }}</p>
            </div>

            <!-- Live Filter / Search Input -->
            <div class="catalog-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="analysisSearchInput" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن اسم التحليل (مثل: FNS, Glycémie, Ferritine...)' : 'Rechercher une analyse...' }}">
            </div>

            <div class="analysis-grid" id="mainAnalysisList">
                @foreach($analyses as $analysis)
                <div class="analysis-item-card" data-name="{{ strtolower($analysis->name) }}" data-code="{{ strtolower($analysis->code ?? '') }}">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <i class="fas fa-vial" style="color:var(--primary); font-size:1.1rem;"></i>
                        <span class="analysis-item-title">{{ $analysis->name }}</span>
                    </div>
                    @if($analysis->availability == 1)
                        <span class="status-badge available">{{ __('messages.available') }}</span>
                    @else
                        <span class="status-badge unavailable">{{ __('messages.unavailable') }}</span>
                    @endif
                </div>
                @endforeach
            </div>

            <div style="text-align:center; margin-top:40px;">
                <a href="{{ route('analysis.info') }}" class="btn-secondary-outline">
                    <i class="fas fa-info-circle" style="color:var(--primary);"></i>
                    <span>{{ __('messages.view_analysis_info') }}</span>
                    <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════ Modern Booking Section ══════════════════ -->
    <section id="booking" class="section-padding booking-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.booking') }}</span>
                <h2 class="section-title">{{ __('messages.booking') }}</h2>
                <p class="section-subtitle">{{ __('messages.booking_desc') }}</p>
            </div>

            <div class="booking-card">
                <form id="bookingForm" action="{{ route('booking') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    
                    <div class="booking-form-grid">
                        
                        <!-- Full Name -->
                        <div class="form-group-modern">
                            <label for="name">{{ __('messages.full_name') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-user"></i>
                                <input type="text" id="name" name="name" placeholder="{{ __('messages.full_name') }}" required>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group-modern">
                            <label for="phone">{{ __('messages.phone_number') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" placeholder="05 / 06 / 07 XX XX XX XX" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group-modern">
                            <label for="email">{{ __('messages.email') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="patient@example.com" required>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="form-group-modern">
                            <label for="gender">{{ __('messages.gender') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-venus-mars"></i>
                                <select id="gender" name="gender" required>
                                    <option value="">{{ __('messages.select_gender') }}</option>
                                    <option value="male">{{ __('messages.male') }}</option>
                                    <option value="female">{{ __('messages.female') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Birth Date (Day, Month, Year selects) -->
                        <div class="form-group-modern form-full">
                            <label>{{ __('messages.birth_date') }} <span style="color:#DC2626;">*</span></label>
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                <div class="input-wrap-modern" style="flex:1; min-width:90px;">
                                    <select id="birth_day" required>
                                        <option value="">{{ app()->getLocale() == 'ar' ? 'اليوم' : 'Jour' }}</option>
                                        @for($d=1; $d<=31; $d++)
                                            <option value="{{ str_pad($d,2,'0',STR_PAD_LEFT) }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="input-wrap-modern" style="flex:1.5; min-width:120px;">
                                    <select id="birth_month" required>
                                        <option value="">{{ app()->getLocale() == 'ar' ? 'الشهر' : 'Mois' }}</option>
                                        @php $months = app()->getLocale() == 'ar'
                                            ? ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
                                            : ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
                                        @endphp
                                        @foreach($months as $mi => $mn)
                                            <option value="{{ str_pad($mi+1,2,'0',STR_PAD_LEFT) }}">{{ $mn }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-wrap-modern" style="flex:1.5; min-width:110px;">
                                    <select id="birth_year" required>
                                        <option value="">{{ app()->getLocale() == 'ar' ? 'السنة' : 'Année' }}</option>
                                        @for($y=date('Y'); $y>=1930; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="birth_date" name="birth_date">
                        </div>

                        <!-- OCR Prescription Scan Dropzone -->
                        <div class="form-group-modern form-full">
                            <label><i class="fas fa-wand-magic-sparkles" style="color:var(--primary);"></i> {{ app()->getLocale() == 'ar' ? 'تحميل الوصفة الطبية (مسح ذكي بالـ AI — اختياري)' : 'Scanner l\'ordonnance médicale (IA Optique - Optionnel)' }}</label>
                            <div class="prescription-uploader">
                                <input type="file" id="prescription" name="prescription" accept="image/*">
                                <div class="uploader-content">
                                    <i class="fas fa-file-medical"></i>
                                    <h4>{{ app()->getLocale() == 'ar' ? 'اسحب صورة الوصفة هنا أو اضغط للاختيار' : 'Glissez l\'ordonnance ou cliquez pour téléverser' }}</h4>
                                    <p>{{ app()->getLocale() == 'ar' ? 'يقوم الذكاء الاصطناعي بقراءة الوصفة وتحديد التحاليل المطلوبة تلقائياً' : 'Reconnaissance optique automatique des analyses demandées' }}</p>
                                </div>
                                <div class="ocr-status" id="ocrStatus">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span class="status-text">{{ __('messages.scanning_prescription') }}</span>
                                </div>
                                <div id="ocrFeedback" class="ocr-feedback-box">
                                    <p id="ocrFeedbackText" style="margin:0; color:#1E3A8A; line-height:1.6;"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Analysis Selection Grid -->
                        <div class="form-group-modern form-full">
                            <label>{{ __('messages.analysis_types') }} <span style="color:#DC2626;">*</span></label>
                            <div class="checkbox-modern-grid">
                                @foreach($analyses as $analysis)
                                    @if($analysis->availability == 1)
                                    <div class="checkbox-pill-item">
                                        <input type="checkbox" name="analysisTypes[]" value="{{ $analysis->id }}" id="analysis_{{ $analysis->id }}" 
                                               data-name="{{ $analysis->name }}" data-code="{{ $analysis->code }}" 
                                               data-preparation="{{ $analysis->preparation_instructions }}">
                                        <label for="analysis_{{ $analysis->id }}">{{ $analysis->name }}</label>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <small style="color:var(--slate-500); display:block; margin-top:6px;">
                                <i class="fas fa-info-circle"></i> {{ app()->getLocale() == 'ar' ? 'يمكنك تحديد تحليل واحد أو أكثر للموعد' : 'Vous pouvez sélectionner une ou plusieurs analyses' }}
                            </small>
                        </div>

                        <!-- Preparation Instructions Alert Box -->
                        <div class="form-full" id="preparationBox" style="display:none;">
                            <div class="preparation-box">
                                <h3><i class="fas fa-exclamation-triangle"></i> {{ __('messages.important_preparation_instructions') }}:</h3>
                                <ul id="preparationList"></ul>
                            </div>
                        </div>

                        <!-- Appointment Date -->
                        <div class="form-group-modern">
                            <label for="date">{{ __('messages.date') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-calendar-day"></i>
                                <input type="date" id="date" name="date" required>
                            </div>
                        </div>

                        <!-- Appointment Time -->
                        <div class="form-group-modern">
                            <label for="time">{{ __('messages.time') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-clock"></i>
                                <input type="time" id="time" name="time" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-full" style="margin-top:16px;">
                            <button type="submit" class="btn-confirm-booking">
                                <i class="fas fa-calendar-check"></i>
                                <span>{{ __('messages.confirm_booking') }}</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- ══════════════════ Contact & Location Section ══════════════════ -->
    <section id="contact" class="section-padding" style="background:#fff;">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">{{ __('messages.contact') }}</span>
                <h2 class="section-title">{{ __('messages.contact_desc') }}</h2>
            </div>

            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-card">
                    <h3 style="font-size:1.4rem; font-weight:800; color:var(--slate-900); margin-bottom:20px;">
                        <i class="fas fa-paper-plane" style="color:var(--primary); margin-inline-end:8px;"></i>
                        {{ app()->getLocale() == 'ar' ? 'راسل المخبر مباشرة' : 'Envoyez-nous un message' }}
                    </h3>

                    <form id="contactForm" action="{{ route('message') }}" method="POST" novalidate>
                        @csrf
                        <div class="form-group-modern" style="margin-bottom:16px;">
                            <label for="contact_name">{{ __('messages.name') }}</label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-user"></i>
                                <input type="text" name="name" id="contact_name" placeholder="{{ __('messages.name') }}" required>
                            </div>
                        </div>

                        <div class="form-group-modern" style="margin-bottom:16px;">
                            <label for="contact_email">{{ __('messages.email') }}</label>
                            <div class="input-wrap-modern">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="contact_email" name="email" placeholder="{{ __('messages.email') }}" required>
                            </div>
                        </div>

                        <div class="form-group-modern" style="margin-bottom:24px;">
                            <label for="message">{{ __('messages.message') }}</label>
                            <div class="input-wrap-modern">
                                <textarea id="message" name="message" rows="4" placeholder="{{ __('messages.message') }}" required style="padding-top:14px;"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary-glow" style="width:100%; justify-content:center;">
                            <i class="fas fa-paper-plane"></i>
                            <span>{{ __('messages.send_message') }}</span>
                        </button>
                    </form>
                </div>

                <!-- Google Maps Card -->
                <div class="map-card">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5693.052425601013!2d5.262623025838582!3d31.957933404038677!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x125d69d1688915f9%3A0xc65def288f0e9a57!2sLaboratoire%20Bela%C3%AFd%20d&#39;analyse%20m%C3%A9dical!5e0!3m2!1sen!2sdz!4v1761573361428!5m2!1sen!2sdz" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ Footer ══════════════════ -->
    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>{{ __('messages.lab_name') }}</h3>
                <p>{{ __('messages.footer_about') }}</p>
                <div class="footer-social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>{{ __('messages.quick_links') }}</h4>
                <ul>
                    <li><a href="#home">{{ __('messages.home') }}</a></li>
                    <li><a href="#features">{{ __('messages.features') }}</a></li>
                    <li><a href="#analysis">{{ __('messages.analysis') }}</a></li>
                    <li><a href="#booking">{{ __('messages.booking') }}</a></li>
                    <li><a href="{{ route('analysis.info') }}">{{ __('messages.tips_title') }}</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>{{ app()->getLocale() == 'ar' ? 'البوابات الرقمية' : 'Portails d\'accès' }}</h4>
                <ul>
                    <li><a href="{{ route('access', ['tab' => 'patient']) }}">{{ __('messages.patient_portal') }}</a></li>
                    <li><a href="{{ route('access', ['tab' => 'doctor']) }}">{{ __('messages.physician_portal') }}</a></li>
                    <li><a href="{{ route('access', ['tab' => 'admin']) }}">{{ __('messages.admin_panel') }}</a></li>
                    <li><a href="{{ url('/labnexa_v1.1.4.apk') }}"><i class="fab fa-android"></i> App Android</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>{{ __('messages.contact_info') }}</h4>
                <ul>
                    <li style="color:var(--slate-400); font-size:0.95rem; margin-bottom:12px;">
                        <i class="fas fa-map-marker-alt" style="color:var(--primary); margin-inline-end:8px;"></i>
                        {{ __('messages.address') }}
                    </li>
                    <li style="color:var(--slate-400); font-size:0.95rem; margin-bottom:12px;">
                        <i class="fas fa-phone" style="color:var(--primary); margin-inline-end:8px;"></i>
                        {{ __('messages.phone_val') }}
                    </li>
                    <li style="color:var(--slate-400); font-size:0.95rem; margin-bottom:12px;">
                        <i class="fas fa-envelope" style="color:var(--primary); margin-inline-end:8px;"></i>
                        {{ __('messages.email_val') }}
                    </li>
                    <li style="color:var(--slate-400); font-size:0.95rem;">
                        <i class="fas fa-clock" style="color:var(--primary); margin-inline-end:8px;"></i>
                        {{ __('messages.work_hours') }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>{!! __('messages.all_rights_reserved') !!}</p>
            <p>{{ __('messages.lab_name') }} &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    <!-- Tesseract OCR Library -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

    <!-- Core App Logic -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Navbar scroll effect
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                });
            });
        }

        // Live Filter for Analysis List
        const analysisSearchInput = document.getElementById('analysisSearchInput');
        const mainAnalysisList = document.getElementById('mainAnalysisList');
        if (analysisSearchInput && mainAnalysisList) {
            analysisSearchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                const cards = mainAnalysisList.querySelectorAll('.analysis-item-card');
                cards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const code = card.getAttribute('data-code') || '';
                    if (name.includes(term) || code.includes(term)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Checkboxes & Preparations logic
        const checkboxes = document.querySelectorAll('input[name="analysisTypes[]"]');
        const preparationBox = document.getElementById('preparationBox');
        const preparationList = document.getElementById('preparationList');

        function updatePreparations() {
            if (!preparationList || !preparationBox) return;
            preparationList.innerHTML = '';
            let hasPrep = false;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const prep = cb.getAttribute('data-preparation');
                    if (prep && prep.trim() !== '') {
                        const li = document.createElement('li');
                        li.innerHTML = `<strong>${cb.getAttribute('data-name')}:</strong> ${prep}`;
                        preparationList.appendChild(li);
                        hasPrep = true;
                    }
                }
            });
            preparationBox.style.display = hasPrep ? 'block' : 'none';
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updatePreparations));

        // Combined Birth Date Logic
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
        updateBirthDate();

        // Tesseract OCR Scanning Logic
        const prescriptionInput = document.getElementById('prescription');
        const ocrStatus = document.getElementById('ocrStatus');

        if (prescriptionInput) {
            prescriptionInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const ocrFeedback = document.getElementById('ocrFeedback');
                const ocrFeedbackText = document.getElementById('ocrFeedbackText');

                if (ocrStatus) ocrStatus.style.display = 'block';
                if (ocrFeedback) ocrFeedback.style.display = 'none';

                Tesseract.recognize(file, 'ara+fra+eng').then(({ data: { text } }) => {
                    if (ocrStatus) ocrStatus.style.display = 'none';
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
                            ocrFeedbackText.innerHTML = `<strong>{{ __('messages.prescription_analyzed_success') }}</strong><br><span style="color:#1D4ED8;">&bull; ${uniqueNames.join('<br>&bull; ')}</span><br><br><small>{{ __('messages.download_prep_instructions') }}</small>`;
                            showNotification('{{ __("messages.ocr_success_detected") }}'.replace(':count', foundCount), 'success');
                        } else {
                            ocrFeedbackText.innerHTML = `<i class="fas fa-info-circle" style="color:#2563EB;"></i> {{ __('messages.prescription_manual_verify_notice') }}`;
                            showNotification('{{ __("messages.ocr_no_matches_found") }}', 'info');
                        }
                        updatePreparations();
                    }
                }).catch(err => {
                    console.error("OCR Error:", err);
                    if (ocrStatus) ocrStatus.style.display = 'none';
                    showNotification('{{ __("messages.ocr_processing_error") }}', 'error');
                });
            });
        }

        // Close Preparation Modal
        const closePrepModal = document.getElementById('closePrepModal');
        const prepModalOverlay = document.getElementById('prepModalOverlay');
        if (closePrepModal && prepModalOverlay) {
            closePrepModal.addEventListener('click', () => {
                prepModalOverlay.style.display = 'none';
            });
        }
    });

    // Global Notification Function
    window.showNotification = function(message, type = 'info') {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.textContent = message;
            notification.className = 'notification ' + type;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 6000);
        } else {
            alert(message);
        }
    };

    // Form Submissions & Session Flash handling
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

        // Auto-open PDF Report if download_pdf session exists
        @if(session('download_pdf'))
            try {
                const viewUrl = "{{ route('report.view', ['id' => session('download_res_id'), 'type' => 'request']) }}";
                window.open(viewUrl, '_blank');
                showNotification("{{ __('messages.viewing_prep_instructions') }}", 'success');
            } catch(e) {
                console.error('Report View Error:', e);
            }
        @endif

        // Booking Validation Handler
        const bookingForm = document.getElementById('bookingForm');
        const phoneInput = document.getElementById('phone');
        const dateInput = document.getElementById('date');
        const timeInput = document.getElementById('time');
        const prescriptionInput = document.getElementById('prescription');

        if (bookingForm) {
            const today = new Date().toISOString().split('T')[0];
            if (dateInput) dateInput.setAttribute('min', today);

            bookingForm.addEventListener('submit', function(e) {
                try {
                    let errorMessage = null;

                    // Update combined birth date
                    const day = document.getElementById('birth_day');
                    const month = document.getElementById('birth_month');
                    const year = document.getElementById('birth_year');
                    const hidden = document.getElementById('birth_date');
                    if (day && month && year && hidden && day.value && month.value && year.value) {
                        hidden.value = year.value + '-' + month.value + '-' + day.value;
                    }

                    // 1. Name
                    const nameInput = document.getElementById('name');
                    if (!errorMessage && (!nameInput || !nameInput.value.trim())) {
                        errorMessage = '{{ __('messages.name_required') }}';
                    }

                    // 2. Gender
                    const genderSelect = document.getElementById('gender');
                    if (!errorMessage && (!genderSelect || !genderSelect.value)) {
                        errorMessage = '{{ __('messages.gender_required') }}';
                    }

                    // 3. Birth Date
                    if (!errorMessage && (!hidden || !hidden.value)) {
                        errorMessage = '{{ __('messages.birth_date_required') }}';
                    }

                    // 4. Phone
                    const phoneRegex = /^(05|06|07)[0-9]{8}$/;
                    if (!errorMessage && (!phoneInput || !phoneInput.value.trim())) {
                        errorMessage = '{{ __('messages.phone_required') }}';
                    } else if (!errorMessage && !phoneRegex.test(phoneInput.value.replace(/\s+/g, ''))) {
                        errorMessage = '{{ __('messages.invalid_phone_format') }}';
                    }

                    // 5. Email
                    const emailInput = document.getElementById('email');
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!errorMessage) {
                        if (!emailInput || !emailInput.value.trim()) {
                            errorMessage = '{{ __('messages.email_required') }}';
                        } else {
                            const cleanEmail = emailInput.value.replace(/[\u00A0\u200B\u200C\u200D\u200E\u200F\uFEFF]/g, '').trim();
                            if (!emailRegex.test(cleanEmail)) {
                                errorMessage = '{{ __('messages.invalid_email_format') }}';
                            } else {
                                emailInput.value = cleanEmail;
                            }
                        }
                    }

                    // 6. Analysis or Prescription
                    const checkedCount = this.querySelectorAll('input[name="analysisTypes[]"]:checked').length;
                    const hasPrescription = prescriptionInput && prescriptionInput.files.length > 0;
                    if (!errorMessage && checkedCount === 0 && !hasPrescription) {
                        errorMessage = '{{ __('messages.at_least_one_analysis_or_prescription') }}';
                    }

                    // 7. Date & Time
                    if (!errorMessage && (!dateInput || !dateInput.value)) {
                        errorMessage = '{{ __('messages.date_required') }}';
                    }
                    if (!errorMessage && (!timeInput || !timeInput.value)) {
                        errorMessage = '{{ __('messages.time_required') }}';
                    }

                    // 8. Lead Time Check (14 Hours)
                    if (!errorMessage && dateInput.value && timeInput.value) {
                        const dateParts = dateInput.value.split('-');
                        const timeParts = timeInput.value.split(':');
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

                    if (errorMessage) {
                        e.preventDefault();
                        showNotification(errorMessage, 'error');
                        return;
                    }

                    // Submit spinner
                    const submitBtn = bookingForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        setTimeout(() => {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("messages.processing") }}...';
                        }, 50);
                    }
                } catch(err) {
                    console.error("Validation err:", err);
                }
            });
        }
    });
    </script>

</body>
</html>
