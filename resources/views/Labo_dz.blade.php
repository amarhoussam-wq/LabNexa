<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.lab_name') }} — {{ __('messages.portal_welcome_subtitle') ?? 'Système Médical Intelligent de Diagnostic' }}</title>
    
    <!-- Meta & Branding -->
    <meta name="description" content="LabNexa — المختبر الطبي الذكي الأول. نتائج تحاليل فائقة الدقة في 45 دقيقة، استشارات ذكاء اصطناعي سريرية، وتطبيق خفيف 3MB في جيبك.">
    <meta name="theme-color" content="#0066FF">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* ══════════════════════════════════════════════════════════════════════
           LabNexa — Hyper-Interactive HealthTech Experience
           Inspired by Sunny Money • Precision Medical Design • Fluid Dynamics
        ══════════════════════════════════════════════════════════════════════ */
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --primary-light: #EBF3FF;
            --primary-glow: rgba(0, 102, 255, 0.35);

            --cyan: #06B6D4;
            --cyan-glow: rgba(6, 182, 212, 0.3);
            --emerald: #10B981;
            --emerald-light: #ECFDF5;
            --emerald-glow: rgba(16, 185, 129, 0.3);
            --amber: #F59E0B;
            --amber-light: #FFFBEB;
            --crimson: #EF4444;
            --crimson-light: #FEE2E2;

            --bg-body: #F8FAFC;
            --slate-900: #0B1120;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748B;
            --slate-400: #94A3B8;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;

            --glass-card: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.6);
            --glass-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(226, 232, 240, 0.8);
            --glass-glow: 0 0 50px -10px rgba(0, 102, 255, 0.2);

            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
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
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal', 'Cairo'" : "'Inter', 'Tajawal'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--slate-800);
            overflow-x: hidden;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            position: relative;
        }

        /* ── Subtle Film Grain Overlay ── */
        .grain-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        /* ── Ambient Background Lighting ── */
        .ambient-glow-1 {
            position: fixed;
            top: -200px;
            left: 20%;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.08) 0%, rgba(6, 182, 212, 0.03) 50%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: pulseGlow 12s infinite alternate ease-in-out;
        }
        .ambient-glow-2 {
            position: fixed;
            top: 40%;
            right: -150px;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.07) 0%, rgba(0, 102, 255, 0.03) 50%, transparent 70%);
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
            animation: pulseGlow 15s infinite alternate-reverse ease-in-out;
        }
        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.15) translate(30px, 40px); }
        }

        /* ── Medical Interactive Preloader (#loader) ── */
        .medical-loader {
            position: fixed;
            inset: 0;
            z-index: 100000;
            background: #0B1120;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.6s;
            overflow: hidden;
        }
        .medical-loader.loaded {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .loader-stage {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }
        .loader-orb-wrap {
            position: relative;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loader-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px dashed rgba(0, 102, 255, 0.4);
            animation: spin 10s linear infinite;
        }
        .loader-ring-pulse {
            position: absolute;
            inset: 12px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.4) 0%, rgba(6, 182, 212, 0.15) 60%, transparent 80%);
            animation: pulseOrb 2s infinite ease-in-out;
        }
        .loader-dna-icon {
            font-size: 2.8rem;
            color: #fff;
            filter: drop-shadow(0 0 15px rgba(0, 102, 255, 0.8));
            animation: floatMicro 2s ease-in-out infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes pulseOrb {
            0%, 100% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        @keyframes floatMicro {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .loader-counter {
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: -1px;
            color: #fff;
            font-family: 'Inter', monospace;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }
        .loader-counter span {
            font-size: 1.2rem;
            color: var(--cyan);
        }
        .loader-status-msg {
            font-size: 0.95rem;
            color: var(--slate-400);
            font-weight: 600;
            letter-spacing: 0.5px;
            min-height: 24px;
        }
        .loader-bar {
            width: 220px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 99px;
            overflow: hidden;
            position: relative;
        }
        .loader-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #0066FF, #06B6D4, #10B981);
            border-radius: 99px;
            transition: width 0.15s ease-out;
        }

        /* ── Floating Toast Notification ── */
        .notification {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(-30px);
            opacity: 0;
            padding: 16px 28px;
            border-radius: var(--radius-md);
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            z-index: 999999;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
            backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            gap: 12px;
            pointer-events: none;
            max-width: 90%;
        }
        .notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
        .notification.success { background: linear-gradient(135deg, #059669, #10B981); }
        .notification.error   { background: linear-gradient(135deg, #DC2626, #EF4444); }
        .notification.info    { background: linear-gradient(135deg, #0284C7, #0EA5E9); }

        /* ── Floating Glass Navbar ── */
        .navbar-main {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 48px);
            max-width: 1280px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 100px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
            padding: 10px 24px;
            transition: all 0.3s ease;
        }
        .navbar-main.scrolled {
            top: 10px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.12);
        }
        .nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 900;
            font-size: 1.35rem;
            color: var(--slate-900);
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .brand-logo img {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 102, 255, 0.2);
        }
        .brand-logo .brand-badge {
            font-size: 0.65rem;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 100px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--slate-600);
            font-weight: 700;
            font-size: 0.92rem;
            transition: all 0.2s;
            position: relative;
        }
        .nav-links a:hover {
            color: var(--primary);
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0%;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
            transition: width 0.25s ease;
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sound-toggle-btn {
            background: var(--slate-100);
            border: 1px solid var(--slate-200);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.2s;
        }
        .sound-toggle-btn:hover {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .sound-toggle-btn.active {
            background: var(--primary-light);
            color: var(--primary);
            border-color: rgba(0, 102, 255, 0.3);
        }
        .btn-portal-pill {
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            color: #fff;
            padding: 9px 20px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 0.88rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(0, 102, 255, 0.35);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-portal-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.45);
            color: #fff;
        }
        .btn-app-pill {
            background: #fff;
            border: 1.5px solid var(--slate-200);
            color: var(--slate-800);
            padding: 8px 16px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-app-pill:hover {
            border-color: var(--emerald);
            color: var(--emerald);
            background: var(--emerald-light);
        }
        .btn-lang-pill {
            background: var(--slate-100);
            color: var(--slate-700);
            font-weight: 800;
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 100px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-lang-pill:hover {
            background: var(--slate-200);
            color: var(--slate-900);
        }
        .menu-toggle {
            display: none;
            background: transparent;
            border: none;
            font-size: 1.3rem;
            color: var(--slate-800);
            cursor: pointer;
        }

        /* ── HERO SECTION (#home) ── */
        .hero-section {
            padding-top: 150px;
            padding-bottom: 90px;
            position: relative;
            z-index: 1;
        }
        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 50px;
            align-items: center;
        }
        .hero-tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 102, 255, 0.2);
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.08);
            margin-bottom: 24px;
        }
        .hero-tag-pill .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--emerald);
            box-shadow: 0 0 10px var(--emerald);
            animation: pulseDot 1.5s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }
        .hero-title {
            font-size: 3.6rem;
            font-weight: 900;
            color: var(--slate-900);
            line-height: 1.15;
            letter-spacing: -1.5px;
            margin-bottom: 22px;
        }
        .hero-title .text-gradient {
            background: linear-gradient(135deg, #0066FF 0%, #06B6D4 50%, #10B981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--slate-600);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 580px;
        }
        .hero-cta-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 40px;
        }
        .btn-hero-primary {
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            color: #fff;
            padding: 16px 36px;
            border-radius: 100px;
            font-size: 1.05rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px -5px rgba(0, 102, 255, 0.45);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 40px -5px rgba(0, 102, 255, 0.6);
            color: #fff;
        }
        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.9);
            border: 1.5px solid var(--slate-200);
            color: var(--slate-800);
            padding: 16px 28px;
            border-radius: 100px;
            font-size: 1rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: all 0.25s;
        }
        .btn-hero-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fff;
            transform: translateY(-2px);
        }

        /* Hero Micro Floating Badges */
        .hero-floating-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .micro-badge {
            background: #fff;
            border: 1px solid var(--slate-200);
            padding: 8px 14px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--slate-700);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            transition: transform 0.2s ease;
        }
        .micro-badge:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
        }
        .micro-badge i {
            color: var(--primary);
        }

        /* ── HERO 3D HOLOGRAPHIC PATIENT CARD (Interactive Live Mockup) ── */
        .hero-mockup-wrapper {
            position: relative;
            perspective: 1200px;
        }
        .hologram-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.15), 0 0 50px -10px rgba(0, 102, 255, 0.2);
            padding: 28px;
            transition: transform 0.1s ease-out;
            transform-style: preserve-3d;
            position: relative;
        }
        .mockup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--slate-100);
        }
        .mockup-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .mockup-avatar {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0066FF, #06B6D4);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.3);
        }
        .mockup-patient-name {
            font-weight: 800;
            font-size: 1rem;
            color: var(--slate-900);
            display: block;
        }
        .mockup-patient-id {
            font-size: 0.78rem;
            color: var(--slate-400);
        }
        .mockup-status-tag {
            background: var(--emerald-light);
            color: var(--emerald);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Mockup Interactive Tabs */
        .mockup-tabs {
            display: flex;
            background: var(--slate-100);
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 20px;
            gap: 4px;
        }
        .mockup-tab-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            background: transparent;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--slate-600);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        .mockup-tab-btn.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* Mockup Tab Contents */
        .mockup-content-item {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .mockup-content-item.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Live test rows in mockup */
        .test-metric-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: #fff;
            border: 1px solid var(--slate-100);
            border-radius: var(--radius-sm);
            margin-bottom: 10px;
            transition: transform 0.2s;
        }
        .test-metric-row:hover {
            transform: translateX(-4px);
            border-color: rgba(0, 102, 255, 0.2);
        }
        [dir="ltr"] .test-metric-row:hover { transform: translateX(4px); }
        .test-metric-info strong {
            display: block;
            font-size: 0.9rem;
            color: var(--slate-900);
        }
        .test-metric-info span {
            font-size: 0.75rem;
            color: var(--slate-400);
        }
        .test-metric-val {
            text-align: end;
        }
        .test-metric-val strong {
            font-size: 1.1rem;
            font-weight: 900;
            color: var(--primary);
        }
        .test-metric-val span {
            font-size: 0.75rem;
            color: var(--slate-500);
            margin-inline-start: 4px;
        }

        /* Mockup AI Insight Banner */
        .mockup-ai-card {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #fff;
            padding: 18px;
            border-radius: var(--radius-md);
            margin-top: 16px;
            position: relative;
            overflow: hidden;
        }
        .mockup-ai-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.4) 0%, transparent 70%);
            border-radius: 50%;
        }
        .mockup-ai-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--cyan);
            margin-bottom: 6px;
        }
        .mockup-ai-text {
            font-size: 0.82rem;
            line-height: 1.5;
            color: var(--slate-300);
        }

        /* Floating satellite badges around card */
        .satellite-badge {
            position: absolute;
            background: #fff;
            border: 1px solid var(--slate-200);
            padding: 8px 16px;
            border-radius: 100px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12);
            font-weight: 800;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 3;
            animation: floatSlow 4s ease-in-out infinite;
        }
        .satellite-badge.top-right {
            top: -20px;
            right: -20px;
            color: var(--emerald);
        }
        .satellite-badge.bottom-left {
            bottom: -20px;
            left: -20px;
            color: var(--primary);
            animation-delay: 2s;
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* ── SECTION COMMONS ── */
        .section-wrap {
            padding: 90px 0;
            position: relative;
            z-index: 1;
        }
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .section-header-center {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 55px;
        }
        .section-tag-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-light);
            color: var(--primary);
            padding: 6px 18px;
            border-radius: 100px;
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .section-heading {
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--slate-900);
            letter-spacing: -0.8px;
            line-height: 1.25;
            margin-bottom: 16px;
        }
        .section-subheading {
            font-size: 1.1rem;
            color: var(--slate-600);
            line-height: 1.7;
        }

        /* ── #1: INTERACTIVE BIOMARKER SIMULATOR (#biomarkers) ── */
        .simulator-card {
            background: #fff;
            border-radius: var(--radius-xl);
            border: 1px solid var(--slate-200);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.08);
            padding: 40px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            position: relative;
            overflow: hidden;
        }
        .simulator-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0066FF, #06B6D4, #10B981, #F59E0B);
        }
        .sim-sliders-col {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .sim-slider-group {
            background: var(--slate-50);
            padding: 18px 22px;
            border-radius: var(--radius-md);
            border: 1px solid var(--slate-200);
            transition: all 0.2s;
        }
        .sim-slider-group:hover {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.08);
        }
        .sim-slider-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .sim-slider-name {
            font-weight: 800;
            font-size: 0.98rem;
            color: var(--slate-800);
        }
        .sim-slider-val {
            font-family: 'Inter', monospace;
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--primary);
        }
        .sim-range-input {
            width: 100%;
            height: 6px;
            border-radius: 99px;
            background: var(--slate-200);
            outline: none;
            -webkit-appearance: none;
            cursor: pointer;
        }
        .sim-range-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.5);
            cursor: pointer;
            transition: transform 0.15s;
        }
        .sim-range-input::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }
        .sim-slider-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: var(--slate-400);
            margin-top: 6px;
        }

        /* Simulator Gauge & AI Result Column */
        .sim-gauge-col {
            background: linear-gradient(145deg, #0B1120 0%, #1E293B 100%);
            border-radius: var(--radius-lg);
            padding: 32px;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            position: relative;
            box-shadow: 0 15px 35px rgba(11, 17, 32, 0.3);
        }
        .gauge-svg-wrap {
            position: relative;
            width: 170px;
            height: 170px;
            margin: 10px 0 20px;
        }
        .gauge-svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }
        .gauge-bg {
            fill: none;
            stroke: rgba(255, 255, 255, 0.1);
            stroke-width: 12;
        }
        .gauge-fill {
            fill: none;
            stroke: var(--emerald);
            stroke-width: 12;
            stroke-linecap: round;
            stroke-dasharray: 440;
            stroke-dashoffset: 120;
            transition: stroke-dashoffset 0.4s ease, stroke 0.4s ease;
        }
        .gauge-center-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .gauge-score {
            font-size: 2.4rem;
            font-weight: 900;
            font-family: 'Inter', monospace;
            line-height: 1;
        }
        .gauge-score-label {
            font-size: 0.72rem;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .gauge-status-badge {
            background: rgba(16, 185, 129, 0.15);
            color: var(--emerald);
            border: 1px solid rgba(16, 185, 129, 0.4);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.88rem;
            font-weight: 800;
            margin-bottom: 16px;
            transition: all 0.3s;
        }
        .gauge-diagnosis-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 16px;
            font-size: 0.88rem;
            line-height: 1.6;
            color: var(--slate-300);
            text-align: right;
            margin-bottom: 20px;
            width: 100%;
            min-height: 80px;
        }
        [dir="ltr"] .gauge-diagnosis-box { text-align: left; }
        .btn-sim-book {
            width: 100%;
            background: linear-gradient(135deg, #0066FF, #06B6D4);
            color: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.95rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 102, 255, 0.4);
            transition: all 0.25s;
        }
        .btn-sim-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0, 102, 255, 0.55);
            color: #fff;
        }

        /* ── #2: INTERACTIVE MEDICAL AI CHAT (#ask-ai) ── */
        .chat-showcase-grid {
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            gap: 40px;
            align-items: center;
        }
        .chat-info-left h3 {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--slate-900);
            line-height: 1.25;
            margin-bottom: 18px;
        }
        .chat-info-left p {
            color: var(--slate-600);
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .preset-questions-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .preset-q-btn {
            background: #fff;
            border: 1.5px solid var(--slate-200);
            padding: 14px 20px;
            border-radius: var(--radius-md);
            text-align: right;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--slate-700);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.25s;
        }
        [dir="ltr"] .preset-q-btn { text-align: left; }
        .preset-q-btn:hover, .preset-q-btn.active {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
            transform: translateX(-4px);
        }
        [dir="ltr"] .preset-q-btn:hover, [dir="ltr"] .preset-q-btn.active {
            transform: translateX(4px);
        }

        /* Chat Window Device Mockup */
        .chat-device-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            box-shadow: 0 25px 50px -15px rgba(15, 23, 42, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 480px;
        }
        .chat-device-top {
            background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-ai-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ai-avatar-glow {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0066FF, #06B6D4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            box-shadow: 0 0 15px rgba(0, 102, 255, 0.5);
        }
        .ai-status-online {
            font-size: 0.75rem;
            color: var(--emerald);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .chat-body-stream {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            background: #F8FAFC;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .chat-bubble {
            max-width: 82%;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            font-size: 0.92rem;
            line-height: 1.6;
            animation: fadeIn 0.3s ease;
        }
        .chat-bubble.user {
            align-self: flex-start;
            background: var(--primary);
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        [dir="rtl"] .chat-bubble.user {
            align-self: flex-end;
            border-bottom-right-radius: var(--radius-md);
            border-bottom-left-radius: 4px;
        }
        .chat-bubble.ai {
            align-self: flex-start;
            background: #fff;
            color: var(--slate-800);
            border: 1px solid var(--slate-200);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border-bottom-left-radius: 4px;
        }
        [dir="rtl"] .chat-bubble.ai {
            border-bottom-left-radius: var(--radius-md);
            border-bottom-right-radius: 4px;
        }
        .ai-cert-stamp {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--emerald);
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid var(--slate-100);
        }
        .chat-input-bar {
            padding: 16px 20px;
            background: #fff;
            border-top: 1px solid var(--slate-200);
            display: flex;
            gap: 10px;
        }
        .chat-input-bar input {
            flex: 1;
            padding: 12px 18px;
            border: 1.5px solid var(--slate-200);
            border-radius: 100px;
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .chat-input-bar input:focus {
            border-color: var(--primary);
        }
        .btn-chat-send {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-chat-send:hover {
            transform: scale(1.08);
            background: var(--primary-dark);
        }

        /* ── #3: INTERACTIVE BENTO GRID (#bento) ── */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .bento-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            padding: 34px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s;
        }
        .bento-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px -10px rgba(0, 102, 255, 0.12);
            border-color: rgba(0, 102, 255, 0.3);
        }
        .bento-card.span-2 {
            grid-column: span 2;
        }
        .bento-card.dark-theme {
            background: linear-gradient(135deg, #0B1120 0%, #1E293B 100%);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .bento-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 22px;
        }
        .bento-icon-box.blue { background: var(--primary-light); color: var(--primary); }
        .bento-icon-box.emerald { background: var(--emerald-light); color: var(--emerald); }
        .bento-icon-box.cyan { background: #ECFEFF; color: var(--cyan); }
        .bento-icon-box.amber { background: var(--amber-light); color: var(--amber); }

        .bento-title {
            font-size: 1.45rem;
            font-weight: 900;
            margin-bottom: 12px;
            color: var(--slate-900);
        }
        .bento-card.dark-theme .bento-title { color: #fff; }
        .bento-desc {
            color: var(--slate-600);
            font-size: 0.98rem;
            line-height: 1.65;
            margin-bottom: 20px;
        }
        .bento-card.dark-theme .bento-desc { color: var(--slate-300); }

        /* Bento Interactive Elements */
        .stopwatch-counter {
            display: flex;
            align-items: baseline;
            gap: 6px;
            font-family: 'Inter', monospace;
            font-size: 3rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 8px;
        }
        .stopwatch-counter span {
            font-size: 1.2rem;
            color: var(--slate-500);
        }
        .bento-progress-track {
            height: 8px;
            background: var(--slate-100);
            border-radius: 99px;
            overflow: hidden;
            margin-top: 14px;
        }
        .bento-progress-fill {
            height: 100%;
            width: 85%;
            background: linear-gradient(90deg, #0066FF, #10B981);
            border-radius: 99px;
            animation: pulseProgress 3s infinite alternate;
        }
        @keyframes pulseProgress {
            0% { width: 75%; }
            100% { width: 92%; }
        }

        /* ── #4: HOW IT WORKS (#how) ── */
        .steps-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            position: relative;
        }
        .step-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 36px 30px;
            position: relative;
            transition: all 0.3s;
        }
        .step-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px -15px rgba(0, 102, 255, 0.15);
        }
        .step-num {
            position: absolute;
            top: 24px;
            right: 28px;
            font-family: 'Inter', monospace;
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--slate-100);
            line-height: 1;
            transition: color 0.3s;
        }
        [dir="ltr"] .step-num { right: auto; left: 28px; }
        .step-card:hover .step-num {
            color: var(--primary-light);
        }
        .step-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 24px;
        }
        .step-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 12px;
        }
        .step-desc {
            color: var(--slate-600);
            font-size: 0.95rem;
            line-height: 1.65;
        }

        /* ── #5: ULTRA-REFINED BOOKING SECTION (#booking) ── */
        .booking-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.08);
            padding: 48px;
            position: relative;
        }
        .booking-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 28px;
        }
        .form-group-full {
            grid-column: span 2;
        }
        .form-label-modern {
            display: block;
            font-weight: 800;
            font-size: 0.92rem;
            color: var(--slate-800);
            margin-bottom: 8px;
        }
        .input-wrap-icon {
            position: relative;
        }
        .input-wrap-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 18px;
            color: var(--slate-400);
            font-size: 1.05rem;
        }
        [dir="rtl"] .input-wrap-icon i {
            left: auto;
            right: 18px;
        }
        .input-wrap-icon input,
        .input-wrap-icon select,
        .input-wrap-icon textarea {
            width: 100%;
            padding: 14px 18px 14px 48px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--slate-200);
            background: var(--slate-50);
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--slate-800);
            outline: none;
            transition: all 0.2s;
        }
        [dir="rtl"] .input-wrap-icon input,
        [dir="rtl"] .input-wrap-icon select,
        [dir="rtl"] .input-wrap-icon textarea {
            padding: 14px 48px 14px 18px;
        }
        .input-wrap-icon input:focus,
        .input-wrap-icon select:focus,
        .input-wrap-icon textarea:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        /* Analysis Search & Pills */
        .analysis-selection-box {
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 18px;
            background: var(--slate-50);
        }
        .analysis-search-input {
            width: 100%;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--slate-200);
            font-family: inherit;
            font-size: 0.9rem;
            margin-bottom: 14px;
            outline: none;
            background: #fff;
        }
        .analysis-checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 10px;
            max-height: 250px;
            overflow-y: auto;
            padding-right: 4px;
        }
        [dir="rtl"] .analysis-checkbox-grid { padding-right: 0; padding-left: 4px; }
        .analysis-pill-label {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid var(--slate-200);
            cursor: pointer;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--slate-700);
            transition: all 0.2s;
        }
        .analysis-pill-label:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .analysis-pill-label input {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Prescription File Upload */
        .prescription-dropzone {
            border: 2px dashed rgba(0, 102, 255, 0.4);
            background: rgba(0, 102, 255, 0.02);
            border-radius: var(--radius-md);
            padding: 26px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            position: relative;
        }
        .prescription-dropzone:hover {
            background: rgba(0, 102, 255, 0.05);
            border-color: var(--primary);
        }
        .prescription-dropzone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .btn-submit-booking {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            color: #fff;
            border: none;
            border-radius: 100px;
            font-family: inherit;
            font-size: 1.1rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-submit-booking:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 15px 40px rgba(0, 102, 255, 0.55);
        }

        /* ── FOOTER ── */
        .site-footer {
            background: #0B1120;
            color: #fff;
            padding: 80px 0 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }
        .footer-brand p {
            color: var(--slate-400);
            font-size: 0.95rem;
            line-height: 1.7;
            margin: 18px 0 24px;
            max-width: 320px;
        }
        .footer-col h4 {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: #fff;
        }
        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .footer-col a {
            color: var(--slate-400);
            text-decoration: none;
            font-size: 0.92rem;
            transition: color 0.2s;
        }
        .footer-col a:hover {
            color: #fff;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--slate-400);
            font-size: 0.88rem;
        }

        /* ── RESPONSIVE RULES ── */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            .simulator-card {
                grid-template-columns: 1fr;
            }
            .chat-showcase-grid {
                grid-template-columns: 1fr;
            }
            .bento-grid {
                grid-template-columns: 1fr;
            }
            .bento-card.span-2 {
                grid-column: span 1;
            }
            .steps-container {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .navbar-main {
                width: calc(100% - 24px);
                top: 12px;
                padding: 10px 18px;
            }
            .nav-links {
                display: none;
            }
            .hero-title {
                font-size: 2.6rem;
            }
            .booking-card {
                padding: 24px;
            }
            .booking-form-grid {
                grid-template-columns: 1fr;
            }
            .form-group-full {
                grid-column: span 1;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 14px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- Ambient Visual Glows & Grain -->
    <div class="grain-overlay"></div>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- ══════════════════ 1. PRELOADER & WARMUP ══════════════════ -->
    <div class="medical-loader" id="medicalLoader">
        <div class="loader-stage">
            <div class="loader-orb-wrap">
                <div class="loader-ring"></div>
                <div class="loader-ring-pulse"></div>
                <i class="fas fa-microscope loader-dna-icon"></i>
            </div>
            <div class="loader-counter">
                <span id="loadPct">0</span><span>%</span>
            </div>
            <div class="loader-status-msg" id="loadMsg">
                {{ app()->getLocale() == 'ar' ? 'معايرة كواشف التحاليل المخبرية...' : 'Calibration des réactifs d\'analyses...' }}
            </div>
            <div class="loader-bar">
                <div class="loader-bar-fill" id="loadBar"></div>
            </div>
        </div>
    </div>

    <!-- Floating Notification -->
    <div id="notificationToast" class="notification">
        <i class="fas fa-circle-check" id="notifIcon"></i>
        <span id="notifText"></span>
    </div>

    <!-- ══════════════════ 2. FLOATING GLASS NAVBAR ══════════════════ -->
    <nav class="navbar-main" id="navbar">
        <div class="nav-container">
            <a href="#home" class="brand-logo" title="{{ __('messages.lab_name') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                <span>{{ __('messages.lab_name') }}</span>
                <span class="brand-badge">AI LAB</span>
            </a>

            <ul class="nav-links">
                <li><a href="#biomarkers">{{ app()->getLocale() == 'ar' ? 'المحاكي التفاعلي' : 'Simulateur' }}</a></li>
                <li><a href="#ask-ai">{{ app()->getLocale() == 'ar' ? 'استشر الذكاء الاصطناعي' : 'LabNexa AI' }}</a></li>
                <li><a href="#bento">{{ app()->getLocale() == 'ar' ? 'الميزات' : 'Fonctionnalités' }}</a></li>
                <li><a href="#how">{{ app()->getLocale() == 'ar' ? 'كيف يعمل؟' : 'Comment ça marche' }}</a></li>
                <li><a href="#booking">{{ __('messages.booking') }}</a></li>
            </ul>

            <div class="nav-actions">
                <!-- Sound Toggle Pill -->
                <button class="sound-toggle-btn" id="soundToggle" title="{{ app()->getLocale() == 'ar' ? 'تفعيل المؤثرات الصوتية' : 'Effets sonores' }}">
                    <i class="fas fa-volume-high" id="soundIcon"></i>
                </button>

                <!-- App Download Pill -->
                <a href="{{ url('/labnexa_v1.1.4.apk') }}" class="btn-app-pill">
                    <i class="fab fa-android text-emerald"></i>
                    <span>3MB App</span>
                </a>

                <!-- Portal Login Button -->
                <a href="{{ route('access') }}" class="btn-portal-pill">
                    <i class="fas fa-shield-halved"></i>
                    <span>{{ __('messages.patient_portal') }}</span>
                </a>

                <!-- Language Switcher -->
                <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'fr' : 'ar') }}" class="btn-lang-pill">
                    {{ app()->getLocale() == 'ar' ? 'FR' : 'العربية' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- ══════════════════ 3. HERO SECTION ══════════════════ -->
    <section id="home" class="hero-section">
        <div class="hero-container">
            
            <!-- Left: Hero Text & Badges -->
            <div class="hero-content-col">
                <div class="hero-tag-pill">
                    <span class="live-dot"></span>
                    <span>{{ app()->getLocale() == 'ar' ? 'الجيل القادم من التحاليل الطبية الذكية' : 'Nouvelle Génération de Diagnostics Médicaux' }}</span>
                </div>

                <h1 class="hero-title">
                    {{ app()->getLocale() == 'ar' ? 'صحتك، بدقة متناهية' : 'Votre Santé, Précision Maximale' }}<br>
                    <span class="text-gradient">{{ app()->getLocale() == 'ar' ? 'وبساطة غير مسبوقة.' : 'Et Clarté Absolue.' }}</span>
                </h1>

                <p class="hero-subtitle">
                    {{ app()->getLocale() == 'ar' 
                        ? 'مخبر LabNexa يجمع بين أدق الفحوصات البيولوجية المعتمدة ومحرك الذكاء الاصطناعي السريري. نتائجك واضحة كأرقام فورية على هاتفك في أقل من 45 دقيقة.' 
                        : 'LabNexa réinvente le laboratoire d\'analyses médicales : résultats instantanés en 45 minutes, interprétation clinique par IA et application ultra-légère.' 
                    }}
                </p>

                <div class="hero-cta-group">
                    <a href="#booking" class="btn-hero-primary">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'احجز تحليلك الآن' : 'Réserver une analyse' }}</span>
                    </a>
                    <a href="#biomarkers" class="btn-hero-secondary">
                        <i class="fas fa-sliders text-primary"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'جرّب المحاكي الحي' : 'Tester le simulateur' }}</span>
                    </a>
                </div>

                <!-- Floating Micro Feature Tags -->
                <div class="hero-floating-badges">
                    <div class="micro-badge"><i class="fas fa-bolt text-amber"></i> {{ app()->getLocale() == 'ar' ? 'نتائج في 45 دقيقة' : 'Résultats en 45 min' }}</div>
                    <div class="micro-badge"><i class="fas fa-certificate text-emerald"></i> {{ app()->getLocale() == 'ar' ? 'معتمد ISO 15189' : 'Certifié ISO 15189' }}</div>
                    <div class="micro-badge"><i class="fas fa-mobile-screen-button text-primary"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق خفيف 3MB' : 'App ultra-légère 3MB' }}</div>
                </div>
            </div>

            <!-- Right: 3D Holographic Patient Card (Interactive Live Mockup) -->
            <div class="hero-mockup-wrapper">
                
                <!-- Satellite Badges -->
                <div class="satellite-badge top-right">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'نتائج اليوم جاهزة' : 'Résultats Prêts' }}</span>
                </div>
                <div class="satellite-badge bottom-left">
                    <i class="fas fa-robot"></i>
                    <span>AI Doctor Verified</span>
                </div>

                <div class="hologram-card" id="hologramCard">
                    <!-- Mockup Header -->
                    <div class="mockup-header">
                        <div class="mockup-user">
                            <div class="mockup-avatar"><i class="fas fa-user-injured"></i></div>
                            <div>
                                <span class="mockup-patient-name">يوسف بن علي</span>
                                <span class="mockup-patient-id">Dossier #LAB-2026-984</span>
                            </div>
                        </div>
                        <div class="mockup-status-tag">
                            <i class="fas fa-circle"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'معتمد' : 'Validé' }}</span>
                        </div>
                    </div>

                    <!-- Mockup Interactive Tabs -->
                    <div class="mockup-tabs">
                        <button class="mockup-tab-btn active" data-tab="live-tests">{{ app()->getLocale() == 'ar' ? 'التحاليل الحية' : 'Analyses' }}</button>
                        <button class="mockup-tab-btn" data-tab="ai-insight">{{ app()->getLocale() == 'ar' ? 'تفسير الذكاء الاصطناعي' : 'Diagnostic IA' }}</button>
                    </div>

                    <!-- Tab 1: Live Tests -->
                    <div class="mockup-content-item active" id="tab-live-tests">
                        <div class="test-metric-row">
                            <div class="test-metric-info">
                                <strong>Glycémie à jeun (سكر الدم)</strong>
                                <span>Ref: 0.70 – 1.10 g/L</span>
                            </div>
                            <div class="test-metric-val">
                                <strong>0.92</strong>
                                <span>g/L</span>
                            </div>
                        </div>

                        <div class="test-metric-row">
                            <div class="test-metric-info">
                                <strong>Vitamine D (25-OH)</strong>
                                <span>Ref: 30 – 100 ng/mL</span>
                            </div>
                            <div class="test-metric-val">
                                <strong style="color:var(--amber);">24.5</strong>
                                <span>ng/mL</span>
                            </div>
                        </div>

                        <div class="test-metric-row">
                            <div class="test-metric-info">
                                <strong>Cholestérol Total (الكوليسترول)</strong>
                                <span>Ref: < 2.00 g/L</span>
                            </div>
                            <div class="test-metric-val">
                                <strong>1.74</strong>
                                <span>g/L</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: AI Clinical Insight -->
                    <div class="mockup-content-item" id="tab-ai-insight">
                        <div class="mockup-ai-card">
                            <div class="mockup-ai-title">
                                <i class="fas fa-brain"></i>
                                <span>LabNexa Clinical AI Engine</span>
                            </div>
                            <p class="mockup-ai-text">
                                {{ app()->getLocale() == 'ar'
                                    ? 'المؤشرات الأيضية ممتازة (استقرار السكر والكولسترول). يُلاحظ نقص طفيف في فيتامين د (24.5 ng/mL). يُنصح بالتعرض للشمس الصباحية ومراجعة الطبيب لوصف جرعة وقائية.'
                                    : 'Paramètres métaboliques parfaits. Déficit léger en Vitamine D (24.5 ng/mL) à supplémenter sous avis médical.'
                                }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ══════════════════ 4. INTERACTIVE BIOMARKER SIMULATOR (#biomarkers) ══════════════════ -->
    <section id="biomarkers" class="section-wrap" style="background: #fff; border-top: 1px solid var(--slate-200); border-bottom: 1px solid var(--slate-200);">
        <div class="container">
            
            <div class="section-header-center">
                <span class="section-tag-pill">
                    <i class="fas fa-sliders"></i>
                    {{ app()->getLocale() == 'ar' ? 'محاكي تفاعلي حي' : 'Simulateur Interactif' }}
                </span>
                <h2 class="section-heading">
                    {{ app()->getLocale() == 'ar' ? 'حرّك المؤشرات وشاهد كيف يحلل الذكاء الاصطناعي صحتك' : 'Ajustez les biomarqueurs en direct' }}
                </h2>
                <p class="section-subheading">
                    {{ app()->getLocale() == 'ar'
                        ? 'جرّب بنفسك: غيّر قيم السكر، الكولسترول، وفيتامين د لمشاهدة تشخيص الخوارزمية الفوري وتغيّر مؤشر الأمان في الوقت الحقيقي.'
                        : 'Simulez vos résultats en temps réel. Obtenez instantanément une évaluation clinique claire avant votre visite au laboratoire.'
                    }}
                </p>
            </div>

            <div class="simulator-card">
                
                <!-- Left: Sliders Controls -->
                <div class="sim-sliders-col">
                    
                    <!-- Slider 1: Glycemia -->
                    <div class="sim-slider-group">
                        <div class="sim-slider-head">
                            <span class="sim-slider-name"><i class="fas fa-droplet text-primary me-1"></i> {{ app()->getLocale() == 'ar' ? 'السكر الصائم (Glycémie)' : 'Glycémie à jeun' }}</span>
                            <span class="sim-slider-val" id="valGlycemia">0.95 g/L</span>
                        </div>
                        <input type="range" class="sim-range-input" id="rangeGlycemia" min="0.60" max="2.60" step="0.05" value="0.95">
                        <div class="sim-slider-meta">
                            <span>0.60 g/L (هبوط)</span>
                            <span>المعدل الطبيعي: 0.70 - 1.10</span>
                            <span>2.60 g/L (ارتفاع)</span>
                        </div>
                    </div>

                    <!-- Slider 2: Cholesterol LDL -->
                    <div class="sim-slider-group">
                        <div class="sim-slider-head">
                            <span class="sim-slider-name"><i class="fas fa-heart-pulse text-amber me-1"></i> {{ app()->getLocale() == 'ar' ? 'الكولسترول الضار (LDL)' : 'Cholestérol LDL' }}</span>
                            <span class="sim-slider-val" id="valLdl">1.10 g/L</span>
                        </div>
                        <input type="range" class="sim-range-input" id="rangeLdl" min="0.80" max="2.60" step="0.05" value="1.10">
                        <div class="sim-slider-meta">
                            <span>0.80 g/L (مثالي)</span>
                            <span>المعدل الطبيعي: < 1.30</span>
                            <span>2.60 g/L (حرج)</span>
                        </div>
                    </div>

                    <!-- Slider 3: Vitamin D -->
                    <div class="sim-slider-group">
                        <div class="sim-slider-head">
                            <span class="sim-slider-name"><i class="fas fa-sun text-amber me-1"></i> {{ app()->getLocale() == 'ar' ? 'فيتامين د (Vitamine D 25-OH)' : 'Vitamine D 25-OH' }}</span>
                            <span class="sim-slider-val" id="valVitD">38 ng/mL</span>
                        </div>
                        <input type="range" class="sim-range-input" id="rangeVitD" min="8" max="80" step="1" value="38">
                        <div class="sim-slider-meta">
                            <span>8 ng/mL (نقص حاد)</span>
                            <span>المعدل الطبيعي: 30 - 80</span>
                            <span>80 ng/mL</span>
                        </div>
                    </div>

                </div>

                <!-- Right: Live SVG Gauge & AI Verdict -->
                <div class="sim-gauge-col">
                    
                    <span style="font-size:0.85rem; font-weight:800; color:var(--cyan); letter-spacing:1px; text-transform:uppercase;">
                        {{ app()->getLocale() == 'ar' ? 'مؤشر التوازن الحيوي' : 'Indice de Vitalité' }}
                    </span>

                    <!-- Circular Progress Gauge -->
                    <div class="gauge-svg-wrap">
                        <svg class="gauge-svg" viewBox="0 0 160 160">
                            <circle class="gauge-bg" cx="80" cy="80" r="70"></circle>
                            <circle class="gauge-fill" id="gaugeFill" cx="80" cy="80" r="70"></circle>
                        </svg>
                        <div class="gauge-center-text">
                            <div class="gauge-score" id="gaugeScore">94</div>
                            <div class="gauge-score-label">/ 100</div>
                        </div>
                    </div>

                    <div class="gauge-status-badge" id="gaugeBadge">
                        <i class="fas fa-circle-check me-1"></i> <span>مؤشرات مثالية تماماً</span>
                    </div>

                    <div class="gauge-diagnosis-box" id="gaugeDiagnosis">
                        جميع المؤشرات الحيوية المختارة ضمن المجال الفسيولوجي المرجعي. لا توجد عوامل خطر استقلابية.
                    </div>

                    <a href="#booking" class="btn-sim-book" id="simBookBtn">
                        <i class="fas fa-calendar-plus"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'احجز هذه التحاليل الآن' : 'Réserver ce bilan' }}</span>
                    </a>

                </div>

            </div>

        </div>
    </section>

    <!-- ══════════════════ 5. INTERACTIVE MEDICAL AI CHAT (#ask-ai) ══════════════════ -->
    <section id="ask-ai" class="section-wrap">
        <div class="container">
            
            <div class="chat-showcase-grid">
                
                <!-- Left: Question Presets & Explanations -->
                <div class="chat-info-left">
                    <span class="section-tag-pill">
                        <i class="fas fa-robot"></i>
                        {{ app()->getLocale() == 'ar' ? 'استشارة سريرية تفاعلية' : 'Consultation Clinique IA' }}
                    </span>
                    <h3>{{ app()->getLocale() == 'ar' ? 'اسأل LabNexa AI أي استفسار حول تحاليلك' : 'Posez vos questions à notre IA Médicale' }}</h3>
                    <p>
                        {{ app()->getLocale() == 'ar'
                            ? 'هل تحتار في شروط الصيام؟ أو ترغب في فهم دلالة فحص معين قبل إجرائه؟ اضغط على الأسئلة الشائعة أو اكتب سؤالك لتشاهد الإجابة اللحظية.'
                            : 'Conditions de jeûne, délais des résultats, signification des bilans... Obtenez des réponses cliniques instantanées et certifiées.'
                        }}
                    </p>

                    <div class="preset-questions-group">
                        <button class="preset-q-btn active" data-q="fasting">
                            <span>🧪 {{ app()->getLocale() == 'ar' ? 'هل يجب الصيام قبل فحص دهنيات الدم (Lipid Profile)؟' : 'Faut-il être à jeun pour un bilan lipidique ?' }}</span>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <button class="preset-q-btn" data-q="tsh">
                            <span>⚡ {{ app()->getLocale() == 'ar' ? 'كم يستغرق خروج تحليل الغدة الدرقية TSH؟' : 'Quel est le délai pour le dosage de la TSH ?' }}</span>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <button class="preset-q-btn" data-q="mobile">
                            <span>📱 {{ app()->getLocale() == 'ar' ? 'كيف تصلني نتائجي على الهاتف بدون الرجوع للمخبر؟' : 'Comment recevoir mes résultats sur mobile ?' }}</span>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                </div>

                <!-- Right: Interactive Live Chat Box -->
                <div class="chat-device-card">
                    <div class="chat-device-top">
                        <div class="chat-ai-profile">
                            <div class="ai-avatar-glow"><i class="fas fa-brain"></i></div>
                            <div>
                                <strong style="font-size:0.95rem; display:block;">LabNexa AI Assistant</strong>
                                <span class="ai-status-online"><i class="fas fa-circle" style="font-size:0.5rem;"></i> متصل ومستعد للإجابة</span>
                            </div>
                        </div>
                        <span class="brand-badge" style="background:rgba(255,255,255,0.15); color:#fff;">ISO 15189</span>
                    </div>

                    <div class="chat-body-stream" id="chatStream">
                        <div class="chat-bubble user" id="chatUserMsg">
                            هل يجب الصيام قبل فحص دهنيات الدم (Lipid Profile)؟
                        </div>
                        <div class="chat-bubble ai" id="chatAiMsg">
                            نعم، يتطلب فحص دهنيات الدم (الكولسترول، الشحوم الثلاثية TG) الصيام التام عن الطعام لمدة <strong>10 إلى 12 ساعة</strong> قبل أخذ العينة. يُسمح بشرب الماء النقي فقط لضمان دقة قياس الدهون.
                            <div class="ai-cert-stamp">
                                <i class="fas fa-circle-check"></i>
                                <span>مراجعة مخبرية مصادقة • البروتوكول السريري المعتمد</span>
                            </div>
                        </div>
                    </div>

                    <div class="chat-input-bar">
                        <input type="text" id="customChatInput" placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب سؤالك هنا وجرب...' : 'Posez une question...' }}">
                        <button class="btn-chat-send" id="chatSendBtn" title="إرسال">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ══════════════════ 6. INTERACTIVE BENTO GRID (#bento) ══════════════════ -->
    <section id="bento" class="section-wrap" style="background: #fff; border-top: 1px solid var(--slate-200); border-bottom: 1px solid var(--slate-200);">
        <div class="container">
            
            <div class="section-header-center">
                <span class="section-tag-pill">
                    <i class="fas fa-cubes"></i>
                    {{ app()->getLocale() == 'ar' ? 'منظومة مخبرية متكاملة' : 'Architecture Intelligente' }}
                </span>
                <h2 class="section-heading">
                    {{ app()->getLocale() == 'ar' ? 'صُممت لتمنحك أسرع وأدق تجربة طبية' : 'Conçue pour une précision absolue' }}
                </h2>
                <p class="section-subheading">
                    {{ app()->getLocale() == 'ar'
                        ? 'تكامل فريد يجمع بين أتمتة التحاليل الروبوتية، السرعة القياسية، وتطبيق هاتف فائق الخفة يغنيك عن الانتظار.'
                        : 'Automatisation robotique, délais records en 45 minutes et application mobile de 3MB.'
                    }}
                </p>
            </div>

            <div class="bento-grid">
                
                <!-- Card 1: 45 Minutes Results (Span 2) -->
                <div class="bento-card span-2">
                    <div class="bento-icon-box blue"><i class="fas fa-stopwatch"></i></div>
                    <div class="stopwatch-counter">
                        <span id="bentoCounter">45</span>
                        <span>{{ app()->getLocale() == 'ar' ? 'دقيقة فقط' : 'min chrono' }}</span>
                    </div>
                    <h3 class="bento-title">{{ app()->getLocale() == 'ar' ? 'السرعة القياسية لصدور النتائج' : 'Vitesse record d\'analyses' }}</h3>
                    <p class="bento-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'بفضل أجهزة التحليل الروبوتية الآلية المتصلة مباشرة بالمنظومة، تصدر أغلب الفحوصات الروتينية (NFS، Glycémie، Urée، Créat) ومصادقتها في أقل من ساعة.'
                            : 'Grâce à l\'interfaçage automatisé de nos automates d\'analyses, vos résultats de routine sont prêts et certifiés en 45 minutes.'
                        }}
                    </p>
                    <div class="bento-progress-track">
                        <div class="bento-progress-fill"></div>
                    </div>
                </div>

                <!-- Card 2: 3MB Light App (Span 1) -->
                <div class="bento-card dark-theme">
                    <div class="bento-icon-box cyan" style="background:rgba(6, 182, 212, 0.15);"><i class="fab fa-android"></i></div>
                    <h3 class="bento-title">3MB Mobile App</h3>
                    <p class="bento-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'مثل تطبيق HayatLink: تطبيق فائق السرعة، صغير الحجم، لا يثقل ذاكرة هاتفك. حجز المواعيد ورؤية النتائج كأرقام واضحة بضغطة زر.'
                            : 'Application ultra-rapide de 3MB. Prise de rendez-vous et consultation des résultats en temps réel.'
                        }}
                    </p>
                    <a href="{{ url('/labnexa_v1.1.4.apk') }}" class="btn-app-pill" style="border:none; background:var(--primary); color:#fff; width:100%; justify-content:center;">
                        <i class="fas fa-download"></i> {{ app()->getLocale() == 'ar' ? 'تحميل APK (3MB)' : 'Télécharger l\'APK' }}
                    </a>
                </div>

                <!-- Card 3: Smart Eligibility Protocol -->
                <div class="bento-card">
                    <div class="bento-icon-box emerald"><i class="fas fa-clipboard-check"></i></div>
                    <h3 class="bento-title">{{ app()->getLocale() == 'ar' ? 'فحص الأهلية السريرية' : 'Diagnostic d\'Éligibilité' }}</h3>
                    <p class="bento-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'نظام يضمن سلامة العينة قبل سحبها؛ يطابق ساعات صيامك، أدويتك، وحالتك الصحية مع متطلبات كل فحص لتفادي إعادة التحليل.'
                            : 'Validation pré-analytique rigoureuse des conditions de prélèvement pour garantir l\'exactitude des résultats.'
                        }}
                    </p>
                </div>

                <!-- Card 4: Critical Alerts & Instant Notifications -->
                <div class="bento-card">
                    <div class="bento-icon-box amber"><i class="fas fa-bell"></i></div>
                    <h3 class="bento-title">{{ app()->getLocale() == 'ar' ? 'تنبيهات القيم الحرجة' : 'Alertes Valeurs Critiques' }}</h3>
                    <p class="bento-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'إذا ظهرت أي نتيجة خارج النطاق الآمن بشكل حاد، يتم إشعارك وطبيبك المعالج فوراً عبر رسالة نصية وواتساب للتدخل السريع.'
                            : 'Notification immédiate par SMS en cas d\'anomalie biologique nécessitant une prise en charge rapide.'
                        }}
                    </p>
                </div>

                <!-- Card 5: Hospital-Grade Security & Privacy -->
                <div class="bento-card">
                    <div class="bento-icon-box blue"><i class="fas fa-shield-virus"></i></div>
                    <h3 class="bento-title">{{ app()->getLocale() == 'ar' ? 'تشفير وحماية سرية' : 'Sécurité Médicale ISO' }}</h3>
                    <p class="bento-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'تشفير سحابي عالي الأمان لجميع سجلاتك الطبية وتقاريرك مع رمز QR محمي للتحقق من أصالة كل وثيقة رسمية.'
                            : 'Cryptage de niveau bancaire de vos données médicales avec vérification QR-Code infalsifiable.'
                        }}
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ══════════════════ 7. HOW IT WORKS (#how) ══════════════════ -->
    <section id="how" class="section-wrap">
        <div class="container">
            
            <div class="section-header-center">
                <span class="section-tag-pill">
                    <i class="fas fa-route"></i>
                    {{ app()->getLocale() == 'ar' ? 'رحلة الفحص' : 'Parcours Patient' }}
                </span>
                <h2 class="section-heading">
                    {{ app()->getLocale() == 'ar' ? '3 خطوات بسيطة للحصول على تحاليلك' : '3 étapes simples pour vos analyses' }}
                </h2>
                <p class="section-subheading">
                    {{ app()->getLocale() == 'ar'
                        ? 'ودّع الانتظار الطوابير المرهقة، وابدأ تجربة صحية رقمية استثنائية تبدأ من هاتفك.'
                        : 'Prenez rendez-vous en ligne, effectuez votre prélèvement sans attente et suivez tout sur mobile.'
                    }}
                </p>
            </div>

            <div class="steps-container">
                
                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-num">01</div>
                    <div class="step-icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                    <h3 class="step-title">{{ app()->getLocale() == 'ar' ? 'احجز موعدك أونلاين' : '1. Réservation en ligne' }}</h3>
                    <p class="step-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'اختر التحاليل المطلوبة أو ارفع صورة وصفتك الطبية، وحدد التاريخ والوقت الأنسب لك في أقل من دقيقة.'
                            : 'Sélectionnez vos analyses ou téléchargez votre ordonnance. Choisissez l\'horaire qui vous convient.'
                        }}
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-num">02</div>
                    <div class="step-icon-wrap" style="background:var(--emerald-light); color:var(--emerald);"><i class="fas fa-syringe"></i></div>
                    <h3 class="step-title">{{ app()->getLocale() == 'ar' ? 'سحب العينة براحة وسرعة' : '2. Prélèvement express' }}</h3>
                    <p class="step-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'استقبال مباشر بدون انتظار بواسطة طاقم بيولوجي معتمد ومعدات تعقيم ذات معايير دولية فائقة الدقة.'
                            : 'Accueil prioritaire dans notre laboratoire par une équipe médicale qualifiée et bienveillante.'
                        }}
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-num">03</div>
                    <div class="step-icon-wrap" style="background:#ECFEFF; color:var(--cyan);"><i class="fas fa-file-waveform"></i></div>
                    <h3 class="step-title">{{ app()->getLocale() == 'ar' ? 'النتائج كأرقام فورية' : '3. Résultats numériques instantanés' }}</h3>
                    <p class="step-desc">
                        {{ app()->getLocale() == 'ar'
                            ? 'تصلك النتائج على هاتفك وتطبيق الموبايل، مشروحة كأرقام وجداول واضحة مدعومة بتحليل الذكاء الاصطناعي.'
                            : 'Consultez vos résultats détaillés sur mobile avec interprétation clinique et téléchargement PDF officiel.'
                        }}
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ══════════════════ 8. ULTRA-REFINED BOOKING SECTION (#booking) ══════════════════ -->
    <section id="booking" class="section-wrap" style="background: #fff; border-top: 1px solid var(--slate-200);">
        <div class="container">
            
            <div class="section-header-center">
                <span class="section-tag-pill">
                    <i class="fas fa-calendar-check"></i>
                    {{ __('messages.booking') }}
                </span>
                <h2 class="section-heading">
                    {{ __('messages.booking') }}
                </h2>
                <p class="section-subheading">
                    {{ __('messages.booking_desc') }}
                </p>
            </div>

            <div class="booking-card">
                <form id="bookingForm" action="{{ route('booking') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    
                    <div class="booking-form-grid">
                        
                        <!-- Full Name -->
                        <div class="form-group-item">
                            <label class="form-label-modern" for="name">{{ __('messages.full_name') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="name" name="name" placeholder="{{ __('messages.full_name') }}" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="form-group-item">
                            <label class="form-label-modern" for="phone">{{ __('messages.phone_number') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-icon">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" placeholder="05XXXXXXXX / 06XXXXXXXX" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group-item">
                            <label class="form-label-modern" for="email">{{ __('messages.email') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="example@mail.com" required>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="form-group-item">
                            <label class="form-label-modern" for="gender">{{ __('messages.gender') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-icon">
                                <i class="fas fa-venus-mars"></i>
                                <select id="gender" name="gender" required>
                                    <option value="" disabled selected>{{ __('messages.select_gender') }}</option>
                                    <option value="male">{{ __('messages.male') }}</option>
                                    <option value="female">{{ __('messages.female') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Birth Date (Combined Day/Month/Year) -->
                        <div class="form-group-item form-group-full">
                            <label class="form-label-modern">{{ __('messages.birth_date') }} <span style="color:#DC2626;">*</span></label>
                            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px;">
                                <div class="input-wrap-icon">
                                    <select id="birth_day" required>
                                        <option value="" disabled selected>{{ __('messages.day') }}</option>
                                        @for ($i = 1; $i <= 31; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="input-wrap-icon">
                                    <select id="birth_month" required>
                                        <option value="" disabled selected>{{ __('messages.month') }}</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="input-wrap-icon">
                                    <select id="birth_year" required>
                                        <option value="" disabled selected>{{ __('messages.year') }}</option>
                                        @for ($i = date('Y'); $i >= 1920; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="birth_date" name="birth_date">
                        </div>

                        <!-- Analysis Selection Filter & Checkbox Grid -->
                        <div class="form-group-item form-group-full">
                            <label class="form-label-modern">{{ __('messages.select_analyses') }}</label>
                            <div class="analysis-selection-box">
                                <input type="text" class="analysis-search-input" id="analysisSearchInput" placeholder="{{ app()->getLocale() == 'ar' ? '🔍 ابحث عن التحليل (NFS, Glycémie, TSH, Vitamine D...)' : '🔍 Rechercher une analyse...' }}">
                                <div class="analysis-checkbox-grid" id="analysisCheckboxGrid">
                                    @foreach($analyses as $analyse)
                                        <label class="analysis-pill-label" data-name="{{ strtolower($analyse->name . ' ' . $analyse->name_fr) }}">
                                            <input type="checkbox" name="analysisTypes[]" value="{{ $analyse->id }}">
                                            <span>{{ app()->getLocale() == 'ar' ? ($analyse->name_fr ? $analyse->name_fr . ' — ' . $analyse->name : $analyse->name) : ($analyse->name_fr ?? $analyse->name) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Prescription File Upload -->
                        <div class="form-group-item form-group-full">
                            <label class="form-label-modern">{{ __('messages.prescription_label') }} ({{ app()->getLocale() == 'ar' ? 'اختياري' : 'Optionnel' }})</label>
                            <div class="prescription-dropzone">
                                <input type="file" id="prescription" name="prescription" accept="image/*,application/pdf">
                                <i class="fas fa-cloud-arrow-up text-primary" style="font-size:2.2rem; margin-bottom:8px;"></i>
                                <h4 style="font-size:1rem; font-weight:800; color:var(--slate-800); margin-bottom:4px;">{{ __('messages.upload_prescription') }}</h4>
                                <p style="font-size:0.85rem; color:var(--slate-500); margin:0;" id="prescriptionFileName">{{ __('messages.drag_drop_prescription') }}</p>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="form-group-item">
                            <label class="form-label-modern" for="date">{{ __('messages.preferred_date') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-icon">
                                <i class="fas fa-calendar"></i>
                                <input type="date" id="date" name="date" required>
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="form-group-item">
                            <label class="form-label-modern" for="time">{{ __('messages.preferred_time') }} <span style="color:#DC2626;">*</span></label>
                            <div class="input-wrap-icon">
                                <i class="fas fa-clock"></i>
                                <select id="time" name="time" required>
                                    <option value="" disabled selected>{{ __('messages.select_time') }}</option>
                                    <option value="07:30">07:30</option>
                                    <option value="08:00">08:00</option>
                                    <option value="08:30">08:30</option>
                                    <option value="09:00">09:00</option>
                                    <option value="09:30">09:30</option>
                                    <option value="10:00">10:00</option>
                                    <option value="10:30">10:30</option>
                                    <option value="11:00">11:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn-submit-booking">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ __('messages.confirm_booking') }}</span>
                    </button>

                </form>
            </div>

        </div>
    </section>

    <!-- ══════════════════ 9. FOOTER ══════════════════ -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                
                <div class="footer-brand">
                    <a href="#home" class="brand-logo" style="color:#fff;">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
                        <span>{{ __('messages.lab_name') }}</span>
                    </a>
                    <p>
                        {{ app()->getLocale() == 'ar'
                            ? 'المخبر الطبي الذكي الرائد. معايير بيولوجية عالمية، دقة رقمية فائقة، ونتائج سريعة في متناول يديك.'
                            : 'Laboratoire d\'analyses médicales nouvelle génération. Précision diagnostique et rapidité certifiée.'
                        }}
                    </p>
                    <div style="display:flex; gap:12px;">
                        <a href="#" class="btn-lang-pill"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn-lang-pill"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn-lang-pill"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>{{ app()->getLocale() == 'ar' ? 'روابط سريعة' : 'Navigation' }}</h4>
                    <ul>
                        <li><a href="#home">{{ __('messages.home') }}</a></li>
                        <li><a href="#biomarkers">{{ app()->getLocale() == 'ar' ? 'المحاكي التفاعلي' : 'Simulateur' }}</a></li>
                        <li><a href="#ask-ai">{{ app()->getLocale() == 'ar' ? 'استشر الذكاء الاصطناعي' : 'LabNexa AI' }}</a></li>
                        <li><a href="#booking">{{ __('messages.booking') }}</a></li>
                        <li><a href="{{ route('analysis.info') }}">{{ __('messages.tips_title') ?? 'Conseils' }}</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>{{ app()->getLocale() == 'ar' ? 'البوابات الرقمية' : 'Portails' }}</h4>
                    <ul>
                        <li><a href="{{ route('access', ['tab' => 'patient']) }}">{{ __('messages.patient_portal') }}</a></li>
                        <li><a href="{{ route('access', ['tab' => 'doctor']) }}">{{ __('messages.physician_portal') }}</a></li>
                        <li><a href="{{ route('access', ['tab' => 'admin']) }}">{{ __('messages.admin_panel') }}</a></li>
                        <li><a href="{{ url('/labnexa_v1.1.4.apk') }}"><i class="fab fa-android text-emerald"></i> App Android (3MB)</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>{{ app()->getLocale() == 'ar' ? 'الموقع والمعلومات' : 'Contact' }}</h4>
                    <ul>
                        <li><span style="color:var(--slate-400);"><i class="fas fa-map-marker-alt text-primary me-2"></i> الجزائر العاصمة</span></li>
                        <li><span style="color:var(--slate-400);"><i class="fas fa-phone text-primary me-2"></i> +213 (0) 550 00 00 00</span></li>
                        <li><span style="color:var(--slate-400);"><i class="fas fa-clock text-primary me-2"></i> السبت - الخميس: 07:00 - 18:00</span></li>
                    </ul>
                </div>

            </div>

            <div class="footer-bottom">
                <div>&copy; {{ date('Y') }} {{ __('messages.lab_name') }}. {{ __('messages.all_rights_reserved') }}</div>
                <div>ISO 15189 Quality Accredited Laboratory • Precision Healthcare</div>
            </div>
        </div>
    </footer>

    <!-- ══════════════════ 10. JAVASCRIPT MASTER INTERACTIVITY ══════════════════ -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── 1. Web Audio API Click Synthesizer (0 KB sound assets) ──
        let audioCtx = null;
        let soundEnabled = false;
        const soundBtn = document.getElementById('soundToggle');
        const soundIcon = document.getElementById('soundIcon');

        function playMedicalTone(freq = 600, type = 'sine', duration = 0.08) {
            if (!soundEnabled) return;
            try {
                if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + duration);
            } catch(e) {}
        }

        if (soundBtn) {
            soundBtn.addEventListener('click', function() {
                soundEnabled = !soundEnabled;
                soundBtn.classList.toggle('active', soundEnabled);
                if (soundEnabled) {
                    soundIcon.className = 'fas fa-volume-high';
                    playMedicalTone(800, 'sine', 0.12);
                    showToast('تم تفعيل المؤثرات التفاعلية 🔊', 'info');
                } else {
                    soundIcon.className = 'fas fa-volume-xmark';
                    showToast('تم كتم الصوت 🔇', 'info');
                }
            });
        }

        // Attach sound to interactive buttons
        document.querySelectorAll('button, .btn-hero-primary, .btn-hero-secondary, .mockup-tab-btn, .preset-q-btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => playMedicalTone(450, 'triangle', 0.04));
            btn.addEventListener('click', () => playMedicalTone(750, 'sine', 0.06));
        });

        // ── 2. Diagnostic Micro-Loader (0% -> 100%) ──
        const loader = document.getElementById('medicalLoader');
        const loadPct = document.getElementById('loadPct');
        const loadMsg = document.getElementById('loadMsg');
        const loadBar = document.getElementById('loadBar');

        const msgs = [
            @if(app()->getLocale() == 'ar')
                { pct: 0, msg: 'معايرة كواشف التحاليل المخبرية...' },
                { pct: 30, msg: 'تهيئة محرك الذكاء الاصطناعي السريري...' },
                { pct: 65, msg: 'فحص معايير الجودة والأمان ISO 15189...' },
                { pct: 90, msg: 'مخبر LabNexa الطبي جاهز بالكامل!' }
            @else
                { pct: 0, msg: 'Calibration des réactifs d\'analyses...' },
                { pct: 30, msg: 'Initialisation du moteur clinique IA...' },
                { pct: 65, msg: 'Vérification des protocoles ISO 15189...' },
                { pct: 90, msg: 'LabNexa est prêt !' }
            @endif
        ];

        let curPct = 0;
        const loadInterval = setInterval(() => {
            curPct += Math.floor(Math.random() * 6) + 3;
            if (curPct >= 100) {
                curPct = 100;
                clearInterval(loadInterval);
                loadPct.textContent = '100';
                loadBar.style.width = '100%';
                setTimeout(() => {
                    loader.classList.add('loaded');
                }, 400);
            } else {
                loadPct.textContent = curPct;
                loadBar.style.width = curPct + '%';
                for (let i = msgs.length - 1; i >= 0; i--) {
                    if (curPct >= msgs[i].pct) {
                        loadMsg.textContent = msgs[i].msg;
                        break;
                    }
                }
            }
        }, 35);

        // ── 3. Toast Notifications ──
        function showToast(text, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const toastText = document.getElementById('notifText');
            const toastIcon = document.getElementById('notifIcon');
            if (!toast) return;

            toastText.textContent = text;
            toast.className = 'notification show ' + type;
            if (type === 'success') toastIcon.className = 'fas fa-circle-check';
            else if (type === 'error') toastIcon.className = 'fas fa-circle-exclamation';
            else toastIcon.className = 'fas fa-circle-info';

            setTimeout(() => {
                toast.classList.remove('show');
            }, 5000);
        }

        // Show session errors/success if present
        @if(session('success')) showToast(@json(session('success')), 'success'); @endif
        @if(session('error')) showToast(@json(session('error')), 'error'); @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast(@json($error), 'error');
            @endforeach
        @endif

        // ── 4. 3D Holographic Card Tilt Effect ──
        const holoCard = document.getElementById('hologramCard');
        if (holoCard) {
            document.addEventListener('mousemove', (e) => {
                const rect = holoCard.getBoundingClientRect();
                const cardX = rect.left + rect.width / 2;
                const cardY = rect.top + rect.height / 2;
                const deltaX = (e.clientX - cardX) / 30;
                const deltaY = (e.clientY - cardY) / 30;
                holoCard.style.transform = `rotateY(${deltaX}deg) rotateX(${-deltaY}deg)`;
            });
            holoCard.addEventListener('mouseleave', () => {
                holoCard.style.transform = 'rotateY(0deg) rotateX(0deg)';
            });
        }

        // ── 5. Mockup Tabs Switcher ──
        document.querySelectorAll('.mockup-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.mockup-tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const target = this.getAttribute('data-tab');
                document.querySelectorAll('.mockup-content-item').forEach(item => item.classList.remove('active'));
                const showItem = document.getElementById('tab-' + target);
                if (showItem) showItem.classList.add('active');
            });
        });

        // ── 6. Interactive Biomarker Simulator (#biomarkers) ──
        const rGly = document.getElementById('rangeGlycemia');
        const rLdl = document.getElementById('rangeLdl');
        const rVit = document.getElementById('rangeVitD');
        const vGly = document.getElementById('valGlycemia');
        const vLdl = document.getElementById('valLdl');
        const vVit = document.getElementById('valVitD');
        const gScore = document.getElementById('gaugeScore');
        const gFill = document.getElementById('gaugeFill');
        const gBadge = document.getElementById('gaugeBadge');
        const gDiag = document.getElementById('gaugeDiagnosis');

        function updateBiomarkerSim() {
            const gly = parseFloat(rGly.value);
            const ldl = parseFloat(rLdl.value);
            const vit = parseFloat(rVit.value);

            vGly.textContent = gly.toFixed(2) + ' g/L';
            vLdl.textContent = ldl.toFixed(2) + ' g/L';
            vVit.textContent = Math.round(vit) + ' ng/mL';

            // Calculate Vitality Index (0 - 100)
            let score = 100;
            let status = 'normal';
            let diagMsg = '';

            // Glycemia penalty
            if (gly < 0.70) {
                score -= 25;
                status = 'caution';
            } else if (gly > 1.25 && gly <= 1.50) {
                score -= 20;
                status = 'caution';
            } else if (gly > 1.50) {
                score -= 40;
                status = 'alert';
            }

            // LDL penalty
            if (ldl > 1.30 && ldl <= 1.60) {
                score -= 15;
                if (status !== 'alert') status = 'caution';
            } else if (ldl > 1.60) {
                score -= 30;
                status = 'alert';
            }

            // Vit D penalty
            if (vit < 20) {
                score -= 20;
                if (status !== 'alert') status = 'caution';
            } else if (vit < 30) {
                score -= 10;
            }

            score = Math.max(20, Math.min(100, score));
            gScore.textContent = score;

            // Gauge SVG circle math (circumference = 2 * PI * 70 = 440)
            const offset = 440 - (440 * (score / 100));
            gFill.style.strokeDashoffset = offset;

            // Colors & Status Messages
            if (status === 'normal') {
                gFill.style.stroke = '#10B981';
                gBadge.style.color = '#10B981';
                gBadge.style.borderColor = 'rgba(16, 185, 129, 0.4)';
                gBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                gBadge.innerHTML = '<i class="fas fa-circle-check me-1"></i> {{ app()->getLocale() == "ar" ? "مؤشرات فسيولوجية ممتازة" : "Indicateurs Biologiques Parfaits" }}';
                diagMsg = '{{ app()->getLocale() == "ar" ? "جميع القيم المختارة تقع في النطاق المرجعي الآمن. لا توجد مؤشرات خطر سكري أو قلبي وعائي." : "Toutes les valeurs sont dans l\'intervalle de référence normal." }}';
            } else if (status === 'caution') {
                gFill.style.stroke = '#F59E0B';
                gBadge.style.color = '#F59E0B';
                gBadge.style.borderColor = 'rgba(245, 158, 11, 0.4)';
                gBadge.style.background = 'rgba(245, 158, 11, 0.15)';
                gBadge.innerHTML = '<i class="fas fa-triangle-exclamation me-1"></i> {{ app()->getLocale() == "ar" ? "انتباه: قيم تستدعي المتابعة" : "Attention : Paramètres à surveiller" }}';
                diagMsg = '{{ app()->getLocale() == "ar" ? "يُلاحظ اضطراب طفيف في أحد المؤشرات (بداية ارتفاع السكر أو نقص فيتامين D). يُوصى بإجراء تحليل مخبري تأكيدي." : "Un ou plusieurs paramètres nécessitent une confirmation biologique en laboratoire." }}';
            } else {
                gFill.style.stroke = '#EF4444';
                gBadge.style.color = '#EF4444';
                gBadge.style.borderColor = 'rgba(239, 68, 68, 0.4)';
                gBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                gBadge.innerHTML = '<i class="fas fa-triangle-exclamation me-1"></i> {{ app()->getLocale() == "ar" ? "مؤشر مرتفع: استشارة ضرورية" : "Alerte : Consultation recommandée" }}';
                diagMsg = '{{ app()->getLocale() == "ar" ? "القيم المختارة تشير إلى ارتفاع حاد قد يرتبط بمقاومة الإنسولين أو فرط دهنيات الدم. يُنصح بحجز موعد فحص فوري." : "Valeurs critiques nécessitant une consultation médicale rapide." }}';
            }
            gDiag.textContent = diagMsg;
            playMedicalTone(300 + (score * 5), 'sine', 0.03);
        }

        if (rGly && rLdl && rVit) {
            [rGly, rLdl, rVit].forEach(input => {
                input.addEventListener('input', updateBiomarkerSim);
            });
            updateBiomarkerSim();
        }

        // ── 7. Interactive Medical AI Chat (#ask-ai) ──
        const chatPresetData = {
            fasting: {
                q: '{{ app()->getLocale() == "ar" ? "هل يجب الصيام قبل فحص دهنيات الدم (Lipid Profile)؟" : "Faut-il être à jeun pour un bilan lipidique ?" }}',
                a: '{{ app()->getLocale() == "ar" ? "نعم، يتطلب فحص دهنيات الدم (الكولسترول، الشحوم الثلاثية TG) الصيام التام عن الطعام لمدة 10 إلى 12 ساعة قبل سحب العينة. يُسمح بشرب الماء النقي فقط لضمان دقة قياس الدهون." : "Oui, le bilan lipidique complet exige un jeûne strict de 10 à 12 heures avant le prélèvement." }}'
            },
            tsh: {
                q: '{{ app()->getLocale() == "ar" ? "كم يستغرق خروج تحليل الغدة الدرقية TSH؟" : "Quel est le délai pour le dosage de la TSH ?" }}',
                a: '{{ app()->getLocale() == "ar" ? "يستغرق تحليل TSH وهرمونات الغدة الدرقية بتقنية الـ ECLIA فائقة الدقة لدينا حوالي 45 إلى 60 دقيقة فقط وتصلك النتيجة فوراً على هاتفك." : "Le dosage de la TSH par chimiluminescence est délivré en 45 à 60 minutes sur votre smartphone." }}'
            },
            mobile: {
                q: '{{ app()->getLocale() == "ar" ? "كيف تصلني نتائجي على الهاتف بدون الرجوع للمخبر؟" : "Comment recevoir mes résultats sur mobile ?" }}',
                a: '{{ app()->getLocale() == "ar" ? "بمجرد مصادقة الطبيب البيولوجي على العينة، تصلك رسالة SMS مع رابط مباشر، أو يمكنك فتح تطبيق LabNexa (3MB) أو بوابة المرضى لمشاهدة نتائجك الرقمية وتحميل التقرير." : "Dès validation par le biologiste, vous recevez un SMS sécurisé et vos résultats sont disponibles sur l\'application mobile LabNexa." }}'
            }
        };

        const chatUserMsg = document.getElementById('chatUserMsg');
        const chatAiMsg = document.getElementById('chatAiMsg');
        const customChatInput = document.getElementById('customChatInput');
        const chatSendBtn = document.getElementById('chatSendBtn');

        function streamAnswer(questionText, answerText) {
            chatUserMsg.textContent = questionText;
            chatAiMsg.innerHTML = '<i class="fas fa-circle-notch fa-spin text-primary"></i> LabNexa AI يحلل السؤال سريرياً...';
            
            setTimeout(() => {
                chatAiMsg.innerHTML = answerText + '<div class="ai-cert-stamp"><i class="fas fa-circle-check"></i> <span>مراجعة مخبرية مصادقة • البروتوكول السريري المعتمد</span></div>';
                playMedicalTone(600, 'sine', 0.1);
            }, 600);
        }

        document.querySelectorAll('.preset-q-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.preset-q-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const key = this.getAttribute('data-q');
                if (chatPresetData[key]) {
                    streamAnswer(chatPresetData[key].q, chatPresetData[key].a);
                }
            });
        });

        if (chatSendBtn && customChatInput) {
            chatSendBtn.addEventListener('click', function() {
                const text = customChatInput.value.trim();
                if (!text) return;
                customChatInput.value = '';
                streamAnswer(text, 'شكراً لسؤالك. وفقاً للبروتوكول المخبري لـ LabNexa، يُنصح دائماً بالالتزام بشروط التحضير الدقيقة وسؤال فريقنا الطبي عند سحب العينة لضمان أعلى مستويات الدقة التشخيصية.');
            });
            customChatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') chatSendBtn.click();
            });
        }

        // ── 8. Analysis Live Search & Filter in Booking Form ──
        const searchInput = document.getElementById('analysisSearchInput');
        const analysisGrid = document.getElementById('analysisCheckboxGrid');
        if (searchInput && analysisGrid) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                analysisGrid.querySelectorAll('.analysis-pill-label').forEach(label => {
                    const name = label.getAttribute('data-name') || '';
                    if (name.includes(query)) {
                        label.style.display = 'flex';
                    } else {
                        label.style.display = 'none';
                    }
                });
            });
        }

        // Prescription drag-and-drop file name preview
        const prescInput = document.getElementById('prescription');
        const prescName = document.getElementById('prescriptionFileName');
        if (prescInput && prescName) {
            prescInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    prescName.innerHTML = '<strong style="color:var(--emerald);"><i class="fas fa-file-check me-1"></i> ' + this.files[0].name + '</strong>';
                    showToast('تم تحميل الوصفة الطبية بنجاح 📄', 'success');
                }
            });
        }

        // ── 9. Booking Form Submission Validation ──
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                // Combine birth date
                const day = document.getElementById('birth_day');
                const month = document.getElementById('birth_month');
                const year = document.getElementById('birth_year');
                const birthHidden = document.getElementById('birth_date');
                if (day && month && year && birthHidden && day.value && month.value && year.value) {
                    birthHidden.value = `${year.value}-${month.value}-${day.value}`;
                }

                const nameInput = document.getElementById('name');
                const phoneInput = document.getElementById('phone');
                const emailInput = document.getElementById('email');
                const dateInput = document.getElementById('date');
                const timeInput = document.getElementById('time');

                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    showToast('{{ __("messages.name_required") }}', 'error');
                    nameInput.focus();
                    return;
                }
                const phoneRegex = /^(05|06|07)[0-9]{8}$/;
                if (!phoneRegex.test(phoneInput.value.replace(/\s+/g, ''))) {
                    e.preventDefault();
                    showToast('{{ __("messages.invalid_phone_format") }}', 'error');
                    phoneInput.focus();
                    return;
                }
                if (!dateInput.value || !timeInput.value) {
                    e.preventDefault();
                    showToast('يرجى تحديد التاريخ والوقت المناسب للزيارة', 'error');
                    return;
                }

                const checkedAnalyses = bookingForm.querySelectorAll('input[name="analysisTypes[]"]:checked').length;
                const hasPresc = prescInput && prescInput.files.length > 0;
                if (checkedAnalyses === 0 && !hasPresc) {
                    e.preventDefault();
                    showToast('{{ __("messages.at_least_one_analysis_or_prescription") }}', 'error');
                    return;
                }

                // Show spinner
                const subBtn = bookingForm.querySelector('button[type="submit"]');
                if (subBtn) {
                    subBtn.disabled = true;
                    subBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("messages.processing") }}...';
                }
            });
        }

        // ── 10. Navbar Scroll Blur ──
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

    });
    </script>

</body>
</html>
