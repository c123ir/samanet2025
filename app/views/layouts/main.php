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
    
    <title><?= $title ?? 'سامانت' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- Vendor CSS -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
    
    <!-- Persian Fonts -->
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <!-- Main App CSS - ترتیب مهم است -->
    <link href="/assets/css/theme-system.css" rel="stylesheet">
    <link href="/assets/css/main.css" rel="stylesheet">
    <link href="/assets/css/@sidebar.css" rel="stylesheet">
    <link href="/assets/css/@header.css" rel="stylesheet">
    <link href="/assets/css/mobile-nav.css" rel="stylesheet">
    
    <!-- ULTIMATE CSS FORCE -->
    <style>
        @media (max-width: 991.98px) {
            /* MOBILE ONLY - ANTI-BLUR FORCE */
            nav#sidebar,
            #sidebar,
            .sidebar {
                position: fixed !important;
                top: 0px !important;
                right: -280px !important;
                left: auto !important;
                width: 280px !important;
                height: 100vh !important;
                background: white !important;
                z-index: 1030 !important;
                border-left: 1px solid #e5e5e5 !important;
                transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                /* REMOVE ALL BLUR EFFECTS FOR MOBILE */
                transform: translate3d(0,0,0) !important;
                -webkit-transform: translate3d(0,0,0) !important;
                margin: 0px !important;
                padding: 0px !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                /* CRITICAL ANTI-BLUR PROPERTIES */
                filter: none !important;
                backdrop-filter: none !important;
                -webkit-filter: none !important;
                -webkit-backdrop-filter: none !important;
                /* FONT RENDERING FOR MOBILE */
                -webkit-font-smoothing: subpixel-antialiased !important;
                -moz-osx-font-smoothing: auto !important;
                text-rendering: geometricPrecision !important;
                font-smooth: never !important;
                -webkit-text-stroke: 0px !important;
                text-shadow: none !important;
                /* MOBILE SPECIFIC FIXES */
                -webkit-perspective: none !important;
                perspective: none !important;
                -webkit-backface-visibility: visible !important;
                backface-visibility: visible !important;
                will-change: auto !important;
            }
            
            /* MOBILE ONLY - Force crisp content inside sidebar */
            nav#sidebar *,
            #sidebar *,
            .sidebar * {
                /* REMOVE ALL BLUR EFFECTS */
                filter: none !important;
                backdrop-filter: none !important;
                -webkit-filter: none !important;
                -webkit-backdrop-filter: none !important;
                /* MOBILE FONT RENDERING */
                -webkit-font-smoothing: subpixel-antialiased !important;
                -moz-osx-font-smoothing: auto !important;
                font-smooth: never !important;
                text-rendering: geometricPrecision !important;
                /* MOBILE TRANSFORM FIXES */
                transform: translate3d(0,0,0) !important;
                -webkit-transform: translate3d(0,0,0) !important;
                will-change: auto !important;
                /* MOBILE TEXT FIXES */
                text-shadow: none !important;
                outline: none !important;
                -webkit-text-stroke: 0px !important;
                /* MOBILE 3D FIXES */
                -webkit-perspective: none !important;
                perspective: none !important;
                -webkit-backface-visibility: visible !important;
                backface-visibility: visible !important;
                /* FONT */
                font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
                font-weight: normal !important;
                letter-spacing: normal !important;
                /* MOBILE HARDWARE ACCELERATION OFF */
                -webkit-transform-style: flat !important;
                transform-style: flat !important;
            }
            
            /* Dark theme support */
            [data-theme="dark"] nav#sidebar,
            [data-theme="dark"] #sidebar,
            [data-theme="dark"] .sidebar {
                background: #27272a !important;
                color: #f4f4f5 !important;
                border-left: 1px solid #3f3f46 !important;
            }
            
            nav#sidebar.show,
            #sidebar.show,
            .sidebar.show {
                right: 0px !important;
            }
            
                        .sidebar-overlay {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: rgba(0, 0, 0, 0.5) !important;
                backdrop-filter: blur(5px) !important;
                -webkit-backdrop-filter: blur(5px) !important;
                z-index: 1029 !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transition: opacity 0.3s ease, visibility 0.3s ease !important;
            }
            
            .sidebar-overlay.show {
                opacity: 1 !important;
                visibility: visible !important;
            }
        }
    </style>
 
    
    <!-- Page-specific CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
            <link href="<?= $css_file ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="main-container">
        <?php 
            if (file_exists(APP_PATH . 'views/layouts/sidebar.php')) {
                require_once APP_PATH . 'views/layouts/sidebar.php';
            }
        ?>
        <div class="content-wrapper">
            <?php 
                if (file_exists(APP_PATH . 'views/layouts/header.php')) {
                    require_once APP_PATH . 'views/layouts/header.php';
                }
            ?>
            <main class="main-content-wrapper">
                <?php
                    if (isset($content_view) && file_exists($content_view)) {
                        require $content_view;
                    } else {
                        echo "<p class='text-danger'>Error: Content view not found.</p>";
                    }
                ?>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <?php require_once(APP_PATH . 'views/layouts/footer.php'); ?>

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
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
            const sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay') || document.querySelector('.sidebar-overlay');
            
            if (sidebar) {
                const isOpen = sidebar.classList.contains('show');
                
                if (isOpen) {
                    sidebar.classList.remove('show');
                    if (overlay) overlay.classList.remove('show');
                } else {
                    sidebar.classList.add('show');
                    if (overlay) overlay.classList.add('show');
                }
                
                // کلیک روی overlay برای بستن سایدبار
                if (overlay && !overlay.hasClickHandler) {
                    overlay.hasClickHandler = true;
                    overlay.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    };
                }
            }
        }

        // بستن sidebar هنگام کلیک خارج از آن
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#sidebar') && !e.target.closest('.mobile-menu-toggle')) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (overlay) overlay.classList.remove('show');
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

            
            // Setup mobile menu toggle event listener
            const mobileToggle = document.getElementById('mobileMenuToggle') || document.querySelector('.mobile-menu-toggle');
            console.log('📱 Mobile toggle button found:', !!mobileToggle);
            
            if (mobileToggle) {
                // Remove any existing onclick attribute
                mobileToggle.removeAttribute('onclick');
                
                // Add proper event listener
                mobileToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('📱 Mobile toggle clicked via event listener');
                    toggleSidebar();
                });
                
                console.log('✅ Mobile toggle event listener added');
            } else {
                console.error('❌ Mobile toggle button not found!');
            }
            
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