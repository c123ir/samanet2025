<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/requests/list.php
 * توضیح: صفحه مدیریت درخواست‌ها - طراحی حرفه‌ای با قابلیت‌های پیشرفته
 * تاریخ بازطراحی: 1404/10/31
 * نسخه: 6.1 Enterprise Grade + Mobile-First + Advanced Search + PHP 8+ Compatible
 */

// Helper functions برای جلوگیری از PHP 8+ deprecated warnings
function safe_htmlspecialchars($string, $flags = ENT_QUOTES, $encoding = 'UTF-8') {
    return htmlspecialchars((string)($string ?? ''), $flags, $encoding);
}

function safe_number_format($number, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',') {
    return number_format((float)($number ?? 0), $decimals, $decimal_separator, $thousands_separator);
}

function safe_substr($string, $start, $length = null) {
    if ($string === null || $string === '') {
        return '';
    }
    return $length !== null ? substr((string)$string, $start, $length) : substr((string)$string, $start);
}

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
        <div class="stat-value"><?= safe_number_format($totalRequests) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>همه موارد</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در انتظار</div>
        <div class="stat-value"><?= safe_number_format($pendingRequests) ?></div>
        <div class="stat-change">
            <i class="fas fa-clock"></i>
            <span>بررسی نشده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در حال بررسی</div>
        <div class="stat-value"><?= safe_number_format($processingRequests) ?></div>
        <div class="stat-change">
            <i class="fas fa-sync-alt"></i>
            <span>در جریان</span>
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
</div>

