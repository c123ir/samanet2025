<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/requests/list.php
 * توضیح: صفحه مدیریت درخواست‌ها - طراحی حرفه‌ای
 * تاریخ بازطراحی: 1404/10/31
 * نسخه: 5.0 Enterprise Grade - مطابق استانداردهای UI/UX
 */

// Helper functions از Utilities.php استفاده می‌شوند

// داده‌های صفحه
$totalRequests = $stats['total'] ?? 0;
$pendingRequests = $stats['pending'] ?? 0;
$processingRequests = $stats['processing'] ?? 0;
$completedRequests = $stats['completed'] ?? 0;
?>

<!-- MANDATORY: Stats Row - دقیقاً مطابق استانداردها -->
<div class="stats-row">
    <div class="stat-card-pro">
        <div class="stat-label">کل درخواست‌ها</div>
        <div class="stat-value"><?= number_format($totalRequests) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>همه موارد</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در انتظار</div>
        <div class="stat-value"><?= number_format($pendingRequests) ?></div>
        <div class="stat-change">
            <i class="fas fa-clock"></i>
            <span>بررسی نشده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در حال بررسی</div>
        <div class="stat-value"><?= number_format($processingRequests) ?></div>
        <div class="stat-change">
            <i class="fas fa-sync-alt"></i>
            <span>در جریان</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">تکمیل شده</div>
        <div class="stat-value"><?= number_format($completedRequests) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-check-circle"></i>
            <span>موفق</span>
        </div>
    </div>
</div>

