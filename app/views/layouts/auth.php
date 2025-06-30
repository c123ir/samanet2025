<?php
/**
 * نام فایل: auth.php
 * مسیر: /app/views/layouts/auth.php
 * هدف: قالب استاندارد و بازنویسی شده برای احراز هویت
 * نسخه: 4.1 (ایزوله شده)
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
    
    <!-- Fonts -->
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* --- CSS Reset & Base --- */
        :root {
            --bg-light: #f4f7fa;
            --bg-dark: #1a202c;
            --card-bg-light: #ffffff;
            --card-bg-dark: #2d3748;
            --text-light: #2d3748;
            --text-dark: #e2e8f0;
            --input-bg-light: #edf2f7;
            --input-bg-dark: #4a5568;
            --primary-color: #667eea;
            --primary-hover: #5a67d8;
            --border-color: #e2e8f0;
            --border-dark: #4a5568;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-family: 'Vazirmatn', sans-serif;
        }

        [data-theme="dark"] {
            --bg-light: var(--bg-dark);
            --card-bg-light: var(--card-bg-dark);
            --text-light: var(--text-dark);
            --input-bg-light: var(--input-bg-dark);
            --border-color: var(--border-dark);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-light);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        /* --- Force Hide Parent Layout --- */
        #main-sidebar, .main-header, .main-sidebar, .page-header, .sidebar, .navbar, .header, .content-wrapper, .main-footer {
            display: none !important;
        }

        .auth-wrapper {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000; /* Lower z-index */
        }

        /* --- Theme Toggle --- */
        .theme-toggle {
            position: fixed;
            top: 25px;
            left: 25px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background-color: var(--card-bg-light);
            color: var(--text-light);
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1001;
        }
        .theme-toggle:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: var(--shadow);
        }

        /* --- Auth Container --- */
        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 40px 35px;
            background-color: var(--card-bg-light);
            border-radius: 16px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: background-color 0.3s;
        }

        .auth-header .logo {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .auth-header p {
            font-size: 14px;
            opacity: 0.7;
            margin-bottom: 30px;
        }

        /* --- Form Elements --- */
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: var(--input-bg-light);
            color: var(--text-light);
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        /* --- Button --- */
        .btn-submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: var(--primary-color);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* --- Test Info --- */
        .test-info {
            margin-top: 25px;
            padding: 15px;
            background-color: var(--input-bg-light);
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
        }
        .test-info p {
            margin: 5px 0;
        }
        
        /* --- Alert Messages --- */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #c53030;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- Theme Toggle Button -->
        <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
            <span id="theme-icon">🌙</span>
        </button>

        <!-- Main Authentication Container -->
        <main class="auth-container">
            
            <header class="auth-header">
                <div class="logo">🔹</div>
                <h1>ورود به سامانت</h1>
                <p>سامانه مدیریت حواله و بایگانی اسناد</p>
            </header>

            <!-- Flash Message -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?= url('login') ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                
                <div class="form-group">
                    <label for="username">نام کاربری</label>
                    <input type="text" id="username" class="form-control" name="username" value="admin" required>
                </div>
                
                <div class="form-group">
                    <label for="password">کلمه عبور</label>
                    <input type="password" id="password" class="form-control" name="password" value="admin123" required>
                </div>
                
                <button type="submit" class="btn-submit">ورود به سامانه</button>
            </form>

            <div class="test-info">
                <p><strong>اطلاعات تست:</strong> نام کاربری: <strong>admin</strong> | رمز عبور: <strong>admin123</strong></p>
            </div>

        </main>
    </div>

    <!-- Load Central Theme System -->
    <script src="<?= asset('js/theme-system.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            if (passwordField) passwordField.focus();
        });
    </script>

</body>
</html>