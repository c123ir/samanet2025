<?php
/**
 * نام فایل: main.php
 * مسیر فایل: /app/views/layouts/main.php
 * توضیح: قالب اصلی صفحات سامانت
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
    <meta name="keywords" content="حواله، بایگانی، اسناد، مدیریت مالی">
    <meta name="author" content="تیم توسعه سامانت">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $csrf_token ?>">
    
    <!-- Theme Management handled by theme-system.js -->
    <script src="<?= asset('js/theme-system.js') ?>"></script>
    
    <title><?= isset($page_title) ? $page_title . ' - ' . $app_name : $app_name ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- Bootstrap RTL از CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Persian Fonts -->
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <!-- CSS Files -->
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/components.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/responsive.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/flat-design.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/theme-system.css') ?>" rel="stylesheet">
    
    <!-- Additional CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link href="<?= asset($css) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        /* CSS Variables handled by theme-system.css */
        
        body {
            font-family: 'Vazirmatn', 'IRANSans', 'Segoe UI', sans-serif;
            background: var(--body-bg);
            background-attachment: fixed;
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        /* Glass Effect Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 200, 255, 0.2) 0%, transparent 50%);
            z-index: -1;
        }
        
        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .content-wrapper {
            flex: 1;
            margin-top: 80px;
            margin-right: 300px; /* فاصله برای sidebar در دسکتاپ */
            position: relative;
            transition: margin-right 0.3s ease;
        }
        
        /* Glass Cards */
        .card {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid var(--glass-border);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .card-body {
            padding: 2rem;
            color: var(--text-color);
        }
        
        /* Glass Buttons */
        .btn {
            border-radius: 15px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #06D6A0, #4ecdc4);
            color: white;
            box-shadow: 0 4px 15px rgba(6, 214, 160, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
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
        
        /* Glass Navigation */
        .navbar {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
        }
        
        /* Glass Sidebar */
        .sidebar {
            position: fixed;
            top: 80px;
            right: 0;
            width: 300px;
            height: calc(100vh - 80px);
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-left: 1px solid var(--glass-border);
            padding: 1rem 0;
            box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: var(--text-color);
            padding: 1rem 1.5rem;
            border-radius: 0 15px 15px 0;
            margin: 0.25rem 0;
            margin-left: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: right 0.3s ease;
            z-index: -1;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            transform: translateX(-5px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .sidebar .nav-link:hover::before,
        .sidebar .nav-link.active::before {
            right: 0;
        }
        
        .sidebar .nav-link i {
            margin-left: 0.75rem;
            width: 20px;
            font-size: 1.1rem;
        }
        
        /* Glass Page Header */
        .page-header {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }
        
        /* Glass Alerts */
        .alert {
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            color: #2d3748;
            font-weight: 500;
        }
        
        .alert-success {
            background: rgba(6, 214, 160, 0.15);
            border-color: rgba(6, 214, 160, 0.3);
        }
        
        .alert-danger {
            background: rgba(255, 107, 107, 0.15);
            border-color: rgba(255, 107, 107, 0.3);
        }
        
        .alert-warning {
            background: rgba(240, 147, 251, 0.15);
            border-color: rgba(240, 147, 251, 0.3);
        }
        
        .alert-info {
            background: rgba(78, 205, 196, 0.15);
            border-color: rgba(78, 205, 196, 0.3);
        }
        
        /* Glass Form Controls */
        .form-control, .form-select {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 0.75rem 1rem;
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }
        
        .form-control::placeholder {
            color: var(--placeholder-color);
        }
        
        /* Glass Table */
        .table {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid var(--glass-border);
            box-shadow: var(--box-shadow);
        }
        
        .table thead th {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-color);
            font-weight: 600;
            padding: 1rem;
        }
        
        .table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.01);
        }
        
        .table tbody td {
            color: var(--text-color);
            padding: 1rem;
        }
        
        /* Glass Loading & Spinners */
        .loading {
            display: none;
            text-align: center;
            padding: 3rem;
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-radius: var(--border-radius);
            margin: 2rem 0;
        }
        
        .spinner-border {
            border: 3px solid rgba(102, 126, 234, 0.2);
            border-top: 3px solid var(--primary-color);
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Glass Footer */
        footer {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border-top: 1px solid var(--glass-border);
            box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Floating Elements */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }
        
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite ease-in-out;
            aspect-ratio: 1 / 1; /* حفظ نسبت مربعی */
            width: 100px;
        }
        
        .floating-circle:nth-child(1) {
            width: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-circle:nth-child(2) {
            width: 150px;
            top: 60%;
            right: 15%;
            animation-delay: 3s;
        }
        
        .floating-circle:nth-child(3) {
            width: 80px;
            bottom: 20%;
            left: 60%;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(20px) rotate(240deg); }
        }
        
        /* Mobile Responsive Improvements */
        @media (max-width: 1199px) {
            .content-wrapper {
                margin-right: 0 !important;
                margin-top: 80px;
                padding: 1rem;
            }
            
            .sidebar {
                position: fixed;
                top: 80px;
                right: -100%;
                width: 300px;
                height: calc(100vh - 80px);
                z-index: 1050;
                transition: right 0.3s ease-in-out;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: var(--backdrop-blur);
                -webkit-backdrop-filter: var(--backdrop-blur);
                border-radius: 20px 0 0 0;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
            }
            
            .sidebar.show {
                right: 0;
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.3);
            }
        }

        @media (max-width: 768px) {
            .navbar {
                height: 70px;
                padding: 0.5rem 1rem;
            }

            .content-wrapper {
                margin-top: 70px;
                padding: 1rem;
            }
            
            .sidebar {
                top: 70px;
                height: calc(100vh - 70px);
                width: 280px;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .navbar-brand span {
                font-size: 1.1rem;
            }
            
            .navbar-brand div {
                width: 35px !important;
                height: 35px !important;
            }

            /* Theme toggle responsive handled by theme-system.css */

            .floating-circle {
                display: none;
            }

            .nav-link.dropdown-toggle {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.9rem !important;
                border-radius: 10px !important;
                background: rgba(255, 255, 255, 0.1) !important;
                backdrop-filter: blur(10px) !important;
                border: 1px solid rgba(255, 255, 255, 0.2) !important;
            }
            
            .nav-link.dropdown-toggle img {
                width: 28px !important;
                height: 28px !important;
            }
            
            .nav-link.dropdown-toggle span {
                font-size: 0.85rem !important;
                max-width: 100px !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
            }

            /* بهبود dropdown menu در موبایل */
            .dropdown-menu {
                position: absolute !important;
                top: 100% !important;
                left: auto !important;
                right: 0 !important;
                min-width: 200px !important;
                max-width: 250px !important;
                width: auto !important;
                margin-top: 0.5rem !important;
                padding: 0.75rem !important;
                border-radius: 15px !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
                z-index: 1060 !important;
            }
            
            .dropdown-item {
                padding: 0.75rem 1rem !important;
                font-size: 0.95rem !important;
                border-radius: 10px !important;
                margin: 0.25rem 0 !important;
                white-space: nowrap !important;
            }
            
            .dropdown-item i {
                width: 18px !important;
                text-align: center !important;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                height: 60px;
                padding: 0.5rem 0.75rem;
            }

            .content-wrapper {
                margin-top: 60px;
                padding: 0.75rem;
            }
            
            .sidebar {
                top: 60px;
                height: calc(100vh - 60px);
                width: 250px;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-brand span {
                font-size: 1rem;
            }
            
            .navbar-brand div {
                width: 30px !important;
                height: 30px !important;
            }

            /* Theme toggle responsive handled by theme-system.css */

            .card-body {
                padding: 1rem;
            }
            
            .page-header {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }

            .nav-link.dropdown-toggle {
                padding: 0.4rem 0.6rem !important;
                font-size: 0.8rem !important;
            }
            
            .nav-link.dropdown-toggle img {
                width: 24px !important;
                height: 24px !important;
            }
            
            .nav-link.dropdown-toggle span {
                font-size: 0.75rem !important;
                max-width: 80px !important;
            }

            /* dropdown کوچکتر در موبایل کوچک */
            .dropdown-menu {
                min-width: 180px !important;
                max-width: 200px !important;
                padding: 0.5rem !important;
            }
            
            .dropdown-item {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.85rem !important;
            }
        }

        /* Desktop Layout */
        @media (min-width: 1200px) {
            .content-wrapper {
                margin-right: 300px;
                margin-top: 80px;
                padding: 2rem;
                transition: margin-right 0.3s ease;
            }

            .sidebar {
                position: fixed;
                top: 80px;
                right: 0;
                width: 300px;
                height: calc(100vh - 80px);
                z-index: 1000;
                overflow-y: auto;
                transform: translateX(0);
                transition: transform 0.3s ease;
            }

            .navbar {
                height: 80px;
                padding: 1rem 2rem;
            }

            .navbar-toggler {
                display: none;
            }

            .floating-circle {
                display: block;
            }
        }

        /* بهبود کلی navbar برای موبایل */
        @media (max-width: 992px) {
            .navbar-nav {
                gap: 0.5rem;
            }
            
            .nav-item {
                margin: 0 0.25rem;
            }
            
            .navbar-toggler {
                padding: 0.5rem;
                border: none !important;
                background: rgba(255, 255, 255, 0.1) !important;
                border-radius: 10px !important;
                backdrop-filter: blur(10px) !important;
            }
            
            .navbar-toggler:focus {
                box-shadow: none !important;
            }
            
            .navbar-toggler i {
                color: white !important;
                font-size: 1.2rem !important;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--info-color);
        }
        
        /* Dropdown Glass Effect */
        .dropdown-menu {
            background: var(--glass-bg);
            backdrop-filter: var(--backdrop-blur);
            -webkit-backdrop-filter: var(--backdrop-blur);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: 10px;
            margin: 0.25rem 0;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            color: var(--text-color);
        }
        
        .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(5px);
        }
        
        /* Avatar Glass Effect */
        .rounded-circle {
            border: 2px solid var(--glass-border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Persian Number Fonts */
        .persian-num {
            font-family: 'IRANSans', monospace;
        }
        
        /* Enhanced Stats Cards */
        .stats-card {
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 6px 25px rgba(0,0,0,0.08) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden !important;
            position: relative !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            background: rgba(255, 255, 255, 0.12) !important;
        }

        .stats-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 12px 35px rgba(0,0,0,0.15) !important;
        }

        .stats-card .card-body {
            position: relative;
            z-index: 2;
            padding: 1.8rem !important;
        }

        .stats-icon {
            opacity: 0.9;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .stats-card:hover .stats-icon {
            opacity: 1;
            transform: scale(1.05);
        }

        /* Better Gradient Buttons */
        .btn-success {
            background: var(--success-gradient) !important;
            border: none !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(6, 214, 160, 0.4) !important;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 214, 160, 0.6) !important;
        }

        .btn-warning {
            background: var(--warning-gradient) !important;
            border: none !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4) !important;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.6) !important;
        }

        .btn-danger {
            background: var(--danger-gradient) !important;
            border: none !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4) !important;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6) !important;
        }

        .btn-info {
            background: var(--info-gradient) !important;
            border: none !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.4) !important;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.6) !important;
        }

        /* Theme Toggle handled by theme-system.css */

        /* بهبود طراحی Flat */
        .flat-card {
            background: rgba(255, 255, 255, 0.9) !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            backdrop-filter: blur(10px) !important;
            transition: all 0.3s ease !important;
        }

        .flat-card:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .flat-btn {
            border-radius: 15px !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 600 !important;
            border: none !important;
            transition: all 0.3s ease !important;
            backdrop-filter: blur(10px) !important;
        }

        .flat-btn:hover {
            transform: translateY(-2px) !important;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <!-- Floating Background Elements -->
        <div class="floating-elements">
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
        </div>

        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid">
                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center" href="<?= url('dashboard') ?>">
                    <div style="width: 40px; height: 40px; background: var(--primary-gradient); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-left: 0.5rem;">
                        <i class="fas fa-gem text-white"></i>
                    </div>
                    <span>سامانت</span>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler d-lg-none" type="button" onclick="toggleSidebar()" style="border: none; background: none; color: white; font-size: 1.5rem;">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Theme Toggle & User Menu -->
                <div class="navbar-nav ms-auto d-flex align-items-center">
                    <!-- Dark/Light Mode Toggle -->
                    <div class="nav-item me-3">
                        <button class="theme-toggle" title="تغییر تم" data-bs-toggle="tooltip">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </div>
                    
                    <?php if (Security::isLoggedIn()): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="<?= asset('img/default-avatar.svg') ?>" alt="<?= htmlspecialchars($user['full_name'] ?? 'کاربر') ?>" class="rounded-circle me-2" width="32" height="32">
                                <span><?= htmlspecialchars($user['full_name'] ?? 'کاربر') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url('profile') ?>">
                                    <i class="fas fa-user me-2"></i>پروفایل
                                </a></li>
                                <li><a class="dropdown-item" href="<?= url('settings') ?>">
                                    <i class="fas fa-cog me-2"></i>تنظیمات
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>خروج
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="nav-link" href="<?= url('login') ?>">ورود</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="d-flex">
            <!-- Sidebar -->
            <?php if (Security::isLoggedIn()): ?>
                <nav class="sidebar d-lg-block" id="sidebar">
                    <div class="sidebar-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link <?= $current_route === 'dashboard' ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                                    <i class="fas fa-tachometer-alt"></i>
                                    داشبورد
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($current_route, 'requests') === 0 ? 'active' : '' ?>" href="<?= url('requests') ?>">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    درخواست‌های حواله
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($current_route, 'documents') === 0 ? 'active' : '' ?>" href="<?= url('documents') ?>">
                                    <i class="fas fa-folder-open"></i>
                                    بایگانی اسناد
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($current_route, 'tags') === 0 ? 'active' : '' ?>" href="<?= url('tags') ?>">
                                    <i class="fas fa-tags"></i>
                                    مدیریت تگ‌ها
                                </a>
                            </li>
                            
                            <?php if (Security::checkPermission('manager')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($current_route, 'users') === 0 ? 'active' : '' ?>" href="<?= url('users') ?>">
                                        <i class="fas fa-users"></i>
                                        مدیریت کاربران
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($current_route, 'groups') === 0 ? 'active' : '' ?>" href="<?= url('groups') ?>">
                                        <i class="fas fa-users-cog"></i>
                                        مدیریت گروه‌ها
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= strpos($current_route, 'reports') === 0 ? 'active' : '' ?>" href="<?= url('reports') ?>">
                                    <i class="fas fa-chart-bar"></i>
                                    گزارش‌ها
                                </a>
                            </li>
                            
                            <?php if (Security::checkPermission('admin')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($current_route, 'settings') === 0 ? 'active' : '' ?>" href="<?= url('settings') ?>">
                                        <i class="fas fa-cogs"></i>
                                        تنظیمات سیستم
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <li class="nav-item mt-3">
                                <a class="nav-link text-light" href="<?= url('help') ?>">
                                    <i class="fas fa-question-circle"></i>
                                    راهنما
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="content-wrapper flex-grow-1">
                <div class="container-fluid px-4">
                    <!-- Flash Messages -->
                    <?php if (isset($flash) && $flash): ?>
                        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $flash['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Content -->
                    <?php include $viewFile; ?>
                </div>
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-white shadow-sm mt-auto py-3">
            <div class="container-fluid px-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted">© <?= fa_num(jdate('Y')) ?> سامانت - تمامی حقوق محفوظ است</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">نسخه <?= fa_num($app_version) ?></span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 9999; display: none !important;">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">در حال بارگذاری...</span>
            </div>
            <div class="mt-2">در حال بارگذاری...</div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <!-- Debug Helper (فقط در development) -->
    <?php if (APP_ENV === 'development'): ?>
    <script src="<?= asset('js/debug_helper.js') ?>"></script>
    <?php endif; ?>
    
    <!-- Additional JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= asset($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // تابع toggle sidebar برای موبایل
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('show');
                
                // اضافه کردن overlay برای موبایل
                if (sidebar.classList.contains('show')) {
                    const overlay = document.createElement('div');
                    overlay.id = 'sidebar-overlay';
                    overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040;';
                    overlay.onclick = function() {
                        sidebar.classList.remove('show');
                        this.remove();
                    };
                    document.body.appendChild(overlay);
                } else {
                    const overlay = document.getElementById('sidebar-overlay');
                    if (overlay) overlay.remove();
                }
            }
        }

        // بستن sidebar هنگام کلیک خارج از آن
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#sidebar') && !e.target.closest('.navbar-toggler')) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.remove('show');
                    const overlay = document.getElementById('sidebar-overlay');
                    if (overlay) overlay.remove();
                }
            }
        });

        // تابع نمایش alert
        function showAlert(message, type = 'info') {
            const alertClass = type === 'error' ? 'danger' : type;
            const alertHtml = `
                <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const container = document.querySelector('.container-fluid');
            if (container) {
                container.insertAdjacentHTML('afterbegin', alertHtml);
                
                // حذف خودکار پس از 5 ثانیه
                setTimeout(function() {
                    const alert = container.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }

        // تایید حذف
        function confirmDelete(message = 'آیا از حذف این مورد اطمینان دارید؟') {
            return confirm(message);
        }

        // فرمت کردن اعداد به فارسی
        function formatPersianNumber(number) {
            const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            return number.toString().replace(/\d/g, function(digit) {
                return persianDigits[digit];
            });
        }

        // تبدیل اعداد فارسی به انگلیسی
        function toEnglishNumbers(str) {
            const persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
            const arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];
            
            for (let i = 0; i < 10; i++) {
                str = str.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
            }
            return str;
        }

        // Theme system handled by theme-system.js

        // آماده سازی اولیه صفحه
        document.addEventListener('DOMContentLoaded', function() {
            // Theme بارگذاری می‌شود توسط theme-system.js
            
            // راه‌اندازی tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // راه‌اندازی popovers  
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // تنظیم تاریخ فارسی در input های date
            const persianDateInputs = document.querySelectorAll('.persian-date');
            persianDateInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = toEnglishNumbers(this.value);
                });
            });

            // اضافه کردن کلاس flat-card به کارت‌ها
            const cards = document.querySelectorAll('.card:not(.stats-card)');
            cards.forEach(card => {
                card.classList.add('flat-card');
            });

            // اضافه کردن کلاس flat-btn به دکمه‌ها
            const buttons = document.querySelectorAll('.btn:not(.theme-toggle):not(.navbar-toggler)');
            buttons.forEach(btn => {
                btn.classList.add('flat-btn');
            });
        });

        // اضافه کردن smooth scroll برای لینک‌ها
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href^="#"]');
            if (link) {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    </script>

    <!-- Inline JavaScript -->
    <?php if (isset($inline_js)): ?>
        <script><?= $inline_js ?></script>
    <?php endif; ?>
</body>
</html>