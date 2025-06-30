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
        
        /* ✅ Simple Theme Toggle */
        .theme-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 22px;
        }
        
        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .theme-toggle:active {
            transform: scale(0.95);
        }
        
        [data-theme="dark"] .theme-toggle {
            background: rgba(45, 55, 72, 0.9);
            color: #e2e8f0;
        }
        
        /* ✅ OPTIMIZED Auth Container - Compact Design */
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 25px 20px;
            width: 100%;
            max-width: 360px;
            min-width: 300px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            /* Compact structure */
            display: flex;
            flex-direction: column;
            gap: 10px;
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
            background: rgba(45, 55, 72, 0.95);
            color: #e2e8f0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
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
            font-size: 40px;
            margin-bottom: 5px;
            line-height: 1;
        }
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 3px;
            line-height: 1.1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            opacity: 0.8;
            margin-bottom: 15px;
            font-size: 0.75rem;
            line-height: 1.2;
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
            margin-bottom: 12px;
            text-align: right;
        }
        
        .form-label {
            display: block;
            margin-bottom: 4px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
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
            padding: 10px 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
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
            margin-top: 10px;
            padding: 8px;
            border-radius: 8px;
            font-size: 11px;
            flex-shrink: 0;
        }
        
        .test-info p {
            margin: 2px 0;
            line-height: 1.2;
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
                padding: 25px 20px 20px 20px;
                margin: 5px;
                max-width: 340px;
                min-width: 280px;
                min-height: 480px;
                max-height: 560px;
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
    <!-- ✅ Theme Toggle Button -->
    <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم" aria-label="تبدیل تم">
        <span id="theme-icon">🌙</span>
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
    
    <!-- Load theme system -->
    <script src="<?= asset('js/theme-system.js') ?>"></script>
    
    <!-- Simple auth page script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple form enhancements
        const form = document.querySelector('form');
        const passwordField = document.querySelector('input[name="password"]');
        
        if (form) {
            // Focus on password field since username is pre-filled
            if (passwordField) {
                passwordField.focus();
            }
        }
    });
    </script>
</body>
</html>