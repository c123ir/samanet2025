<?php
/**
 * نام فایل: auth.php
 * مسیر فایل: /app/views/layouts/auth.php
 * توضیح: قالب صفحات احراز هویت سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="سامانت - سامانه مدیریت حواله و بایگانی اسناد">
    <meta name="keywords" content="ورود، سامانت، حواله، بایگانی">
    <meta name="author" content="تیم توسعه سامانت">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $csrf_token ?>">
    
    <title>ورود به سامانت - <?= APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- Vendor CSS Files -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
    
    <!-- Persian Fonts -->
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #06D6A0, #4ecdc4);
            --warning-gradient: linear-gradient(135deg, #f093fb, #f5576c);
            --danger-gradient: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            --success-gradient: linear-gradient(135deg, #06D6A0, #4ecdc4);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --backdrop-blur: blur(20px);
            --box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --border-radius: 20px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Vazirmatn', 'IRANSans', 'Vazir', 'Tahoma', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 35%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Animated Background */
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
            z-index: -2;
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
        
        /* Auth Container */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
        }
        
        .auth-card {
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
        
        .auth-card::before {
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
        
        /* Logo & Header */
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
            color: rgba(45, 55, 72, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.9rem 1rem 0.9rem 3rem;
            font-size: 1rem;
            color: #2d3748;
            width: 100%;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .form-control::placeholder {
            color: rgba(45, 55, 72, 0.5);
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
        
        /* Buttons */
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
        
        /* Alerts */
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
        
        /* Test Info */
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
        
        .test-info .credential {
            background: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #052c65;
        }
        
        /* Floating Shapes */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }
        
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite ease-in-out;
        }
        
        .floating-shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 15%;
            animation-delay: 3s;
        }
        
        .floating-shape:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 20%;
            left: 60%;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(20px) rotate(240deg); }
        }
        
        /* Mobile Responsive */
        @media (max-width: 576px) {
            .auth-wrapper {
                padding: 1rem 0.5rem;
            }
            
            .auth-card {
                padding: 2rem 1.5rem;
                margin: 1rem 0;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 0.8rem 0.8rem 0.8rem 2.5rem;
            }
            
            .form-icon {
                left: 0.8rem;
            }
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .auth-card {
                background: rgba(45, 55, 72, 0.15);
            }
            
            .form-control {
                background: rgba(45, 55, 72, 0.15);
            }
        }
        
        /* High Contrast Mode */
        @media (prefers-contrast: high) {
            .auth-card {
                border: 2px solid var(--primary-color);
            }
        }
        
        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <!-- Auth Container -->
    <div class="auth-wrapper">
        <div class="auth-card">
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

            <!-- Form Content -->
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
                            placeholder="نام کاربری خود را وارد کنید"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>
                
                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>
                        رمز عبور
                    </label>
                    <div class="position-relative">
                        <i class="form-icon fas fa-lock"></i>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="رمز عبور خود را وارد کنید"
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

            <!-- Test Info -->
            <div class="test-info">
                <h6>
                    <i class="fas fa-info-circle me-2"></i>
                    اطلاعات تست
                </h6>
                <div class="credential">
                    <strong>نام کاربری:</strong> admin
                </div>
                <div class="credential">
                    <strong>رمز عبور:</strong> 123456
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Page Specific JS -->
    <script>
        // Focus on username field
        document.getElementById('username').focus();
        
        // Form animation on submit
        document.querySelector('form').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال ورود...';
            btn.disabled = true;
        });
    </script>
</body>
</html>