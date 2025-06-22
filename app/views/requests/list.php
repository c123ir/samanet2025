<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/requests/list.php
 * توضیح: صفحه نمایش لیست درخواست‌های حواله
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
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
$requests_data = $requests ?? ['data' => [], 'total' => 0, 'current_page' => 1];
$filters = $filters ?? [];
?>

<script>
document.body.classList.add('requests-page');
console.log('📄 Added requests-page class to body');
</script>

<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-list-alt text-primary me-2"></i>
                    مدیریت درخواست‌ها
                </h1>
                <p class="page-subtitle text-muted">
                    مشاهده، مدیریت و پیگیری درخواست‌های حواله
                </p>
            </div>
            <div>
                <a href="?route=requests&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    درخواست جدید
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- آمار درخواست‌ها -->
    <div class="stats-section mb-4">
        <div class="stats-grid" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important; gap: 1rem !important;">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?= number_format($stats['total'] ?? 0) ?></h3>
                    <p class="stat-label">کل درخواست‌ها</p>
                </div>
            </div>
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?= number_format($stats['pending'] ?? 0) ?></h3>
                    <p class="stat-label">در انتظار</p>
                </div>
            </div>
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?= number_format($stats['processing'] ?? 0) ?></h3>
                    <p class="stat-label">در حال پردازش</p>
                </div>
            </div>
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number"><?= number_format($stats['completed'] ?? 0) ?></h3>
                    <p class="stat-label">تکمیل شده</p>
                </div>
            </div>
        </div>
    </div>

    <!-- فیلترها و جستجو -->
    <div class="filters-section mb-4">
        <div class="flat-card">
            <div class="card-body p-3">
                <form id="filtersForm" method="GET" class="filters-form">
                    <input type="hidden" name="route" value="requests">
                    
                    <div class="row g-3">
                        <!-- جستجو -->
                        <div class="col-12">
                            <label for="search" class="form-label">جستجو</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                       placeholder="جستجو در عنوان، توضیحات، شماره مرجع...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- فیلترهای دوم -->
                        <div class="col-6 col-md-3">
                            <label for="status" class="form-label">وضعیت</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">همه وضعیت‌ها</option>
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- اولویت -->
                        <div class="col-6 col-md-3">
                            <label for="priority" class="form-label">اولویت</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">همه اولویت‌ها</option>
                                <?php foreach ($priorities as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($filters['priority'] ?? '') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- تاریخ از -->
                        <div class="col-6 col-md-3">
                            <label for="date_from" class="form-label">از تاریخ</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="<?= $filters['date_from'] ?? '' ?>">
                        </div>

                        <!-- تاریخ تا -->
                        <div class="col-6 col-md-3">
                            <label for="date_to" class="form-label">تا تاریخ</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="<?= $filters['date_to'] ?? '' ?>">
                        </div>

                        <!-- دکمه‌ها -->
                        <div class="col-12">
                            <div class="filters-actions">
                                <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                                    <i class="fas fa-eraser me-2"></i>
                                    <span class="d-none d-md-inline">پاک کردن فیلترها</span>
                                    <span class="d-md-none">پاک</span>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    <span class="d-none d-md-inline">اعمال فیلتر</span>
                                    <span class="d-md-none">جستجو</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول درخواست‌ها -->
    <div class="requests-table-section">
        <div class="flat-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    لیست درخواست‌ها
                    <span class="badge bg-secondary ms-2"><?= number_format($requests_data['total'] ?? 0) ?></span>
                </h5>
                <div class="table-controls">
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="refreshTable()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>
                            صدور
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportTable('excel')">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportTable('pdf')">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($requests_data['data'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="requestsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>شماره مرجع</th>
                                <th>عنوان درخواست</th>
                                <th class="text-center">مبلغ</th>
                                <th class="text-center">صاحب حساب</th>
                                <th class="text-center">وضعیت</th>
                                <th class="text-center">اولویت</th>
                                <th class="text-center">تاریخ ایجاد</th>
                                <th class="text-center" style="width: 120px;">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests_data['data'] as $index => $request): ?>
                            <tr>
                                <td class="text-center">
                                    <?= ($requests_data['current_page'] - 1) * 20 + $index + 1 ?>
                                </td>
                                <td>
                                    <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                                       class="text-decoration-none fw-medium">
                                        <?= htmlspecialchars($request['reference_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($request['is_urgent']): ?>
                                            <i class="fas fa-exclamation-triangle text-warning me-2" title="فوری"></i>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-medium"><?= htmlspecialchars($request['title']) ?></div>
                                            <?php if (!empty($request['description'])): ?>
                                                <small class="text-muted">
                                                    <?= mb_substr(htmlspecialchars($request['description']), 0, 50) ?>
                                                    <?= mb_strlen($request['description']) > 50 ? '...' : '' ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold"><?= $request['amount'] !== null ? number_format($request['amount']) : 'مشخص نشده' ?></span>
                                    <small class="text-muted d-block">ریال</small>
                                </td>
                                <td class="text-center">
                                    <?= htmlspecialchars($request['account_holder']) ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $status_classes = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'approved' => 'primary',
                                        'completed' => 'success',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $status_class = $status_classes[$request['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $status_class ?>">
                                        <?= $request['status_label'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $priority_classes = [
                                        'low' => 'success',
                                        'normal' => 'secondary',
                                        'high' => 'warning',
                                        'urgent' => 'danger'
                                    ];
                                    $priority_class = $priority_classes[$request['priority']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $priority_class ?>">
                                        <?= $request['priority_label'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small><?= $request['created_at_jalali'] ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                                           class="btn btn-outline-primary" 
                                           title="نمایش جزئیات">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($request['status'] === 'pending'): ?>
                                            <button class="btn btn-outline-success" 
                                                    onclick="approveRequest(<?= $request['id'] ?>)"
                                                    title="تایید">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="rejectRequest(<?= $request['id'] ?>)"
                                                    title="رد">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($requests_data['total'] > 20): ?>
                <div class="card-footer">
                    <nav aria-label="Navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <?php
                            $current_page = $requests_data['current_page'];
                            $last_page = $requests_data['last_page'];
                            $start = max(1, $current_page - 2);
                            $end = min($last_page, $current_page + 2);
                            ?>
                            
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?route=requests&page=<?= $current_page - 1 ?>&<?= http_build_query(array_filter($filters)) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
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
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            نمایش <?= $requests_data['from'] ?> تا <?= $requests_data['to'] ?> از <?= number_format($requests_data['total'] ?? 0) ?> درخواست
                        </small>
                    </div>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">درخواستی یافت نشد</h5>
                    <p class="text-muted">هیچ درخواستی با معیارهای انتخابی شما یافت نشد.</p>
                    <a href="?route=requests&action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        ایجاد اولین درخواست
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Force No Cache -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<!-- Load CSS مخصوص این صفحه - RTL Fixed Version -->
<link rel="stylesheet" href="assets/css/requests-page.css?v=rtl-fixed-<?= time() ?>" id="requests-css">
<script>
console.log('🎯 Loading Requests CSS RTL Fixed: rtl-fixed-<?= time() ?>');
</script>

<!-- Modal تایید درخواست -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تایید درخواست</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" id="approve_request_id">
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">توضیحات (اختیاری)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3"
                                  placeholder="توضیحات تایید..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-success">تایید درخواست</button>
                </div>
            </form>
        </div>
    </div>
</div>

 <!-- Modal رد درخواست -->
 <div class="modal fade" id="rejectModal" tabindex="-1">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">رد درخواست</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <form id="rejectForm" method="POST">
                 <div class="modal-body">
                     <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                     <input type="hidden" name="id" id="reject_request_id">
                     <div class="mb-3">
                         <label for="reject_reason" class="form-label required">دلیل رد</label>
                         <textarea class="form-control" id="reject_reason" name="reason" rows="3"
                                   placeholder="لطفاً دلیل رد درخواست را توضیح دهید..." required></textarea>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                     <button type="submit" class="btn btn-danger">رد درخواست</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

<!-- JavaScript مخصوص این صفحه -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر DOM
    const filtersForm = document.getElementById('filtersForm');
    const searchInput = document.getElementById('search');
    const clearSearchBtn = document.getElementById('clearSearch');
    const clearFiltersBtn = document.getElementById('clearFilters');

    // جستجوی زنده
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                submitFilters();
            }
        }, 500);
    });

    // پاک کردن جستجو
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        submitFilters();
    });

    // پاک کردن همه فیلترها
    clearFiltersBtn.addEventListener('click', function() {
        const inputs = filtersForm.querySelectorAll('input[type="text"], input[type="date"], select');
        inputs.forEach(input => {
            if (input.name !== 'route') {
                input.value = '';
            }
        });
        submitFilters();
    });

    // ارسال فیلترها
    function submitFilters() {
        const formData = new FormData(filtersForm);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }
        
        showLoading();
        window.location.href = '?' + params.toString();
    }

    // نمایش loading
    function showLoading() {
        const tableBody = document.querySelector('#requestsTable tbody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        در حال بارگذاری...
                    </td>
                </tr>
            `;
        }
    }

    // تایید درخواست
    window.approveRequest = function(requestId) {
        if (confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?route=requests&action=approve';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= $csrf_token ?>';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = requestId;
            
            form.appendChild(csrfInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    };

    // رد درخواست
    window.rejectRequest = function(requestId) {
        const reason = prompt('لطفاً دلیل رد درخواست را وارد کنید:');
        if (reason && reason.trim()) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?route=requests&action=reject';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= $csrf_token ?>';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = requestId;
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            
            form.appendChild(csrfInput);
            form.appendChild(idInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    };

    // رفرش جدول
    window.refreshTable = function() {
        showLoading();
        setTimeout(() => {
            window.location.reload();
        }, 500);
    };

    // صدور Excel/PDF
    window.exportTable = function(format) {
        alert(`صدور ${format.toUpperCase()} به زودی پیاده‌سازی می‌شود`);
    };

    // Force Layout and Fix RTL Overflow
    function forceLayoutFix() {
        // Fix content wrapper
        const contentWrapper = document.querySelector('.content-wrapper');
        if (contentWrapper) {
            contentWrapper.style.width = '100%';
            contentWrapper.style.maxWidth = '100%';
            contentWrapper.style.overflowX = 'hidden';
            contentWrapper.style.boxSizing = 'border-box';
            contentWrapper.style.paddingLeft = '0';
            contentWrapper.style.paddingRight = '0';
            
            console.log('🔧 Content wrapper fixed for RTL');
        }
        
        // Fix container fluid
        const containerFluid = document.querySelector('.container-fluid');
        if (containerFluid) {
            containerFluid.style.width = '100%';
            containerFluid.style.maxWidth = '100%';
            containerFluid.style.paddingLeft = '0';
            containerFluid.style.paddingRight = '0';
            containerFluid.style.margin = '0';
            containerFluid.style.boxSizing = 'border-box';
            
            console.log('🔧 Container fluid fixed');
        }
        
        // Force Stats Grid
        const statsGrid = document.querySelector('.stats-grid');
        if (statsGrid) {
            statsGrid.style.display = 'grid';
            statsGrid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(250px, 1fr))';
            statsGrid.style.gap = '1rem';
            statsGrid.style.width = '100%';
            statsGrid.style.maxWidth = '100%';
            statsGrid.style.boxSizing = 'border-box';
            
            // Remove any Bootstrap classes
            statsGrid.classList.remove('row');
            
            console.log('🎯 Stats Grid Forced');
        }
        
        // Force stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.width = '100%';
            card.style.maxWidth = '100%';
            card.style.boxSizing = 'border-box';
            card.style.margin = '0';
            card.style.flex = 'none';
            card.classList.remove('col-md-3', 'col-sm-6', 'col-12');
        });
        
        // Fix all flat-card elements
        const flatCards = document.querySelectorAll('.flat-card');
        flatCards.forEach(card => {
            card.style.width = '100%';
            card.style.maxWidth = '100%';
            card.style.boxSizing = 'border-box';
            card.style.overflow = 'hidden';
        });
        
        console.log(`✅ Layout fixed: ${statCards.length} stat cards, ${flatCards.length} flat cards`);
    }
    
    // Force layout fix on load and after delays
    forceLayoutFix();
    setTimeout(forceLayoutFix, 100);
    setTimeout(forceLayoutFix, 500);
    setTimeout(forceLayoutFix, 1000); // اضافه کردن delay بیشتر
});
</script>

<!-- Debug Layout Issues -->
<script>
// اضافه کردن debug برای layout
window.debugLayout = function() {
    const contentWrapper = document.querySelector('.content-wrapper');
    const rect = contentWrapper.getBoundingClientRect();
    
    console.log('🔍 Layout Debug:', {
        width: rect.width,
        right: rect.right,
        windowWidth: window.innerWidth,
        overflowing: rect.right > window.innerWidth,
        marginRight: getComputedStyle(contentWrapper).marginRight,
        paddingRight: getComputedStyle(contentWrapper).paddingRight
    });
    
    if (rect.right > window.innerWidth) {
        console.warn('⚠️ Content is overflowing!');
    } else {
        console.log('✅ Layout is contained within viewport');
    }
};

// Auto debug after 2 seconds
setTimeout(() => {
    window.debugLayout();
}, 2000);
</script>

<!-- Force CSS Refresh -->
<style id="requests-list-style-v3">
/* استایل‌های مخصوص صفحه لیست - نسخه Responsive - Force Override - v2.1 */

/* =================
   Stats Grid System - FORCED
================== */
.stats-grid {
    display: grid !important;
    gap: 1rem !important;
    margin-bottom: 2rem !important;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

/* Remove Bootstrap Row/Col from stats if any */
.stats-section .row,
.stats-section .col-md-3,
.stats-section .col-sm-6 {
    all: unset !important;
}

.stats-section {
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}

.stat-card {
    background: var(--glass-bg) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: 15px !important;
    backdrop-filter: var(--glass-blur) !important;
    padding: 1.5rem !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    overflow: hidden !important;
    min-height: 120px !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    display: block !important;
    flex: none !important;
    margin: 0 !important;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stat-card-primary::before {
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stat-card-warning::before {
    background: linear-gradient(90deg, #f093fb, #f5576c);
}

.stat-card-info::before {
    background: linear-gradient(90deg, #4facfe, #00f2fe);
}

.stat-card-success::before {
    background: linear-gradient(90deg, #43e97b, #38f9d7);
}

.stat-card .stat-icon {
    position: absolute;
    top: 1rem;
    left: 1rem;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--primary-color);
}

.stat-content {
    text-align: right;
    padding-right: 0;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}

.stat-label {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}

/* =================
   Filters Section
================== */
.filters-section .flat-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 15px;
    backdrop-filter: var(--glass-blur);
    overflow: hidden;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.filters-form {
    width: 100%;
}

.filters-form .form-control,
.filters-form .form-select {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.filters-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
}

/* =================
   Table Section
================== */
.requests-table-section .flat-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 15px;
    backdrop-filter: var(--glass-blur);
    overflow: hidden;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    min-width: 800px; /* حداقل عرض برای جدول */
}

.table thead th {
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid var(--glass-border);
    color: var(--text-primary);
    font-weight: 600;
    padding: 1rem 0.75rem;
    white-space: nowrap;
}

.table tbody td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    vertical-align: middle;
}

.table tbody tr:hover {
    background: rgba(255, 255, 255, 0.02);
}

/* Badge ها */
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 500;
    white-space: nowrap;
}

/* دکمه‌های عملیات */
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-group-sm .btn:hover {
    transform: translateY(-2px);
}

/* =================
   Mobile Responsive
================== */
@media (max-width: 768px) {
    .content-wrapper {
        padding: 0.75rem !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    
         /* Stats Grid - موبایل */
     .stats-grid {
         grid-template-columns: 1fr !important;
         gap: 0.75rem !important;
         margin-bottom: 1.5rem !important;
         display: grid !important;
     }
    
    .stat-card {
        padding: 1rem;
        min-height: 100px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-icon {
        width: 35px !important;
        height: 35px !important;
        font-size: 1rem !important;
    }
    
    /* Page Header - موبایل */
    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .page-header .btn {
        width: 100%;
        text-align: center;
    }
    
    /* Filters - موبایل */
    .filters-section .card-body {
        padding: 1rem !important;
    }
    
    .filters-actions {
        justify-content: stretch;
        gap: 0.5rem;
    }
    
    .filters-actions .btn {
        flex: 1;
        min-width: 0;
    }
    
    /* Table - موبایل */
    .requests-table-section .card-header {
        padding: 1rem !important;
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
    }
    
    .table-controls {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table thead th,
    .table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Card Footer Pagination */
    .card-footer {
        padding: 1rem !important;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

 @media (max-width: 576px) {
     .stats-grid {
         gap: 0.5rem !important;
         grid-template-columns: 1fr !important;
         display: grid !important;
     }
    
    .stat-card {
        padding: 0.75rem;
        min-height: 80px;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .filters-section .card-body {
        padding: 0.75rem !important;
    }
    
    .filters-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filters-actions .btn {
        width: 100%;
    }
    
    .table {
        min-width: 700px; /* کمتر برای موبایل کوچک */
    }
    
    .table thead th,
    .table tbody td {
        padding: 0.5rem 0.25rem;
        font-size: 0.8rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.125rem 0.25rem;
        font-size: 0.7rem;
    }
}

/* =================
   Tablet Responsive
================== */
 @media (min-width: 769px) and (max-width: 1024px) {
     .stats-grid {
         grid-template-columns: repeat(2, 1fr) !important;
         gap: 1rem !important;
         display: grid !important;
     }
    
    .content-wrapper {
        padding: 1.5rem !important;
    }
}

/* =================
   Large Screen
================== */
 @media (min-width: 1200px) {
     .stats-grid {
         grid-template-columns: repeat(4, 1fr) !important;
         gap: 1.5rem !important;
         display: grid !important;
     }
    
    .stat-card {
        padding: 2rem;
    }
    
    .content-wrapper {
        padding: 2rem !important;
    }
}

/* =================
   Print Styles
================== */
@media print {
    .filters-section,
    .page-header .btn,
    .table-controls,
    .btn-group {
        display: none !important;
    }
    
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .table {
        border: 1px solid #000;
    }
    
    .table thead th,
    .table tbody td {
        border: 1px solid #000;
        color: #000;
        background: #fff;
    }
}

/* =================
   Dark Theme Support
================== */
[data-theme="dark"] .stat-card {
    background: rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.1);
}

[data-theme="dark"] .table thead th {
    background: rgba(0, 0, 0, 0.2);
}

[data-theme="dark"] .table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}
</style>
