<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /app/views/requests/index.php  
 * توضیح: صفحه مدیریت درخواست‌ها - طراحی حرفه‌ای
 * تاریخ بازطراحی: 1404/10/31
 * نسخه: 5.1 Enterprise Grade - مطابق استانداردهای UI/UX + PHP 8+ Compatible
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

// Load main layout
require_once(APP_PATH . 'views/layouts/main.php');

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

<!-- جستجوی پیشرفته -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-search"></i>
            جستجوی پیشرفته
        </h2>
    </div>
    <div class="search-container">
        <!-- Search input with live functionality -->
        <div class="search-input-wrapper">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" 
                       id="searchInput" 
                       class="form-control border-start-0 ps-0" 
                       placeholder="جستجو در درخواست‌ها... (مثال: حواله، REQ001، علی احمدی، 1000000)"
                       autocomplete="off">
                <button class="btn btn-outline-secondary border-start-0" 
                        id="clearSearch" 
                        type="button" 
                        style="display: none;"
                        title="پاک کردن جستجو">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="search-help-text">
                <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    نکته: برای جستجوی دقیق‌تر از کلمات کلیدی مثل مبلغ، نام صاحب حساب، یا شماره مرجع استفاده کنید | ESC برای پاک کردن
                </small>
            </div>
        </div>
        
        <!-- Live search statistics -->
        <div class="search-stats mt-2" id="searchStats" style="display: none;">
            <small class="text-info">
                <i class="fas fa-chart-bar me-1"></i>
                <span id="searchResultCount">0</span> نتیجه یافت شد
                <span id="searchTermsDisplay"></span>
            </small>
        </div>
    </div>
</div>

<!-- Loading indicator -->
<div id="loadingIndicator" class="text-center p-4" style="display: none;">
    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
    در حال جستجو...
</div>

<!-- No results message -->
<div id="noResultsMessage" class="text-center p-5" style="display: none;">
    <div class="empty-state">
        <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">نتیجه‌ای یافت نشد</h5>
        <p class="text-muted">
            برای عبارت جستجوی شما هیچ نتیجه‌ای یافت نشد.<br>
            لطفاً کلمات کلیدی دیگری امتحان کنید.
        </p>
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
                                            <?= safe_htmlspecialchars($request['reference_number'] ?? 'REQ' . str_pad($request['id'], 3, '0', STR_PAD_LEFT)) ?>
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
                                <div class="fw-medium"><?= safe_htmlspecialchars($request['title'] ?? 'بدون عنوان') ?></div>
                                <?php if (!empty($request['description'])): ?>
                                    <div class="text-muted small">
                                        <?= safe_htmlspecialchars(safe_substr($request['description'], 0, 50)) ?>
                                        <?= mb_strlen($request['description']) > 50 ? '...' : '' ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($request['amount'])): ?>
                                <span class="fw-bold text-success">
                                    <?= safe_number_format($request['amount']) ?> ریال
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
                                    <div class="fw-medium"><?= safe_htmlspecialchars($request['account_holder']) ?></div>
                                    <?php if (!empty($request['bank_name'])): ?>
                                        <div class="text-muted small"><?= safe_htmlspecialchars($request['bank_name']) ?></div>
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
                            <?= safe_htmlspecialchars($request['reference_number'] ?? 'REQ' . str_pad($request['id'], 3, '0', STR_PAD_LEFT)) ?>
                            <?php if ($request['is_urgent'] ?? false): ?>
                                <span class="badge bg-danger ms-1">فوری</span>
                            <?php endif; ?>
                        </div>
                        <div class="mobile-item-meta">
                            <span class="badge bg-<?= getStatusColor($request['status'] ?? 'pending') ?>"><?= getStatusLabel($request['status'] ?? 'pending') ?></span>
                            • <span class="badge bg-<?= getPriorityColor($request['priority'] ?? 'normal') ?>"><?= getPriorityLabel($request['priority'] ?? 'normal') ?></span>
                            <?php if (!empty($request['amount'])): ?>
                                • <span class="text-success fw-bold"><?= safe_number_format($request['amount']) ?> ریال</span>
                            <?php endif; ?>
                        </div>
                        <div class="mobile-item-date"><?= safe_htmlspecialchars($request['title'] ?? 'بدون عنوان') ?></div>
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
                        <i class="fas fa-times-circle me-1 text-danger"></i>
                        رد شده
                    </span>
                    <span style="font-weight: 600; color: var(--danger);"><?= safe_number_format($stats['rejected'] ?? 0) ?></span>
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
/**
 * Advanced Search System for Requests
 */
