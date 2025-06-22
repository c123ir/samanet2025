<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /app/views/users/index.php
 * توضیح: صفحه لیست کاربران - طراحی flat و responsive
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تابع دریافت رنگ نقش
function getRoleColor($role) {
    $colors = [
        'admin' => 'danger',
        'manager' => 'warning', 
        'accountant' => 'info',
        'user' => 'primary'
    ];
    return $colors[$role] ?? 'secondary';
}
?>

<!-- Page Header -->
<div class="flat-header mb-4">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h1 class="flat-title">
                    <span class="flat-icon">👥</span>
                    مدیریت کاربران
                </h1>
                <p class="flat-subtitle">
                    مدیریت و نظارت بر کاربران سیستم
                </p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="<?= url('users/create') ?>" class="btn-flat btn-flat-primary">
                    <span class="btn-icon">➕</span>
                    کاربر جدید
                </a>
                <button type="button" class="btn-flat btn-flat-secondary" onclick="refreshUserList()">
                    <span class="btn-icon">🔄</span>
                    به‌روزرسانی
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-content">
            <div class="stat-info">
                <h3 class="stat-number"><?= fa_num($stats['total'] ?? 0) ?></h3>
                <p class="stat-label">کل کاربران</p>
                <small class="stat-desc">ثبت‌نام‌شده در سیستم</small>
            </div>
            <div class="stat-icon">
                <span>👥</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-card-success">
        <div class="stat-content">
            <div class="stat-info">
                <h3 class="stat-number"><?= fa_num($stats['active'] ?? 0) ?></h3>
                <p class="stat-label">کاربران فعال</p>
                <small class="stat-desc">دارای دسترسی به سیستم</small>
            </div>
            <div class="stat-icon">
                <span>✅</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-card-warning">
        <div class="stat-content">
            <div class="stat-info">
                <h3 class="stat-number"><?= fa_num($stats['inactive'] ?? 0) ?></h3>
                <p class="stat-label">کاربران غیرفعال</p>
                <small class="stat-desc">تعلیق شده یا غیرفعال</small>
            </div>
            <div class="stat-icon">
                <span>⏸️</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-card-info">
        <div class="stat-content">
            <div class="stat-info">
                <h3 class="stat-number"><?= fa_num(count(array_filter($users ?? [], function($u) { return in_array($u['role'], ['admin', 'manager']); }))) ?></h3>
                <p class="stat-label">مدیران</p>
                <small class="stat-desc">مدیر و ادمین سیستم</small>
            </div>
            <div class="stat-icon">
                <span>🛡️</span>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search -->
