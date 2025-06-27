<?php
/**
 * داشبورد اصلی سامانت - طراحی حرفه‌ای
 * نسخه: 3.0
 */

require_once(APP_PATH . 'views/layouts/main.php');
?>

<!-- Page-specific CSS -->
<link href="/assets/css/dashboard.css" rel="stylesheet">

<!-- Main Dashboard Content -->
<div class="dashboard-pro">
    <!-- محتوای اصلی داشبورد -->
    <div class="dashboard-content">
        <!-- ردیف آمارهای اصلی -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل مبلغ پرداختی</div>
                <div class="stat-value">8,589.7<span class="text-muted">M</span></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>12% از ماه قبل</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">تکمیل شده</div>
                <div class="stat-value">0</div>
                <div class="stat-change positive">
                    <i class="fas fa-check"></i>
                    <span>آماده برای بررسی</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">درخواست‌های امروز</div>
                <div class="stat-value">0</div>
                <div class="stat-change neutral">
                    <i class="fas fa-calendar"></i>
                    <span>بدون درخواست جدید</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">در انتظار</div>
                <div class="stat-value">0</div>
                <div class="stat-change warning">
                    <i class="fas fa-clock"></i>
                    <span>نیاز به بررسی</span>
                </div>
            </div>
        </div>

        <!-- Grid اصلی صفحه -->
        <div class="dashboard-grid">
            <!-- ستون اصلی -->
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
                                <th>شماره درخواست</th>
                                <th>عنوان</th>
                                <th>مبلغ</th>
                                <th>وضعیت</th>
                                <th>تاریخ ایجاد</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="font-mono">REQ2506238564</span></td>
                                <td>تست جدید</td>
                                <td class="text-right font-mono">0 ریال</td>
                                <td><span class="status-badge-pro pending">در انتظار</span></td>
                                <td>1404/03/30</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-icon" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="font-mono">REQ2506232896</span></td>
                                <td>تست تاریخ</td>
                                <td class="text-right font-mono">0 ریال</td>
                                <td><span class="status-badge-pro pending">در انتظار</span></td>
                                <td>1404/03/30</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-icon" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="font-mono">REQ2506232939</span></td>
                                <td>سیو</td>
                                <td class="text-right font-mono">23,231,000 ریال</td>
                                <td><span class="status-badge-pro completed">تکمیل شده</span></td>
                                <td>1404/03/30</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-icon" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon" title="دانلود">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="font-mono">REQ2506220943</span></td>
                                <td>سیو</td>
                                <td class="text-right font-mono">23,231,000 ریال</td>
                                <td><span class="status-badge-pro completed">تکمیل شده</span></td>
                                <td>1404/03/30</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-icon" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon" title="دانلود">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="font-mono">REQ2506212400</span></td>
                                <td>سیو</td>
                                <td class="text-right font-mono">12,400,000 ریال</td>
                                <td><span class="status-badge-pro completed">تکمیل شده</span></td>
                                <td>1404/03/30</td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-icon" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon" title="دانلود">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- لیست موبایل -->
                    <div class="mobile-list">
                        <div class="mobile-list-item">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title">تست جدید</div>
                                <div class="mobile-item-meta">
                                    <span class="font-mono">REQ2506238564</span> • 
                                    <span class="status-badge-pro pending">در انتظار</span>
                                </div>
                                <div class="mobile-item-amount">0 ریال</div>
                            </div>
                            <div class="mobile-item-actions">
                                <button class="btn-icon" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mobile-list-item">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title">تست تاریخ</div>
                                <div class="mobile-item-meta">
                                    <span class="font-mono">REQ2506232896</span> • 
                                    <span class="status-badge-pro pending">در انتظار</span>
                                </div>
                                <div class="mobile-item-amount">0 ریال</div>
                            </div>
                            <div class="mobile-item-actions">
                                <button class="btn-icon" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mobile-list-item">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title">سیو</div>
                                <div class="mobile-item-meta">
                                    <span class="font-mono">REQ2506232939</span> • 
                                    <span class="status-badge-pro completed">تکمیل شده</span>
                                </div>
                                <div class="mobile-item-amount">23,231,000 ریال</div>
                            </div>
                            <div class="mobile-item-actions">
                                <button class="btn-icon" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ستون جانبی -->
            <div class="side-column">
                <!-- پنل درخواست‌های فوری -->
                <div class="panel urgent-panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            درخواست‌های فوری
                        </div>
                        <span class="panel-badge">0</span>
                    </div>
                    <div class="panel-body">
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>هیچ درخواست فوری‌ای در انتظار نیست</p>
                        </div>
                    </div>
                </div>

                <!-- پنل عملیات سریع -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-bolt"></i>
                            عملیات سریع
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="quick-action" onclick="location.href='<?= url('requests/create') ?>'">
                            <div class="quick-action-icon bg-primary">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">درخواست جدید</div>
                                <div class="quick-action-desc">ایجاد درخواست حواله</div>
                            </div>
                        </div>
                        
                        <div class="quick-action" onclick="location.href='<?= url('requests') ?>'">
                            <div class="quick-action-icon bg-info">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">مدیریت درخواست‌ها</div>
                                <div class="quick-action-desc">مشاهده همه درخواست‌ها</div>
                            </div>
                        </div>
                        
                        <div class="quick-action" onclick="location.href='<?= url('reports') ?>'">
                            <div class="quick-action-icon bg-success">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">گزارشات</div>
                                <div class="quick-action-desc">مشاهده آمار و گزارش‌ها</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS اضافی برای empty state و quick actions -->
