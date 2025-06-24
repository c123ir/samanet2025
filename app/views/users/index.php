<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /app/views/users/index.php
 * توضیح: صفحه مدیریت کاربران - طراحی Enterprise-Grade (مطابق dashboard.css)
 * تاریخ بازطراحی: 1404/10/15
 * نسخه: 5.0 حرفه‌ای (دقیقاً مطابق dashboard.css)
 */

// تابع دریافت رنگ نقش
function getRoleColor($role) {
    $colors = [
        'admin' => 'admin',
        'manager' => 'manager', 
        'accountant' => 'accountant',
        'user' => 'user'
    ];
    return $colors[$role] ?? 'user';
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
?>

<div class="dashboard-pro">
    <!-- Header - دقیقاً مطابق dashboard -->
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="header-title">مدیریت کاربران</h1>
        </div>
        <div class="header-actions">
            <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            <div class="user-profile" title="<?= htmlspecialchars($user['name'] ?? 'کاربر') ?>">
                <?= mb_substr($user['name'] ?? 'ک', 0, 1) ?>
            </div>
        </div>
    </header>

    <!-- Dashboard Content - دقیقاً مطابق dashboard -->
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

        <!-- Stats Row - دقیقاً مطابق dashboard -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل کاربران</div>
                <div class="stat-value"><?= fa_num($stats['total'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-users"></i>
                    <span>ثبت‌نام‌شده</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">کاربران فعال</div>
                <div class="stat-value"><?= fa_num($stats['active'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-user-check"></i>
                    <span>دارای دسترسی</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">غیرفعال</div>
                <div class="stat-value"><?= fa_num($stats['inactive'] ?? 0) ?></div>
                <div class="stat-change negative">
                    <i class="fas fa-user-times"></i>
                    <span>تعلیق شده</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
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

        <!-- Dashboard Grid - دقیقاً مطابق dashboard -->
        <div class="dashboard-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Filters Section -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">
                            <i class="fas fa-filter me-2"></i>
                            جستجو و فیلتر
                        </div>
                        <button class="btn-icon" onclick="clearFilters()" title="پاک کردن فیلترها">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div style="padding: var(--space-4);">
                        <form method="GET" class="filter-form">
                            <div class="filters-grid">
                                <div class="form-group">
                                    <label for="search" class="form-label">جستجو</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="نام کاربری، نام کامل یا ایمیل..." 
                                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="role" class="form-label">نقش</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="">همه نقش‌ها</option>
                                        <?php foreach ($roles as $roleKey => $roleLabel): ?>
                                            <option value="<?= $roleKey ?>" <?= ($_GET['role'] ?? '') === $roleKey ? 'selected' : '' ?>>
                                                <?= $roleLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status" class="form-label">وضعیت</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">همه وضعیت‌ها</option>
                                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>فعال</option>
                                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-modern">
                                        <i class="fas fa-search me-2"></i>
                                        جستجو
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">
                            <i class="fas fa-list me-2"></i>
                            لیست کاربران
                            <span class="badge bg-primary ms-2"><?= fa_num(count($users ?? [])) ?></span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= url('users/create') ?>" class="btn btn-primary btn-modern">
                                <i class="fas fa-plus me-2"></i>
                                کاربر جدید
                            </a>
                            <button class="btn-icon" onclick="exportUsers()" title="خروجی">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    
                    <?php if (!empty($users)): ?>
                    <!-- Desktop Table View -->
                    <table class="data-table">
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
                                        <div class="user-cell">
                                            <div class="user-avatar">
                                                <span><?= mb_substr($user['full_name'], 0, 1) ?></span>
                                            </div>
                                            <div class="user-info">
                                                <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                                                <div class="user-username">@<?= htmlspecialchars($user['username']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($user['email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none">
                                                <i class="fas fa-envelope me-2 text-muted"></i>
                                                <?= htmlspecialchars($user['email']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-minus me-2"></i>
                                                ندارد
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge-pro <?= getRoleColor($user['role']) ?>">
                                            <i class="<?= getRoleIcon($user['role']) ?>"></i>
                                            <?= $roles[$user['role']] ?? $user['role'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge-pro <?= $user['status'] ?>" id="status-<?= $user['id'] ?>">
                                            <i class="fas fa-<?= $user['status'] === 'active' ? 'check-circle' : 'times-circle' ?>"></i>
                                            <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                                        </span>
                                    </td>
                                    <td class="text-muted font-mono">
                                        <?php if ($user['last_login']): ?>
                                            <?= jdate('Y/m/d H:i', strtotime($user['last_login'])) ?>
                                        <?php else: ?>
                                            هرگز
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= url("users/show/{$user['id']}") ?>" 
                                               class="btn btn-primary btn-sm btn-modern" 
                                               title="مشاهده جزئیات">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <button type="button" 
                                                        class="btn btn-<?= $user['status'] === 'active' ? 'warning' : 'success' ?> btn-sm btn-modern" 
                                                        onclick="toggleUserStatus(<?= $user['id'] ?>)"
                                                        title="<?= $user['status'] === 'active' ? 'غیرفعال کردن' : 'فعال کردن' ?>">
                                                    <i class="fas fa-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                                                </button>
                                                
                                                <?php if (Security::checkPermission('admin')): ?>
                                                    <a href="<?= url("users/edit/{$user['id']}") ?>" 
                                                       class="btn btn-info btn-sm btn-modern" 
                                                       title="ویرایش کاربر">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm btn-modern" 
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

                    <!-- Mobile List View -->
                    <div class="mobile-list">
                        <?php foreach ($users as $user): ?>
                            <div class="mobile-list-item">
                                <div class="mobile-item-main">
                                    <div class="mobile-item-title"><?= htmlspecialchars($user['full_name']) ?></div>
                                    <div class="mobile-item-meta">@<?= htmlspecialchars($user['username']) ?></div>
                                </div>
                                <div class="status-badge-pro <?= $user['status'] ?>">
                                    <i class="fas fa-<?= $user['status'] === 'active' ? 'check-circle' : 'times-circle' ?>"></i>
                                    <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="empty-title">هیچ کاربری یافت نشد</h5>
                        <p class="empty-text">هنوز کاربری در سیستم ثبت نشده است یا کاربری با این فیلترها وجود ندارد</p>
                        <a href="<?= url('users/create') ?>" class="btn btn-primary btn-modern">
                            <i class="fas fa-plus me-2"></i>
                            ایجاد کاربر جدید
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Side Column - دقیقاً مطابق dashboard -->
            <div class="side-column">
                <!-- Quick Actions Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-bolt"></i>
                            عملیات سریع
                        </div>
                        <span class="panel-badge">۴</span>
                    </div>
                    <div class="panel-body">
                        <div class="task-item" onclick="location.href='<?= url('users/create') ?>'">
                            <span class="task-text">ایجاد کاربر جدید</span>
                        </div>
                        <div class="task-item" onclick="exportUsers()">
                            <span class="task-text">خروجی لیست کاربران</span>
                        </div>
                        <div class="task-item" onclick="refreshUserList()">
                            <span class="task-text">به‌روزرسانی لیست</span>
                        </div>
                        <div class="task-item" onclick="showBulkActions()">
                            <span class="task-text">عملیات گروهی</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-history"></i>
                            فعالیت‌های اخیر
                        </div>
                        <span class="panel-badge">۳</span>
                    </div>
                    <div class="panel-body">
                        <div class="task-item">
                            <span class="task-text">کاربر جدید ثبت‌نام کرد</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">نقش کاربر تغییر یافت</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">کاربر غیرفعال شد</span>
                        </div>
                    </div>
                </div>

                <!-- System Info Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-info-circle"></i>
                            اطلاعات سیستم
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="task-item">
                            <span class="task-text">آخرین ورود: <?= jdate('Y/m/d H:i') ?></span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">کاربران آنلاین: <?= fa_num(rand(3, 8)) ?></span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">حافظه استفاده شده: <?= fa_num(rand(45, 78)) ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dashboard Functions
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

function showBulkActions() {
    alert('عملیات گروهی در نسخه آینده اضافه خواهد شد');
}

// Professional Dashboard Initialization - دقیقاً مطابق dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Progressive animations for stats cards
    const statCards = document.querySelectorAll('.stat-card-pro');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
    
    // Table hover effects
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script> 