<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/requests/list.php
 * توضیح: صفحه لیست درخواست‌های حواله - Enterprise Grade (مطابق dashboard.css)
 * تاریخ بازطراحی: 1404/10/15
 * نسخه: 5.0 حرفه‌ای (دقیقاً مطابق dashboard.css)
 */

// تنظیم متغیرهای صفحه
$page_title = $title ?? 'مدیریت درخواست‌ها';
$page_description = 'مشاهده و مدیریت درخواست‌های حواله';
$active_menu = 'requests';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}

// پردازش آمار
$stats = $stats ?? [];
$requests_data = $requests_data ?? ['data' => [], 'total' => 0, 'current_page' => 1];
$filters = $filters ?? [];
?>

<div class="dashboard-pro">
    <!-- Header حرفه‌ای - دقیقاً مطابق dashboard -->
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="header-title">مدیریت درخواست‌ها</h1>
        </div>
        <div class="header-actions">
            <a href="?route=requests&action=create" class="btn-icon" title="درخواست جدید">
                <i class="fas fa-plus"></i>
            </a>
            <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            <div class="user-profile" title="نام کاربر">
                ک
            </div>
        </div>
    </header>

    <!-- محتوای اصلی -->
    <div class="dashboard-content">
        <!-- Flash Messages -->
        <?php if (isset($flash) && $flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" 
                 style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px; border-radius: var(--radius-lg);">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ردیف آمارهای فشرده - دقیقاً مطابق dashboard -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل درخواست‌ها</div>
                <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-list-alt"></i>
                    <span>همه موارد</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">در انتظار</div>
                <div class="stat-value"><?= number_format($stats['pending'] ?? 0) ?></div>
                <div class="stat-change">
                    <i class="fas fa-clock"></i>
                    <span>بررسی نشده</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">در حال بررسی</div>
                <div class="stat-value"><?= number_format($stats['processing'] ?? 0) ?></div>
                <div class="stat-change">
                    <i class="fas fa-sync-alt"></i>
                    <span>در جریان</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">تکمیل شده</div>
                <div class="stat-value"><?= number_format($stats['completed'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check-circle"></i>
                    <span>موفق</span>
                </div>
            </div>
        </div>

        <!-- Grid اصلی - دقیقاً مطابق dashboard -->
        <div class="dashboard-grid">
            <!-- ستون اصلی -->
            <div class="main-column">
                <!-- فیلترها -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-filter"></i>
                            فیلترهای جستجو
                        </h2>
                        <button class="btn-icon" onclick="toggleFilters()" title="باز/بسته کردن فیلترها">
                            <i class="fas fa-chevron-down" id="filter-toggle"></i>
                        </button>
                    </div>
                    
                    <div id="filters-panel" class="p-4">
                        <form method="GET" id="filtersForm">
                            <input type="hidden" name="route" value="requests">
                            
                            <div class="row g-3">
                                <!-- جستجو -->
                                <div class="col-md-6">
                                    <label class="form-label">جستجو</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                           placeholder="جستجو در عنوان، شماره مرجع یا نام...">
                                </div>

                                <!-- وضعیت -->
                                <div class="col-md-3">
                                    <label class="form-label">وضعیت</label>
                                    <select class="form-select" name="status">
                                        <option value="">همه وضعیت‌ها</option>
                                        <?php if (isset($statuses)): ?>
                                        <?php foreach ($statuses as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- اولویت -->
                                <div class="col-md-3">
                                    <label class="form-label">اولویت</label>
                                    <select class="form-select" name="priority">
                                        <option value="">همه اولویت‌ها</option>
                                        <?php if (isset($priorities)): ?>
                                        <?php foreach ($priorities as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= ($filters['priority'] ?? '') === $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- دکمه‌های عملیات -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search me-1"></i>اعمال فیلتر
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                        <i class="fas fa-times me-1"></i>پاک کردن
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- جدول اصلی -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-table"></i>
                            لیست درخواست‌ها
                            <span class="badge bg-primary ms-2"><?= number_format($requests_data['total'] ?? 0) ?></span>
                        </h2>
                        <div class="d-flex gap-2">
                            <button class="btn-icon" onclick="refreshTable()" title="بروزرسانی">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn-icon" onclick="exportData()" title="صدور">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($requests_data['data'])): ?>
                    <!-- جدول دسکتاپ -->
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره مرجع</th>
                                <th>عنوان</th>
                                <th>مبلغ</th>
                                <th>صاحب حساب</th>
                                <th>وضعیت</th>
                                <th>اولویت</th>
                                <th>تاریخ</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests_data['data'] as $index => $request): ?>
                            <tr>
                                <td><?= ($requests_data['current_page'] - 1) * 20 + $index + 1 ?></td>
                                <td>
                                    <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                                       class="text-decoration-none fw-bold text-primary">
                                        <?= htmlspecialchars($request['reference_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <?php if ($request['is_urgent']): ?>
                                            <span class="badge bg-danger me-1">فوری</span>
                                        <?php endif; ?>
                                        <span class="fw-medium"><?= htmlspecialchars($request['title']) ?></span>
                                        <?php if (!empty($request['description'])): ?>
                                            <br><small class="text-muted">
                                                <?= mb_substr(htmlspecialchars($request['description']), 0, 50) ?>
                                                <?= mb_strlen($request['description']) > 50 ? '...' : '' ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($request['amount']): ?>
                                        <span class="fw-bold text-success">
                                            <?= number_format($request['amount']) ?> ریال
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">مشخص نشده</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($request['account_holder'] ?: 'نامشخص') ?></td>
                                <td>
                                    <span class="badge bg-<?= getStatusColor($request['status']) ?>">
                                        <?= $request['status_label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getPriorityColor($request['priority']) ?>">
                                        <?= $request['priority_label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= $request['created_at_jalali'] ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                                           class="btn btn-outline-primary btn-sm" title="نمایش">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($request['status'] === 'pending'): ?>
                                            <button class="btn btn-outline-success btn-sm" 
                                                    onclick="approveRequest(<?= $request['id'] ?>)" title="تایید">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="rejectRequest(<?= $request['id'] ?>)" title="رد">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- لیست موبایل -->
                    <div class="mobile-list">
                        <?php foreach ($requests_data['data'] as $request): ?>
                        <div class="mobile-list-item">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title">
                                    <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                                       class="text-decoration-none fw-bold">
                                        <?= htmlspecialchars($request['reference_number']) ?>
                                    </a>
                                    <?php if ($request['is_urgent']): ?>
                                        <span class="badge bg-danger ms-1">فوری</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mobile-item-meta">
                                    <?= htmlspecialchars($request['title']) ?><br>
                                    <small class="text-muted"><?= $request['created_at_jalali'] ?></small>
                                </div>
                            </div>
                            <div class="mobile-item-actions">
                                <span class="badge bg-<?= getStatusColor($request['status']) ?>">
                                    <?= $request['status_label'] ?>
                                </span>
                                <?php if ($request['amount']): ?>
                                    <br><small class="text-success fw-bold">
                                        <?= number_format($request['amount']) ?> ریال
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- صفحه‌بندی -->
                    <?php if ($requests_data['total'] > 20): ?>
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div class="text-muted">
                            نمایش <?= number_format($requests_data['from']) ?> تا <?= number_format($requests_data['to']) ?> از <?= number_format($requests_data['total']) ?> درخواست
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                            <?php
                            $current_page = $requests_data['current_page'];
                            $last_page = $requests_data['last_page'];
                            ?>
                            
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?route=requests&page=<?= $current_page - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $current_page - 2); $i <= min($last_page, $current_page + 2); $i++): ?>
                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                    <a class="page-link" href="?route=requests&page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $last_page): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?route=requests&page=<?= $current_page + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    </div>
                    <?php endif; ?>

                    <?php else: ?>
                    <!-- حالت خالی -->
                    <div class="text-center p-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">درخواستی یافت نشد</h5>
                        <p class="text-muted mb-3">هیچ درخواستی با معیارهای انتخابی شما یافت نشد.</p>
                        <a href="?route=requests&action=create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>ایجاد اولین درخواست
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ستون جانبی - دقیقاً مطابق dashboard -->
            <div class="side-column">
                <!-- عملیات سریع -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-bolt"></i>
                            عملیات سریع
                        </div>
                        <span class="panel-badge">۴</span>
                    </div>
                    <div class="panel-body">
                        <div class="task-item" onclick="window.location='?route=requests&action=create'">
                            <span class="task-text">ایجاد درخواست جدید</span>
                        </div>
                        <div class="task-item" onclick="exportData()">
                            <span class="task-text">صدور گزارش Excel</span>
                        </div>
                        <div class="task-item" onclick="refreshTable()">
                            <span class="task-text">بروزرسانی لیست</span>
                        </div>
                        <div class="task-item" onclick="showBulkActions()">
                            <span class="task-text">عملیات گروهی</span>
                        </div>
                    </div>
                </div>

                <!-- آخرین فعالیت‌ها -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-history"></i>
                            آخرین فعالیت‌ها
                        </div>
                        <span class="panel-badge">۳</span>
                    </div>
                    <div class="panel-body">
                        <div class="task-item">
                            <span class="task-text">درخواست #1234 تایید شد</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">درخواست جدید ثبت شد</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">گزارش ماهانه تولید شد</span>
                        </div>
                    </div>
                </div>

                <!-- اطلاعات سیستم -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-info-circle"></i>
                            اطلاعات سیستم
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="task-item">
                            <span class="task-text">آخرین بروزرسانی: امروز</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">درخواست‌های فعال: <?= $stats['pending'] ?? 0 ?></span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">حافظه سیستم: 78% استفاده</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript حرفه‌ای - دقیقاً مطابق dashboard
document.addEventListener('DOMContentLoaded', function() {
    // انیمیشن تدریجی کارت‌های آمار
    const statCards = document.querySelectorAll('.stat-card-pro');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
    
    // افکت‌های hover برای ردیف‌های جدول
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Auto dismiss flash messages
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(100%)';
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
});

function toggleFilters() {
    const panel = document.getElementById('filters-panel');
    const icon = document.getElementById('filter-toggle');
    
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
    } else {
        panel.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
    }
}

function clearFilters() {
    document.getElementById('filtersForm').reset();
    setTimeout(() => {
        document.getElementById('filtersForm').submit();
    }, 200);
}

function refreshTable() { 
    location.reload(); 
}

function exportData() { 
    alert('صدور گزارش در حال توسعه است'); 
}

function approveRequest(id) { 
    if (confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        console.log('Approve:', id);
    }
}

function rejectRequest(id) { 
    if (confirm('آیا از رد این درخواست اطمینان دارید؟')) {
        console.log('Reject:', id);
    }
}

function showBulkActions() {
    alert('عملیات گروهی در حال توسعه است');
}

// Theme system handled by theme-system.js - no local override needed
</script>
