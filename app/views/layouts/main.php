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
    </style>
    
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
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
                <button class="mobile-menu-toggle" onclick="toggleSidebar()" title="منو">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Page Title -->
                <h1 class="header-title"><?= htmlspecialchars($page_title) ?></h1>
                <?php if (!empty($page_subtitle)): ?>
                    <p class="header-subtitle"><?= htmlspecialchars($page_subtitle) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="header-actions">
                <!-- Global Search Field -->
                <div class="global-search">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="جستجو در اسناد و درخواست‌ها..." id="globalSearch">
                        <div class="search-results" id="searchResults"></div>
                    </div>
                </div>

                <!-- Theme Toggle Button -->
                <button id="themeToggle" class="theme-toggle-btn" title="تغییر تم">
                    <i class="fas fa-sun theme-icon-light"></i>
                    <i class="fas fa-moon theme-icon-dark"></i>
                </button>
                
                <!-- Profile Dropdown -->
                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileDropdown" title="پروفایل کاربری">
                        <div class="avatar-wrapper">
                            <div class="avatar-circle">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="profile-name">مدیر سیستم</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div>
                    </button>
                    
                    <div class="profile-menu" id="profileMenu">
                        <div class="profile-header">
                            <div class="avatar-large">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="profile-info">
                                <h6>مدیر سیستم</h6>
                                <small>admin@samanat.com</small>
                            </div>
                        </div>
                        <hr>
                        <a href="#" class="profile-item">
                            <i class="fas fa-user-edit"></i>
                            ویرایش پروفایل
                        </a>
                        <a href="#" class="profile-item">
                            <i class="fas fa-cog"></i>
                            تنظیمات
                        </a>
                        <a href="#" class="profile-item">
                            <i class="fas fa-bell"></i>
                            اعلان‌ها
                        </a>
                        <hr>
                        <a href="<?= url('logout') ?>" class="profile-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            خروج
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
                        <span class="sidebar-menu-icon">
                            <i class="fas fa-home"></i>
                        </span>
                        داشبورد
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/requests" class="sidebar-menu-link <?= (strpos($current_route, 'request') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        درخواست‌ها
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/users" class="sidebar-menu-link <?= (strpos($current_route, 'user') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">
                            <i class="fas fa-users"></i>
                        </span>
                        کاربران
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/tags" class="sidebar-menu-link <?= (strpos($current_route, 'tag') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">
                            <i class="fas fa-tags"></i>
                        </span>
                        برچسب‌ها
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/settings" class="sidebar-menu-link <?= (strpos($current_route, 'setting') === 0) ? 'active' : '' ?>">
                        <span class="sidebar-menu-icon">
                            <i class="fas fa-cog"></i>
                        </span>
                        تنظیمات
                    </a>
                </li>
                
                <li class="sidebar-menu-item">
                    <a href="/logout" class="sidebar-menu-link">
                        <span class="sidebar-menu-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </span>
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
        
        <!-- MANDATORY: Mobile Navigation -->
        <div class="mobile-nav" id="mobileNav">
            <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
            
            <!-- Hamburger Menu Button -->
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
            
            <!-- Mobile Menu -->
            <div class="mobile-menu" id="mobileMenu">
                <div class="mobile-header">
                    <div class="mobile-logo">
                        <span>🔹</span>
                        <h6>سامانت</h6>
                    </div>
                    <button class="mobile-close" id="mobileClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <nav class="mobile-nav-items">
                    <a href="<?= url('dashboard') ?>" class="mobile-nav-item active">
                        <i class="fas fa-home"></i>
                        <span>داشبورد</span>
                    </a>
                    <a href="<?= url('requests') ?>" class="mobile-nav-item">
                        <i class="fas fa-file-invoice"></i>
                        <span>درخواست‌ها</span>
                    </a>
                    <a href="<?= url('users') ?>" class="mobile-nav-item">
                        <i class="fas fa-users"></i>
                        <span>کاربران</span>
                    </a>
                    <a href="<?= url('tags') ?>" class="mobile-nav-item">
                        <i class="fas fa-tags"></i>
                        <span>برچسب‌ها</span>
                    </a>
                    <a href="<?= url('settings') ?>" class="mobile-nav-item">
                        <i class="fas fa-cog"></i>
                        <span>تنظیمات</span>
                    </a>
                </nav>
                
                <div class="mobile-footer">
                    <div class="mobile-profile">
                        <div class="mobile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="mobile-profile-info">
                            <span>مدیر سیستم</span>
                            <small>admin@samanat.com</small>
                        </div>
                    </div>
                    <a href="<?= url('logout') ?>" class="mobile-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        خروج
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation for Mobile -->
        <div class="bottom-nav">
            <a href="<?= url('dashboard') ?>" class="bottom-nav-item active">
                <i class="fas fa-home"></i>
                <span>داشبورد</span>
            </a>
            <a href="<?= url('requests') ?>" class="bottom-nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>جدید</span>
            </a>
            <a href="<?= url('requests') ?>" class="bottom-nav-item">
                <i class="fas fa-list"></i>
                <span>لیست</span>
            </a>
            <a href="<?= url('tags') ?>" class="bottom-nav-item">
                <i class="fas fa-tags"></i>
                <span>برچسب</span>
            </a>
            <a href="<?= url('users') ?>" class="bottom-nav-item">
                <i class="fas fa-user-circle"></i>
                <span>پروفایل</span>
            </a>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        // Global Variables
        window.SAMANAT = {
            baseUrl: '<?= BASE_URL ?? '' ?>',
            currentRoute: '<?= htmlspecialchars($current_route) ?>',
            csrfToken: '<?= $csrf_token ?>',
            userId: <?= isset($user_info['id']) ? (int)$user_info['id'] : 'null' ?>
        };
        
        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('open');
            
            if (sidebar.classList.contains('open')) {
                overlay.style.display = 'block';
                setTimeout(() => overlay.style.opacity = '1', 10);
            } else {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 300);
            }
        }
        
        // Alert Auto-close
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
            
            // Font Loading Test
            if (document.fonts && document.fonts.check) {
                if (document.fonts.check('1em Vazirmatn')) {
                    console.log('✅ فونت وزیرمتن با موفقیت لود شد');
                } else {
                    console.warn('⚠️ فونت وزیرمتن لود نشده - در حال fallback...');
                    document.fonts.ready.then(() => {
                        if (document.fonts.check('1em Vazirmatn')) {
                            console.log('✅ فونت وزیرمتن با تاخیر لود شد');
                        } else {
                            console.error('❌ فونت وزیرمتن لود نشد - استفاده از fallback font');
                        }
                    });
                }
            }
            
            console.log('✅ Samanet Dashboard v3.0 loaded');
        });
    </script>
    
    <!-- Additional JavaScript for new UI components -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Profile Dropdown
            const profileDropdownBtn = document.getElementById('profileDropdown');
            if (profileDropdownBtn) {
                const profileDropdown = profileDropdownBtn.parentElement;
                profileDropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('active');
                });
            }
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const openDropdown = document.querySelector('.profile-dropdown.active');
                if (openDropdown && !openDropdown.contains(e.target)) {
                    openDropdown.classList.remove('active');
                }
            });
            
            // Global Search
            const globalSearch = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            
            if (globalSearch && searchResults) {
                let searchTimeout;
                
                globalSearch.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length > 2) {
                        searchResults.style.display = 'block'; // Show loading state
                        searchResults.innerHTML = '<div class="search-loading">در حال جستجو...</div>';
                        
                        searchTimeout = setTimeout(() => {
                            performGlobalSearch(query);
                        }, 500); // Debounce
                    } else {
                        searchResults.style.display = 'none';
                    }
                });

                // Hide search results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!globalSearch.contains(e.target)) {
                        searchResults.style.display = 'none';
                    }
                });
            }
            
            // Mobile Menu
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileClose = document.getElementById('mobileClose');
            const mobileOverlay = document.getElementById('mobileNavOverlay');
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    document.body.classList.add('mobile-menu-open');
                });
            }
            
            if (mobileClose) {
                mobileClose.addEventListener('click', function() {
                    document.body.classList.remove('mobile-menu-open');
                });
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    document.body.classList.remove('mobile-menu-open');
                });
            }
            
            // Re-bind theme toggle to the new button, as the old logic might be tied to a different ID or class
            const newThemeToggle = document.getElementById('themeToggle');
            if (newThemeToggle) {
                newThemeToggle.addEventListener('click', function() {
                    // This re-implements the toggle logic locally to ensure it works, 
                    // assuming the global `toggleTheme()` might not be available or might conflict.
                    const html = document.documentElement;
                    const currentTheme = html.getAttribute('data-theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    html.setAttribute('data-theme', newTheme);
                    localStorage.setItem('samanat_theme', newTheme);
                });

                // Set initial state from localStorage
                const savedTheme = localStorage.getItem('samanat_theme');
                if (savedTheme) {
                    document.documentElement.setAttribute('data-theme', savedTheme);
                }
            }
        });

        // Global Search API Call Function
        function performGlobalSearch(query) {
            // This is a mock API call. Replace with your actual endpoint.
            console.log(`Searching for: ${query}`);
            const searchResults = document.getElementById('searchResults');
            
            // Mock data
            const results = [
                { url: '#', icon: 'fas fa-file-invoice', title: 'درخواست حواله #123', description: 'مبلغ: 1,500,000 تومان برای پروژه آلفا' },
                { url: '#', icon: 'fas fa-file-alt', title: 'سند قرارداد پروژه بتا', description: 'قرارداد همکاری با شرکت توسعه‌دهندگان' },
                { url: '#', icon: 'fas fa-user', title: 'کاربر: علی رضایی', description: 'آخرین فعالیت: 2 ساعت پیش' }
            ];
            
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="search-no-results">نتیجه‌ای یافت نشد</div>';
            } else {
                searchResults.innerHTML = results.map(item => `
                    <a href="${item.url}" class="search-result-item">
                        <i class="${item.icon}"></i>
                        <div>
                            <div class="result-title">${item.title}</div>
                            <div class="result-desc">${item.description}</div>
                        </div>
                    </a>
                `).join('');
            }
        }
    </script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($page_js)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?= $page_js ?>
            });
        </script>
    <?php endif; ?>
</body>
</html>