<style>
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--gray-500);
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--success);
}

.empty-state p {
    margin: 0;
    font-size: 13px;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
    margin-bottom: 0.5rem;
}

.quick-action:hover {
    background: var(--gray-50);
}

.quick-action:last-child {
    margin-bottom: 0;
}

.quick-action-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}

.quick-action-icon.bg-primary { background: var(--primary); }
.quick-action-icon.bg-info { background: var(--info); }
.quick-action-icon.bg-success { background: var(--success); }

.quick-action-content {
    flex: 1;
    min-width: 0;
}

.quick-action-title {
    font-weight: 600;
    font-size: 13px;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.quick-action-desc {
    font-size: 11px;
    color: var(--gray-500);
}

.stat-change.neutral {
    color: var(--gray-500);
}

.stat-change.warning {
    color: var(--warning);
}

.mobile-item-actions {
    display: flex;
    gap: 0.25rem;
}

.mobile-item-amount {
    font-size: 12px;
    font-weight: 600;
    color: var(--primary);
    margin-top: 4px;
}

[data-theme="dark"] .empty-state {
    color: var(--gray-600);
}

[data-theme="dark"] .quick-action:hover {
    background: var(--gray-200);
}

[data-theme="dark"] .quick-action-title {
    color: var(--gray-900);
}

[data-theme="dark"] .quick-action-desc {
    color: var(--gray-600);
}
</style>

<script>
// توابع عملیاتی
function refreshRequests() {
    // نمایش loading
    const icon = document.querySelector('[onclick="refreshRequests()"] i');
    icon.classList.add('fa-spin');
    
    // شبیه‌سازی بروزرسانی
    setTimeout(() => {
        icon.classList.remove('fa-spin');
        showToast('درخواست‌ها بروزرسانی شد', 'success');
    }, 1000);
}

function filterRequests() {
    showToast('فیلترهای پیشرفته به‌زودی اضافه می‌شود', 'info');
}

// نمایش toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        left: 20px;
        background: var(--${type === 'success' ? 'success' : 'info'});
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 1100;
        animation: slideInLeft 0.3s ease;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutLeft 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// انیمیشن‌های CSS
const style = document.createElement('style');
style.textContent = `
@keyframes slideInLeft {
    from { transform: translateX(-100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutLeft {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(-100%); opacity: 0; }
}
`;
document.head.appendChild(style);
</script>

<?php
require_once(APP_PATH . 'views/layouts/footer.php');
?> 