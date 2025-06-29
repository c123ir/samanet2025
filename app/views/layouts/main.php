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
    
    <!-- Critical CSS - Dashboard System -->
    <link href="/assets/css/dashboard.css" rel="stylesheet">
    
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
                <!-- Theme Toggle Button - دقیقاً مطابق استانداردها -->
                <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>
                
                <!-- User Profile - دقیقاً مطابق استانداردها -->
                <div class="user-profile" title="<?= htmlspecialchars($user_name) ?>">
                    <?php if (!empty($user_avatar)): ?>
                        <img src="<?= htmlspecialchars($user_avatar) ?>" alt="پروفایل" style="width: 100%; height: 100%; border-radius: 50%;">
                    <?php else: ?>
                        <?= strtoupper(substr($user_name, 0, 1)) ?>
                    <?php endif; ?>
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
        <nav class="mobile-nav">
            <a href="/dashboard" class="mobile-nav-item <?= ($current_route === 'dashboard') ? 'active' : '' ?>">
                <i class="mobile-nav-icon fas fa-home"></i>
                <span>خانه</span>
            </a>
            
            <a href="/requests" class="mobile-nav-item <?= (strpos($current_route, 'request') === 0) ? 'active' : '' ?>">
                <i class="mobile-nav-icon fas fa-file-alt"></i>
                <span>درخواست‌ها</span>
            </a>
            
            <a href="/users" class="mobile-nav-item <?= (strpos($current_route, 'user') === 0) ? 'active' : '' ?>">
                <i class="mobile-nav-icon fas fa-users"></i>
                <span>کاربران</span>
            </a>
            
            <a href="/tags" class="mobile-nav-item <?= (strpos($current_route, 'tag') === 0) ? 'active' : '' ?>">
                <i class="mobile-nav-icon fas fa-tags"></i>
                <span>برچسب‌ها</span>
            </a>
            
            <a href="/settings" class="mobile-nav-item <?= (strpos($current_route, 'setting') === 0) ? 'active' : '' ?>">
                <i class="mobile-nav-icon fas fa-cog"></i>
                <span>تنظیمات</span>
            </a>
        </nav>
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
            
            console.log('✅ Samanet Dashboard v3.0 loaded');
        });
    </script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($additional_js) && is_array($additional_js)): ?>
        <?php foreach ($additional_js as $js_file): ?>
            <script src="<?= htmlspecialchars($js_file) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
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