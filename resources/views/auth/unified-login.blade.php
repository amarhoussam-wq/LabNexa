<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.lab_name') }} — {{ __('messages.portal_welcome_subtitle') ?? "Portail d'accès" }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ══════════════════════════════════════════════
           LabNexa — World-Class Medical Login Portal
           Premium Glassmorphism + Animated DNA Helix
        ══════════════════════════════════════════════ */

        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --primary-glow: rgba(0, 102, 255, 0.35);
            --emerald: #10B981;
            --emerald-dark: #059669;
            --emerald-glow: rgba(16, 185, 129, 0.35);
            --violet: #7C3AED;
            --violet-dark: #6D28D9;
            --violet-glow: rgba(124, 58, 237, 0.35);
            --surface: rgba(255, 255, 255, 0.72);
            --surface-hover: rgba(255, 255, 255, 0.88);
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-tertiary: #94A3B8;
            --border: rgba(148, 163, 184, 0.2);
            --border-active: rgba(0, 102, 255, 0.4);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 20px 40px -8px rgba(0,0,0,0.1), 0 8px 16px -4px rgba(0,0,0,0.06);
            --shadow-xl: 0 32px 64px -12px rgba(0,0,0,0.14), 0 16px 32px -8px rgba(0,0,0,0.08);
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Tajawal'" : "'Inter'" }}, -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-primary);
            overflow-x: hidden;
            background: #0B1120;
        }

        /* ── Animated Background ── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 40%, rgba(0, 102, 255, 0.12) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 80% 60%, rgba(124, 58, 237, 0.1) 0%, transparent 70%),
                radial-gradient(ellipse 50% 50% at 50% 100%, rgba(16, 185, 129, 0.08) 0%, transparent 60%),
                linear-gradient(180deg, #0B1120 0%, #111827 50%, #0F172A 100%);
        }

        /* Floating orbs */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: float-orb 20s infinite ease-in-out;
            z-index: 0;
        }
        .bg-orb-1 { width: 500px; height: 500px; background: rgba(0,102,255,0.15); top: -10%; left: -5%; animation-delay: 0s; }
        .bg-orb-2 { width: 400px; height: 400px; background: rgba(124,58,237,0.12); bottom: -5%; right: -5%; animation-delay: -7s; }
        .bg-orb-3 { width: 300px; height: 300px; background: rgba(16,185,129,0.1); top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: -14s; }

        @keyframes float-orb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -40px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(40px, 30px) scale(1.03); }
        }

        /* DNA helix particles */
        .dna-strand {
            position: fixed;
            z-index: 0;
            opacity: 0.06;
        }
        .dna-strand-left { left: 5%; top: 0; bottom: 0; width: 60px; }
        .dna-strand-right { right: 5%; top: 0; bottom: 0; width: 60px; }

        .dna-dot {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #0066FF;
            animation: dna-move 4s infinite ease-in-out;
        }

        @keyframes dna-move {
            0%, 100% { transform: translateX(0); opacity: 0.3; }
            50% { transform: translateX(25px); opacity: 1; }
        }

        /* Grid pattern overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: 0.03;
            background-image:
                linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── Main Layout ── */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            animation: page-enter 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes page-enter {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Language Switcher ── */
        .lang-bar {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 100;
        }
        [dir="rtl"] .lang-bar { right: auto; left: 24px; }

        .lang-bar-inner {
            display: flex;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 100px;
            padding: 3px;
            gap: 2px;
        }

        .lang-bar-inner a {
            padding: 7px 18px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            color: rgba(255,255,255,0.5);
            border-radius: 100px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .lang-bar-inner a:hover { color: rgba(255,255,255,0.8); }
        .lang-bar-inner a.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        /* ── Brand Header ── */
        .brand-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo-ring {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            margin-bottom: 20px;
        }

        .brand-logo-ring::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 24px;
            background: linear-gradient(135deg, var(--primary), var(--violet), var(--emerald));
            opacity: 0.8;
            animation: ring-rotate 8s linear infinite;
        }

        @keyframes ring-rotate {
            from { filter: hue-rotate(0deg); }
            to { filter: hue-rotate(360deg); }
        }

        .brand-logo-ring img {
            position: relative;
            z-index: 1;
            width: 80px;
            height: 80px;
            border-radius: 20px;
            object-fit: cover;
            background: #0F172A;
            padding: 8px;
        }

        .brand-name {
            color: white;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.7) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-tagline {
            color: var(--text-tertiary);
            font-size: 14px;
            font-weight: 400;
            letter-spacing: 0.2px;
        }

        /* ── Portal Tabs ── */
        .portal-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }

        .portal-tab {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 18px 8px 14px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .portal-tab::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .portal-tab:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.12);
            transform: translateY(-3px);
        }

        .portal-tab .tab-icon {
            font-size: 22px;
            color: rgba(255,255,255,0.35);
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
        }

        .portal-tab .tab-label {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: all 0.4s ease;
            position: relative;
            z-index: 1;
        }

        /* Active states per type */
        .portal-tab.active[data-type="patient"] {
            background: rgba(0,102,255,0.1);
            border-color: rgba(0,102,255,0.3);
            box-shadow: 0 0 30px var(--primary-glow), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .portal-tab.active[data-type="patient"] .tab-icon { color: var(--primary); }
        .portal-tab.active[data-type="patient"] .tab-label { color: var(--primary); }

        .portal-tab.active[data-type="doctor"] {
            background: rgba(16,185,129,0.1);
            border-color: rgba(16,185,129,0.3);
            box-shadow: 0 0 30px var(--emerald-glow), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .portal-tab.active[data-type="doctor"] .tab-icon { color: var(--emerald); }
        .portal-tab.active[data-type="doctor"] .tab-label { color: var(--emerald); }

        .portal-tab.active[data-type="admin"] {
            background: rgba(124,58,237,0.1);
            border-color: rgba(124,58,237,0.3);
            box-shadow: 0 0 30px var(--violet-glow), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .portal-tab.active[data-type="admin"] .tab-icon { color: var(--violet); }
        .portal-tab.active[data-type="admin"] .tab-label { color: var(--violet); }

        .portal-tab .active-dot {
            position: absolute;
            bottom: 6px;
            left: 50%;
            transform: translateX(-50%) scale(0);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .portal-tab.active .active-dot { transform: translateX(-50%) scale(1); }
        .portal-tab[data-type="patient"] .active-dot { background: var(--primary); box-shadow: 0 0 8px var(--primary-glow); }
        .portal-tab[data-type="doctor"] .active-dot { background: var(--emerald); box-shadow: 0 0 8px var(--emerald-glow); }
        .portal-tab[data-type="admin"] .active-dot { background: var(--violet); box-shadow: 0 0 8px var(--violet-glow); }

        /* ── Glass Card ── */
        .glass-card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(40px) saturate(1.5);
            -webkit-backdrop-filter: blur(40px) saturate(1.5);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius-xl);
            padding: 40px 36px;
            box-shadow: var(--shadow-xl), inset 0 1px 0 rgba(255,255,255,0.08);
            transition: all 0.5s ease;
        }

        /* ── Form Header ── */
        .form-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .form-header-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .form-header-icon.patient-icon { background: rgba(0,102,255,0.15); color: var(--primary); }
        .form-header-icon.doctor-icon { background: rgba(16,185,129,0.15); color: var(--emerald); }
        .form-header-icon.admin-icon { background: rgba(124,58,237,0.15); color: var(--violet); }

        .form-header-text h2 {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 2px;
        }

        .form-header-text p {
            font-size: 13px;
            color: var(--text-tertiary);
            font-weight: 400;
        }

        /* ── Demo Credentials ── */
        .demo-box {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            margin-bottom: 28px;
        }

        .demo-box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-tertiary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
        }

        .demo-row + .demo-row { border-top: 1px solid rgba(255,255,255,0.04); padding-top: 8px; margin-top: 2px; }

        .demo-label {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.5);
        }

        .demo-value {
            font-family: 'SF Mono', 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 14px;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .demo-value.patient-val { background: rgba(0,102,255,0.12); color: var(--primary); border: 1px solid rgba(0,102,255,0.2); }
        .demo-value.doctor-val { background: rgba(16,185,129,0.12); color: var(--emerald); border: 1px solid rgba(16,185,129,0.2); }
        .demo-value.admin-val { background: rgba(124,58,237,0.12); color: var(--violet); border: 1px solid rgba(124,58,237,0.2); }

        .demo-value:hover { filter: brightness(1.2); transform: scale(1.03); }
        .demo-value:active { transform: scale(0.97); }

        /* Copy tooltip */
        .demo-value::after {
            content: 'Copied!';
            position: absolute;
            top: -28px;
            left: 50%;
            transform: translateX(-50%) scale(0);
            background: white;
            color: var(--text-primary);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            pointer-events: none;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            white-space: nowrap;
        }
        .demo-value.copied::after { transform: translateX(-50%) scale(1); }

        /* ── Input Fields ── */
        .field-group {
            margin-bottom: 22px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

        .field-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 16px;
            color: var(--text-tertiary);
            font-size: 15px;
            z-index: 2;
            transition: color 0.3s ease;
        }

        [dir="rtl"] .field-icon { left: auto; right: 16px; }

        .field-input {
            width: 100%;
            padding: 15px 16px 15px 48px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 500;
            color: white;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: var(--radius-sm);
            outline: none;
            transition: all 0.3s ease;
        }

        [dir="rtl"] .field-input { padding: 15px 48px 15px 16px; }

        .field-input::placeholder { color: rgba(255,255,255,0.2); font-weight: 400; }

        .field-input:hover {
            border-color: rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.07);
        }

        .field-input:focus {
            background: rgba(255,255,255,0.08);
        }

        .field-input:focus ~ .field-focus-ring { opacity: 1; }

        /* Focus colors per type */
        .patient-form .field-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }
        .patient-form .field-input:focus + .field-icon,
        .patient-form .field-input:focus ~ .field-icon { color: var(--primary); }

        .doctor-form .field-input:focus { border-color: var(--emerald); box-shadow: 0 0 0 3px var(--emerald-glow); }
        .doctor-form .field-input:focus + .field-icon,
        .doctor-form .field-input:focus ~ .field-icon { color: var(--emerald); }

        .admin-form .field-input:focus { border-color: var(--violet); box-shadow: 0 0 0 3px var(--violet-glow); }
        .admin-form .field-input:focus + .field-icon,
        .admin-form .field-input:focus ~ .field-icon { color: var(--violet); }

        /* Password toggle */
        .field-toggle-pass {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: var(--text-tertiary);
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            z-index: 2;
            transition: color 0.2s ease;
        }
        [dir="rtl"] .field-toggle-pass { right: auto; left: 16px; }
        .field-toggle-pass:hover { color: rgba(255,255,255,0.7); }

        /* ── Remember Me ── */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.15);
            border-radius: 5px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            flex-shrink: 0;
        }

        .custom-checkbox:checked {
            border-color: var(--primary);
            background: var(--primary);
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 11px;
            font-weight: 900;
        }

        .remember-label {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.45);
            cursor: pointer;
        }

        /* ── Submit Button ── */
        .btn-submit {
            position: relative;
            width: 100%;
            padding: 16px 24px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-submit:hover { transform: translateY(-2px); }
        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }

        .btn-submit.patient-btn {
            background: linear-gradient(135deg, var(--primary) 0%, #3B82F6 100%);
            box-shadow: 0 4px 20px var(--primary-glow);
        }
        .btn-submit.patient-btn:hover { box-shadow: 0 8px 30px rgba(0,102,255,0.5); }

        .btn-submit.doctor-btn {
            background: linear-gradient(135deg, var(--emerald) 0%, #34D399 100%);
            box-shadow: 0 4px 20px var(--emerald-glow);
        }
        .btn-submit.doctor-btn:hover { box-shadow: 0 8px 30px rgba(16,185,129,0.5); }

        .btn-submit.admin-btn {
            background: linear-gradient(135deg, var(--violet) 0%, #8B5CF6 100%);
            box-shadow: 0 4px 20px var(--violet-glow);
        }
        .btn-submit.admin-btn:hover { box-shadow: 0 8px 30px rgba(124,58,237,0.5); }

        .btn-submit .btn-text {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Button loading spinner */
        .btn-spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-submit.loading .btn-text span { display: none; }
        .btn-submit.loading .btn-spinner { display: inline-block; }

        /* ── Alert ── */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: var(--radius-sm);
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-error-icon {
            color: #EF4444;
            font-size: 16px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .alert-error ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert-error li {
            color: #FCA5A5;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
        }

        /* ── Back Link ── */
        .back-home {
            text-align: center;
            margin-top: 28px;
        }

        .back-home a {
            color: var(--text-tertiary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-home a:hover { color: rgba(255,255,255,0.7); }

        /* ── Security Badge ── */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.04);
        }

        .security-badge i { color: var(--emerald); font-size: 12px; }
        .security-badge span {
            font-size: 11px;
            color: var(--text-tertiary);
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* ── Form Transitions ── */
        .auth-form {
            display: none;
            animation: form-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .auth-form.active { display: block; }

        @keyframes form-in {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Responsive ── */
        @media (max-width: 540px) {
            .login-container { max-width: 100%; }
            .glass-card { padding: 28px 22px; border-radius: var(--radius-lg); }
            .brand-name { font-size: 26px; }
            .portal-tab { padding: 14px 6px 12px; }
            .portal-tab .tab-icon { font-size: 20px; }
            .portal-tab .tab-label { font-size: 10px; letter-spacing: 0.5px; }
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="bg-canvas"></div>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>
    <div class="grid-overlay"></div>

    <!-- DNA Helix Particles -->
    <div class="dna-strand dna-strand-left">
        @for($i = 0; $i < 12; $i++)
            <div class="dna-dot" style="top: {{ $i * 8.3 }}%; animation-delay: {{ $i * 0.35 }}s;"></div>
        @endfor
    </div>
    <div class="dna-strand dna-strand-right">
        @for($i = 0; $i < 12; $i++)
            <div class="dna-dot" style="top: {{ $i * 8.3 }}%; animation-delay: {{ ($i * 0.35) + 2 }}s; background: var(--violet);"></div>
        @endfor
    </div>

    <!-- Language Switcher -->
    <div class="lang-bar">
        <div class="lang-bar-inner">
            <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">عربية</a>
            <a href="{{ route('lang.switch', 'fr') }}" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">Français</a>
        </div>
    </div>

    <div class="login-container">

        <!-- Brand -->
        <div class="brand-section">
            <div class="brand-logo-ring">
                <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.lab_name') }}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/822/822143.png'">
            </div>
            <div class="brand-name">{{ __('messages.lab_name') }}</div>
            <div class="brand-tagline">{{ __('messages.portal_welcome_subtitle') ?? 'Système de gestion du laboratoire médical' }}</div>
        </div>

        <!-- Portal Tabs -->
        <div class="portal-tabs">
            <div class="portal-tab active" data-type="patient" onclick="switchPortal('patient')">
                <i class="fas fa-heartbeat tab-icon"></i>
                <span class="tab-label">{{ __('messages.patient') ?? 'Patient' }}</span>
                <div class="active-dot"></div>
            </div>
            <div class="portal-tab" data-type="doctor" onclick="switchPortal('doctor')">
                <i class="fas fa-stethoscope tab-icon"></i>
                <span class="tab-label">{{ __('messages.doctor') ?? 'Médecin' }}</span>
                <div class="active-dot"></div>
            </div>
            <div class="portal-tab" data-type="admin" onclick="switchPortal('admin')">
                <i class="fas fa-shield-halved tab-icon"></i>
                <span class="tab-label">{{ __('messages.admin') ?? 'Admin' }}</span>
                <div class="active-dot"></div>
            </div>
        </div>

        <!-- Glass Card -->
        <div class="glass-card">

            @if($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle alert-error-icon"></i>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ═══ Patient Form ═══ --}}
            <form id="patientForm" class="auth-form patient-form active" action="{{ route('access.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="auth_type" value="patient">

                <div class="form-header">
                    <div class="form-header-icon patient-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="form-header-text">
                        <h2>{{ __('messages.patient_portal') }}</h2>
                        <p>{{ __('messages.portal_welcome_subtitle') ?? 'Accédez à vos résultats' }}</p>
                    </div>
                </div>

                <div class="demo-box">
                    <div class="demo-box-title"><i class="fas fa-key"></i> {{ app()->getLocale() == 'ar' ? 'بيانات الدخول التجريبية' : 'Identifiants de démonstration' }}</div>
                    <div class="demo-row">
                        <span class="demo-label">{{ app()->getLocale() == 'ar' ? 'المستخدم' : 'Utilisateur' }}</span>
                        <span class="demo-value patient-val" onclick="copyDemo(this, 'admin')">admin</span>
                    </div>
                    <div class="demo-row">
                        <span class="demo-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Mot de passe' }}</span>
                        <span class="demo-value patient-val" onclick="copyDemo(this, 'admin123')">admin123</span>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{{ __('messages.username_or_email') }}</label>
                    <div class="field-input-wrap">
                        <input type="text" name="email_or_phone" class="field-input" placeholder="{{ __('messages.name') }} / {{ __('messages.email') }} / {{ __('messages.phone_number') }}" value="{{ old('email_or_phone') }}" required>
                        <i class="fas fa-user field-icon"></i>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{{ __('messages.password') }}</label>
                    <div class="field-input-wrap">
                        <input type="password" name="password" class="field-input" placeholder="••••••••" required>
                        <i class="fas fa-lock field-icon"></i>
                        <button type="button" class="field-toggle-pass" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" class="custom-checkbox" id="rememberPatient">
                    <label class="remember-label" for="rememberPatient">{{ __('messages.remember_me') }}</label>
                </div>

                <button type="submit" class="btn-submit patient-btn" onclick="this.classList.add('loading')">
                    <span class="btn-text">
                        <span>{{ __('messages.login') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </span>
                    <div class="btn-spinner"></div>
                </button>
            </form>

            {{-- ═══ Doctor Form ═══ --}}
            <form id="doctorForm" class="auth-form doctor-form" action="{{ route('access.submit') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="auth_type" value="doctor">

                <div class="form-header">
                    <div class="form-header-icon doctor-icon"><i class="fas fa-stethoscope"></i></div>
                    <div class="form-header-text">
                        <h2>{{ __('messages.physician_portal') }}</h2>
                        <p>{{ app()->getLocale() == 'ar' ? 'بوابة الأطباء والمتخصصين' : 'Portail médecins et spécialistes' }}</p>
                    </div>
                </div>

                <div class="demo-box">
                    <div class="demo-box-title"><i class="fas fa-key"></i> {{ app()->getLocale() == 'ar' ? 'بيانات الدخول التجريبية' : 'Identifiants de démonstration' }}</div>
                    <div class="demo-row">
                        <span class="demo-label">{{ app()->getLocale() == 'ar' ? 'المستخدم' : 'Utilisateur' }}</span>
                        <span class="demo-value doctor-val" onclick="copyDemo(this, 'admin')">admin</span>
                    </div>
                    <div class="demo-row">
                        <span class="demo-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Mot de passe' }}</span>
                        <span class="demo-value doctor-val" onclick="copyDemo(this, 'admin123')">admin123</span>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{{ __('messages.username_or_email') }}</label>
                    <div class="field-input-wrap">
                        <input type="text" name="email" class="field-input" placeholder="admin / doctor@lab.dz" value="{{ old('email') }}">
                        <i class="fas fa-envelope field-icon"></i>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{{ __('messages.password') }}</label>
                    <div class="field-input-wrap">
                        <input type="password" name="password" class="field-input" placeholder="••••••••" required>
                        <i class="fas fa-lock field-icon"></i>
                        <button type="button" class="field-toggle-pass" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn-submit doctor-btn" onclick="this.classList.add('loading')">
                    <span class="btn-text">
                        <span>{{ __('messages.login_as_doctor') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </span>
                    <div class="btn-spinner"></div>
                </button>
            </form>

            {{-- ═══ Admin Form ═══ --}}
            <form id="adminForm" class="auth-form admin-form" action="{{ route('auth.administrator') }}" method="POST">
                @csrf

                <div class="form-header">
                    <div class="form-header-icon admin-icon"><i class="fas fa-shield-halved"></i></div>
                    <div class="form-header-text">
                        <h2>{{ __('messages.admin_panel') }}</h2>
                        <p>{{ app()->getLocale() == 'ar' ? 'لوحة الإدارة والتحكم' : 'Panneau de contrôle administratif' }}</p>
                    </div>
                </div>

                <div class="demo-box">
                    <div class="demo-box-title"><i class="fas fa-key"></i> {{ app()->getLocale() == 'ar' ? 'بيانات الدخول التجريبية' : 'Identifiants de démonstration' }}</div>
                    <div class="demo-row">
                        <span class="demo-label">{{ app()->getLocale() == 'ar' ? 'المستخدم' : 'Utilisateur' }}</span>
                        <span class="demo-value admin-val" onclick="copyDemo(this, 'admin')">admin</span>
                    </div>
                    <div class="demo-row">
                        <span class="demo-label">{{ app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Mot de passe' }}</span>
                        <span class="demo-value admin-val" onclick="copyDemo(this, 'admin123')">admin123</span>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{{ __('messages.username_or_email') }}</label>
                    <div class="field-input-wrap">
                        <input type="text" name="name" class="field-input" placeholder="admin" value="{{ old('name') }}" required>
                        <i class="fas fa-user-gear field-icon"></i>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">{{ __('messages.password') }}</label>
                    <div class="field-input-wrap">
                        <input type="password" name="password" class="field-input" placeholder="••••••••" required>
                        <i class="fas fa-lock field-icon"></i>
                        <button type="button" class="field-toggle-pass" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn-submit admin-btn" onclick="this.classList.add('loading')">
                    <span class="btn-text">
                        <span>{{ __('messages.login') }} (Admin)</span>
                        <i class="fas fa-arrow-right"></i>
                    </span>
                    <div class="btn-spinner"></div>
                </button>
            </form>

            <!-- Security Badge -->
            <div class="security-badge">
                <i class="fas fa-lock"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'اتصال مشفّر وآمن — TLS 256-bit' : 'Connexion chiffrée et sécurisée — TLS 256-bit' }}</span>
            </div>

        </div>

        <!-- Back -->
        <div class="back-home">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left"></i>
                {{ __('messages.back_to_home') ?? "Retour à l'accueil" }}
            </a>
        </div>

    </div>

    <script>
        /* ── Portal Switcher ── */
        function switchPortal(type) {
            // Update tabs
            document.querySelectorAll('.portal-tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`.portal-tab[data-type="${type}"]`).classList.add('active');

            // Switch forms
            document.querySelectorAll('.auth-form').forEach(f => {
                f.classList.remove('active');
                f.style.display = 'none';
            });
            const form = document.getElementById(type + 'Form');
            form.style.display = 'block';
            // Trigger reflow for animation
            void form.offsetWidth;
            form.classList.add('active');

            // Focus first input
            setTimeout(() => {
                const input = form.querySelector('.field-input');
                if (input) input.focus();
            }, 300);
        }

        /* ── Toggle Password ── */
        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('.field-input');
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        /* ── Copy Demo Credentials ── */
        function copyDemo(el, value) {
            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(value);
            }
            // Show tooltip
            el.classList.add('copied');
            setTimeout(() => el.classList.remove('copied'), 1200);
        }

        /* ── Restore state on validation error ── */
        @if(request('tab') == 'admin')
            switchPortal('admin');
        @elseif(old('auth_type') == 'doctor')
            switchPortal('doctor');
        @elseif($errors->has('name') || old('name'))
            switchPortal('admin');
        @endif
    </script>

</body>
</html>
