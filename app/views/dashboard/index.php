<?php
/**
 * SAMANET DASHBOARD VIEW - ENTERPRISE GRADE
 * نسخه: 3.0 حرفه‌ای
 * تاریخ: 1404/10/17
 * مطابق: استانداردهای MANDATORY Dashboard Design
 */

// داده‌های نمونه - در نسخه واقعی از دیتابیس دریافت می‌شود
$total_amount = 8589700000; // 8.5 میلیارد
$completed_requests = 5;
$today_requests = 3;
$pending_requests = 2;
$urgent_requests = 0;

// فرمت مبلغ
function formatAmount($amount) {
    if ($amount >= 1000000000) {
        return number_format($amount / 1000000000, 1) . 'B';
    } elseif ($amount >= 1000000) {
        return number_format($amount / 1000000, 1) . 'M';
    } else {
        return number_format($amount);
    }
}

// آخرین درخواست‌ها - نمونه داده
$latest_requests = [
    [
        'id' => 'REQ2506238564',
        'title' => 'تست جدید',
        'amount' => 0,
        'status' => 'pending',
        'status_label' => 'در انتظار',
        'status_class' => 'warning',
        'created_at' => '1404/03/30',
        'created_by' => 'کاربر تست'
    ],
    [
        'id' => 'REQ2506232896',
        'title' => 'تست تاریخ',
        'amount' => 0,
        'status' => 'pending',
        'status_label' => 'در انتظار',
        'status_class' => 'warning',
        'created_at' => '1404/03/30',
        'created_by' => 'کاربر تست'
    ],
    [
        'id' => 'REQ2506232939',
        'title' => 'درخواست تکمیل شده',
        'amount' => 23231000,
        'status' => 'completed',
        'status_label' => 'تکمیل شده',
        'status_class' => 'success',
        'created_at' => '1404/03/30',
        'created_by' => 'مدیر سیستم'
    ],
    [
        'id' => 'REQ2506220943',
        'title' => 'پرداخت اول',
        'amount' => 23231000,
        'status' => 'completed',
        'status_label' => 'تکمیل شده',
        'status_class' => 'success',
        'created_at' => '1404/03/30',
        'created_by' => 'مدیر مالی'
    ],
    [
        'id' => 'REQ2506212400',
        'title' => 'پرداخت دوم',
        'amount' => 12400000,
        'status' => 'completed',
        'status_label' => 'تکمیل شده',
        'status_class' => 'success',
        'created_at' => '1404/03/30',
        'created_by' => 'مدیر مالی'
    ]
];

// تولید کلاس badge
function getBadgeClass($status) {
    switch ($status) {
        case 'completed':
            return 'text-success';
        case 'pending':
            return 'text-warning';
        case 'processing':
            return 'text-info';
        case 'rejected':
            return 'text-danger';
        default:
            return 'text-muted';
    }
}
?>

<!-- MANDATORY: Stats Row - دقیقاً مطابق استانداردها -->
<div class="stats-row">
    <div class="stat-card-pro">
        <div class="stat-label">کل مبلغ پرداختی</div>
        <div class="stat-value"><?= formatAmount($total_amount) ?><span style="color: var(--gray-500); font-size: 0.8em;">ریال</span></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>12% از ماه قبل</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">تکمیل شده</div>
        <div class="stat-value"><?= number_format($completed_requests) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-check-circle"></i>
            <span>درخواست تایید شده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">درخواست‌های امروز</div>
        <div class="stat-value"><?= number_format($today_requests) ?></div>
        <div class="stat-change neutral">
            <i class="fas fa-calendar-day"></i>
            <span>درخواست جدید</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در انتظار بررسی</div>
        <div class="stat-value"><?= number_format($pending_requests) ?></div>
        <div class="stat-change <?= $pending_requests > 0 ? 'negative' : 'positive' ?>">
            <i class="fas fa-<?= $pending_requests > 0 ? 'clock' : 'check' ?>"></i>
            <span><?= $pending_requests > 0 ? 'نیاز به بررسی' : 'همه بررسی شده' ?></span>
        </div>
    </div>
</div>

