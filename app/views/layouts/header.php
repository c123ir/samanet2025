<?php
/**
 * نام فایل: header.php
 * مسیر فایل: /app/views/layouts/header.php
 * توضیح: هدر حرفه‌ای صفحات سامانت - نسخه بهینه شده
 * تاریخ بازطراحی: 1404/10/17
 * نسخه: 3.0 یکپارچه
 */
?>

<header class="dashboard-header">
    <!-- بخش راست: دکمه منو موبایل + عنوان -->
    <div class="header-content">
        <!-- دکمه Toggle سایدبار (فقط موبایل) -->
        <button class="mobile-menu-toggle" onclick="toggleSidebar()" aria-label="باز کردن منو" title="منو">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- عنوان صفحه -->
        <div class="header-title-section">
            <h1 class="header-title"><?= $page_title ?? 'داشبورد سامانت' ?></h1>
            <?php if (isset($page_subtitle)): ?>
                <p class="header-subtitle"><?= $page_subtitle ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- بخش چپ: عملیات کاربر -->
    <div class="header-actions">
        <!-- دکمه جستجو سریع -->
        <button class="header-btn" onclick="toggleQuickSearch()" title="جستجو سریع">
            <i class="fas fa-search"></i>
        </button>
        
        <!-- اعلان‌ها -->
        <div class="header-notifications">
            <button class="header-btn notifications-btn" onclick="toggleNotifications()" title="اعلان‌ها">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
            
            <!-- dropdown اعلان‌ها -->
            <div class="notifications-dropdown" id="notificationsDropdown">
                <div class="notifications-header">
                    <h6>اعلان‌های جدید</h6>
                    <span class="notifications-count">3 مورد</span>
                </div>
                <div class="notifications-list">
                    <div class="notification-item">
                        <div class="notification-icon bg-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="notification-content">
                            <h6>درخواست جدید</h6>
                            <p>درخواست REQ2506238564 ثبت شد</p>
                            <small>۵ دقیقه پیش</small>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="notification-content">
                            <h6>تایید درخواست</h6>
                            <p>درخواست شما تایید شد</p>
                            <small>۱۰ دقیقه پیش</small>
                        </div>
                    </div>
                </div>
                <div class="notifications-footer">
                    <a href="<?= url('notifications') ?>" class="view-all-notifications">
                        مشاهده همه اعلان‌ها
                    </a>
                </div>
            </div>
        </div>
        
        <!-- تغییر تم -->
        <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
            <i class="fas fa-moon" id="theme-icon"></i>
        </button>
        
        <!-- پروفایل کاربر -->
        <div class="user-profile" title="<?= $current_user['name'] ?? 'کاربر سامانت' ?>">
            <?= substr($current_user['name'] ?? 'کاربر', 0, 1) ?>
        </div>
    </div>
</header>

<!-- جستجو سریع (مخفی) -->
<div class="quick-search-overlay" id="quickSearchOverlay">
    <div class="quick-search-modal">
        <div class="quick-search-header">
            <input type="text" class="quick-search-input" placeholder="جستجو در سامانت..." autofocus>
            <button class="quick-search-close" onclick="toggleQuickSearch()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="quick-search-results">
            <div class="search-suggestion">
                <i class="fas fa-search"></i>
                <span>شروع به تایپ کنید...</span>
            </div>
        </div>
    </div>
</div>

<script>
// جستجو سریع
function toggleQuickSearch() {
    const overlay = document.getElementById('quickSearchOverlay');
    overlay.classList.toggle('show');
    if (overlay.classList.contains('show')) {
        document.querySelector('.quick-search-input').focus();
    }
}

// اعلان‌ها
function toggleNotifications() {
    const dropdown = document.getElementById('notificationsDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    // بستن منوی کاربر
    userDropdown.classList.remove('show');
    
    dropdown.classList.toggle('show');
}

// منوی کاربر
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    
    // بستن منوی اعلان‌ها
    notificationsDropdown.classList.remove('show');
    
    dropdown.classList.toggle('show');
}

// بستن dropdown ها هنگام کلیک خارج
document.addEventListener('click', function(e) {
    if (!e.target.closest('.header-notifications') && !e.target.closest('.header-user-profile')) {
        document.getElementById('notificationsDropdown').classList.remove('show');
        document.getElementById('userDropdown').classList.remove('show');
    }
    
    if (!e.target.closest('.quick-search-overlay') && !e.target.closest('.header-btn')) {
        document.getElementById('quickSearchOverlay').classList.remove('show');
    }
});

// جستجو سریع با کلید Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('quickSearchOverlay').classList.remove('show');
        document.getElementById('notificationsDropdown').classList.remove('show');
        document.getElementById('userDropdown').classList.remove('show');
    }
    
    // جستجو سریع با Ctrl+K
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        toggleQuickSearch();
    }
});
</script> 