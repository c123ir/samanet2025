<?php
/**
 * نام فایل: sidebar.php
 * مسیر فایل: /app/views/layouts/sidebar.php
 * توضیح: نوار کناری اصلی سامانت - نسخه بهینه شده
 * تاریخ بازطراحی: 1404/10/17
 * نسخه: 3.0 یکپارچه
 */
?>
<nav class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="<?= url('dashboard') ?>" class="sidebar-brand">
            <i class="fas fa-gem"></i>
            <span>سامانت</span>
        </a>
    </div>
    
    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="<?= url('dashboard') ?>" class="sidebar-link <?= ($this->getCurrentRoute() == 'dashboard') ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>داشبورد</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= url('requests') ?>" class="sidebar-link <?= (strpos($this->getCurrentRoute(), 'requests') === 0) ? 'active' : '' ?>">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>درخواست‌ها</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= url('documents') ?>" class="sidebar-link <?= (strpos($this->getCurrentRoute(), 'documents') === 0) ? 'active' : '' ?>">
                <i class="fas fa-folder-open"></i>
                <span>اسناد</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= url('users') ?>" class="sidebar-link <?= (strpos($this->getCurrentRoute(), 'users') === 0) ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>کاربران</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= url('tags') ?>" class="sidebar-link <?= (strpos($this->getCurrentRoute(), 'tags') === 0) ? 'active' : '' ?>">
                <i class="fas fa-tags"></i>
                <span>مدیریت تگ‌ها</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= url('reports') ?>" class="sidebar-link <?= (strpos($this->getCurrentRoute(), 'reports') === 0) ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i>
                <span>گزارشات</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= url('settings') ?>" class="sidebar-link <?= (strpos($this->getCurrentRoute(), 'settings') === 0) ? 'active' : '' ?>">
                <i class="fas fa-cogs"></i>
                <span>تنظیمات</span>
            </a>
        </li>
    </ul>
</nav> 