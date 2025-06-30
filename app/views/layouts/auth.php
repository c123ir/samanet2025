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
        
        /* ✅ FIXED: Theme Toggle - Clear Icons */
        .theme-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .theme-toggle:active {
            transform: scale(0.95);
        }
        
        /* ✅ FIXED: Theme Icons - Proper Positioning */
        .theme-icon {
            font-size: 20px;
            transition: all 0.3s ease;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        /* Light theme - Show Sun Icon */
        [data-theme="light"] .sun-icon {
            opacity: 1;
            color: #f59e0b;
            visibility: visible;
        }
        
        [data-theme="light"] .moon-icon {
            opacity: 0;
            visibility: hidden;
        }
        
        /* Dark theme - Show Moon Icon */
        [data-theme="dark"] .sun-icon {
            opacity: 0;
            visibility: hidden;
        }
        
        [data-theme="dark"] .moon-icon {
            opacity: 1;
            color: #e5e7eb;
            visibility: visible;
        }
        
        /* ✅ FIXED: Auth Container - Natural Proportions */
        .auth-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px 35px;
            width: 100%;
            max-width: 420px;
            min-width: 320px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            text-align: center;
            /* ✅ Natural height constraints */
            min-height: auto;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
        
        /* Logo & Branding */
        .auth-logo {
            font-size: 60px;
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .auth-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.2;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            opacity: 0.8;
            margin-bottom: 30px;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        /* ✅ IMPROVED: Form Styles */
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
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
            padding: 14px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            font-family: inherit;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        /* Test Info */
        .test-info {
            margin-top: 30px;
            padding: 16px;
            border-radius: 12px;
            font-size: 14px;
        }
        
        .test-info p {
            margin: 6px 0;
            line-height: 1.4;
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
        
        /* ✅ IMPROVED: Mobile Responsive */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }
            
            .auth-container {
                padding: 30px 25px;
                margin: 10px;
                max-width: 350px;
                min-width: 280px;
            }
            
            .theme-toggle {
                top: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
            }
            
            .theme-icon {
                font-size: 18px;
            }
            
            .auth-title {
                font-size: 1.6rem;
            }
            
            .auth-logo {
                font-size: 50px;
            }
            
            .form-control {
                font-size: 16px; /* Prevent iOS zoom */
                padding: 12px 14px;
            }
            
            .btn-primary {
                padding: 12px 14px;
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
    <!-- Theme Toggle -->
    <button id="themeToggle" class="theme-toggle" title="تغییر تم">
        <i class="fas fa-sun theme-icon sun-icon"></i>
        <i class="fas fa-moon theme-icon moon-icon"></i>
    </button>
    
    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Logo -->
        <div class="auth-logo">🔹</div>
        <h1 class="auth-title">سامانت</h1>
        <p class="auth-subtitle">سامانه مدیریت حواله و بایگانی اسناد</p>
        
        <!-- Flash Message -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : 'success' ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
        
        <!-- Form -->
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
    
    <!-- ✅ IMPROVED: Enhanced JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Samanet Auth Page Initialized');
        
        // ✅ Remove any duplicate theme toggles first
        const allToggles = document.querySelectorAll('.theme-toggle');
        if (allToggles.length > 1) {
            console.warn('⚠️ Multiple theme toggles found, removing duplicates');
            for (let i = 1; i < allToggles.length; i++) {
                allToggles[i].remove();
            }
        }
        
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