class RequestsAdvancedSearch {
    constructor() {
        this.apiUrl = '/?route=requests&action=api';
        this.debounceDelay = 200;
        this.currentRequest = null;
        
        // DOM elements
        this.searchInput = document.getElementById('searchInput');
        this.clearButton = document.getElementById('clearSearch');
        this.resultsContainer = document.getElementById('resultsContainer');
        this.loadingIndicator = document.getElementById('loadingIndicator');
        this.noResultsMessage = document.getElementById('noResultsMessage');
        this.searchStats = document.getElementById('searchStats');
        this.searchResultCount = document.getElementById('searchResultCount');
        this.searchTermsDisplay = document.getElementById('searchTermsDisplay');
        this.totalRequestsCount = document.getElementById('totalRequestsCount');
        this.requestsTableBody = document.getElementById('requestsTableBody');
        this.mobileRequestsList = document.getElementById('mobileRequestsList');
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadInitialData();
    }
    
    bindEvents() {
        // Real-time search with debouncing
        this.searchInput.addEventListener('input', this.debounce((e) => {
            const query = e.target.value.trim();
            this.performSearch(query);
            this.toggleClearButton(query);
        }, this.debounceDelay));
        
        // Clear search functionality
        this.clearButton.addEventListener('click', () => {
            this.clearSearch();
        });
        
        // Enter key navigation to first result
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.navigateToFirstResult();
            }
            
