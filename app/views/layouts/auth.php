<?php
/**
 * نام فایل: auth.php
 * مسیر فایل: /app/views/layouts/auth.php
 * توضیح: قالب ساده صفحات احراز هویت - ورژن رفع باگ
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سامانت</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    
    <!-- Bootstrap RTL -->
    <link href="<?= asset('vendor/bootstrap/css/bootstrap.rtl.min.css') ?>" rel="stylesheet">
    <link href="<?= asset('vendor/fontawesome/css/all.min.css') ?>" rel="stylesheet">
    <!-- ✅ FontAwesome CDN Fallback -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        /* هیچ sidebar نمایش داده نشود */
        .sidebar, .main-navigation, .navbar, .nav-menu {
            display: none !important;
        }
        
        /* ✅ PERFECT: Single Theme Toggle with Beautiful Animation */
        .theme-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.08) rotate(5deg);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
        }
        
        .theme-toggle:active {
            transform: scale(0.92);
            transition: transform 0.1s ease;
        }
        
        /* ✅ SINGLE Icon Container with Rotation Animation */
        .theme-icon-container {
            position: relative;
            width: 24px;
            height: 24px;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .theme-icon {
            font-size: 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* ✅ Beautiful Light Theme Animation */
        [data-theme="light"] .theme-icon-container {
            transform: rotate(0deg);
        }
        
        [data-theme="light"] .sun-icon {
            opacity: 1;
            color: #f59e0b;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1) rotate(0deg);
        }
        
        [data-theme="light"] .moon-icon {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, -50%) scale(0.3) rotate(180deg);
        }
        
        /* ✅ Beautiful Dark Theme Animation */
        [data-theme="dark"] .theme-icon-container {
            transform: rotate(180deg);
        }
        
        [data-theme="dark"] .sun-icon {
            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, -50%) scale(0.3) rotate(-180deg);
        }
        
        [data-theme="dark"] .moon-icon {
            opacity: 1;
            color: #e5e7eb;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1) rotate(0deg);
        }
        
        /* ✅ Hover Glow Effects */
        [data-theme="light"] .theme-toggle:hover .sun-icon {
            color: #fbbf24;
            text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
            transform: translate(-50%, -50%) scale(1.1) rotate(90deg);
        }
        
        [data-theme="dark"] .theme-toggle:hover .moon-icon {
            color: #f3f4f6;
            text-shadow: 0 0 15px rgba(229, 231, 235, 0.4);
            transform: translate(-50%, -50%) scale(1.1) rotate(20deg);
        }
        
        /* ✅ FIXED: Auth Container - Perfect Proportions */
        .auth-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 35px 30px 30px 30px; /* ✅ Reduced vertical padding */
            width: 100%;
            max-width: 400px; /* ✅ Reduced from 420px */
            min-width: 320px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            text-align: center;
            /* ✅ Compact height constraints */
            min-height: auto;
            height: auto;
            aspect-ratio: 4/5; /* ✅ Perfect ratio constraint */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* ✅ Better distribution */
            position: relative;
        }
        
        /* ✅ IMPROVED: Light theme styles */
        [data-theme="light"] .auth-container {
            color: #2d3748;
        }
        
        [data-theme="light"] .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #2d3748;
        }
        
        [data-theme="light"] .form-control::placeholder {
            color: #718096;
            opacity: 0.8;
        }
        
        [data-theme="light"] .form-label {
            color: #2d3748;
        }
        
        [data-theme="light"] .test-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #1e40af;
        }
        
        /* ✅ IMPROVED: Dark theme styles */
        [data-theme="dark"] .auth-container {
            background: rgba(45, 55, 72, 0.25);
            color: #e2e8f0;
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        [data-theme="dark"] .form-control {
            background: rgba(45, 55, 72, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
        }
        
        [data-theme="dark"] .form-control::placeholder {
            color: #a0aec0;
            opacity: 0.8;
        }
        
        [data-theme="dark"] .form-label {
            color: #e2e8f0;
        }
        
        [data-theme="dark"] .test-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        
        /* ✅ Compact Logo & Branding */
        .auth-header {
            flex-shrink: 0;
        }
        
        .auth-logo {
            font-size: 50px; /* ✅ Reduced from 60px */
            margin-bottom: 15px; /* ✅ Reduced from 20px */
            line-height: 1;
        }
        
        .auth-title {
            font-size: 1.8rem; /* ✅ Reduced from 2rem */
            font-weight: 600;
            margin-bottom: 8px; /* ✅ Reduced from 10px */
            line-height: 1.2;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            opacity: 0.8;
            margin-bottom: 25px; /* ✅ Reduced from 30px */
            font-size: 0.85rem; /* ✅ Slightly smaller */
            line-height: 1.4;
        }
        
        /* ✅ Compact Form Container */
        .form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        /* ✅ IMPROVED: Form Styles */
        .form-group {
            margin-bottom: 18px; /* ✅ Slightly reduced */
            text-align: right;
        }
        
        .form-label {
            display: block;
            margin-bottom: 6px; /* ✅ Reduced from 8px */
            font-weight: 500;
            font-size: 0.85rem; /* ✅ Slightly smaller */
        }
        
        .form-control {
            width: 100%;
            padding: 12px 14px; /* ✅ Reduced padding */
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px 14px; /* ✅ Reduced padding */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 12px; /* ✅ Reduced from 15px */
            font-family: inherit;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        /* ✅ Compact Test Info */
        .test-info {
            margin-top: 20px; /* ✅ Reduced from 30px */
            padding: 12px; /* ✅ Reduced padding */
            border-radius: 10px;
            font-size: 13px; /* ✅ Slightly smaller */
            flex-shrink: 0;
        }
        
        .test-info p {
            margin: 4px 0; /* ✅ Reduced from 6px */
            line-height: 1.3;
        }
        
        .test-info strong {
            font-weight: 600;
        }
        
        /* Alert styles */
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 12px;
            text-align: center;
            font-size: 14px;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #059669;
        }
        
        /* ✅ PERFECT: Mobile Responsive */
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .auth-container {
                padding: 25px 20px 20px 20px; /* ✅ More compact */
                margin: 5px;
                max-width: 340px;
                min-width: 280px;
                aspect-ratio: 3.5/4.5; /* ✅ Better mobile ratio */
            }
            
            .theme-toggle {
                top: 12px;
                left: 12px;
                width: 42px;
                height: 42px;
            }
            
            .theme-icon-container {
                width: 20px;
                height: 20px;
            }
            
            .theme-icon {
                font-size: 16px;
            }
            
            .auth-logo {
                font-size: 40px; /* ✅ Smaller for mobile */
                margin-bottom: 12px;
            }
            
            .auth-title {
                font-size: 1.5rem; /* ✅ Smaller for mobile */
                margin-bottom: 6px;
            }
            
            .auth-subtitle {
                font-size: 0.8rem;
                margin-bottom: 20px;
            }
            
            .form-group {
                margin-bottom: 15px;
            }
            
            .form-label {
                font-size: 0.8rem;
                margin-bottom: 5px;
            }
            
            .form-control {
                font-size: 16px; /* Prevent iOS zoom */
                padding: 11px 12px;
            }
            
            .btn-primary {
                padding: 11px 12px;
                margin-top: 10px;
            }
            
            .test-info {
                margin-top: 15px;
                padding: 10px;
                font-size: 12px;
            }
            
            .test-info p {
                margin: 3px 0;
            }
        }
        
        /* ✅ اطمینان از عدم نمایش هر چیز اضافی */
        .main-wrapper,
        .dashboard-wrapper,
        .layout-wrapper {
            display: none !important;
        }
        
        /* ✅ Animation for smooth transitions */
        * {
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>

<body data-theme="light">
    <!-- ✅ Single Theme Toggle with Animation Container -->
    <button id="themeToggle" class="theme-toggle" title="تغییر تم" aria-label="تبدیل تم">
        <div class="theme-icon-container">
            <i class="fas fa-sun theme-icon sun-icon"></i>
            <i class="fas fa-moon theme-icon moon-icon"></i>
        </div>
    </button>
    
    <!-- ✅ Optimized Auth Container -->
    <div class="auth-container">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-logo">🔹</div>
            <h1 class="auth-title">سامانت</h1>
            <p class="auth-subtitle">سامانه مدیریت حواله و بایگانی اسناد</p>
        </div>
        
        <!-- Form Container -->
        <div class="form-container">
            <!-- Flash Message -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success' ?>">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="<?= url('login') ?>">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>
                        نام کاربری
                    </label>
                    <input type="text" 
                           class="form-control" 
                           name="username" 
                           placeholder="admin"
                           value="admin"
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>
                        کلمه عبور
                    </label>
                    <input type="password" 
                           class="form-control" 
                           name="password" 
                           placeholder="admin123"
                           required>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    ورود به سامانت
                </button>
            </form>
            
            <!-- Test Info -->
            <div class="test-info">
                <p><strong>اطلاعات تست</strong></p>
                <p>نام کاربری: <strong>admin</strong></p>
                <p>رمز عبور: <strong>admin123</strong></p>
            </div>
        </div>
    </div>
    
    <!-- ✅ IMPROVED: Enhanced JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Samanet Auth Page Initialized');
        
        // ✅ AGGRESSIVE: Remove any duplicate theme toggles
        const allToggles = document.querySelectorAll('.theme-toggle');
        if (allToggles.length > 1) {
            console.warn(`⚠️ Found ${allToggles.length} theme toggles, removing duplicates`);
            // Keep only the first one and remove all others
            for (let i = 1; i < allToggles.length; i++) {
                allToggles[i].remove();
            }
        }
        
        // ✅ Also remove any orphaned theme buttons from other systems
        const orphanedButtons = document.querySelectorAll('button[onclick*="theme"], .btn-theme, #theme-btn');
        orphanedButtons.forEach(btn => {
            if (btn.id !== 'themeToggle') {
                console.warn('⚠️ Removing orphaned theme button:', btn);
                btn.remove();
            }
        });
        
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // ✅ Verify elements exist
        if (!themeToggle) {
            console.error('❌ Theme toggle button not found');
            return;
        }
        
        // ✅ Load saved theme with validation
        let savedTheme = localStorage.getItem('samanat_theme');
        if (!savedTheme || !['light', 'dark'].includes(savedTheme)) {
            savedTheme = 'light';
            localStorage.setItem('samanat_theme', savedTheme);
        }
        
        body.setAttribute('data-theme', savedTheme);
        console.log('✅ Theme loaded:', savedTheme);
        
        // ✅ Enhanced toggle theme function
        function toggleTheme() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Update theme
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('samanat_theme', newTheme);
            
            // Enhanced animation
            themeToggle.style.transform = 'scale(0.9)';
            themeToggle.style.opacity = '0.8';
            
            setTimeout(() => {
                themeToggle.style.transform = 'scale(1)';
                themeToggle.style.opacity = '1';
            }, 150);
            
            console.log('🎨 Theme changed to:', newTheme);
            
            // Dispatch custom event for other components
            const event = new CustomEvent('themeChanged', { 
                detail: { theme: newTheme, timestamp: Date.now() } 
            });
            document.dispatchEvent(event);
        }
        
        // ✅ Add click event with proper error handling
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                toggleTheme();
            } catch (error) {
                console.error('❌ Theme toggle error:', error);
            }
        });
        
        // ✅ Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+Shift+T for theme toggle
            if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                toggleTheme();
            }
            
            // Tab navigation enhancement for accessibility
            if (e.key === 'Tab' && e.target === themeToggle) {
                themeToggle.style.outline = '2px solid #667eea';
            }
        });
        
        // ✅ Enhanced form functionality
        const form = document.querySelector('form');
        const usernameField = document.querySelector('input[name="username"]');
        const passwordField = document.querySelector('input[name="password"]');
        const submitBtn = document.querySelector('.btn-primary');
        
        if (form && submitBtn) {
            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال ورود...';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.8';
                
                // Prevent double submission
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>ورود به سامانت';
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                    }
                }, 5000);
            });
            
            // Enhanced keyboard navigation
            if (usernameField && passwordField) {
                usernameField.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        passwordField.focus();
                    }
                });
                
                passwordField.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        form.submit();
                    }
                });
                
                // Auto-focus username field
                usernameField.focus();
            }
        }
        
        // ✅ Accessibility improvements
        const removeOutline = () => {
            themeToggle.style.outline = 'none';
        };
        
        themeToggle.addEventListener('blur', removeOutline);
        themeToggle.addEventListener('click', removeOutline);
        
        // ✅ FontAwesome loading verification
        setTimeout(() => {
            const sunIcon = document.querySelector('.sun-icon');
            const moonIcon = document.querySelector('.moon-icon');
            
            if (sunIcon && moonIcon) {
                const sunComputed = window.getComputedStyle(sunIcon, '::before');
                const moonComputed = window.getComputedStyle(moonIcon, '::before');
                
                if (sunComputed.content === 'none' || moonComputed.content === 'none') {
                    console.warn('⚠️ FontAwesome icons may not be loaded properly');
                    
                    // Fallback to text icons if FontAwesome fails
                    sunIcon.textContent = '☀️';
                    moonIcon.textContent = '🌙';
                    sunIcon.style.fontSize = '18px';
                    moonIcon.style.fontSize = '18px';
                }
            }
        }, 1000);
        
        console.log('✅ Auth page fully initialized');
    });
    </script>
</body>
</html>