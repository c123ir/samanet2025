<?php
/**
 * نام فایل: view.php
 * مسیر فایل: /app/views/users/view.php
 * توضیح: صفحه نمایش جزئیات کاربر
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
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-2">
                <i class="fas fa-user me-3" style="color: var(--primary-color);"></i>
                جزئیات کاربر
            </h1>
            <p class="text-muted mb-0">
                اطلاعات کامل کاربر <?= htmlspecialchars($user['full_name']) ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('users') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                بازگشت به لیست
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="printUserInfo()">
                <i class="fas fa-print me-2"></i>
                چاپ
            </button>
        </div>
    </div>
</div>

<!-- User Info Cards -->
<div class="row">
    <!-- اطلاعات اصلی -->
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-user-circle me-2"></i>
                    اطلاعات شخصی
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="user-avatar mx-auto">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                    <h5 class="mt-3 mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
                    <span class="badge bg-<?= getRoleColor($user['role']) ?> fs-6">
                        <?= $roles[$user['role']] ?? $user['role'] ?>
                    </span>
                </div>

                <div class="user-info-list">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user text-primary me-2"></i>
                            نام کاربری
                        </div>
                        <div class="info-value">
                            <code><?= htmlspecialchars($user['username']) ?></code>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope text-info me-2"></i>
                            آدرس ایمیل
                        </div>
                        <div class="info-value">
                            <?php if (!empty($user['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($user['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">ثبت نشده</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-phone text-success me-2"></i>
                            شماره موبایل
                        </div>
                        <div class="info-value">
                            <?php if (!empty($user['phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($user['phone']) ?>" class="text-decoration-none persian-num">
                                    <?= fa_num($user['phone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">ثبت نشده</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-toggle-on text-warning me-2"></i>
                            وضعیت
                        </div>
                        <div class="info-value">
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?> fs-6">
                                <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- آمار و فعالیت -->
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    آمار و فعالیت
                </h6>
            </div>
            <div class="card-body">
                <div class="activity-stats">
                    <div class="stat-item">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">تاریخ عضویت</div>
                            <div class="stat-value persian-num">
                                <?= jdate('Y/m/d', strtotime($user['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">آخرین ورود</div>
                            <div class="stat-value persian-num">
                                <?php if ($user['last_login']): ?>
                                    <?= jdate('Y/m/d H:i', strtotime($user['last_login'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">هرگز</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">IP آخرین ورود</div>
                            <div class="stat-value">
                                <?php if (!empty($user['last_login_ip'])): ?>
                                    <code><?= htmlspecialchars($user['last_login_ip']) ?></code>
                                <?php else: ?>
                                    <span class="text-muted">ثبت نشده</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">تعداد درخواست‌ها</div>
                            <div class="stat-value persian-num">
                                <?= fa_num(0) ?> <!-- TODO: محاسبه تعداد درخواست‌ها -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- عملیات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    عملیات
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <!-- تغییر وضعیت -->
                        <button type="button" 
                                class="btn btn-<?= $user['status'] === 'active' ? 'warning' : 'success' ?>"
                                onclick="toggleUserStatus(<?= $user['id'] ?>)">
                            <i class="fas fa-<?= $user['status'] === 'active' ? 'user-slash' : 'user-check' ?> me-2"></i>
                            <?= $user['status'] === 'active' ? 'غیرفعال کردن' : 'فعال کردن' ?>
                        </button>

                        <!-- بازنشانی رمز عبور -->
                        <button type="button" 
                                class="btn btn-info"
                                onclick="resetUserPassword(<?= $user['id'] ?>)">
                            <i class="fas fa-key me-2"></i>
                            بازنشانی رمز عبور
                        </button>

                        <!-- ارسال ایمیل -->
                        <?php if (!empty($user['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>
                                ارسال ایمیل
                            </a>
                        <?php endif; ?>

                        <!-- تماس تلفنی -->
                        <?php if (!empty($user['phone'])): ?>
                            <a href="tel:<?= htmlspecialchars($user['phone']) ?>" 
                               class="btn btn-outline-success">
                                <i class="fas fa-phone me-2"></i>
                                تماس تلفنی
                            </a>
                        <?php endif; ?>

                        <!-- حذف کاربر -->
                        <?php if (Security::checkPermission('admin')): ?>
                            <button type="button" 
                                    class="btn btn-danger"
                                    onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')">
                                <i class="fas fa-trash me-2"></i>
                                حذف کاربر
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            این حساب متعلق به خود شما است و امکان تغییر وضعیت آن وجود ندارد.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.user-info-list {
    space-y: 1rem;
}

.info-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--text-color);
    min-width: 140px;
}

.info-value {
    flex: 1;
    text-align: left;
}

.activity-stats {
    display: grid;
    gap: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-color);
    opacity: 0.8;
}

.stat-value {
    font-weight: 600;
    color: var(--text-color);
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .info-label {
        min-width: auto;
    }
    
    .info-value {
        text-align: right;
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

function resetUserPassword(userId) {
    if (!confirm('آیا از بازنشانی رمز عبور این کاربر اطمینان دارید؟\nرمز عبور جدید به ایمیل کاربر ارسال خواهد شد.')) {
        return;
    }
    
    showLoading();
    
    fetch(`<?= url('users/resetPassword') ?>/${userId}`, {
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
            showAlert(data.message + '<br>رمز موقت: <code>' + data.tempPassword + '</code>', 'success');
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
                window.location.href = '<?= url('users') ?>';
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

function printUserInfo() {
    window.print();
}
</script> 