            if (e.key === 'Escape') {
                e.preventDefault();
                this.clearSearch();
                this.searchInput.blur();
            }
        });
        
        // Global ESC key to clear search from anywhere
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.searchInput.value && document.activeElement !== this.searchInput) {
                e.preventDefault();
                this.clearSearch();
            }
        });
    }
    
    async performSearch(query = '') {
        try {
            // Cancel previous request
            if (this.currentRequest) {
                this.currentRequest.abort();
            }
            
            // Show loading state
            if (query) {
                this.showLoading();
            }
            
            // Create new request
            this.currentRequest = new AbortController();
            
            // Build search URL
            const searchParams = new URLSearchParams({
                search: query
            });
            
            const response = await fetch(`${this.apiUrl}&${searchParams}`, {
                signal: this.currentRequest.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update UI with results
            this.updateSearchResults(data, query);
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Search error:', error);
                this.showError('خطا در انجام جستجو');
            }
        } finally {
            this.hideLoading();
        }
    }
    
    updateSearchResults(data, query) {
        if (!data.success) {
            this.showError(data.message || 'خطا در دریافت نتایج');
            return;
        }
        
        const requests = data.data || [];
        
        // Update counters
        this.updateSearchStats(requests.length, query);
        this.totalRequestsCount.textContent = requests.length;
        
        // Show/hide no results message
        if (requests.length === 0 && query) {
            this.showNoResults();
        } else {
            this.hideNoResults();
        }
        
        // Update tables
        this.updateDesktopTable(requests);
        this.updateMobileList(requests);
    }
    
    updateDesktopTable(requests) {
        const tbody = this.requestsTableBody;
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        requests.forEach(request => {
            const row = this.createTableRow(request);
            tbody.appendChild(row);
        });
    }
    
    updateMobileList(requests) {
        const list = this.mobileRequestsList;
        if (!list) return;
        
        list.innerHTML = '';
        
        requests.forEach(request => {
            const item = this.createMobileItem(request);
            list.appendChild(item);
        });
    }
    
    createTableRow(request) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">
                <code>#${request.id}</code>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="status-indicator" style="width: 8px; height: 8px; border-radius: 50%; background: ${this.getStatusIndicatorColor(request.status)};"></div>
                    <div>
                        <div class="fw-semibold">
                            <a href="/?route=requests&action=show&id=${request.id}" class="text-decoration-none">
                                ${request.reference_number || 'REQ' + String(request.id).padStart(3, '0')}
                            </a>
                        </div>
                        ${request.is_urgent ? '<span class="badge bg-danger small">فوری</span>' : ''}
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <div class="fw-medium">${request.title || 'بدون عنوان'}</div>
                    ${request.description ? `<div class="text-muted small">${request.description.substring(0, 50)}${request.description.length > 50 ? '...' : ''}</div>` : ''}
                </div>
            </td>
            <td>
                ${request.amount ? 
                    `<span class="fw-bold text-success">${new Intl.NumberFormat('fa-IR').format(request.amount)} ریال</span>` : 
                    '<span class="text-muted"><i class="fas fa-minus me-1"></i>مشخص نشده</span>'
                }
            </td>
            <td>
                ${request.account_holder ? 
                    `<div>
                        <div class="fw-medium">${request.account_holder}</div>
                        ${request.bank_name ? `<div class="text-muted small">${request.bank_name}</div>` : ''}
                    </div>` : 
                    '<span class="text-muted"><i class="fas fa-user me-1"></i>نامشخص</span>'
                }
            </td>
            <td>
                <span class="badge bg-${this.getStatusColor(request.status)}">
                    <i class="${this.getStatusIcon(request.status)} me-1"></i>
                    ${this.getStatusLabel(request.status)}
                </span>
            </td>
            <td>
                <span class="badge bg-${this.getPriorityColor(request.priority)}">
                    <i class="${this.getPriorityIcon(request.priority)} me-1"></i>
                    ${this.getPriorityLabel(request.priority)}
                </span>
            </td>
            <td class="text-muted">
                ${request.created_at ? new Date(request.created_at).toLocaleDateString('fa-IR') : new Date().toLocaleDateString('fa-IR')}
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <a href="/?route=requests&action=show&id=${request.id}" class="btn btn-outline-primary btn-sm" title="مشاهده جزئیات">
                        <i class="fas fa-eye"></i>
                    </a>
                    ${request.status === 'pending' ? `
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="approveRequest(${request.id})" title="تایید درخواست">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="rejectRequest(${request.id})" title="رد درخواست">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        return row;
    }
    
    createMobileItem(request) {
        const item = document.createElement('div');
        item.className = 'mobile-list-item';
        item.innerHTML = `
            <div class="mobile-item-main">
                <div class="mobile-item-title">
                    ${request.reference_number || 'REQ' + String(request.id).padStart(3, '0')}
                    ${request.is_urgent ? '<span class="badge bg-danger ms-1">فوری</span>' : ''}
                </div>
                <div class="mobile-item-meta">
                    <span class="badge bg-${this.getStatusColor(request.status)}">${this.getStatusLabel(request.status)}</span>
                    • <span class="badge bg-${this.getPriorityColor(request.priority)}">${this.getPriorityLabel(request.priority)}</span>
                    ${request.amount ? `• <span class="text-success fw-bold">${new Intl.NumberFormat('fa-IR').format(request.amount)} ریال</span>` : ''}
                </div>
                <div class="mobile-item-date">${request.title || 'بدون عنوان'}</div>
            </div>
            <div class="mobile-item-actions">
                <a href="/?route=requests&action=show&id=${request.id}" class="btn-icon" title="مشاهده">
                    <i class="fas fa-eye"></i>
                </a>
                ${request.status === 'pending' ? `
                    <button class="btn-icon text-success" onclick="approveRequest(${request.id})" title="تایید">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn-icon text-danger" onclick="rejectRequest(${request.id})" title="رد">
                        <i class="fas fa-times"></i>
                    </button>
                ` : ''}
            </div>
        `;
        return item;
    }
    
    // Helper methods for colors and icons
    getStatusIndicatorColor(status) {
        const colors = {
            'pending': '#F59E0B',
            'processing': '#3B82F6', 
            'completed': '#10B981',
            'rejected': '#EF4444'
        };
        return colors[status] || '#6B7280';
    }
    
    getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'processing': 'info',
            'completed': 'success', 
            'rejected': 'danger'
        };
        return colors[status] || 'secondary';
    }
    
    getStatusIcon(status) {
        const icons = {
            'pending': 'fas fa-clock',
            'processing': 'fas fa-sync-alt',
            'completed': 'fas fa-check-circle',
            'rejected': 'fas fa-times-circle'
        };
        return icons[status] || 'fas fa-question-circle';
    }
    
    getStatusLabel(status) {
        const labels = {
            'pending': 'در انتظار',
            'processing': 'در حال بررسی',
            'completed': 'تکمیل شده',
            'rejected': 'رد شده'
        };
        return labels[status] || 'نامشخص';
    }
    
    getPriorityColor(priority) {
        const colors = {
            'low': 'info',
            'normal': 'secondary',
            'high': 'warning',
            'urgent': 'danger'
        };
        return colors[priority] || 'secondary';
    }
    
    getPriorityIcon(priority) {
        const icons = {
            'low': 'fas fa-arrow-down',
            'normal': 'fas fa-minus',
            'high': 'fas fa-arrow-up',
            'urgent': 'fas fa-exclamation-triangle'
        };
        return icons[priority] || 'fas fa-minus';
    }
    
    getPriorityLabel(priority) {
        const labels = {
            'low': 'کم',
            'normal': 'معمولی',
            'high': 'بالا',
            'urgent': 'فوری'
        };
        return labels[priority] || 'معمولی';
    }
    
    // UI state management methods
    showLoading() {
        if (this.loadingIndicator) {
            this.loadingIndicator.style.display = 'block';
        }
        this.hideNoResults();
    }
    
    hideLoading() {
        if (this.loadingIndicator) {
            this.loadingIndicator.style.display = 'none';
        }
    }
    
    showNoResults() {
        if (this.noResultsMessage) {
            this.noResultsMessage.style.display = 'block';
        }
    }
    
    hideNoResults() {
        if (this.noResultsMessage) {
            this.noResultsMessage.style.display = 'none';
        }
    }
    
    showError(message) {
        console.error('Search error:', message);
        // You can implement a toast notification here
    }
    
    updateSearchStats(count, query) {
        if (this.searchStats && this.searchResultCount) {
            if (query) {
                this.searchResultCount.textContent = count;
                this.searchTermsDisplay.textContent = ` برای "${query}"`;
                this.searchStats.style.display = 'block';
            } else {
                this.searchStats.style.display = 'none';
            }
        }
    }
    
    toggleClearButton(query) {
        if (this.clearButton) {
            this.clearButton.style.display = query ? 'block' : 'none';
        }
    }
    
    clearSearch() {
        this.searchInput.value = '';
        this.toggleClearButton('');
        this.performSearch('');
        this.hideNoResults();
    }
    
    navigateToFirstResult() {
        const firstLink = document.querySelector('#requestsTableBody a, #mobileRequestsList a');
        if (firstLink) {
            firstLink.click();
        }
    }
    
    loadInitialData() {
        // Load initial data without search query
        this.performSearch('');
    }
    
    // Utility function for debouncing
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

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

// Initialize the search system when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.requestsSearch = new RequestsAdvancedSearch();
    console.log('✅ Requests Advanced Search System initialized');
});
</script> 