<!-- Grid اصلی صفحه -->
<div class="dashboard-grid">
    <!-- ستون اصلی -->
    <div class="main-column">
        <!-- لیست درخواست‌ها -->
        <div class="table-container" id="resultsContainer">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    لیست درخواست‌ها
                    <span class="badge badge-primary ms-2" id="totalRequestsCount"><?= count($requests_data['data'] ?? []) ?></span>
                </h2>
                <div class="table-actions">
                    <a href="/?route=requests&action=create" class="btn-icon btn-primary" title="درخواست جدید">
                        <i class="fas fa-plus"></i>
                    </a>
                    <button class="btn-icon" onclick="exportRequests()" title="دانلود لیست">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-icon" onclick="refreshRequestList()" title="بروزرسانی">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            
            <?php if (!empty($requests_data['data'])): ?>
            <!-- جدول دسکتاپ -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">شناسه</th>
                        <th>مرجع</th>
                        <th>عنوان</th>
                        <th>مبلغ</th>
                        <th>صاحب حساب</th>
                        <th>وضعیت</th>
                        <th>اولویت</th>
                        <th>تاریخ</th>
                        <th class="text-center" style="width: 150px;">عملیات</th>
                    </tr>
                </thead>
                <tbody id="requestsTableBody">
                    <?php foreach ($requests_data['data'] as $request): ?>
                    <tr>
                        <td class="text-center">
                            <code>#<?= $request['id'] ?></code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="status-indicator" style="width: 8px; height: 8px; border-radius: 50%; background: <?= getStatusColor($request['status'] ?? 'pending') === 'success' ? '#10B981' : (getStatusColor($request['status'] ?? 'pending') === 'warning' ? '#F59E0B' : '#6B7280') ?>;"></div>
                                <div>
                                    <div class="fw-semibold">
                                        <a href="/?route=requests&action=show&id=<?= $request['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($request['reference_number'] ?? 'REQ' . str_pad($request['id'], 3, '0', STR_PAD_LEFT)) ?>
                                        </a>
                                    </div>
                                    <?php if ($request['is_urgent'] ?? false): ?>
                                        <span class="badge bg-danger small">فوری</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($request['title'] ?? 'بدون عنوان') ?></div>
                                <?php if (!empty($request['description'])): ?>
                                    <div class="text-muted small">
                                        <?= htmlspecialchars(mb_substr($request['description'], 0, 50)) ?>
                                        <?= mb_strlen($request['description']) > 50 ? '...' : '' ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($request['amount'])): ?>
                                <span class="fw-bold text-success">
                                    <?= number_format($request['amount']) ?> ریال
                                </span>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="fas fa-minus me-1"></i>
                                    مشخص نشده
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($request['account_holder'])): ?>
                                <div>
                                    <div class="fw-medium"><?= htmlspecialchars($request['account_holder']) ?></div>
                                    <?php if (!empty($request['bank_name'])): ?>
                                        <div class="text-muted small"><?= htmlspecialchars($request['bank_name']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    نامشخص
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= getStatusColor($request['status'] ?? 'pending') ?>">
                                <i class="<?= getStatusIcon($request['status'] ?? 'pending') ?> me-1"></i>
                                <?= getStatusLabel($request['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= getPriorityColor($request['priority'] ?? 'normal') ?>">
                                <i class="<?= getPriorityIcon($request['priority'] ?? 'normal') ?> me-1"></i>
                                <?= getPriorityLabel($request['priority'] ?? 'normal') ?>
                            </span>
                        </td>
                        <td class="text-muted">
                            <?php if (!empty($request['created_at'])): ?>
                                <?= jdate('Y/m/d H:i', strtotime($request['created_at'])) ?>
                            <?php else: ?>
                                <?= jdate('Y/m/d H:i', time()) ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="/?route=requests&action=show&id=<?= $request['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="مشاهده جزئیات">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if (($request['status'] ?? 'pending') === 'pending'): ?>
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm" 
                                            onclick="approveRequest(<?= $request['id'] ?>)"
                                            title="تایید درخواست">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="rejectRequest(<?= $request['id'] ?>)"
                                            title="رد درخواست">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                        data-bs-toggle="dropdown" 
                                        title="عملیات بیشتر">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/?route=requests&action=edit&id=<?= $request['id'] ?>">
                                        <i class="fas fa-edit me-2"></i>ویرایش
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="duplicateRequest(<?= $request['id'] ?>)">
                                        <i class="fas fa-copy me-2"></i>کپی
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteRequest(<?= $request['id'] ?>)">
                                        <i class="fas fa-trash me-2"></i>حذف
                                    </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- لیست موبایل -->
            <div class="mobile-list" id="mobileRequestsList">
                <?php foreach ($requests_data['data'] as $request): ?>
                <div class="mobile-list-item">
                    <div class="mobile-item-main">
                        <div class="mobile-item-title">
                            <?= htmlspecialchars($request['reference_number'] ?? 'REQ' . str_pad($request['id'], 3, '0', STR_PAD_LEFT)) ?>
                            <?php if ($request['is_urgent'] ?? false): ?>
                                <span class="badge bg-danger ms-1">فوری</span>
                            <?php endif; ?>
                        </div>
                        <div class="mobile-item-meta">
                            <span class="badge bg-<?= getStatusColor($request['status'] ?? 'pending') ?>"><?= getStatusLabel($request['status'] ?? 'pending') ?></span>
                            • <span class="badge bg-<?= getPriorityColor($request['priority'] ?? 'normal') ?>"><?= getPriorityLabel($request['priority'] ?? 'normal') ?></span>
                            <?php if (!empty($request['amount'])): ?>
                                • <span class="text-success fw-bold"><?= number_format($request['amount']) ?> ریال</span>
                            <?php endif; ?>
                        </div>
                        <div class="mobile-item-date"><?= htmlspecialchars($request['title'] ?? 'بدون عنوان') ?></div>
                    </div>
                    <div class="mobile-item-actions">
                        <a href="/?route=requests&action=show&id=<?= $request['id'] ?>" class="btn-icon" title="مشاهده">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if (($request['status'] ?? 'pending') === 'pending'): ?>
                        <button class="btn-icon text-success" onclick="approveRequest(<?= $request['id'] ?>)" title="تایید">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-icon text-danger" onclick="rejectRequest(<?= $request['id'] ?>)" title="رد">
                            <i class="fas fa-times"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- پیام خالی -->
            <div style="text-align: center; padding: var(--space-8); color: var(--gray-500);">
                <i class="fas fa-file-invoice-dollar" style="font-size: 3rem; margin-bottom: var(--space-4); opacity: 0.5;"></i>
                <h5>هیچ درخواستی یافت نشد</h5>
                <p>هنوز درخواستی در سیستم ثبت نشده است یا درخواستی با این فیلترها وجود ندارد</p>
                <a href="/?route=requests&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    ایجاد درخواست جدید
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ستون جانبی -->
    <div class="side-column">
        <!-- عملیات سریع -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-bolt"></i>
                    عملیات سریع
                </div>
            </div>
            <div class="panel-body">
                <div class="task-item" onclick="location.href='/?route=requests&action=create'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-plus" style="color: var(--success); margin-left: var(--space-2);"></i>
                        درخواست جدید
                    </span>
                </div>
                
                <div class="task-item" onclick="exportRequests()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-download" style="color: var(--primary); margin-left: var(--space-2);"></i>
                        خروجی Excel
                    </span>
                </div>
                
                <div class="task-item" onclick="refreshRequestList()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-sync-alt" style="color: var(--info); margin-left: var(--space-2);"></i>
                        بروزرسانی لیست
                    </span>
                </div>
                
                <div class="task-item" onclick="showBulkActions()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-tasks" style="color: var(--warning); margin-left: var(--space-2);"></i>
                        عملیات گروهی
                    </span>
                </div>
            </div>
        </div>

        <!-- آمار این ماه -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-chart-pie"></i>
                    آمار این ماه
                </div>
            </div>
            <div class="panel-body">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-check-circle me-1 text-success"></i>
                        تکمیل شده
                    </span>
                    <span style="font-weight: 600; color: var(--success);"><?= $completedRequests ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-clock me-1 text-warning"></i>
                        در انتظار
                    </span>
                    <span style="font-weight: 600; color: var(--warning);"><?= $pendingRequests ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-sync-alt me-1 text-info"></i>
                        در حال بررسی
                    </span>
                    <span style="font-weight: 600; color: var(--info);"><?= $processingRequests ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0;">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-times-circle me-1 text-danger"></i>
                        رد شده
                    </span>
                    <span style="font-weight: 600; color: var(--danger);"><?= ($stats['rejected'] ?? 0) ?></span>
                </div>
            </div>
        </div>

        <!-- فیلترهای سریع -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-filter"></i>
                    فیلترهای سریع
                </div>
            </div>
            <div class="panel-body">
                <div class="task-item" onclick="filterByStatus('pending')" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-clock" style="color: var(--warning); margin-left: var(--space-2);"></i>
                        فقط در انتظار
                    </span>
                </div>
                
                <div class="task-item" onclick="filterByPriority('urgent')" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-exclamation-triangle" style="color: var(--danger); margin-left: var(--space-2);"></i>
                        فقط فوری‌ها
                    </span>
                </div>
                
                <div class="task-item" onclick="filterByStatus('completed')" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-left: var(--space-2);"></i>
                        تکمیل شده‌ها
                    </span>
                </div>
                
                <div class="task-item" onclick="clearAllFilters()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-eraser" style="color: var(--gray-500); margin-left: var(--space-2);"></i>
                        پاک کردن فیلترها
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global functions for request management
function approveRequest(requestId) {
    if (confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        // Implementation for approve
        console.log('Approving request:', requestId);
    }
}

function rejectRequest(requestId) {
    if (confirm('آیا از رد این درخواست اطمینان دارید؟')) {
        // Implementation for reject
        console.log('Rejecting request:', requestId);
    }
}

function deleteRequest(requestId) {
    if (confirm('آیا از حذف این درخواست اطمینان دارید؟ این عمل غیرقابل برگشت است.')) {
        // Implementation for delete
        console.log('Deleting request:', requestId);
    }
}

function duplicateRequest(requestId) {
    // Implementation for duplicate
    console.log('Duplicating request:', requestId);
}

function exportRequests() {
    // Implementation for export
    console.log('Exporting requests...');
}

function refreshRequestList() {
    location.reload();
}

function showBulkActions() {
    // Implementation for bulk actions
    console.log('Showing bulk actions...');
}

function filterByStatus(status) {
    // Implementation for status filter
    console.log('Filtering by status:', status);
}

function filterByPriority(priority) {
    // Implementation for priority filter
    console.log('Filtering by priority:', priority);
}

function clearAllFilters() {
    // Implementation for clear filters
    console.log('Clearing all filters...');
    location.href = '/?route=requests';
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Requests Page loaded with Professional UI/UX Standards');
});
</script>