<!-- Search and Filter Bar - جدید و پیشرفته -->
<div class="search-filter-bar">
    <div class="search-filter-container">
        <!-- Search Input -->
        <div class="search-input-wrapper">
            <input type="text" 
                   class="form-control" 
                   id="searchInput" 
                   placeholder="جستجو در عنوان، مرجع، صاحب حساب..."
                   value="<?= safe_htmlspecialchars($filters['search'] ?? '') ?>">
            <i class="fas fa-search search-icon"></i>
            <button type="button" class="clear-search" id="clearSearch" title="پاک کردن جستجو">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Status Filter -->
        <select class="filter-select" id="statusFilter">
            <option value="">همه وضعیت‌ها</option>
            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>در انتظار</option>
            <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>در حال بررسی</option>
            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>تکمیل شده</option>
            <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>رد شده</option>
        </select>
        
        <!-- Priority Filter -->
        <select class="filter-select" id="priorityFilter">
            <option value="">همه اولویت‌ها</option>
            <option value="urgent" <?= ($filters['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>فوری</option>
            <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>بالا</option>
            <option value="normal" <?= ($filters['priority'] ?? '') === 'normal' ? 'selected' : '' ?>>معمولی</option>
            <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>پایین</option>
        </select>
        
        <!-- Filter Actions -->
        <div class="filter-actions">
            <button type="button" class="btn-filter" id="applyFilters">
                <i class="fas fa-filter"></i>
                اعمال فیلتر
            </button>
            <button type="button" class="btn-filter" id="resetFilters">
                <i class="fas fa-undo"></i>
                پاک کردن
            </button>
        </div>
    </div>
</div>

<!-- Search Results Info -->
<div class="search-results-info" id="searchResultsInfo" style="display: none;">
    <div>
        <span class="search-results-count" id="searchResultsCount">0</span>
        نتیجه یافت شد
        <span id="searchTermsDisplay" style="display: none;">
            برای: <span class="search-terms" id="searchTerms"></span>
        </span>
    </div>
    <button type="button" class="btn-filter" id="clearAllFilters">
        <i class="fas fa-times"></i>
        پاک کردن همه فیلترها
    </button>
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
            
            <!-- Loading Indicator -->
            <div class="loading-indicator" id="loadingIndicator">
                <div class="loading-spinner"></div>
                <div>در حال بارگذاری...</div>
            </div>
            
            <!-- No Results Message -->
            <div class="no-results" id="noResultsMessage">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="no-results-title">نتیجه‌ای یافت نشد</div>
                <div class="no-results-message">لطفاً کلمات کلیدی یا فیلترهای مختلفی امتحان کنید</div>
            </div>
            
            <?php if (!empty($requests_data['data'])): ?>
            <!-- جدول دسکتاپ -->
            <table class="data-table" id="desktopTable">
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
                    <tr data-request-id="<?= $request['id'] ?>">
                        <td class="text-center">
                            <code>#<?= $request['id'] ?></code>
                        </td>
                        <td>
                            <span class="text-primary fw-bold"><?= safe_htmlspecialchars($request['reference_number'] ?? '#' . $request['id']) ?></span>
                        </td>
                        <td>
                            <div class="fw-bold"><?= safe_htmlspecialchars($request['title']) ?></div>
                            <?php if (!empty($request['description'])): ?>
                            <small class="text-muted"><?= safe_htmlspecialchars(safe_substr($request['description'], 0, 50)) ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-success fw-bold persian-num">
                                <?= safe_number_format($request['amount']) ?> ریال
                            </span>
                        </td>
                        <td>
                            <div><?= safe_htmlspecialchars($request['account_holder']) ?></div>
                            <?php if (!empty($request['account_number'])): ?>
                            <small class="text-muted"><?= safe_htmlspecialchars($request['account_number']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $statusClass = '';
                            $statusText = '';
                            switch($request['status']) {
                                case 'pending':
                                    $statusClass = 'bg-warning';
                                    $statusText = 'در انتظار';
                                    break;
                                case 'processing':
                                    $statusClass = 'bg-info';
                                    $statusText = 'در حال بررسی';
                                    break;
                                case 'completed':
                                    $statusClass = 'bg-success';
                                    $statusText = 'تکمیل شده';
                                    break;
                                case 'rejected':
                                    $statusClass = 'bg-danger';
                                    $statusText = 'رد شده';
                                    break;
                                default:
                                    $statusClass = 'bg-secondary';
                                    $statusText = 'نامشخص';
                            }
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <?php
                            $priorityClass = '';
                            $priorityText = '';
                            $priorityIcon = '';
                            switch($request['priority'] ?? 'normal') {
                                case 'urgent':
                                    $priorityClass = 'bg-danger';
                                    $priorityText = 'فوری';
                                    $priorityIcon = 'fas fa-exclamation-triangle';
                                    break;
                                case 'high':
                                    $priorityClass = 'bg-warning';
                                    $priorityText = 'بالا';
                                    $priorityIcon = 'fas fa-arrow-up';
                                    break;
                                case 'normal':
                                    $priorityClass = 'bg-primary';
                                    $priorityText = 'معمولی';
                                    $priorityIcon = 'fas fa-minus';
                                    break;
                                case 'low':
                                    $priorityClass = 'bg-secondary';
                                    $priorityText = 'پایین';
                                    $priorityIcon = 'fas fa-arrow-down';
                                    break;
                                default:
                                    $priorityClass = 'bg-secondary';
                                    $priorityText = 'معمولی';
                                    $priorityIcon = 'fas fa-minus';
                            }
                            ?>
                            <span class="badge <?= $priorityClass ?>">
                                <i class="<?= $priorityIcon ?> me-1"></i>
                                <?= $priorityText ?>
                            </span>
                        </td>
                        <td>
                            <div class="small"><?= $request['created_at_jalali'] ?? date('Y/m/d H:i') ?></div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (($request['status'] ?? '') === 'pending'): ?>
                                <a href="?route=requests&action=edit&id=<?= $request['id'] ?>" 
                                   class="btn btn-outline-info btn-sm" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <button type="button" 
                                        class="btn btn-outline-success btn-sm" 
                                        onclick="approveRequest(<?= $request['id'] ?>)" 
                                        title="تایید">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Mobile Cards Container - جدید -->
            <div class="mobile-cards-container" id="mobileCardsContainer">
                <?php foreach ($requests_data['data'] as $request): ?>
                <div class="request-card" data-request-id="<?= $request['id'] ?>">
                    <div class="request-card-header">
                        <div class="request-card-id">#<?= $request['id'] ?></div>
                        <div class="request-card-status <?= $request['status'] ?? 'pending' ?>">
                            <?php
                            switch($request['status'] ?? 'pending') {
                                case 'pending': echo 'در انتظار'; break;
                                case 'processing': echo 'در حال بررسی'; break;
                                case 'completed': echo 'تکمیل شده'; break;
                                case 'rejected': echo 'رد شده'; break;
                                default: echo 'نامشخص';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="request-card-title">
                        <?= safe_htmlspecialchars($request['title']) ?>
                    </div>
                    
                    <div class="request-card-meta">
                        <div class="request-card-meta-item">
                            <div class="request-card-meta-label">مرجع</div>
                            <div class="request-card-meta-value">
                                <?= safe_htmlspecialchars($request['reference_number'] ?? '#' . $request['id']) ?>
                            </div>
                        </div>
                        
                        <div class="request-card-meta-item">
                            <div class="request-card-meta-label">مبلغ</div>
                            <div class="request-card-meta-value request-card-amount">
                                <?= safe_number_format($request['amount']) ?> ریال
                            </div>
                        </div>
                        
                        <div class="request-card-meta-item">
                            <div class="request-card-meta-label">صاحب حساب</div>
                            <div class="request-card-meta-value">
                                <?= safe_htmlspecialchars($request['account_holder']) ?>
                            </div>
                        </div>
                        
                        <div class="request-card-meta-item">
                            <div class="request-card-meta-label">اولویت</div>
                            <div class="request-card-meta-value">
                                <span class="request-card-priority <?= $request['priority'] ?? 'normal' ?>">
                                    <?php
                                    switch($request['priority'] ?? 'normal') {
                                        case 'urgent': echo '<i class="fas fa-exclamation-triangle"></i> فوری'; break;
                                        case 'high': echo '<i class="fas fa-arrow-up"></i> بالا'; break;
                                        case 'normal': echo '<i class="fas fa-minus"></i> معمولی'; break;
                                        case 'low': echo '<i class="fas fa-arrow-down"></i> پایین'; break;
                                        default: echo '<i class="fas fa-minus"></i> معمولی';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="request-card-actions">
                        <a href="?route=requests&action=show&id=<?= $request['id'] ?>" 
                           class="request-card-btn primary">
                            <i class="fas fa-eye"></i>
                            مشاهده
                        </a>
                        <?php if (($request['status'] ?? '') === 'pending'): ?>
                        <a href="?route=requests&action=edit&id=<?= $request['id'] ?>" 
                           class="request-card-btn">
                            <i class="fas fa-edit"></i>
                            ویرایش
                        </a>
                        <?php endif; ?>
                        <button type="button" 
                                class="request-card-btn" 
                                onclick="approveRequest(<?= $request['id'] ?>)">
                            <i class="fas fa-check"></i>
                            تایید
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php else: ?>
            <div class="no-results show">
                <div class="no-results-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="no-results-title">هیچ درخواستی یافت نشد</div>
                <div class="no-results-message">
                    <a href="/?route=requests&action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        ایجاد اولین درخواست
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- ستون کناری -->
    <div class="sidebar-column">
        <!-- پنل آمار -->
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
                    <span style="font-weight: 600; color: var(--success);"><?= safe_number_format($completedRequests) ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-clock me-1 text-warning"></i>
                        در انتظار
                    </span>
                    <span style="font-weight: 600; color: var(--warning);"><?= safe_number_format($pendingRequests) ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-sync-alt me-1 text-info"></i>
                        در حال بررسی
                    </span>
                    <span style="font-weight: 600; color: var(--info);"><?= safe_number_format($processingRequests) ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0;">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="fas fa-file-invoice-dollar me-1 text-primary"></i>
                        کل
                    </span>
                    <span style="font-weight: 600; color: var(--primary);"><?= safe_number_format($totalRequests) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Advanced Search and Filter -->
<script src="/assets/js/requests-advanced-search.js"></script>

<script>
// Additional helper functions that are specific to this page
document.addEventListener('DOMContentLoaded', () => {
    console.log('✅ Requests List Page Loaded Successfully');
    console.log('📊 Total Requests:', <?= count($requests_data['data'] ?? []) ?>);
    console.log('🔍 Advanced Search System Ready');
});

// Page-specific initialization
window.addEventListener('load', () => {
    // Auto-focus search if URL has search parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('search') && window.requestsSearch) {
        document.getElementById('searchInput')?.focus();
    }
    
    // Show welcome message for first-time users
    if (<?= $totalRequests ?> === 0) {
        console.log('💡 No requests found - showing onboarding experience');
    }
});

// Export functionality
function exportRequests() {
    // Create export URL with current filters
    const searchParams = new URLSearchParams();
    
    if (window.requestsSearch) {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const priorityFilter = document.getElementById('priorityFilter');
        
        if (searchInput?.value.trim()) {
            searchParams.set('search', searchInput.value.trim());
        }
        if (statusFilter?.value) {
            searchParams.set('status', statusFilter.value);
        }
        if (priorityFilter?.value) {
            searchParams.set('priority', priorityFilter.value);
        }
    }
    
    searchParams.set('export', 'excel');
    
    // Create download link
    const exportUrl = `/?route=requests&action=export&${searchParams.toString()}`;
    
    // Show loading indicator
    const originalText = event.target.innerHTML;
    event.target.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال تهیه...';
    event.target.disabled = true;
    
    // Create hidden download link
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `requests-${new Date().toISOString().split('T')[0]}.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button
    setTimeout(() => {
        event.target.innerHTML = originalText;
        event.target.disabled = false;
    }, 2000);
}

// Approval functionality with enhanced UX
function approveRequest(requestId) {
    // Create custom modal for approval
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تایید درخواست #${requestId}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>آیا از تایید این درخواست اطمینان دارید؟</p>
                    <textarea class="form-control mt-3" placeholder="توضیحات (اختیاری)" 
                              id="approvalNotes" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لغو</button>
                    <button type="button" class="btn btn-success" onclick="confirmApproval(${requestId})">
                        <i class="fas fa-check me-2"></i>تایید نهایی
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal (assuming Bootstrap is available)
    if (window.bootstrap) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Remove modal from DOM when hidden
        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    } else {
        // Fallback to simple confirm
        if (confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
            confirmApproval(requestId);
        }
        document.body.removeChild(modal);
    }
}

function confirmApproval(requestId) {
    const notes = document.getElementById('approvalNotes')?.value || '';
    
    // Show loading state
    const approveBtn = event.target;
    const originalText = approveBtn.innerHTML;
    approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال پردازش...';
    approveBtn.disabled = true;
    
    // API call for approval
    fetch('/?route=requests&action=approve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            request_id: requestId,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            if (window.requestsSearch && window.requestsSearch.showToast) {
                window.requestsSearch.showToast('درخواست با موفقیت تایید شد', 'success');
            }
            
            // Refresh the list
            if (window.requestsSearch) {
                window.requestsSearch.performSearch();
            } else {
                location.reload();
            }
            
            // Close modal if open
            const modal = document.querySelector('.modal.show');
            if (modal && window.bootstrap) {
                bootstrap.Modal.getInstance(modal).hide();
            }
        } else {
            throw new Error(data.message || 'خطا در تایید درخواست');
        }
    })
    .catch(error => {
        console.error('Approval error:', error);
        if (window.requestsSearch && window.requestsSearch.showToast) {
            window.requestsSearch.showToast(error.message || 'خطا در تایید درخواست', 'error');
        } else {
            alert(error.message || 'خطا در تایید درخواست');
        }
    })
    .finally(() => {
        // Reset button
        approveBtn.innerHTML = originalText;
        approveBtn.disabled = false;
    });
}

// Quick filter functions for external use
function quickFilterStatus(status) {
    if (window.requestsSearch) {
        document.getElementById('statusFilter').value = status;
        window.requestsSearch.currentPage = 1;
        window.requestsSearch.performSearch();
    }
}

function quickFilterPriority(priority) {
    if (window.requestsSearch) {
        document.getElementById('priorityFilter').value = priority;
        window.requestsSearch.currentPage = 1;
        window.requestsSearch.performSearch();
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Alt + N for new request
    if (e.altKey && e.key === 'n') {
        e.preventDefault();
        location.href = '/?route=requests&action=create';
    }
    
    // Alt + R for refresh
    if (e.altKey && e.key === 'r') {
        e.preventDefault();
        refreshRequestList();
    }
    
    // Alt + E for export
    if (e.altKey && e.key === 'e') {
        e.preventDefault();
        exportRequests();
    }
});

// Performance monitoring
if (window.performance) {
    window.addEventListener('load', () => {
        const loadTime = window.performance.now();
        console.log(`📈 Page Load Time: ${Math.round(loadTime)}ms`);
        
        // Report slow loads
        if (loadTime > 3000) {
            console.warn('⚠️ Slow page load detected');
        }
    });
}
</script>
