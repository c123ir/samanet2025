<?php
/**
 * نام فایل: auth.php
 * مسیر فایل: /app/views/layouts/auth.php
 * توضیح: قالب ساده صفحات احراز هویت
 * تاریخ بروزرسانی: 1404/10/17
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سامانت - <?= APP_NAME ?? 'سامانت' ?></title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- Bootstrap RTL -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <style>
        /* Simple CSS Variables */
        :root {
            --primary: #667eea;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --bg-light: #ffffff;
            --bg-dark: #2d3748;
            --text-light: #2d3748;
            --text-dark: #e2e8f0;
            --border-radius: 15px;
            --input-bg-light: rgba(255, 255, 255, 0.9);
            --input-bg-dark: rgba(45, 55, 72, 0.9);
            --input-border-light: rgba(255, 255, 255, 0.3);
            --input-border-dark: rgba(255, 255, 255, 0.2);
        }
        
        /* Dark Theme */
        [data-theme="dark"] {
            --text-color: var(--text-dark);
            --input-bg: var(--input-bg-dark);
            --input-border: var(--input-border-dark);
        }
        
        [data-theme="light"] {
            --text-color: var(--text-light);
            --input-bg: var(--input-bg-light);
            --input-border: var(--input-border-light);
        }
        
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Body */
        body {
            font-family: 'Vazirmatn', 'IRANSans', 'Vazir', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .theme-icon {
            font-size: 16px;
            transition: opacity 0.3s ease;
        }
        
        [data-theme="light"] .sun-icon {
            opacity: 1;
            color: #f6d55c;
        }
        
        [data-theme="light"] .moon-icon {
            opacity: 0;
        }
        
        [data-theme="dark"] .sun-icon {
            opacity: 0;
        }
        
        [data-theme="dark"] .moon-icon {
            opacity: 1;
            color: #e2e8f0;
        }
        
        /* Auth Container */
        .auth-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        /* Logo */
        .auth-logo {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .auth-title {
            color: var(--text-color);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            color: var(--text-color);
            opacity: 0.8;
            margin-bottom: 30px;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .form-label {
            display: block;
            color: var(--text-color);
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: var(--border-radius);
            color: var(--text-color);
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-control::placeholder {
            color: var(--text-color);
            opacity: 0.6;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        /* Button */
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--primary-gradient);
            border: none;
            border-radius: var(--border-radius);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        /* Alert */
        .alert {
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #721c24;
        }
        
        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #0a3622;
        }
        
        [data-theme="dark"] .alert-danger {
            color: #fca5a5;
        }
        
        [data-theme="dark"] .alert-success {
            color: #86efac;
        }
        
        /* Test Info */
        .test-info {
            margin-top: 30px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 14px;
        }
        
        .test-info p {
            color: var(--text-color);
            margin: 5px 0;
        }
        
        .test-info strong {
            color: var(--text-color);
        }
        
        /* Mobile Responsive */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }
            
            .auth-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .theme-toggle {
                top: 15px;
                left: 15px;
                width: 35px;
                height: 35px;
            }
            
            .theme-icon {
                font-size: 14px;
            }
            
            .auth-title {
                font-size: 1.6rem;
            }
            
            .form-control {
                font-size: 16px; /* Prevent iOS zoom */
            }
        }
    </style>
</head>

<body>
    <!-- Theme Toggle -->
    <button id="themeToggle" class="theme-toggle" aria-label="تغییر تم">
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
            <div class="alert alert-<?= $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'] ?>">
                <i class="fas fa-<?= $_SESSION['flash']['type'] === 'error' ? 'exclamation-triangle' : 'check-circle' ?> me-2"></i>
                <?= $_SESSION['flash']['message'] ?>
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
                       required
                       autocomplete="username"
                       id="username">
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock me-2"></i>
                    کلمه عبور
                </label>
                <input type="password" 
                       class="form-control" 
                       name="password" 
                       placeholder="123456"
                       required
                       autocomplete="current-password"
                       id="password">
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
            <p>کلمه عبور: <strong>123456</strong></p>
        </div>
    </div>
    
    <!-- Simple JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        // Load saved theme
        const savedTheme = localStorage.getItem('samanat_theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        
        // Toggle theme
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('samanat_theme', newTheme);
            
            // Simple animation
            this.style.transform = 'scale(0.9)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            console.log(`Theme changed to: ${newTheme}`);
        });
        
        // Focus on username
        document.getElementById('username')?.focus();
        
        // Form submission
        document.querySelector('form')?.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-primary');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال ورود...';
            btn.disabled = true;
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.id === 'username') {
                e.preventDefault();
                document.getElementById('password')?.focus();
            }
            
            if (e.key === 'Escape') {
                document.getElementById('username').value = '';
                document.getElementById('password').value = '';
                document.getElementById('username')?.focus();
            }
        });
    });
    </script>
</body>
</html>