<!-- MANDATORY: Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Main Column -->
    <div class="main-column">
        <!-- آخرین درخواست‌ها -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    آخرین درخواست‌ها
                </h2>
                <div class="table-actions">
                    <button class="btn-icon" onclick="refreshRequests()" title="بروزرسانی">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="btn-icon" onclick="filterRequests()" title="فیلتر">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>

            <!-- جدول دسکتاپ -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>شماره</th>
                        <th>عنوان</th>
                        <th>مبلغ</th>
                        <th>وضعیت</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest_requests as $request): ?>
                    <tr>
                        <td>
                            <code style="background: var(--gray-100); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                <?= htmlspecialchars($request['id']) ?>
                            </code>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= htmlspecialchars($request['title']) ?></div>
                            <small style="color: var(--gray-500);">توسط <?= htmlspecialchars($request['created_by']) ?></small>
                        </td>
                        <td style="text-align: left; font-family: var(--font-mono);">
                            <?= $request['amount'] > 0 ? number_format($request['amount']) . ' ریال' : '-' ?>
                        </td>
                        <td>
                            <span class="<?= getBadgeClass($request['status']) ?>" style="font-weight: 500;">
                                <i class="fas fa-circle" style="font-size: 8px; margin-left: 4px;"></i>
                                <?= htmlspecialchars($request['status_label']) ?>
                            </span>
                        </td>
                        <td style="color: var(--gray-600); font-size: 13px;">
                            <?= htmlspecialchars($request['created_at']) ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <button class="btn-icon" title="مشاهده" onclick="viewRequest('<?= $request['id'] ?>')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($request['status'] === 'pending'): ?>
                                <button class="btn-icon" title="ویرایش" onclick="editRequest('<?= $request['id'] ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn-icon" title="دانلود" onclick="downloadRequest('<?= $request['id'] ?>')">
                                    <i class="fas fa-download"></i>
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
                <?php foreach ($latest_requests as $request): ?>
                <div class="mobile-list-item">
                    <div class="mobile-item-main">
                        <div class="mobile-item-title"><?= htmlspecialchars($request['title']) ?></div>
                        <div class="mobile-item-meta">
                            <code style="background: var(--gray-100); padding: 2px 4px; border-radius: 2px; font-size: 10px;">
                                <?= htmlspecialchars($request['id']) ?>
                            </code>
                            •
                            <span class="<?= getBadgeClass($request['status']) ?>" style="font-weight: 500; font-size: 11px;">
                                <?= htmlspecialchars($request['status_label']) ?>
                            </span>
                            •
                            <span style="color: var(--gray-500); font-size: 11px;">
                                <?= htmlspecialchars($request['created_at']) ?>
                            </span>
                        </div>
                        <?php if ($request['amount'] > 0): ?>
                        <div style="font-weight: 600; color: var(--primary); font-size: 12px; margin-top: 4px;">
                            <?= number_format($request['amount']) ?> ریال
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="mobile-item-actions">
                        <button class="btn-icon" title="مشاهده" onclick="viewRequest('<?= $request['id'] ?>')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Side Column -->
    <div class="side-column">
        <!-- درخواست‌های فوری -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i>
                    درخواست‌های فوری
                </div>
                <span class="panel-badge" style="background: <?= $urgent_requests > 0 ? 'var(--danger)' : 'var(--success)' ?>; color: white;">
                    <?= $urgent_requests ?>
                </span>
            </div>
            <div class="panel-body">
                <?php if ($urgent_requests === 0): ?>
                <div style="text-align: center; padding: var(--space-8) var(--space-4); color: var(--gray-500);">
                    <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--success); margin-bottom: var(--space-2);"></i>
                    <p style="margin: 0; font-size: 13px;">هیچ درخواست فوری‌ای در انتظار نیست</p>
                </div>
                <?php else: ?>
                <!-- لیست درخواست‌های فوری -->
                <div class="task-item">
                    <span class="task-text">درخواست فوری نمونه</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- عملیات سریع -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-bolt" style="color: var(--warning);"></i>
                    عملیات سریع
                </div>
            </div>
            <div class="panel-body">
                <div class="task-item" onclick="location.href='/requests/create'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-plus" style="color: var(--primary); margin-left: var(--space-2);"></i>
                        ایجاد درخواست جدید
                    </span>
                </div>
                
                <div class="task-item" onclick="location.href='/requests'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-list" style="color: var(--info); margin-left: var(--space-2);"></i>
                        مدیریت درخواست‌ها
                    </span>
                </div>
                
                <div class="task-item" onclick="location.href='/users'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-users" style="color: var(--success); margin-left: var(--space-2);"></i>
                        مدیریت کاربران
                    </span>
                </div>
                
                <div class="task-item" onclick="location.href='/tags'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-tags" style="color: var(--warning); margin-left: var(--space-2);"></i>
                        مدیریت برچسب‌ها
                    </span>
                </div>
            </div>
        </div>

        <!-- آمار امروز -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-chart-line" style="color: var(--info);"></i>
                    آمار امروز
                </div>
            </div>
            <div class="panel-body">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">درخواست‌های جدید</span>
                    <span style="font-weight: 600; color: var(--primary);"><?= $today_requests ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">تکمیل شده</span>
                    <span style="font-weight: 600; color: var(--success);"><?= $completed_requests ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0;">
                    <span style="font-size: 12px; color: var(--gray-600);">در انتظار</span>
                    <span style="font-weight: 600; color: var(--warning);"><?= $pending_requests ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Functions -->
<script>
// Global Dashboard Functions
function refreshRequests() {
    const icon = document.querySelector('[onclick="refreshRequests()"] i');
    icon.classList.add('fa-spin');
    
    setTimeout(() => {
        icon.classList.remove('fa-spin');
        showAlert('درخواست‌ها بروزرسانی شد', 'success');
    }, 1000);
}

function filterRequests() {
    showAlert('فیلترهای پیشرفته به‌زودی اضافه می‌شود', 'info');
}

function viewRequest(id) {
    window.location.href = `/requests/view/${id}`;
}

function editRequest(id) {
    window.location.href = `/requests/edit/${id}`;
}

function downloadRequest(id) {
    showAlert(`دانلود درخواست ${id} شروع شد`, 'info');
}

function showAlert(message, type = 'info') {
    // ایجاد alert موقت
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1000;
        max-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

// Progressive Enhancement
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
    
    console.log('✅ Dashboard initialized successfully');
});

// CSS Animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .alert {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-3) var(--space-4);
        border-radius: var(--radius-lg);
        font-size: 14px;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
    }
    
    .alert-success {
        background: var(--success);
        color: white;
    }
    
    .alert-info {
        background: var(--info);
        color: white;
    }
    
    .alert-error {
        background: var(--danger);
        color: white;
    }
`;
document.head.appendChild(style);
</script> 