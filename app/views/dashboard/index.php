<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /app/views/dashboard/index.php
 * توضیح: صفحه داشبورد اصلی سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */
?>

<!-- Page Header -->
<div class="page-header dashboard-header fade-in">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt me-3"></i>
                داشبورد سامانت
            </h1>
            <p class="dashboard-subtitle mb-0">
                خوش آمدید <?= htmlspecialchars($user['full_name']) ?> 
                <span class="badge bg-primary ms-2"><?= $user_role_label ?></span>
            </p>
        </div>
        <div class="quick-actions">
            <button type="button" class="quick-action-btn" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i>
                به‌روزرسانی
            </button>
            <button type="button" class="quick-action-btn" onclick="toggleDashboardMode()">
                <i class="fas fa-expand-alt"></i>
                تغییر نمایش
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards Row -->
<div class="dashboard-stats slide-up">
    <!-- Total Requests Card -->
    <div class="stat-card stats-card">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-2">کل درخواست‌ها</h6>
                    <h2 class="mb-0 persian-num"><?= fa_num($stats['requests']['total']) ?></h2>
                    <small class="opacity-75">
                        <i class="fas fa-arrow-up me-1"></i>
                        امروز: <?= fa_num($stats['today']['total'] ?? 0) ?>
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-file-invoice-dollar fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card -->
    <div class="stat-card stats-card">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-2">در انتظار</h6>
                    <h2 class="mb-0 persian-num"><?= fa_num($stats['requests']['pending']) ?></h2>
                    <small class="opacity-75">
                        <?= fa_num($stats['requests']['pending_percentage']) ?>% از کل
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
            <?php if ($stats['requests']['pending'] > 0): ?>
                <div class="mt-2">
                    <a href="<?= url('requests?status=pending') ?>" class="btn btn-light btn-sm flat-btn">
                        <i class="fas fa-eye me-1"></i>
                        مشاهده
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Completed Requests Card -->
    <div class="stat-card stats-card">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #06D6A0 0%, #4ecdc4 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-2">تکمیل شده</h6>
                    <h2 class="mb-0 persian-num"><?= fa_num($stats['requests']['completed']) ?></h2>
                    <small class="opacity-75">
                        <?= fa_num($stats['requests']['completed_percentage']) ?>% از کل
                    </small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Amount Card -->
    <div class="stat-card stats-card">
        <div class="card-body text-white" style="background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-2">کل مبلغ</h6>
                                                <h4 class="mb-0 persian-num"><?= isset($stats['requests']['total_amount']) && $stats['requests']['total_amount'] !== null ? fa_num(number_format($stats['requests']['total_amount'])) : '۰' ?></h4>
                    <small class="opacity-75">ریال</small>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-coins fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid scale-in">
    <!-- Main Content -->
    <div class="dashboard-main">
        <!-- Charts Section -->
        <div class="chart-container">
            <div class="chart-header">
                <h5 class="chart-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    نمودار عملکرد هفتگی
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active flat-btn" data-chart="week">هفته</button>
                    <button type="button" class="btn btn-outline-primary flat-btn" data-chart="month">ماه</button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="performanceChart" height="100"></canvas>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="flat-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    آخرین درخواست‌ها
                </h5>
                <a href="<?= url('requests') ?>" class="btn btn-outline-primary btn-sm flat-btn">
                    <i class="fas fa-list me-1"></i>
                    مشاهده همه
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recent_requests)): ?>
                    <div class="table-responsive">
                        <table class="table flat-table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>شماره مرجع</th>
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
                                            <code class="persian-num"><?= fa_num($request['reference_number']) ?></code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($request['is_urgent']): ?>
                                                    <i class="fas fa-exclamation-triangle text-warning me-2" title="فوری"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars(mb_substr($request['title'], 0, 30)) ?>
                                                <?php if (mb_strlen($request['title']) > 30): ?>...<?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="persian-num"><?= $request['amount'] !== null ? fa_num(number_format($request['amount'])) : 'مشخص نشده' ?></td>
                                        <td>
                                            <span class="badge bg-<?= getStatusColor($request['status']) ?>">
                                                <?= $request['status_label'] ?>
                                            </span>
                                        </td>
                                        <td class="persian-num"><?= $request['created_at_jalali'] ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= url("requests/view/{$request['id']}") ?>" 
                                                   class="btn btn-outline-primary btn-sm flat-btn">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($request['status'] === 'pending'): ?>
                                                    <button type="button" class="btn btn-outline-success btn-sm flat-btn" 
                                                            onclick="quickApprove(<?= $request['id'] ?>)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">هیچ درخواستی یافت نشد</p>
                        <a href="<?= url('requests/create') ?>" class="btn btn-primary flat-btn">
                            <i class="fas fa-plus me-2"></i>
                            ایجاد درخواست جدید
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Content -->
    <div class="dashboard-sidebar">
        <!-- Urgent Requests Alert -->
        <?php if (!empty($urgent_requests)): ?>
            <div class="flat-card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        درخواست‌های فوری
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach (array_slice($urgent_requests, 0, 3) as $urgent): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong><?= htmlspecialchars(mb_substr($urgent['title'], 0, 25)) ?></strong>
                                <small class="d-block text-muted persian-num">
                                    <?= $urgent['amount'] !== null ? fa_num(number_format($urgent['amount'])) : 'مشخص نشده' ?> ریال
                                </small>
                            </div>
                            <a href="<?= url("requests/view/{$urgent['id']}") ?>" class="btn btn-warning btn-sm flat-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($urgent_requests) > 3): ?>
                        <div class="text-center mt-3">
                            <a href="<?= url('requests?urgent=1') ?>" class="btn btn-outline-warning btn-sm flat-btn">
                                مشاهده همه (<?= fa_num(count($urgent_requests)) ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Notifications -->
        <div class="flat-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-bell me-2"></i>
                    اعلان‌های مهم
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="flat-alert alert-<?= $notification['type'] ?> alert-dismissible fade show py-2" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="<?= $notification['icon'] ?> me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <strong><?= $notification['title'] ?></strong>
                                    <div><?= $notification['message'] ?></div>
                                    <?php if (isset($notification['action_url'])): ?>
                                        <a href="<?= $notification['action_url'] ?>" class="btn btn-<?= $notification['type'] ?> btn-sm mt-2 flat-btn">
                                            مشاهده
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>همه چیز آرام است!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- User Tasks -->
        <div class="flat-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    وظایف شما
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($user_tasks)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($user_tasks, 0, 5) as $task): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-2">
                                        <i class="<?= $task['icon'] ?> me-2 text-<?= getPriorityColor($task['priority']) ?>"></i>
                                        <strong><?= $task['title'] ?></strong>
                                        <small class="d-block text-muted">
                                            <?= htmlspecialchars($task['description']) ?>
                                        </small>
                                        <?php if (isset($task['reference'])): ?>
                                            <code class="persian-num"><?= fa_num($task['reference']) ?></code>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= $task['action_url'] ?>" class="btn btn-outline-primary btn-sm flat-btn">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($user_tasks) > 5): ?>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                و <?= fa_num(count($user_tasks) - 5) ?> وظیفه دیگر...
                            </small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-check-double fa-2x mb-2"></i>
                        <p>همه وظایف انجام شده!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Floating Button -->
