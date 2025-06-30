<?php
/**
 * نام فایل: auth.php
 * مسیر فایل: /app/views/layouts/auth.php
 * توضیح: قالب مستقل صفحات احراز هویت (بدون sidebar)
 * تاریخ بروزرسانی: 1404/10/17
 * نویسنده: توسعه‌دهنده سامانت
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="سامانت - سامانه مدیریت حواله و بایگانی اسناد">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    
    <title>ورود به سامانت - <?= APP_NAME ?? 'سامانت' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- Vendor CSS Files -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
    
    <!-- Persian Fonts -->
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <style>
        /* ✅ CSS Variables - Enterprise Grade */
        :root {
            --primary-color: #667eea;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #06D6A0, #4ecdc4);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --backdrop-blur: blur(20px);
            --border-radius: 20px;
            
            /* Text & Input Variables */
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --input-bg: rgba(255, 255, 255, 0.9);
            --input-border: rgba(255, 255, 255, 0.3);
            --placeholder-color: rgba(45, 55, 72, 0.6);
            --label-color: #2d3748;
            
            /* Theme Toggle */
            --toggle-bg: rgba(255, 255, 255, 0.2);
            --toggle-hover: rgba(255, 255, 255, 0.3);
        }
        
        /* ✅ Dark Theme Variables */
        [data-theme="dark"] {
            --text-primary: #e2e8f0;
            --text-secondary: #a0aec0;
            --input-bg: rgba(45, 55, 72, 0.9);
            --input-border: rgba(255, 255, 255, 0.2);
            --placeholder-color: #a0aec0;
            --label-color: #e2e8f0;
            --glass-bg: rgba(45, 55, 72, 0.15);
            --toggle-bg: rgba(45, 55, 72, 0.3);
            --toggle-hover: rgba(45, 55, 72, 0.5);
        }
        
        /* ✅ Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Vazirmatn', 'IRANSans', 'Vazir', 'Tahoma', Arial, sans-serif;
            background: var(--primary-gradient);
            background-attachment: fixed;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* ✅ Background Animation */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 200, 255, 0.3) 0%, transparent 50%);
            animation: backgroundMove 20s ease-in-out infinite;
            z-index: -1;
            pointer-events: none;
        }
        
        @keyframes backgroundMove {
            0%, 100% { 
                transform: translateX(0) translateY(0) rotate(0deg);
                opacity: 0.8;
            }
            33% { 
                transform: translateX(30px) translateY(-30px) rotate(120deg);
                opacity: 1;
            }
            66% { 
                transform: translateX(-20px) translateY(20px) rotate(240deg);
                opacity: 0.6;
            }
        }
        
        /* ✅ FIXED: Theme Toggle - No Overlapping */
        .theme-toggle-wrapper {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        .theme-toggle-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: var(--toggle-bg);
            backdrop-filter: var(--backdrop-blur);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .theme-toggle-btn:hover {
            background: var(--toggle-hover);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .theme-toggle-btn:active {
            transform: translateY(-1px) scale(0.95);
        }
        
        /* ✅ FIXED: Single Icon System */
        .theme-icon {
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }
        
        [data-theme="light"] .theme-icon {
            color: #f6d55c;
        }
        
        [data-theme="dark"] .theme-icon {
            color: #e2e8f0;
        }
        
        /* ✅ Auth Page Layout - NO SIDEBAR */
        .auth-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }
        
        /* ✅ FORCE Remove Any Sidebar Elements */
        .sidebar,
        .main-navigation,
        .navbar,
        .nav-menu,
        .navigation-wrapper,
        .side-nav {
            display: none !important;
            visibility: hidden !important;
        }
        
        /* ✅ Auth Container */
        .auth-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .auth-container {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }
        
        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* ✅ Logo & Header */
        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .auth-logo {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }
        
        .auth-logo i {
            font-size: 2rem;
            color: white;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .auth-title {
            font-size: 2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .auth-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        /* ✅ Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--label-color);
            font-size: 0.9rem;
        }
        
        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 0.9rem 1rem 0.9rem 3rem;
            font-size: 1rem;
            color: var(--text-primary);
            width: 100%;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
            background: var(--input-bg);
        }
        
        .form-control::placeholder {
            color: var(--placeholder-color);
            opacity: 0.8;
        }
        
        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(102, 126, 234, 0.7);
            z-index: 10;
            pointer-events: none;
        }
        
        /* ✅ Buttons */
        .btn {
            border-radius: 12px;
            padding: 0.9rem 2rem;
            font-weight: 600;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: inherit;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        /* ✅ Alerts */
        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
            backdrop-filter: blur(10px);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
            color: #721c24;
        }
        
        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            border-color: rgba(25, 135, 84, 0.3);
            color: #0a3622;
        }
        
        [data-theme="dark"] .alert-danger {
            color: #fca5a5;
        }
        
        [data-theme="dark"] .alert-success {
            color: #86efac;
        }
        
        /* ✅ Test Info */
        .test-info {
            background: rgba(13, 110, 253, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        
        .test-info h6 {
            color: #052c65;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        [data-theme="dark"] .test-info {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        [data-theme="dark"] .test-info h6 {
            color: #93c5fd;
        }
        
        .test-info .credential {
            background: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #052c65;
        }
        
        [data-theme="dark"] .test-info .credential {
            background: rgba(45, 55, 72, 0.8);
            color: #93c5fd;
        }
        
        /* ✅ Mobile Responsive */
        @media (max-width: 576px) {
            .theme-toggle-wrapper {
                top: 15px;
                left: 15px;
            }
            
            .theme-toggle-btn {
                width: 45px;
                height: 45px;
            }
            
            .theme-icon {
                font-size: 1.1rem;
            }
            
            .auth-page {
                padding: 1rem 0.5rem;
            }
            
            .auth-container {
                padding: 2rem 1.5rem;
                margin: 1rem 0;
                border-radius: 15px;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 15px 15px 15px 45px;
                font-size: 16px; /* Prevent iOS zoom */
            }
            
            .form-icon {
                left: 15px;
            }
            
            .btn-primary {
                padding: 15px;
                font-size: 16px;
            }
        }
        
        /* ✅ Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="auth-page">
    <!-- ✅ FIXED: Theme Toggle - Single Icon -->
    <div class="theme-toggle-wrapper">
        <button id="themeToggle" class="theme-toggle-btn" aria-label="تغییر تم" title="تغییر تم">
            <i class="fas fa-moon theme-icon" id="themeIcon"></i>
        </button>
    </div>
    
    <!-- ✅ Auth Content - NO SIDEBAR -->
    <div class="auth-wrapper">
        <div class="auth-container">
            <!-- Logo & Header -->
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-gem"></i>
                </div>
                <h1 class="auth-title">سامانت</h1>
                <p class="auth-subtitle">سامانه مدیریت حواله و بایگانی اسناد</p>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'] ?>">
                    <i class="fas fa-<?= $_SESSION['flash']['type'] === 'error' ? 'exclamation-triangle' : 'check-circle' ?> me-2"></i>
                    <?= $_SESSION['flash']['message'] ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?= url('login') ?>">
                <!-- Username Field -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>
                        نام کاربری
                    </label>
                    <div class="position-relative">
                        <i class="form-icon fas fa-user"></i>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username" 
                            placeholder="admin"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>
                
                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>
                        کلمه عبور
                    </label>
                    <div class="position-relative">
                        <i class="form-icon fas fa-lock"></i>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="123456"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    ورود به سامانت
                </button>
            </form>

            <!-- ✅ FIXED: Correct Credentials Display -->
            <div class="test-info">
                <h6>
                    <i class="fas fa-info-circle me-2"></i>
                    اطلاعات تست
                </h6>
                <div class="credential">
                    <strong>نام کاربری:</strong> admin
                </div>
                <div class="credential">
                    <strong>کلمه عبور:</strong> 123456
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ JavaScript - Theme System Integration -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');
            const html = document.documentElement;
            
            // ✅ Load saved theme
            const savedTheme = localStorage.getItem('samanat_theme') || 'light';
            html.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
            
            // ✅ Theme toggle functionality
            themeToggle?.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                // Update theme
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('samanat_theme', newTheme);
                
                // Update icon with animation
                this.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    updateThemeIcon(newTheme);
                    this.style.transform = 'scale(1)';
                }, 150);
                
                console.log(`✅ Theme changed to: ${newTheme}`);
            });
            
            // ✅ Update theme icon function
            function updateThemeIcon(theme) {
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'fas fa-sun theme-icon';
                    } else {
                        themeIcon.className = 'fas fa-moon theme-icon';
                    }
                }
            }
            
            // ✅ Focus management
            document.getElementById('username')?.focus();
            
            // ✅ Form submission handling
            document.querySelector('form')?.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال ورود...';
                btn.disabled = true;
            });
            
            // ✅ Enhanced UX
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                // Input focus effects
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
                
                // Keyboard shortcuts
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && this.id === 'username') {
                        e.preventDefault();
                        document.getElementById('password')?.focus();
                    }
                    
                    if (e.key === 'Escape') {
                        this.value = '';
                        document.getElementById('username')?.focus();
                    }
                });
            });
        });
    </script>
</body>
</html>