<?php
/**
 * SAMANET DASHBOARD VIEW - ENTERPRISE GRADE with REAL DATA
 * نسخه: 4.1 کامل با داده‌های واقعی + PHP 8+ Compatible
 * تاریخ: 1404/10/31
 * مطابق: استانداردهای MANDATORY Dashboard Design
 */

// Helper functions برای جلوگیری از PHP 8+ deprecated warnings
function safe_htmlspecialchars($string, $flags = ENT_QUOTES, $encoding = 'UTF-8') {
    return htmlspecialchars((string)($string ?? ''), $flags, $encoding);
}

function safe_number_format($number, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',') {
    return number_format((float)($number ?? 0), $decimals, $decimal_separator, $thousands_separator);
}

// استخراج داده‌ها از متغیرهای کنترلر
$totalUsers = $stats['totalUsers'] ?? 0;
$totalRequests = $stats['totalRequests'] ?? 0;
$pendingRequests = $stats['pendingRequests'] ?? 0;
$completedRequests = $stats['completedRequests'] ?? 0;
$totalAmount = $stats['totalAmount'] ?? 0;
$todayRequests = $stats['todayRequests'] ?? 0;
$urgentCount = count($urgent_requests ?? []);
?>

<!-- MANDATORY: Stats Row - دقیقاً مطابق استانداردها -->
<div class="stats-row">
    <div class="stat-card-pro">
        <div class="stat-label">کل مبلغ پرداختی</div>
        <div class="stat-value"><?= formatAmount($totalAmount) ?><span style="color: var(--gray-500); font-size: 0.8em;">ریال</span></div>
        <div class="stat-change positive">
            <i class="fas fa-chart-line"></i>
            <span>از ابتدای سال</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">کل درخواست‌ها</div>
        <div class="stat-value"><?= safe_number_format($totalRequests) ?></div>
        <div class="stat-change neutral">
            <i class="fas fa-file-alt"></i>
            <span>در سیستم</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">تکمیل شده</div>
        <div class="stat-value"><?= safe_number_format($completedRequests) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-check-circle"></i>
            <span>موفق</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در انتظار بررسی</div>
        <div class="stat-value"><?= safe_number_format($pendingRequests) ?></div>
        <div class="stat-change <?= $pendingRequests > 0 ? 'negative' : 'positive' ?>">
            <i class="fas fa-<?= $pendingRequests > 0 ? 'clock' : 'check' ?>"></i>
            <span><?= $pendingRequests > 0 ? 'نیاز به بررسی' : 'همه بررسی شده' ?></span>
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
                    <a href="/?route=requests" class="btn-icon" title="همه درخواست‌ها">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
            </div>

            <?php if (!empty($recent_requests)): ?>
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
                    <?php foreach ($recent_requests as $request): ?>
                    <tr>
                        <td>
                            <code style="background: var(--gray-100); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                <?= safe_htmlspecialchars($request['reference_number'] ?? $request['id']) ?>
                            </code>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= safe_htmlspecialchars($request['title']) ?></div>
                            <small style="color: var(--gray-500);">توسط <?= safe_htmlspecialchars($request['created_by_name'] ?? 'نامشخص') ?></small>
                        </td>
                        <td style="text-align: left; font-family: var(--font-mono);">
                            <?= isset($request['amount']) && $request['amount'] > 0 ? safe_number_format($request['amount']) . ' ریال' : '-' ?>
                        </td>
                        <td>
                            <span class="<?= getBadgeClass($request['status']) ?>" style="font-weight: 500;">
                                <i class="fas fa-circle" style="font-size: 8px; margin-left: 4px;"></i>
                                <?= getStatusLabel($request['status']) ?>
                            </span>
                        </td>
                        <td style="color: var(--gray-600); font-size: 13px;">
                            <?= isset($request['created_at']) ? jdate('j M Y', strtotime($request['created_at'])) : '-' ?>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- لیست موبایل -->
            <div class="mobile-list">
                <?php foreach ($recent_requests as $request): ?>
                <div class="mobile-list-item">
                    <div class="mobile-item-main">
                        <div class="mobile-item-title"><?= safe_htmlspecialchars($request['title']) ?></div>
                        <div class="mobile-item-meta">
                            <code style="background: var(--gray-100); padding: 2px 4px; border-radius: 2px; font-size: 10px;">
                                <?= safe_htmlspecialchars($request['reference_number'] ?? $request['id']) ?>
                            </code>
                            •
                            <span class="<?= getBadgeClass($request['status']) ?>" style="font-weight: 500; font-size: 11px;">
                                <?= getStatusLabel($request['status']) ?>
                            </span>
                            •
                            <span style="color: var(--gray-500); font-size: 11px;">
                                <?= isset($request['created_at']) ? jdate('j M', strtotime($request['created_at'])) : '-' ?>
                            </span>
                        </div>
                        <?php if (isset($request['amount']) && $request['amount'] > 0): ?>
                        <div style="font-weight: 600; color: var(--primary); font-size: 12px; margin-top: 4px;">
                            <?= safe_number_format($request['amount']) ?> ریال
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="mobile-item-actions">
                        <a href="/?route=requests&action=show&id=<?= $request['id'] ?>" class="btn-icon" title="مشاهده">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- پیام خالی -->
            <div style="text-align: center; padding: var(--space-8); color: var(--gray-500);">
                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: var(--space-4); opacity: 0.5;"></i>
                <h5>هنوز درخواستی ثبت نشده است</h5>
                <p>اولین درخواست خود را ایجاد کنید</p>
                <a href="/?route=requests&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    ایجاد درخواست جدید
                </a>
            </div>
            <?php endif; ?>
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
                <span class="panel-badge" style="background: <?= $urgentCount > 0 ? 'var(--danger)' : 'var(--success)' ?>; color: white;">
                    <?= safe_number_format($urgentCount) ?>
                </span>
            </div>
            <div class="panel-body">
                <?php if (empty($urgent_requests)): ?>
                <div style="text-align: center; padding: var(--space-6) var(--space-4); color: var(--gray-500);">
                    <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--success); margin-bottom: var(--space-2);"></i>
                    <p style="margin: 0; font-size: 13px;">هیچ درخواست فوری‌ای در انتظار نیست</p>
                </div>
                <?php else: ?>
                <?php foreach ($urgent_requests as $urgentRequest): ?>
                <div class="task-item" onclick="location.href='/?route=requests&action=show&id=<?= $urgentRequest['id'] ?>'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <span class="task-text" style="flex: 1;"><?= safe_htmlspecialchars($urgentRequest['title']) ?></span>
                        <span style="background: var(--danger); color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-right: var(--space-2);">فوری</span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- تگ‌های محبوب -->
        <?php if (!empty($popular_tags)): ?>
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-tags" style="color: var(--info);"></i>
                    تگ‌های محبوب
                </div>
                <a href="/?route=tags" class="panel-badge" style="text-decoration: none;">مشاهده همه</a>
            </div>
            <div class="panel-body">
                <div style="display: flex; flex-wrap: wrap; gap: var(--space-2);">
                    <?php foreach ($popular_tags as $tag): ?>
                    <span class="tag-badge" style="
                        background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>);
                        color: <?= $tag['text_color'] ?>;
                        padding: 4px 8px;
                        border-radius: 12px;
                        font-size: 11px;
                        font-weight: 500;
                        display: inline-flex;
                        align-items: center;
                        gap: 4px;
                    ">
                        <?= safe_htmlspecialchars($tag['title']) ?>
                        <small style="opacity: 0.8;"><?= safe_number_format($tag['usage_count']) ?></small>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- عملیات سریع -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-bolt" style="color: var(--warning);"></i>
                    عملیات سریع
                </div>
            </div>
            <div class="panel-body">
                <div class="task-item" onclick="location.href='/?route=requests&action=create'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-plus" style="color: var(--primary); margin-left: var(--space-2);"></i>
                        ایجاد درخواست جدید
                    </span>
                </div>
                
                <div class="task-item" onclick="location.href='/?route=requests'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-list" style="color: var(--info); margin-left: var(--space-2);"></i>
                        مدیریت درخواست‌ها
                    </span>
                </div>
                
                <div class="task-item" onclick="location.href='/?route=users'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-users" style="color: var(--success); margin-left: var(--space-2);"></i>
                        مدیریت کاربران
                    </span>
                </div>
                
                <div class="task-item" onclick="location.href='/?route=tags'" style="cursor: pointer;">
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
                    <span style="font-weight: 600; color: var(--primary);"><?= safe_number_format($todayRequests) ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">تکمیل شده</span>
                    <span style="font-weight: 600; color: var(--success);"><?= safe_number_format($stats['todayCompleted'] ?? 0) ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0;">
                    <span style="font-size: 12px; color: var(--gray-600);">در انتظار</span>
                    <span style="font-weight: 600; color: var(--warning);"><?= safe_number_format($pendingRequests) ?></span>
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
    
    // AJAX call to refresh data
    fetch('/?route=dashboard&action=getRecentRequestsData', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the table content
            location.reload(); // Simple reload for now
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        icon.classList.remove('fa-spin');
    });
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
    
    console.log('✅ Dashboard initialized with real data');
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