<?php
/**
 * Dashboard Professional View
 * Design: Enterprise-grade UI
 * Version: 5.1
 */
?>

<div class="dashboard-pro">
    <!-- Professional Header -->
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="header-title">داشبورد سامانت</h1>
        </div>
        <div class="header-actions">
            <button class="btn-icon theme-toggle" onclick="toggleTheme()" title="تغییر تم">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            <div class="user-profile" title="<?= htmlspecialchars($user['name'] ?? 'کاربر') ?>">
                <?= mb_substr($user['name'] ?? 'ک', 0, 1) ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="dashboard-content">
        <!-- Compact Stats Row -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل مبلغ</div>
                <div class="stat-value">12,500<span class="text-muted" style="font-size: 16px;">M</span></div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up" style="font-size: 10px;"></i>
                    <span>12% از ماه قبل</span>
                </div>
            </div>

            <div class="stat-card-pro">
                <div class="stat-label">تکمیل شده</div>
                <div class="stat-value">195</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up" style="font-size: 10px;"></i>
                    <span>79% از کل</span>
                </div>
            </div>

            <div class="stat-card-pro">
                <div class="stat-label">در انتظار</div>
                <div class="stat-value">18</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down" style="font-size: 10px;"></i>
                    <span>7% از کل</span>
                </div>
            </div>

            <div class="stat-card-pro">
                <div class="stat-label">کل درخواست‌ها</div>
                <div class="stat-value">247</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up" style="font-size: 10px;"></i>
                    <span>8 امروز</span>
                </div>
            </div>
        </div>

        <!-- Main Grid Layout -->
        <div class="dashboard-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Data Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">آخرین درخواست‌ها</h2>
                        <button class="btn-icon" title="فیلتر">
                            <i class="fas fa-filter" style="font-size: 14px;"></i>
                        </button>
                    </div>

                    <!-- Desktop Table -->
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>شماره</th>
                                <th>عنوان</th>
                                <th>مبلغ</th>
                                <th>وضعیت</th>
                                <th>تاریخ</th>
                                <th style="width: 100px;">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_requests as $request): ?>
                            <tr>
                                <td class="font-mono"><?= htmlspecialchars($request['reference_number']) ?></td>
                                <td><?= htmlspecialchars(mb_substr($request['title'], 0, 30)) ?></td>
                                <td><?= number_format($request['amount'] ?? 0) ?></td>
                                <td>
                                    <span class="status-badge-pro <?= htmlspecialchars($request['status']) ?>">
                                        <?= htmlspecialchars($request['status_label']) ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($request['created_at_jalali']) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-icon" onclick="viewRequest(<?= $request['id'] ?>)" title="مشاهده">
                                            <i class="fas fa-eye" style="font-size: 14px;"></i>
                                        </button>
                                        <?php if($request['status'] === 'pending'): ?>
                                        <button class="btn-icon" onclick="approveRequest(<?= $request['id'] ?>)" title="تایید">
                                            <i class="fas fa-check" style="font-size: 14px;"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Mobile List -->
                    <div class="mobile-list">
                        <?php foreach($recent_requests as $request): ?>
                        <div class="mobile-list-item" onclick="viewRequest(<?= $request['id'] ?>)">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title"><?= htmlspecialchars(mb_substr($request['title'], 0, 25)) ?></div>
                                <div class="mobile-item-meta">
                                    <span><?= htmlspecialchars($request['reference_number']) ?></span> • 
                                    <span><?= htmlspecialchars($request['created_at_jalali']) ?></span>
                                </div>
                            </div>
                            <div style="text-align: left;">
                                <div class="mobile-item-amount"><?= number_format($request['amount'] ?? 0) ?></div>
                                <span class="status-badge-pro <?= htmlspecialchars($request['status']) ?>" style="margin-top: 4px; display: inline-block;">
                                    <?= htmlspecialchars($request['status_label']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Side Column -->
            <div class="side-column">
                <!-- Urgent Requests Panel -->
                <?php if(!empty($urgent_requests)): ?>
                <div class="panel urgent-panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            درخواست‌های فوری
                        </div>
                        <span class="panel-badge"><?= count($urgent_requests) ?></span>
                    </div>
                    <div class="panel-body">
                        <?php foreach(array_slice($urgent_requests, 0, 3) as $urgent): ?>
                        <div class="urgent-item" onclick="viewRequest(<?= $urgent['id'] ?>)">
                            <div>
                                <div class="urgent-item-title"><?= htmlspecialchars(mb_substr($urgent['title'], 0, 25)) ?></div>
                                <div class="urgent-item-meta"><?= number_format($urgent['amount'] ?? 0) ?> ریال</div>
                            </div>
                            <i class="fas fa-chevron-left urgent-item-icon"></i>
                        </div>
                        <?php endforeach; ?>
                        <?php if(count($urgent_requests) > 3): ?>
                        <a href="<?= url('requests?urgent=1') ?>" class="show-all">
                            مشاهده همه (<?= count($urgent_requests) ?>)
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Pending Tasks Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-tasks"></i>
                            وظایف در انتظار
                        </div>
                        <span class="panel-badge"><?= count($user_tasks ?? []) ?></span>
                    </div>
                    <div class="panel-body">
                        <?php if(!empty($user_tasks)): ?>
                            <?php foreach(array_slice($user_tasks, 0, 5) as $task): ?>
                            <div class="task-item" onclick="location.href='<?= $task['action_url'] ?>'">
                                <span class="task-checkbox"></span>
                                <span class="task-text"><?= htmlspecialchars($task['title']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <?php if(count($user_tasks) > 5): ?>
                            <a href="<?= url('tasks') ?>" class="show-all">
                                مشاهده همه (<?= count($user_tasks) ?>)
                            </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: var(--space-4); color: var(--gray-500); font-size: 13px;">
                                <i class="fas fa-check-circle" style="font-size: 24px; margin-bottom: var(--space-2); display: block;"></i>
                                همه وظایف انجام شده!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activities Panel -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-history"></i>
                            فعالیت‌های اخیر
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="task-item">
                            <span class="task-text">ورود موفق به سیستم</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">تایید درخواست REQ-2025-0001</span>
                        </div>
                        <div class="task-item">
                            <span class="task-text">ایجاد درخواست جدید</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dashboard Functions
function viewRequest(id) {
    window.location.href = `<?= url('requests/view/') ?>${id}`;
}

function approveRequest(id) {
    if(confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        fetch(`<?= url('api/requests/approve/') ?>${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
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
}

// Dashboard specific initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 Dashboard loaded');
    
    // Progressive animation for stats cards
    const statCards = document.querySelectorAll('.stat-card-pro');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
    
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Test localStorage for theme persistence
    try {
        localStorage.setItem('test', 'test');
        localStorage.removeItem('test');
        console.log('✅ localStorage is working');
    } catch (error) {
        console.error('❌ localStorage is not available:', error);
    }
});

// CSS Animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script> 