<div class="flat-card mb-4">
    <div class="flat-card-header">
        <h6 class="flat-card-title">
            <span class="card-icon">🔍</span>
            جستجو و فیلتر
        </h6>
    </div>
    <div class="flat-card-body">
        <form method="GET" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search" class="form-label">جستجو</label>
                    <input type="text" class="form-input" id="search" name="search" 
                           placeholder="نام کاربری، نام کامل یا ایمیل..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="role" class="form-label">نقش</label>
                    <select class="form-select" id="role" name="role">
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
                    <select class="form-select" id="status" name="status">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>فعال</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn-flat btn-flat-primary w-100">
                        <span class="btn-icon">🔍</span>
                        جستجو
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="flat-card">
    <div class="flat-card-header">
        <h6 class="flat-card-title">
            <span class="card-icon">📋</span>
            لیست کاربران
        </h6>
        <span class="flat-badge flat-badge-primary"><?= fa_num(count($users ?? [])) ?> کاربر</span>
    </div>
    <div class="flat-card-body">
        <?php if (!empty($users)): ?>
            <!-- Mobile Cards View -->
            <div class="mobile-users d-md-none">
                <?php foreach ($users as $user): ?>
                    <div class="user-card-mobile">
                        <div class="user-header">
                            <div class="user-avatar">
                                <span><?= mb_substr($user['full_name'], 0, 1) ?></span>
                            </div>
                            <div class="user-info">
                                <h6 class="user-name"><?= htmlspecialchars($user['full_name']) ?></h6>
                                <p class="user-username">@<?= htmlspecialchars($user['username']) ?></p>
                            </div>
                            <div class="user-status">
                                <span class="flat-badge flat-badge-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                                </span>
                            </div>
                        </div>
                        <div class="user-details">
                            <div class="detail-item">
                                <span class="detail-label">نقش:</span>
                                <span class="flat-badge flat-badge-<?= getRoleColor($user['role']) ?>">
                                    <?= $roles[$user['role']] ?? $user['role'] ?>
                                </span>
                            </div>
                            <?php if (!empty($user['email'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">ایمیل:</span>
                                    <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="detail-value">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="detail-item">
                                <span class="detail-label">آخرین ورود:</span>
                                <span class="detail-value persian-num">
                                    <?php if ($user['last_login']): ?>
                                        <?= jdate('Y/m/d H:i', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        هرگز
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        <div class="user-actions">
                            <a href="<?= url("users/show/{$user['id']}") ?>" class="btn-flat-sm btn-flat-primary">
                                <span>👁️</span>
                                مشاهده
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button type="button" 
                                        class="btn-flat-sm btn-flat-<?= $user['status'] === 'active' ? 'warning' : 'success' ?>" 
                                        onclick="toggleUserStatus(<?= $user['id'] ?>)">
                                    <span><?= $user['status'] === 'active' ? '⏸️' : '▶️' ?></span>
                                    <?= $user['status'] === 'active' ? 'غیرفعال' : 'فعال' ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="flat-table">
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
                                    <code class="flat-code persian-num"><?= fa_num($user['id']) ?></code>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-small">
                                            <span><?= mb_substr($user['full_name'], 0, 1) ?></span>
                                        </div>
                                        <div>
                                            <div class="user-name-small"><?= htmlspecialchars($user['full_name']) ?></div>
                                            <div class="user-username-small">@<?= htmlspecialchars($user['username']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($user['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="flat-link">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">ندارد</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="flat-badge flat-badge-<?= getRoleColor($user['role']) ?>">
                                        <?= $roles[$user['role']] ?? $user['role'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="flat-badge flat-badge-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>" 
                                          id="status-<?= $user['id'] ?>">
                                        <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                                    </span>
                                </td>
                                <td class="persian-num">
                                    <?php if ($user['last_login']): ?>
                                        <?= jdate('Y/m/d H:i', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">هرگز</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= url("users/show/{$user['id']}") ?>" 
                                           class="btn-flat-sm btn-flat-primary" title="مشاهده">
                                            👁️
                                        </a>
                                        
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" 
                                                    class="btn-flat-sm btn-flat-<?= $user['status'] === 'active' ? 'warning' : 'success' ?>" 
                                                    onclick="toggleUserStatus(<?= $user['id'] ?>)"
                                                    title="<?= $user['status'] === 'active' ? 'غیرفعال کردن' : 'فعال کردن' ?>">
                                                <?= $user['status'] === 'active' ? '⏸️' : '▶️' ?>
                                            </button>
                                            
                                            <?php if (Security::checkPermission('admin')): ?>
                                                <button type="button" 
                                                        class="btn-flat-sm btn-flat-danger" 
                                                        onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                                        title="حذف کاربر">
                                                    🗑️
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
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <h5 class="empty-title">هیچ کاربری یافت نشد</h5>
                <p class="empty-text">هنوز کاربری در سیستم ثبت نشده است</p>
                <a href="<?= url('users/create') ?>" class="btn-flat btn-flat-primary">
                    <span class="btn-icon">➕</span>
                    ایجاد کاربر جدید
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* ========================================
   Flat Design CSS
======================================== */

/* Header */
.flat-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,0.05);
}

.flat-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.flat-icon {
    font-size: 1.8rem;
    opacity: 0.8;
}

.flat-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

/* Buttons */
.btn-flat {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-flat-primary {
    background: #667eea;
    color: white;
}

.btn-flat-primary:hover {
    background: #5a6fd8;
    transform: translateY(-1px);
    color: white;
}

.btn-flat-secondary {
    background: #6c757d;
    color: white;
}

.btn-flat-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
    color: white;
}

.btn-flat-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-flat-success { background: #06d6a0; color: white; }
.btn-flat-warning { background: #f59e0b; color: white; }
.btn-flat-danger { background: #ef4444; color: white; }

.btn-flat-success:hover { background: #05b893; transform: translateY(-1px); color: white; }
.btn-flat-warning:hover { background: #d97706; transform: translateY(-1px); color: white; }
.btn-flat-danger:hover { background: #dc2626; transform: translateY(-1px); color: white; }

.btn-icon {
    font-size: 1.1em;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(0,0,0,0.08);
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #4a5568;
}

.stat-desc {
    font-size: 0.85rem;
    color: #718096;
}

.stat-icon {
    font-size: 3rem;
    opacity: 0.6;
}

.stat-card-primary .stat-number { color: #667eea; }
.stat-card-success .stat-number { color: #06d6a0; }
.stat-card-warning .stat-number { color: #f59e0b; }
.stat-card-info .stat-number { color: #3b82f6; }

/* Cards */
.flat-card {
    background: white;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.08);
    overflow: hidden;
}

.flat-card-header {
    background: #f8f9fa;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    display: flex;
    justify-content: between;
    align-items: center;
}

.flat-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}

.card-icon {
    font-size: 1.2rem;
    opacity: 0.7;
}

.flat-card-body {
    padding: 1.5rem;
}

/* Forms */
.filter-form .form-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.form-input, .form-select {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Badges */
.flat-badge {
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.flat-badge-primary { background: #ddd6fe; color: #667eea; }
.flat-badge-success { background: #d1fae5; color: #065f46; }
.flat-badge-danger { background: #fee2e2; color: #991b1b; }
.flat-badge-warning { background: #fef3c7; color: #92400e; }
.flat-badge-info { background: #dbeafe; color: #1e40af; }
.flat-badge-secondary { background: #f3f4f6; color: #4b5563; }

/* Table */
.flat-table {
    width: 100%;
    border-collapse: collapse;
}

.flat-table th {
    background: #f8f9fa;
    padding: 1rem;
    text-align: right;
    font-weight: 600;
    color: #4a5568;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-size: 0.9rem;
}

.flat-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.03);
    vertical-align: middle;
}

.flat-table tbody tr:hover {
    background: #f8f9fa;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar-small {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.user-name-small {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9rem;
}

.user-username-small {
    color: #6c757d;
    font-size: 0.8rem;
}

.flat-code {
    background: #f1f5f9;
    color: #1e293b;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-family: monospace;
    font-size: 0.8rem;
}

.flat-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.flat-link:hover {
    color: #5a6fd8;
    text-decoration: underline;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Mobile Cards */
.mobile-users {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.user-card-mobile {
    background: white;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 12px;
    padding: 1.25rem;
}

.user-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

.user-info {
    flex: 1;
}

.user-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.user-username {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

.user-details {
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.03);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.detail-value {
    color: #2d3748;
    font-size: 0.9rem;
}

.user-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.empty-text {
    color: #6c757d;
    margin-bottom: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .flat-header {
        padding: 1.5rem;
    }
    
    .flat-title {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1.25rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .stat-icon {
        font-size: 2.5rem;
    }
    
    .filter-form .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .flat-card-body {
        padding: 1rem;
    }
    
    .btn-flat {
        justify-content: center;
        width: 100%;
    }
}

@media (max-width: 576px) {
    .flat-header {
        padding: 1rem;
    }
    
    .flat-title {
        font-size: 1.3rem;
    }
    
    .flat-card-header {
        padding: 1rem;
        flex-direction: column;
        align-items: start;
        gap: 0.5rem;
    }
    
    .user-header {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .user-actions {
        justify-content: center;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: start;
        gap: 0.25rem;
    }
}
</style>

<script>
function toggleUserStatus(userId) {
    if (!confirm('آیا از تغییر وضعیت این کاربر اطمینان دارید؟')) {
        return;
    }
    
    showLoading();
    
    fetch(`<?= url('users/toggleStatus') ?>/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showAlert(data.message, 'success');
            
            // به‌روزرسانی badge وضعیت
            const statusBadge = document.getElementById(`status-${userId}`);
            if (statusBadge) {
                if (data.newStatus === 'active') {
                    statusBadge.className = 'flat-badge flat-badge-success';
                    statusBadge.textContent = 'فعال';
                } else {
                    statusBadge.className = 'flat-badge flat-badge-danger';
                    statusBadge.textContent = 'غیرفعال';
                }
            }
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('خطا:', error);
        showAlert('خطای ارتباط با سرور', 'error');
    });
}

function deleteUser(userId, username) {
    if (!confirm(`آیا از حذف کاربر "${username}" اطمینان دارید؟\nاین عمل غیرقابل برگشت است.`)) {
        return;
    }
    
    showLoading();
    
    fetch(`<?= url('users/delete') ?>/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('خطا:', error);
        showAlert('خطای ارتباط با سرور', 'error');
    });
}

function refreshUserList() {
    location.reload();
}
</script> 