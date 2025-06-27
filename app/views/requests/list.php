<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/requests/list.php
 * توضیح: صفحه لیست درخواست‌های حواله - Bootstrap 5 استاندارد
 * تاریخ بازطراحی: 1404/10/16
 * نسخه: 3.0 (Layout-based)
 */
?>

<!-- Page-specific CSS -->
<link href="/assets/css/advanced-search.css" rel="stylesheet">
<link href="/assets/css/requests-compact.css" rel="stylesheet">

<div class="dashboard-main">
    <!-- Flash Messages -->
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" 
             style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row stats-row g-3 animate-fade-in">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card animate-delay-1">
                <div class="stat-label">کل درخواست‌ها</div>
                <div class="stat-value"><?= fa_num($stats['total'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>همه موارد</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card animate-delay-2">
                <div class="stat-label">در انتظار</div>
                <div class="stat-value"><?= fa_num($stats['pending'] ?? 0) ?></div>
                <div class="stat-change">
                    <i class="fas fa-clock"></i>
                    <span>بررسی نشده</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card animate-delay-3">
                <div class="stat-label">در حال بررسی</div>
                <div class="stat-value"><?= fa_num($stats['processing'] ?? 0) ?></div>
                <div class="stat-change">
                    <i class="fas fa-sync-alt"></i>
                    <span>در جریان</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card animate-delay-4">
                <div class="stat-label">تکمیل شده</div>
                <div class="stat-value"><?= fa_num($stats['completed'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check-circle"></i>
                    <span>موفق</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Compact Search Bar -->
    <div class="search-filter-bar animate-fade-in animate-delay-4">
        <div class="row g-2 align-items-center">
            <!-- جستجو -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="search-wrapper" id="requestAdvancedSearch"></div>
            </div>
            
            <!-- فیلتر وضعیت -->
            <div class="col-6 col-md-2 col-lg-2">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="" selected>
                        <i class="fas fa-list"></i> همه وضعیت‌ها
                    </option>
                    <?php if (isset($statuses)): ?>
                        <?php foreach ($statuses as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- فیلتر اولویت -->
            <div class="col-6 col-md-2 col-lg-2">
                <select id="priorityFilter" class="form-select form-select-sm">
                    <option value="" selected>همه اولویت‌ها</option>
                    <?php if (isset($priorities)): ?>
                        <?php foreach ($priorities as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($filters['priority'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- دکمه‌های سریع -->
            <div class="col-12 col-md-4 col-lg-5">
                <div class="d-flex gap-1 justify-content-md-end">
                    <button type="button" class="btn btn-light btn-sm px-2 py-1" 
                            onclick="requestSearch.setQuickFilter('status', 'pending')" 
                            data-bs-toggle="tooltip" title="نمایش در انتظار">
                        <i class="fas fa-clock"></i>
                        <span class="d-none d-sm-inline ms-1">در انتظار</span>
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-2 py-1" 
                            onclick="requestSearch.setQuickFilter('priority', 'urgent')"
                            data-bs-toggle="tooltip" title="نمایش فوری‌ها">
                        <i class="fas fa-exclamation text-danger"></i>
                        <span class="d-none d-sm-inline ms-1">فوری</span>
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-2 py-1" 
                            onclick="requestSearch.clearFilters()"
                            data-bs-toggle="tooltip" title="پاک کردن فیلترها">
                        <i class="fas fa-filter-circle-xmark"></i>
                    </button>
                    <div class="vr mx-1"></div>
                    <button class="btn btn-light btn-sm px-2 py-1" 
                            onclick="refreshTable()" 
                            data-bs-toggle="tooltip" title="بروزرسانی">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="btn btn-light btn-sm px-2 py-1" 
                            onclick="exportData()" 
                            data-bs-toggle="tooltip" title="خروجی Excel">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-3">
        <!-- Requests List (75%) -->
        <div class="col-12 col-xl-9">
            <div class="dashboard-card animate-fade-in animate-delay-4">
                
                <?php if (!empty($requests_data['data'])): ?>
                <div class="dashboard-card-body p-0">
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table dashboard-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>شماره</th>
                                    <th>مرجع</th>
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
                                        <td>
                                            <code class="persian-num"><?= fa_num($request['id'] ?? $index + 1) ?></code>
                                        </td>
                                        <td>
                                            <a href="<?= url("requests/show/{$request['id']}") ?>" 
                                               class="text-decoration-none fw-semibold text-primary">
                                                <?= htmlspecialchars($request['reference_number'] ?? 'REQ' . ($request['id'] ?? $index + 1)) ?>
                                            </a>
                                            <?php if ($request['is_urgent'] ?? false): ?>
                                                <span class="badge bg-danger ms-1">فوری</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($request['title'] ?? 'بدون عنوان') ?></div>
                                                <?php if (!empty($request['description'])): ?>
                                                    <div class="text-muted small">
                                                        <?= htmlspecialchars(mb_substr($request['description'], 0, 50)) ?>
                                                        <?= mb_strlen($request['description']) > 50 ? '...' : '' ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($request['amount'])): ?>
                                                <span class="fw-bold text-success persian-num">
                                                    <?= fa_num(number_format($request['amount'])) ?> ریال
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-minus me-1"></i>
                                                    مشخص نشده
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($request['account_holder'])): ?>
                                                <div>
                                                    <div class="fw-medium"><?= htmlspecialchars($request['account_holder']) ?></div>
                                                    <?php if (!empty($request['bank_name'])): ?>
                                                        <div class="text-muted small"><?= htmlspecialchars($request['bank_name']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    نامشخص
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getStatusColor($request['status'] ?? 'pending') ?>">
                                                <i class="<?= getStatusIcon($request['status'] ?? 'pending') ?> me-1"></i>
                                                <?= $request['status_label'] ?? 'در انتظار' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getPriorityColor($request['priority'] ?? 'normal') ?>">
                                                <i class="<?= getPriorityIcon($request['priority'] ?? 'normal') ?> me-1"></i>
                                                <?= $request['priority_label'] ?? 'معمولی' ?>
                                            </span>
                                        </td>
                                        <td class="text-muted persian-num">
                                            <?php if (!empty($request['created_at_jalali'])): ?>
                                                <?= $request['created_at_jalali'] ?>
                                            <?php else: ?>
                                                <?= jdate('Y/m/d H:i', time()) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= url("requests/show/{$request['id']}") ?>" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="مشاهده جزئیات">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php if (($request['status'] ?? 'pending') === 'pending'): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-success btn-sm" 
                                                            onclick="approveRequest(<?= $request['id'] ?>)"
                                                            title="تایید درخواست">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            onclick="rejectRequest(<?= $request['id'] ?>)"
                                                            title="رد درخواست">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button type="button" 
                                                        class="btn btn-outline-info btn-sm dropdown-toggle" 
                                                        data-bs-toggle="dropdown" 
                                                        title="عملیات بیشتر">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?= url("requests/edit/{$request['id']}") ?>">
                                                        <i class="fas fa-edit me-2"></i>ویرایش
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="duplicateRequest(<?= $request['id'] ?>)">
                                                        <i class="fas fa-copy me-2"></i>کپی
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteRequest(<?= $request['id'] ?>)">
                                                        <i class="fas fa-trash me-2"></i>حذف
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards View -->
                    <div class="d-md-none">
                        <?php foreach ($requests_data['data'] as $request): ?>
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-primary fs-3">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="<?= url("requests/show/{$request['id']}") ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($request['reference_number'] ?? 'REQ' . $request['id']) ?>
                                            </a>
                                            <?php if ($request['is_urgent'] ?? false): ?>
                                                <span class="badge bg-danger ms-1">فوری</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="text-muted small mb-2"><?= htmlspecialchars($request['title'] ?? 'بدون عنوان') ?></p>
                                        <div class="d-flex gap-2 mb-2 flex-wrap">
                                            <span class="badge bg-<?= getStatusColor($request['status'] ?? 'pending') ?> small">
                                                <?= $request['status_label'] ?? 'در انتظار' ?>
                                            </span>
                                            <span class="badge bg-<?= getPriorityColor($request['priority'] ?? 'normal') ?> small">
                                                <?= $request['priority_label'] ?? 'معمولی' ?>
                                            </span>
                                            <?php if (!empty($request['amount'])): ?>
                                                <span class="badge bg-success small">
                                                    <?= fa_num(number_format($request['amount'])) ?> ریال
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= url("requests/show/{$request['id']}") ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (($request['status'] ?? 'pending') === 'pending'): ?>
                                                <button type="button" class="btn btn-outline-success btn-sm" 
                                                        onclick="approveRequest(<?= $request['id'] ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="rejectRequest(<?= $request['id'] ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if (($requests_data['total'] ?? 0) > 20): ?>
                <div class="dashboard-card-body border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            نمایش <?= fa_num($requests_data['from'] ?? 1) ?> تا <?= fa_num($requests_data['to'] ?? 1) ?> از <?= fa_num($requests_data['total'] ?? 0) ?> درخواست
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <?php 
                                $current_page = $requests_data['current_page'] ?? 1;
                                $last_page = $requests_data['last_page'] ?? 1;
                                ?>
                                
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= url('requests') ?>?page=<?= $current_page - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $current_page - 2); $i <= min($last_page, $current_page + 2); $i++): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= url('requests') ?>?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>">
                                            <?= fa_num($i) ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $last_page): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= url('requests') ?>?page=<?= $current_page + 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="dashboard-card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice-dollar fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">هیچ درخواستی یافت نشد</h5>
                    <p class="text-muted">هنوز درخواستی در سیستم ثبت نشده است یا درخواستی با این فیلترها وجود ندارد</p>
                    <a href="<?= url('requests/create') ?>" class="btn btn-dashboard btn-dashboard-primary">
                        <i class="fas fa-plus me-2"></i>
                        ایجاد اولین درخواست
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar (25%) -->
        <div class="col-12 col-xl-3">
            <div class="dashboard-card animate-fade-in animate-delay-4">
                <div class="dashboard-card-header">
                    <h6 class="dashboard-card-title">
                        <i class="fas fa-bolt"></i>
                        عملیات سریع
                    </h6>
                </div>
                <div class="dashboard-card-body">
                    <a href="<?= url('requests/create') ?>" class="quick-action-item">
                        <div class="quick-action-icon bg-success">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">درخواست جدید</div>
                            <div class="quick-action-desc">ایجاد درخواست حواله</div>
                        </div>
                    </a>
                    
                    <a href="#" onclick="exportData(); return false;" class="quick-action-item">
                        <div class="quick-action-icon bg-primary">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">خروجی Excel</div>
                            <div class="quick-action-desc">دانلود گزارش درخواست‌ها</div>
                        </div>
                    </a>
                    
                    <a href="#" onclick="refreshTable(); return false;" class="quick-action-item">
                        <div class="quick-action-icon bg-info">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">بروزرسانی</div>
                            <div class="quick-action-desc">بارگذاری مجدد لیست</div>
                        </div>
                    </a>
                    
                    <a href="#" onclick="showBulkActions(); return false;" class="quick-action-item">
                        <div class="quick-action-icon bg-warning">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">عملیات گروهی</div>
                            <div class="quick-action-desc">اعمال تغییرات دسته‌ای</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Stats Panel -->
            <div class="dashboard-card animate-fade-in animate-delay-4">
                <div class="dashboard-card-header">
                    <h6 class="dashboard-card-title">
                        <i class="fas fa-chart-pie"></i>
                        آمار این ماه
                    </h6>
                </div>
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-check text-white small"></i>
                        </div>
                        <div class="small">
                            <div>تکمیل شده: <span class="fw-bold"><?= fa_num($stats['completed'] ?? 0) ?></span></div>
                            <div class="text-muted">موفقیت آمیز</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-clock text-white small"></i>
                        </div>
                        <div class="small">
                            <div>در انتظار: <span class="fw-bold"><?= fa_num($stats['pending'] ?? 0) ?></span></div>
                            <div class="text-muted">نیاز به بررسی</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-times text-white small"></i>
                        </div>
                        <div class="small">
                            <div>رد شده: <span class="fw-bold"><?= fa_num($stats['rejected'] ?? 0) ?></span></div>
                            <div class="text-muted">عدم تایید</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="dashboard-card animate-fade-in animate-delay-4">
                <div class="dashboard-card-header">
                    <h6 class="dashboard-card-title">
                        <i class="fas fa-info-circle"></i>
                        اطلاعات سیستم
                    </h6>
                </div>
                <div class="dashboard-card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-calendar text-primary"></i>
                        <div class="small">آخرین بروزرسانی: <?= jdate('Y/m/d', time()) ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-database text-info"></i>
                        <div class="small">کل درخواست‌ها: <?= fa_num($stats['total'] ?? 0) ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-server text-success"></i>
                        <div class="small">وضعیت سیستم: آنلاین</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/theme-system.js"></script>
<script src="/assets/js/advanced-search.js"></script>

<script>
// Request Management Functions
function approveRequest(requestId) {
    if (!confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        return;
    }
    
    fetch(`<?= url('requests/approve') ?>/${requestId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در تایید درخواست');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function rejectRequest(requestId) {
    const reason = prompt('لطفاً دلیل رد درخواست را وارد کنید:');
    if (!reason || reason.trim() === '') {
        return;
    }
    
    fetch(`<?= url('requests/reject') ?>/${requestId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در رد درخواست');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function deleteRequest(requestId) {
    if (!confirm('آیا از حذف این درخواست اطمینان دارید؟ این عمل قابل بازگشت نیست.')) {
        return;
    }
    
    fetch(`<?= url('requests/delete') ?>/${requestId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در حذف درخواست');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function duplicateRequest(requestId) {
    if (!confirm('آیا می‌خواهید یک کپی از این درخواست ایجاد کنید؟')) {
        return;
    }
    
    fetch(`<?= url('requests/duplicate') ?>/${requestId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در کپی کردن درخواست');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function exportData() {
    window.open('<?= url('requests/export') ?>', '_blank');
}

function refreshTable() {
    location.reload();
}

function clearFilters() {
    if (requestSearch) {
        requestSearch.clearFilters();
    } else {
        // Fallback برای صورتی که requestSearch آماده نباشد
        const statusFilter = document.getElementById('statusFilter');
        const priorityFilter = document.getElementById('priorityFilter');
        
        if (statusFilter) statusFilter.value = '';
        if (priorityFilter) priorityFilter.value = '';
        
        location.reload();
    }
}

function showBulkActions() {
    alert('عملیات گروهی در حال توسعه است');
}

// Advanced Search Component Configuration
let requestSearch;

// Professional Dashboard Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Initialize Advanced Search for Requests
    requestSearch = new AdvancedSearch({
        containerSelector: '#requestAdvancedSearch',
        resultsSelector: '.dashboard-table tbody',
        apiUrl: '<?= url('requests') ?>&action=api',
        placeholder: 'جستجو... (Ctrl+K)',
        helpText: '',
        debounceDelay: 300,
        enableStats: false,
        enableKeyboardShortcuts: true,
        
        // تابع رندر سفارشی نتایج
        customResultRenderer: function(results) {
            return results.map(result => {
                const statusColor = getStatusColorForResult(result.status || 'pending');
                const priorityColor = getPriorityColorForResult(result.priority || 'normal');
                
                return `
                    <tr onclick="window.location.href='<?= url('requests/show') ?>/${result.id}'" style="cursor: pointer;">
                        <td>
                            <code class="persian-num">${result.id || ''}</code>
                        </td>
                        <td>
                            <a href="<?= url("requests/show") ?>/${result.id}" 
                               class="text-decoration-none fw-semibold text-primary">
                                ${result.reference_number || 'REQ' + result.id}
                            </a>
                            ${result.is_urgent ? '<span class="badge bg-danger ms-1">فوری</span>' : ''}
                        </td>
                        <td>
                            <div>
                                <div class="fw-semibold">${result.title_highlighted || result.title || 'بدون عنوان'}</div>
                                ${result.description_highlighted ? '<div class="text-muted small">' + result.description_highlighted + '</div>' : ''}
                            </div>
                        </td>
                        <td>
                            ${result.amount ? '<span class="fw-bold text-success persian-num">' + formatAmount(result.amount) + ' ریال</span>' : '<span class="text-muted"><i class="fas fa-minus me-1"></i>مشخص نشده</span>'}
                        </td>
                        <td>
                            ${result.account_holder_highlighted || result.account_holder || '<span class="text-muted"><i class="fas fa-user me-1"></i>نامشخص</span>'}
                        </td>
                        <td>
                            <span class="badge bg-${statusColor}">
                                ${result.status_label || 'در انتظار'}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-${priorityColor}">
                                ${result.priority_label || 'معمولی'}
                            </span>
                        </td>
                        <td class="text-muted persian-num">
                            ${result.created_at_jalali || new Date().toLocaleDateString('fa-IR')}
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url("requests/show") ?>/${result.id}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="مشاهده جزئیات">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        },
        
        // Event callbacks
        onSearchStart: function(searchTerm) {
            console.log('جستجو شروع شد:', searchTerm);
        },
        
        onSearchComplete: function(results, searchTerm) {
            console.log('جستجو تکمیل شد:', results.length, 'نتیجه برای', searchTerm);
            updatePageResults(results);
        },
        
        onSearchError: function(error) {
            console.error('خطا در جستجو:', error);
            showErrorMessage('خطا در جستجو. لطفاً دوباره تلاش کنید.');
        }
    });

    // اتصال فیلترها
    document.getElementById('statusFilter').addEventListener('change', function() {
        requestSearch.setFilter('status', this.value);
    });
    
    document.getElementById('priorityFilter').addEventListener('change', function() {
        requestSearch.setFilter('priority', this.value);
    });
    
    // Keyboard shortcut برای جستجو (Ctrl+K)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('#requestAdvancedSearch input')?.focus();
        }
    });

    // Progressive animations for stats cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
    
    // Table hover effects
    const tableRows = document.querySelectorAll('.dashboard-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.005)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Auto dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Helper Functions for Advanced Search
function getStatusColorForResult(status) {
    const colors = {
        'pending': 'warning',
        'processing': 'info', 
        'approved': 'primary',
        'completed': 'success',
        'rejected': 'danger',
        'cancelled': 'secondary'
    };
    return colors[status] || 'warning';
}

function getPriorityColorForResult(priority) {
    const colors = {
        'low': 'secondary',
        'normal': 'info',
        'high': 'warning', 
        'urgent': 'danger'
    };
    return colors[priority] || 'info';
}

function formatAmount(amount) {
    return new Intl.NumberFormat('fa-IR').format(amount);
}

function updatePageResults(results) {
    // می‌توان نتایج را در صفحه به‌روزرسانی کرد
    console.log('نتایج جدید:', results);
    // اختیاری: صفحه را reload کن یا نتایج را dynamic به‌روزرسانی کن
}

function showErrorMessage(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" 
             style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // خودکار حذف کردن
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-child');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}
</script>