<div class="floating-actions">
    <button type="button" class="btn btn-primary btn-lg rounded-circle position-fixed" 
            style="bottom: 20px; left: 20px; z-index: 1000; width: 60px; height: 60px;"
            data-bs-toggle="dropdown">
        <i class="fas fa-plus"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="<?= url('requests/create') ?>">
                <i class="fas fa-file-plus me-2"></i>
                درخواست جدید
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="<?= url('documents/upload') ?>">
                <i class="fas fa-upload me-2"></i>
                آپلود سند
            </a>
        </li>
        <?php if (Security::checkPermission('manager')): ?>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="<?= url('users/create') ?>">
                    <i class="fas fa-user-plus me-2"></i>
                    کاربر جدید
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<style>
.stats-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stats-card:hover::before {
    opacity: 1;
}

.stats-icon {
    opacity: 0.7;
    transition: all 0.3s ease;
}

.stats-card:hover .stats-icon {
    opacity: 1;
    transform: scale(1.1);
}

.floating-actions .btn {
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
}

.floating-actions .btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
}

.card {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
}

.badge {
    font-weight: 600;
    padding: 0.5em 0.75em;
}

.persian-num {
    font-family: 'IRANSans', monospace;
}

@media (max-width: 768px) {
    .floating-actions .btn {
        width: 50px;
        height: 50px;
        bottom: 15px;
        left: 15px;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .quick-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .dashboard-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

@media (max-width: 576px) {
    .chart-container {
        padding: 1rem;
    }
    
    .dashboard-header {
        padding: 1rem;
    }
    
    .quick-actions {
        align-items: stretch;
    }
    
    .quick-action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// متغیرهای گلوبال
let performanceChart;
let dashboardRefreshInterval;

// آماده‌سازی اولیه
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    startAutoRefresh();
    setupEventListeners();
    
    // اعمال حالت ذخیره شده
    const savedMode = localStorage.getItem('dashboard_mode') || 'full';
    applyDashboardMode(savedMode);
});

// راه‌اندازی نمودارها
function initializeCharts() {
    const canvas = document.getElementById('performanceChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // اگر Chart.js بارگذاری نشده، نمودار ساده بکشیم
    if (typeof Chart === 'undefined') {
        canvas.style.background = 'linear-gradient(45deg, #f0f0f0, #e0e0e0)';
        canvas.style.borderRadius = '10px';
        return;
    }
    
    performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($weekly_chart_data['labels'] ?? ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه']) ?>,
            datasets: [
                {
                    label: 'تعداد درخواست‌ها',
                    data: <?= json_encode($weekly_chart_data['requests'] ?? [10, 15, 8, 20, 12, 18, 25]) ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// تنظیم event listeners
function setupEventListeners() {
    // تغییر نوع نمودار
    const chartButtons = document.querySelectorAll('[data-chart]');
    chartButtons.forEach(button => {
        button.addEventListener('click', function() {
            // حذف active از همه
            chartButtons.forEach(btn => btn.classList.remove('active'));
            // اضافه کردن active به کلیک شده
            this.classList.add('active');
            
            const chartType = this.dataset.chart;
            loadChartData(chartType);
        });
    });
}

// شروع به‌روزرسانی خودکار
function startAutoRefresh() {
    dashboardRefreshInterval = setInterval(function() {
        refreshStats();
    }, 5 * 60 * 1000); // هر 5 دقیقه
}

// به‌روزرسانی آمار
function refreshStats() {
    Samanat.ajax.get('<?= url("dashboard/live-stats") ?>')
        .then(response => {
            if (response.data && response.data.stats) {
                updateStatsCards(response.data.stats);
                showToast('آمار به‌روزرسانی شد', 'success');
            }
        })
        .catch(error => {
            console.log('خطا در به‌روزرسانی آمار:', error);
        });
}

// به‌روزرسانی کامل داشبورد
function refreshDashboard() {
    Samanat.loading.show('در حال به‌روزرسانی...');
    
    Promise.all([
        refreshStats(),
        loadNotifications()
    ]).then(() => {
        Samanat.loading.hide();
        Samanat.notifications.success('داشبورد به‌روزرسانی شد');
    }).catch(() => {
        Samanat.loading.hide();
        Samanat.notifications.error('خطا در به‌روزرسانی');
    });
}

// بارگذاری اعلان‌ها
function loadNotifications() {
    return Samanet.ajax.get('<?= url("dashboard/notifications") ?>')
        .then(response => {
            if (response.data && response.data.notifications && response.data.notifications.length > 0) {
                updateNotificationsPanel(response.data.notifications);
            }
        });
}

// تایید سریع درخواست
function quickApprove(requestId) {
    if (!confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        return;
    }

    Samanet.ajax.post('<?= url("requests/approve") ?>', {
        request_id: requestId,
        notes: 'تایید سریع از داشبورد'
    })
    .then(response => {
        if (response.data && response.data.success) {
            Samanat.notifications.success('درخواست با موفقیت تایید شد');
            setTimeout(refreshDashboard, 1000);
        } else {
            Samanat.notifications.error(response.data.message || 'خطا در تایید درخواست');
        }
    })
    .catch(error => {
        Samanat.notifications.error('خطا در ارتباط با سرور');
    });
}

// تغییر حالت داشبورد
function toggleDashboardMode() {
    const currentMode = localStorage.getItem('dashboard_mode') || 'full';
    const newMode = currentMode === 'full' ? 'compact' : 'full';
    
    localStorage.setItem('dashboard_mode', newMode);
    applyDashboardMode(newMode);
    Samanat.notifications.success('حالت نمایش تغییر یافت');
}

// اعمال حالت داشبورد
function applyDashboardMode(mode) {
    const body = document.body;
    
    if (mode === 'compact') {
        body.classList.add('dashboard-compact');
    } else {
        body.classList.remove('dashboard-compact');
    }
}

// بارگذاری داده‌های نمودار
function loadChartData(type) {
    Samanet.ajax.get('<?= url("dashboard/chart-data") ?>', { type: type })
        .then(response => {
            if (response.data) {
                updateChart(response.data);
            }
        })
        .catch(error => {
            Samanat.notifications.error('خطا در بارگذاری نمودار');
        });
}

// به‌روزرسانی نمودار
function updateChart(data) {
    if (performanceChart) {
        performanceChart.data.labels = data.labels;
        performanceChart.data.datasets[0].data = data.requests;
        performanceChart.update('active');
    }
}

// توابع کمکی
function updateStatsCards(stats) {
    // Implementation for updating stats cards
    console.log('Updating stats:', stats);
}

function updateNotificationsPanel(notifications) {
    // Implementation for updating notifications
    console.log('Updating notifications:', notifications);
}

function showToast(message, type = 'info') {
    if (typeof Samanat !== 'undefined') {
        if (type === 'error') {
            Samanat.notifications.error(message);
        } else if (type === 'success') {
            Samanat.notifications.success(message);
        } else {
            Samanat.notifications.info(message);
        }
    }
}

// پاکسازی تایمرها هنگام خروج از صفحه
window.addEventListener('beforeunload', function() {
    if (dashboardRefreshInterval) {
        clearInterval(dashboardRefreshInterval);
    }
});
</script>

