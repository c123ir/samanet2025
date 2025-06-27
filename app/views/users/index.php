<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /app/views/users/index.php
 * توضیح: صفحه مدیریت کاربران - Bootstrap 5 استاندارد
 * تاریخ بازطراحی: 1404/10/16
 * نسخه: 2.0 Bootstrap Standard
 */

// تابع دریافت رنگ نقش
function getRoleColor($role) {
    $colors = [
        'admin' => 'danger',
        'manager' => 'primary', 
        'accountant' => 'info',
        'user' => 'secondary'
    ];
    return $colors[$role] ?? 'secondary';
}

// تابع دریافت ایکون نقش
function getRoleIcon($role) {
    $icons = [
        'admin' => 'fas fa-crown',
        'manager' => 'fas fa-user-tie',
        'accountant' => 'fas fa-calculator',
        'user' => 'fas fa-user'
    ];
    return $icons[$role] ?? 'fas fa-user';
}

// Load main layout
require_once(APP_PATH . 'views/layouts/main.php');
?>

<!-- Page-specific CSS -->
<link href="/assets/css/users.css" rel="stylesheet">

<!-- Page Content -->
<div class="users-page">
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <nav class="navbar navbar-expand-lg h-100">
                <div class="container-fluid">
                    <a class="navbar-brand text-gradient" href="<?= url('dashboard') ?>">
                        <i class="fas fa-users me-2"></i>
                        مدیریت کاربران
                    </a>
                    
                    <div class="d-flex align-items-center gap-3">
                        <button class="theme-toggle btn d-flex align-items-center justify-content-center" 
                                onclick="toggleTheme()" title="تغییر تم">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                        <div class="user-avatar" title="<?= htmlspecialchars($user['name'] ?? 'کاربر') ?>">
                            <?= mb_substr($user['name'] ?? 'ک', 0, 1) ?>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="dashboard-main">
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
                        <div class="stat-label">کل کاربران</div>
                        <div class="stat-value"><?= fa_num($stats['total'] ?? 0) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-users"></i>
                            <span>ثبت‌نام‌شده</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card animate-delay-2">
                        <div class="stat-label">کاربران فعال</div>
                        <div class="stat-value"><?= fa_num($stats['active'] ?? 0) ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-user-check"></i>
                            <span>دارای دسترسی</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card animate-delay-3">
                        <div class="stat-label">غیرفعال</div>
                        <div class="stat-value"><?= fa_num($stats['inactive'] ?? 0) ?></div>
                        <div class="stat-change negative">
                            <i class="fas fa-user-times"></i>
                            <span>تعلیق شده</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card animate-delay-4">
                        <div class="stat-label">مدیران</div>
                        <div class="stat-value">
                            <?= fa_num(count(array_filter($users ?? [], function($u) { 
                                return in_array($u['role'], ['admin', 'manager']); 
                            }))) ?>
                        </div>
                        <div class="stat-change positive">
                            <i class="fas fa-user-shield"></i>
                            <span>مدیر و ادمین</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="dashboard-card animate-fade-in animate-delay-4">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-search"></i>
                        جستجو و فیلتر
                    </h5>
                </div>
                <div class="dashboard-card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">جستجو</label>
                            <input type="text" name="search" class="form-control form-control-dashboard" 
                                   placeholder="نام کاربری، نام کامل یا ایمیل..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">نقش</label>
                            <select name="role" class="form-select form-control-dashboard">
                                <option value="">همه نقش‌ها</option>
                                <?php foreach ($roles as $roleKey => $roleLabel): ?>
                                    <option value="<?= $roleKey ?>" <?= ($_GET['role'] ?? '') === $roleKey ? 'selected' : '' ?>>
                                        <?= $roleLabel ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">وضعیت</label>
                            <select name="status" class="form-select form-control-dashboard">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>فعال</option>
                                <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-dashboard btn-dashboard-primary">
                                <i class="fas fa-search me-1"></i>
                                جستجو
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times me-1"></i>
                                پاک کردن
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="row g-3">
                <!-- Users List (75%) -->
                <div class="col-12 col-xl-9">
                    <div class="dashboard-card animate-fade-in animate-delay-4">
                        <div class="dashboard-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="dashboard-card-title mb-0">
                                <i class="fas fa-list"></i>
                                لیست کاربران
                                <span class="card-badge"><?= fa_num(count($users ?? [])) ?></span>
                            </h5>
                            <div class="d-flex gap-2">
                                <a href="<?= url('users/create') ?>" class="btn btn-dashboard btn-dashboard-success btn-sm">
                                    <i class="fas fa-plus me-1"></i>
                                    کاربر جدید
                                </a>
                                <button class="btn btn-outline-primary btn-sm" onclick="exportUsers()" title="خروجی Excel">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if (!empty($users)): ?>
                        <div class="dashboard-card-body p-0">
                            <!-- Desktop Table View -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table dashboard-table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>شناسه</th>
                                            <th>کاربر</th>
                                            <th>ایمیل</th>
                                            <th>نقش</th>
                                            <th>وضعیت</th>
                                            <th>آخرین ورود</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <code class="persian-num"><?= fa_num($user['id']) ?></code>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                                            <?= mb_substr($user['full_name'], 0, 1) ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold"><?= htmlspecialchars($user['full_name']) ?></div>
                                                            <div class="text-muted small">@<?= htmlspecialchars($user['username']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($user['email'])): ?>
                                                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none">
                                                            <i class="fas fa-envelope me-1 text-muted"></i>
                                                            <?= htmlspecialchars($user['email']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-minus me-1"></i>
                                                            ندارد
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= getRoleColor($user['role']) ?>">
                                                        <i class="<?= getRoleIcon($user['role']) ?> me-1"></i>
                                                        <?= $roles[$user['role']] ?? $user['role'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?= $user['status'] ?>" id="status-<?= $user['id'] ?>">
                                                        <i class="fas fa-<?= $user['status'] === 'active' ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                                        <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted persian-num">
                                                    <?php if ($user['last_login']): ?>
                                                        <?= jdate('Y/m/d H:i', strtotime($user['last_login'])) ?>
                                                    <?php else: ?>
                                                        هرگز
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= url("users/show/{$user['id']}") ?>" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           title="مشاهده جزئیات">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        
                                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                            <button type="button" 
                                                                    class="btn btn-outline-<?= $user['status'] === 'active' ? 'warning' : 'success' ?> btn-sm" 
                                                                    onclick="toggleUserStatus(<?= $user['id'] ?>)"
                                                                    title="<?= $user['status'] === 'active' ? 'غیرفعال کردن' : 'فعال کردن' ?>">
                                                                <i class="fas fa-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                                                            </button>
                                                            
                                                            <?php if (Security::checkPermission('admin')): ?>
                                                                <a href="<?= url("users/edit/{$user['id']}") ?>" 
                                                                   class="btn btn-outline-info btn-sm" 
                                                                   title="ویرایش کاربر">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button type="button" 
                                                                        class="btn btn-outline-danger btn-sm" 
                                                                        onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                                                        title="حذف کاربر">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Cards View -->
                            <div class="d-md-none">
                                <?php foreach ($users as $user): ?>
                                    <div class="p-3 border-bottom">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="user-avatar">
                                                <?= mb_substr($user['full_name'], 0, 1) ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= htmlspecialchars($user['full_name']) ?></h6>
                                                <p class="text-muted small mb-2">@<?= htmlspecialchars($user['username']) ?></p>
                                                <div class="d-flex gap-2 mb-2">
                                                    <span class="badge bg-<?= getRoleColor($user['role']) ?> small">
                                                        <?= $roles[$user['role']] ?? $user['role'] ?>
                                                    </span>
                                                    <span class="status-badge <?= $user['status'] ?> small">
                                                        <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                                                    </span>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= url("users/show/{$user['id']}") ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                                onclick="toggleUserStatus(<?= $user['id'] ?>)">
                                                            <i class="fas fa-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="dashboard-card-body text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">هیچ کاربری یافت نشد</h5>
                            <p class="text-muted">هنوز کاربری در سیستم ثبت نشده است یا کاربری با این فیلترها وجود ندارد</p>
                            <a href="<?= url('users/create') ?>" class="btn btn-dashboard btn-dashboard-primary">
                                <i class="fas fa-plus me-2"></i>
                                ایجاد کاربر جدید
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
                            <a href="<?= url('users/create') ?>" class="quick-action-item">
                                <div class="quick-action-icon bg-success">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">ایجاد کاربر جدید</div>
                                    <div class="quick-action-desc">افزودن کاربر به سیستم</div>
                                </div>
                            </a>
                            
                            <a href="#" onclick="exportUsers(); return false;" class="quick-action-item">
                                <div class="quick-action-icon bg-primary">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">خروجی Excel</div>
                                    <div class="quick-action-desc">دانلود لیست کاربران</div>
                                </div>
                            </a>
                            
                            <a href="#" onclick="refreshUserList(); return false;" class="quick-action-item">
                                <div class="quick-action-icon bg-info">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">به‌روزرسانی</div>
                                    <div class="quick-action-desc">بارگذاری مجدد لیست</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="dashboard-card animate-fade-in animate-delay-4">
                        <div class="dashboard-card-header">
                            <h6 class="dashboard-card-title">
                                <i class="fas fa-history"></i>
                                فعالیت‌های اخیر
                            </h6>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-plus text-white small"></i>
                                </div>
                                <div class="small">
                                    <div>کاربر جدید ثبت‌نام کرد</div>
                                    <div class="text-muted">۵ دقیقه پیش</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-edit text-white small"></i>
                                </div>
                                <div class="small">
                                    <div>نقش کاربر تغییر یافت</div>
                                    <div class="text-muted">۱۵ دقیقه پیش</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-times text-white small"></i>
                                </div>
                                <div class="small">
                                    <div>کاربر غیرفعال شد</div>
                                    <div class="text-muted">۳۰ دقیقه پیش</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/theme-system.js"></script>

<script>
// User Management Functions
function toggleUserStatus(userId) {
    if (!confirm('آیا از تغییر وضعیت این کاربر اطمینان دارید؟')) {
        return;
    }
    
    fetch(`<?= url('users/toggleStatus') ?>/${userId}`, {
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
            alert(data.message || 'خطا در تغییر وضعیت کاربر');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function deleteUser(userId, username) {
    if (!confirm(`آیا از حذف کاربر "${username}" اطمینان دارید؟`)) {
        return;
    }
    
    fetch(`<?= url('users/delete') ?>/${userId}`, {
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
            alert(data.message || 'خطا در حذف کاربر');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function exportUsers() {
    window.open('<?= url('users/export') ?>', '_blank');
}

function refreshUserList() {
    location.reload();
}

function clearFilters() {
    window.location.href = '<?= url('users') ?>';
}

// Professional Dashboard Initialization
document.addEventListener('DOMContentLoaded', function() {
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
</script>

<?php
// Load main layout footer
require_once(APP_PATH . 'views/layouts/footer.php');
?> 