<?php
/**
 * نام فایل: main.php
 * مسیر فایل: /app/views/layouts/main.php
 * توضیح: قالب اصلی صفحات سامانت - نسخه بهینه شده
 * تاریخ بازطراحی: 1404/10/17
 * نسخه: 3.0 یکپارچه
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
    
    <title><?= $title ?? 'سامانت' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    
    <!-- Persian Fonts -->
    <link href="<?= asset('fonts/Vazirmatn-font-face.css') ?>" rel="stylesheet">
    
    <!-- Bootstrap RTL -->
    <link href="/assets/css/bootstrap-rtl.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link href="/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
    
    <!-- Core CSS - ترتیب مهم است -->
    <link href="/assets/css/variables.css" rel="stylesheet">
    <link href="/assets/css/main.css" rel="stylesheet">
    <link href="/assets/css/layout.css" rel="stylesheet">
    <link href="/assets/css/components.css" rel="stylesheet">
    <link href="/assets/css/theme-system.css" rel="stylesheet">
    
    <!-- Page-specific CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
            <link href="<?= $css_file ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Theme System -->
    <script src="<?= asset('js/theme-system.js') ?>"></script>
</head>
<body>
    <!-- Main Layout Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <?php require_once APP_PATH . 'views/layouts/sidebar.php'; ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Header -->
            <?php require_once APP_PATH . 'views/layouts/header.php'; ?>
            
            <!-- Main Content -->
            <main class="main-content" role="main">
                <?php
                    if (isset($content_view) && file_exists($content_view)) {
                        require $content_view;
                    } else {
                        echo '<div class="alert alert-danger">خطا: محتوای صفحه یافت نشد.</div>';
                    }
                ?>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav d-md-none">
        <a href="<?= url('dashboard') ?>" class="mobile-nav-item <?= (strpos($this->getCurrentRoute(), 'dashboard') === 0) ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>خانه</span>
        </a>
        <a href="<?= url('requests') ?>" class="mobile-nav-item <?= (strpos($this->getCurrentRoute(), 'requests') === 0) ? 'active' : '' ?>">
            <i class="fas fa-file-invoice"></i>
            <span>درخواست‌ها</span>
        </a>
        <a href="<?= url('dashboard') ?>" class="mobile-nav-item main-nav-item active">
            <i class="fas fa-plus"></i>
        </a>
        <a href="<?= url('users') ?>" class="mobile-nav-item <?= (strpos($this->getCurrentRoute(), 'users') === 0) ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>کاربران</span>
        </a>
        <a href="<?= url('tags') ?>" class="mobile-nav-item <?= (strpos($this->getCurrentRoute(), 'tags') === 0) ? 'active' : '' ?>">
            <i class="fas fa-tags"></i>
            <span>تگ‌ها</span>
        </a>
    </nav>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div class="loading-text">در حال بارگذاری...</div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?= asset($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar && overlay) {
                const isOpen = sidebar.classList.contains('show');
                
                if (isOpen) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                } else {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                }
            }
        }

        // Close sidebar when clicking overlay
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('sidebar-overlay')) {
                toggleSidebar();
            }
        });

        // Global Functions
        function showAlert(message, type = 'info') {
            const alertClass = type === 'error' ? 'danger' : type;
            const alertHtml = `
                <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const container = document.querySelector('.main-content');
            if (container) {
                container.insertAdjacentHTML('afterbegin', alertHtml);
                
                setTimeout(function() {
                    const alert = container.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }

        function confirmDelete(message = 'آیا از حذف این مورد اطمینان دارید؟') {
            return confirm(message);
        }

        function formatPersianNumber(number) {
            const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            return number.toString().replace(/\d/g, function(digit) {
                return persianDigits[digit];
            });
        }

        function toEnglishNumbers(str) {
            const persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
            const arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];
            
            for (let i = 0; i < 10; i++) {
                str = str.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
            }
            return str;
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            // Setup mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSidebar();
                });
            }
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Setup persian date inputs
            const persianDateInputs = document.querySelectorAll('.persian-date');
            persianDateInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = toEnglishNumbers(this.value);
                });
            });
            
            console.log('✅ Main layout initialized');
        });
    </script>

    <!-- Inline JavaScript -->
    <?php if (isset($inline_js)): ?>
        <script><?= $inline_js ?></script>
    <?php endif; ?>
</body>
</html>