<?php
/**
 * SAMANET MAIN LAYOUT - ENTERPRISE GRADE
 * نسخه: 3.0 حرفه‌ای
 * تاریخ: 1404/10/17
 * مطابق: استانداردهای MANDATORY Dashboard Design
 */

// امنیت: جلوگیری از دسترسی مستقیم
if (!defined('APP_PATH')) {
    die('دسترسی مستقیم مجاز نیست');
}

// تولید CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// متغیرهای صفحه
$page_title = $title ?? 'داشبورد سامانت';
$page_subtitle = $subtitle ?? '';
$current_route = $_GET['route'] ?? 'dashboard';
$user_info = $_SESSION['user'] ?? null;
$user_name = $user_info['full_name'] ?? 'کاربر';
$user_avatar = $user_info['avatar'] ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO & Security -->
    <meta name="description" content="سامانه مدیریت حواله و بایگانی اسناد - سامانت">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= $csrf_token ?>">
    
    <!-- Title -->
    <title><?= htmlspecialchars($page_title) ?> | سامانت</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico">
    
    <!-- Persian Font - Vazirmatn (Critical Load + Preload) -->
    <link rel="preload" href="/assets/fonts/webfonts/Vazirmatn-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/webfonts/Vazirmatn-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/webfonts/Vazirmatn-SemiBold.woff2" as="font" type="font/woff2" crossorigin>
    <link href="/assets/fonts/Vazirmatn-font-face.css?v=<?= time() ?>" rel="stylesheet">
    
    <!-- Critical CSS - Dashboard System -->
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    
    <!-- Persian Font Critical CSS - فونت فارسی ضروری -->
    <style>
        /* تضمین اعمال فونت وزیرمتن در همه المان‌ها */
        html, body, * {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'IRANSans', 'Vazir', 'Tahoma', Arial, sans-serif !important;
        }
        
        /* رفع مشکل نمایش فونت در مرورگرهای مختلف */
        body {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-variant-ligatures: common-ligatures;
        }
        
        /* تقویت فونت برای عناصر خاص فارسی */
        .fa, .persian-text, [lang="fa"], [dir="rtl"] {
            font-family: 'Vazirmatn', 'IRANSans', 'Vazir', sans-serif !important;
            font-feature-settings: 'liga' 1, 'dlig' 1;
        }
        
        /* تضمین اعمال در المان‌های مشکل‌دار */
        input, textarea, select, button, .btn, .form-control, 
        h1, h2, h3, h4, h5, h6, p, span, div, td, th, li {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }
        
        /* مشکل خاص با sidebar و header */
        .sidebar-menu-link, .header-title, .header-subtitle,
        .stat-label, .stat-value, .table-title, .panel-title, .task-text {
            font-family: 'Vazirmatn', sans-serif !important;
        }

        /* Profile Dropdown Styles */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-primary);
        }

        .profile-btn:hover {
            background: var(--gray-100);
            transform: translateY(-1px);
        }

        .avatar-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary, #667eea) 0%, var(--primary-light, #764ba2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .profile-name {
            font-weight: 500;
            font-size: 14px;
        }

        .dropdown-arrow {
            font-size: 10px;
            transition: transform 0.3s ease;
        }

        .profile-dropdown.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* Profile Menu */
        .profile-menu {
            position: absolute;
            top: 110%; /* Adjusted for better spacing */
            left: 0; /* Changed from right to left for RTL */
            background: var(--bg-primary, #fff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            min-width: 250px;
            padding: 16px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .profile-dropdown.active .profile-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px 12px;
        }

        .avatar-large {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary, #667eea) 0%, var(--primary-light, #764ba2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .profile-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary, #2d3748);
        }

        .profile-info small {
            color: var(--text-secondary, #718096);
        }

        .profile-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-primary, #2d3748);
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .profile-item:hover {
            background: var(--gray-50, #f9fafb);
            color: var(--text-primary, #2d3748);
            text-decoration: none;
        }

        .profile-item.logout {
            color: var(--danger, #e53e3e);
        }

        .profile-item.logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* Global Search Styles */
        .global-search {
            flex: 1;
            max-width: 400px;
            margin: 0 20px;
        }

        .search-wrapper {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 12px; /* Adjusted for RTL */
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            background: var(--bg-secondary, #f1f5f9);
            color: var(--text-primary, #2d3748);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary, #667eea);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 102, 126, 234), 0.1);
        }

        .search-icon {
            position: absolute;
            right: 12px; /* Adjusted for RTL */
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary, #718096);
            font-size: 14px;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--bg-primary, #fff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        /* Theme Toggle Fixed */
        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--gray-100, #f1f5f9);
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden; /* To hide icons during transition */
        }

        .theme-toggle-btn:hover {
            background: var(--gray-200, #e5e7eb);
            transform: scale(1.05);
        }

        .theme-icon-light,
        .theme-icon-dark {
            font-size: 16px;
            transition: all 0.3s ease;
            position: absolute;
        }

        [data-theme="light"] .theme-icon-light {
            opacity: 1;
            transform: translateY(0);
            color: #f59e0b;
        }

        [data-theme="light"] .theme-icon-dark {
            opacity: 0;
            transform: translateY(100%);
        }

        [data-theme="dark"] .theme-icon-light {
            opacity: 0;
            transform: translateY(-100%);
        }

        [data-theme="dark"] .theme-icon-dark {
            opacity: 1;
            transform: translateY(0);
            color: #e5e7eb;
        }

        .task-text {
            font-family: 'Vazirmatn', sans-serif !important;
        }

        /* --- HEADER FIXES --- */
        
        /* Dark mode theme colors */
        [data-theme="dark"] {
            --bg-primary: #1f2937;
            --text-primary: #f9fafb;
            --border-color: #374151;
        }
        
        [data-theme="light"] {
            --bg-primary: #ffffff;
            --text-primary: #1f2937;
            --border-color: #e5e7eb;
        }
        
        .dashboard-header {
            background: var(--bg-primary);
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }

        .action-buttons { display: flex; gap: 4px; justify-content: center; }
        .btn-action {
            width: 28px; height: 28px; border-radius: 6px; border: none;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            font-size: 12px; transition: all 0.2s ease;
        }
        .btn-view { background: #3b82f6; color: white; }
        .btn-edit { background: #10b981; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-action:hover { transform: scale(1.1); opacity: 0.9; }

        /* Profile Dropdown Improved */
        .profile-simple { 
            position: relative; 
        }
        
        .profile-btn-simple {
            background: none; border: none; display: flex; align-items: center;
            gap: 8px; padding: 8px 12px; border-radius: 8px;
            cursor: pointer; color: inherit; transition: all 0.2s ease;
        }
        
        .profile-btn-simple:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .profile-avatar-simple {
            width: 32px; height: 32px; border-radius: 50%; 
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 13px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .profile-text {
            font-weight: 500; font-size: 14px; 
            max-width: 120px; overflow: hidden; 
            text-overflow: ellipsis; white-space: nowrap;
        }
        
        .profile-menu-simple {
            position: absolute; top: 110%; left: 0; 
            background: var(--bg-primary, #fff);
            border: 1px solid var(--border-color, #e5e7eb); 
            border-radius: 12px; min-width: 200px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15); 
            z-index: 1000; padding: 12px 0;
            opacity: 0; visibility: hidden; transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .profile-menu-simple[style*="block"] {
            opacity: 1 !important; visibility: visible !important; 
            transform: translateY(0) !important;
        }
        
        .profile-item-simple {
            display: flex; align-items: center; gap: 10px; padding: 10px 16px;
            color: var(--text-primary, #2d3748); text-decoration: none; 
            font-size: 14px; transition: all 0.2s ease;
        }
        
        .profile-item-simple:hover { 
            background: var(--gray-100, #f3f4f6); 
            color: var(--text-primary, #2d3748);
            text-decoration: none;
        }
        
        .profile-item-simple.logout { 
            color: #ef4444; 
        }
        
        .profile-item-simple.logout:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        
        .profile-item-simple i {
            width: 16px; text-align: center; font-size: 13px;
        }
        
        /* Header Search */
        .header-search {
            position: relative; margin: 0 15px;
        }
        
        .search-input-header {
            width: 250px; padding: 8px 35px 8px 12px; border: 1px solid #e5e7eb;
            border-radius: 20px; background: var(--gray-100, #f3f4f6);
            font-size: 13px; transition: all 0.2s ease;
        }
        
        .search-input-header:focus {
            outline: none; border-color: #667eea; width: 300px;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .header-search i {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; font-size: 12px; pointer-events: none;
        }
        
        @media (max-width: 768px) {
            .header-search { display: none; }
        }

        .bottom-nav-fixed {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: var(--bg-primary, #fff); border-top: 1px solid #e5e7eb;
            display: flex; justify-content: space-around;
            padding: 8px 0 calc(8px + env(safe-area-inset-bottom));
            z-index: 1000;
        }
        .nav-item-fixed {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            padding: 8px 4px; text-decoration: none; color: #6b7280;
            font-size: 12px; min-width: 60px; transition: color 0.2s ease;
        }
        .nav-item-fixed.active { color: #667eea; }
        .nav-item-fixed i { font-size: 18px; }
        .nav-item-fixed span { font-size: 10px; }
        
        @media (min-width: 768px) { .bottom-nav-fixed { display: none; } }
        @media (max-width: 767px) { body { padding-bottom: 70px !important; } }
    </style>
    
    <!-- FontAwesome Icons - با fallback -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Fallback Icons with Unicode -->
    <style>
        /* Fallback اگر FontAwesome لود نشد */
        .fas.fa-sun::before { content: "☀️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-moon::before { content: "🌙"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-user::before { content: "👤"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-chevron-down::before { content: "▼"; font-family: "Arial", sans-serif; font-size: 8px; }
        .fas.fa-search::before { content: "🔍"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-eye::before { content: "👁️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-edit::before { content: "✏️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-trash::before { content: "🗑️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-user-edit::before { content: "⚙️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-cog::before { content: "⚙️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-sign-out-alt::before { content: "🚪"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-bars::before { content: "☰"; font-family: "Arial", sans-serif; }
        .fas.fa-home::before { content: "🏠"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-file-alt::before { content: "📄"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-users::before { content: "👥"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-tags::before { content: "🏷️"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-plus::before { content: "➕"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .fas.fa-list::before { content: "📝"; font-family: "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
    </style>
    
    <!-- Additional CSS -->
    <?php if (isset($additional_css) && is_array($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
            <link href="<?= htmlspecialchars($css_file) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Theme System - Preload -->
    <script src="/assets/js/theme-system.js"></script>
</head>

<body>
    <!-- MANDATORY: Professional Dashboard Structure -->
    <div class="dashboard-pro">
        
        <!-- MANDATORY: Professional Header (60px) -->
        <header class="dashboard-header">
            <div class="header-content">
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" onclick="toggleSidebar()" title="منو" style="
                    width: 40px; height: 40px; border-radius: 50%;
                    background: rgba(255, 255, 255, 0.1); border: none;
                    cursor: pointer; display: none; align-items: center; justify-content: center;
                    font-size: 16px;
                ">
                    ☰
                </button>
                
                <!-- Page Title -->
                <h1 class="header-title"><?= htmlspecialchars($page_title) ?></h1>
                <?php if (!empty($page_subtitle)): ?>
                    <p class="header-subtitle"><?= htmlspecialchars($page_subtitle) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="header-actions" style="display: flex; align-items: center; gap: 12px;">
                <!-- Global Search -->
                <div style="position: relative; margin: 0 10px;">
                                         <input type="text" placeholder="جستجو..." style="
                        width: 200px; padding: 8px 35px 8px 12px; border: 1px solid #e5e7eb;
                        border-radius: 20px; background: rgba(255,255,255,0.1);
                        font-size: 13px; color: inherit;
                    ">
                    <span style="
                        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                        color: #9ca3af; font-size: 14px; pointer-events: none;
                    ">🔍</span>
                </div>

                <!-- Theme Toggle Button (EMOJI VERSION) -->
                <button id="themeToggle" onclick="toggleTheme()" style="
                    width: 40px; height: 40px; border-radius: 50%; border: none;
                    background: rgba(255, 255, 255, 0.1); cursor: pointer;
                    display: flex; align-items: center; justify-content: center;
                    color: inherit; transition: all 0.2s ease; font-size: 18px;
                " title="تغییر تم">
                    <span id="theme-icon-light">☀️</span>
                    <span id="theme-icon-dark" style="display: none;">🌙</span>
                </button>
                
                <!-- Profile Menu (FIXED) -->
                <div style="position: relative;">
                    <button id="profileBtn" onclick="toggleProfileMenu()" style="
                        background: none; border: none; display: flex; align-items: center;
                        gap: 8px; padding: 8px 12px; border-radius: 8px;
                        cursor: pointer; color: inherit; transition: all 0.2s ease;
                    ">
                        <div style="
                            width: 32px; height: 32px; border-radius: 50%; 
                            background: linear-gradient(135deg, #667eea, #764ba2);
                            display: flex; align-items: center; justify-content: center;
                            color: white; font-size: 16px;
                        ">
                            👤
                        </div>
                        <span style="font-weight: 500; font-size: 14px;">مدیر سیستم</span>
                        <span style="font-size: 10px;">▼</span>
                    </button>
                    
                    <div id="profileMenu" style="
                        position: absolute; top: 110%; left: 0; 
                        background: var(--bg-primary, #fff);
                        border: 1px solid #e5e7eb; border-radius: 12px; min-width: 200px;
                        box-shadow: 0 8px 25px rgba(0,0,0,0.15); 
                        z-index: 1000; padding: 12px 0; display: none;
                    ">
                        <a href="#" style="
                            display: flex; align-items: center; gap: 10px; padding: 10px 16px;
                            color: #2d3748; text-decoration: none; font-size: 14px;
                            transition: all 0.2s ease;
                        " onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                            <span style="width: 16px; text-align: center;">⚙️</span> پروفایل
                        </a>
                        <a href="#" style="
                            display: flex; align-items: center; gap: 10px; padding: 10px 16px;
                            color: #2d3748; text-decoration: none; font-size: 14px;
                            transition: all 0.2s ease;
                        " onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                            <span style="width: 16px; text-align: center;">⚙️</span> تنظیمات
                        </a>
                        <hr style="margin: 8px 0; border: none; border-top: 1px solid #e5e7eb;">
                        <a href="/logout" style="
                            display: flex; align-items: center; gap: 10px; padding: 10px 16px;
                            color: #ef4444; text-decoration: none; font-size: 14px;
                            transition: all 0.2s ease;
                        " onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                            <span style="width: 16px; text-align: center;">🚪</span> خروج
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- MANDATORY: Professional Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">سامانت</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="/dashboard" class="sidebar-menu-link <?= ($current_route === 'dashboard') ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">🏠</span>
                        داشبورد
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/requests" class="sidebar-menu-link <?= (strpos($current_route, 'request') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">📄</span>
                        درخواست‌ها
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/users" class="sidebar-menu-link <?= (strpos($current_route, 'user') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">👥</span>
                        کاربران
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/tags" class="sidebar-menu-link <?= (strpos($current_route, 'tag') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">🏷️</span>
                        برچسب‌ها
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/settings" class="sidebar-menu-link <?= (strpos($current_route, 'setting') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">⚙️</span>
                        تنظیمات
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/logout" class="sidebar-menu-link">
                        <span class="sidebar-menu-icon">🚪</span>
                        خروج
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        
        <!-- MANDATORY: Main Content Area -->
        <div class="dashboard-content">
            
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> mb-4">
                    <i class="fas fa-<?= $_SESSION['flash_message']['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>
            
            <!-- Page Content -->
            <div class="page-content">
                <?php
                // بارگذاری محتوای صفحه
                if (isset($content_view) && file_exists($content_view)) {
                    require $content_view;
                } elseif (isset($content)) {
                    echo $content;
                } else {
                    echo '<div class="alert alert-danger">محتوای صفحه یافت نشد.</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- MANDATORY: Mobile Navigation & Footer -->
        <div class="bottom-nav-fixed">
            <a href="/dashboard" class="nav-item-fixed active">
                <span style="font-size: 18px;">🏠</span>
                <span>داشبورد</span>
            </a>
            <a href="/requests/create" class="nav-item-fixed">
                <span style="font-size: 18px;">➕</span>
                <span>جدید</span>
            </a>
            <a href="/requests" class="nav-item-fixed">
                <span style="font-size: 18px;">📝</span>
                <span>درخواست‌ها</span>
            </a>
            <a href="/tags" class="nav-item-fixed">
                <span style="font-size: 18px;">🏷️</span>
                <span>برچسب‌ها</span>
            </a>
            <a href="/users" class="nav-item-fixed">
                <span style="font-size: 18px;">👥</span>
                <span>کاربران</span>
            </a>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        // ======== GLOBAL FUNCTIONS ========
        
        // Mobile Sidebar Toggle (Global)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (!sidebar) return;
            
            sidebar.classList.toggle('open');
            
            if (sidebar.classList.contains('open')) {
                if (overlay) {
                    overlay.style.display = 'block';
                    setTimeout(() => overlay.style.opacity = '1', 10);
                }
            } else {
                if (overlay) {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.style.display = 'none', 300);
                }
            }
        }
        
        // Theme Toggle (Global)
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('samanat_theme', newTheme);
            
            // Update theme icons
            const lightIcon = document.getElementById('theme-icon-light');
            const darkIcon = document.getElementById('theme-icon-dark');
            
            if (lightIcon && darkIcon) {
                if (newTheme === 'dark') {
                    lightIcon.style.display = 'none';
                    darkIcon.style.display = 'inline-block';
                } else {
                    lightIcon.style.display = 'inline-block';
                    darkIcon.style.display = 'none';
                }
            }
        }
        
        // Profile Toggle (Global)
        function toggleProfileMenu() {
            const profileMenu = document.getElementById('profileMenu');
            if (profileMenu) {
                const isVisible = profileMenu.style.display === 'block';
                profileMenu.style.display = isVisible ? 'none' : 'block';
            }
        }
        
        // ======== DOM READY ========
        document.addEventListener('DOMContentLoaded', function() {
            
            // Load saved theme
            const savedTheme = localStorage.getItem('samanat_theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Set initial theme icons
            const lightIcon = document.getElementById('theme-icon-light');
            const darkIcon = document.getElementById('theme-icon-dark');
            
            if (lightIcon && darkIcon) {
                if (savedTheme === 'dark') {
                    lightIcon.style.display = 'none';
                    darkIcon.style.display = 'inline-block';
                } else {
                    lightIcon.style.display = 'inline-block';
                    darkIcon.style.display = 'none';
                }
            }
            
            // Theme Toggle Event
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
            }
            
            // Profile Dropdown Events
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');
            
            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleProfileMenu();
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileMenu.style.display = 'none';
                    }
                });
                
                // Prevent menu from closing when clicking inside
                profileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Mobile Menu Events
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', toggleSidebar);
            }
            
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }

            // Alert Auto-close
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
            
            console.log('✅ Samanet Dashboard v3.0 - All functions loaded');
        });
    </script>
</body>
</html>