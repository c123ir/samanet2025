<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /app/views/users/index.php
 * توضیح: صفحه مدیریت کاربران - نسخه بازسازی شده
 * تاریخ بازطراحی: 1404/10/31
 * نسخه: 4.0 کامل با جستجوی پیشرفته
 */

// Helper functions از Utilities.php استفاده می‌شوند

// Load main layout
require_once(APP_PATH . 'views/layouts/main.php');

// داده‌های صفحه
$totalUsers = $stats['total'] ?? 0;
$activeUsers = $stats['active'] ?? 0;
$inactiveUsers = $stats['inactive'] ?? 0;
$adminCount = count(array_filter($users ?? [], function($u) { 
    return in_array($u['role'], ['admin', 'manager']); 
}));
?>

<!-- MANDATORY: Stats Row - دقیقاً مطابق استانداردها -->
<div class="stats-row">
    <div class="stat-card-pro">
        <div class="stat-label">کل کاربران</div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-users"></i>
            <span>ثبت‌نام‌شده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">کاربران فعال</div>
        <div class="stat-value"><?= number_format($activeUsers) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-user-check"></i>
            <span>دارای دسترسی</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">غیرفعال</div>
        <div class="stat-value"><?= number_format($inactiveUsers) ?></div>
        <div class="stat-change negative">
            <i class="fas fa-user-times"></i>
            <span>تعلیق شده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">مدیران</div>
        <div class="stat-value"><?= number_format($adminCount) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-user-shield"></i>
            <span>مدیر و ادمین</span>
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
                       placeholder="جستجو در کاربران... (مثال: احمد، admin، علی@example.com، 09123456789)"
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
                    نکته: با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید | ESC برای پاک کردن
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
        <!-- لیست کاربران -->
        <div class="table-container" id="resultsContainer">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-users"></i>
                    لیست کاربران
                    <span class="badge badge-primary ms-2" id="totalUsersCount"><?= count($users ?? []) ?></span>
                </h2>
                <div class="table-actions">
                    <a href="/?route=users&action=create" class="btn-icon btn-primary" title="کاربر جدید">
                        <i class="fas fa-plus"></i>
                    </a>
                    <button class="btn-icon" onclick="exportUsers()" title="دانلود لیست">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-icon" onclick="refreshUserList()" title="بروزرسانی">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            
            <?php if (!empty($users)): ?>
            <!-- جدول دسکتاپ -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">شناسه</th>
                        <th>کاربر</th>
                        <th>ایمیل</th>
                        <th>نقش</th>
                        <th>وضعیت</th>
                        <th>تگ‌ها</th>
                        <th>آخرین ورود</th>
                        <th class="text-center" style="width: 150px;">عملیات</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="text-center">
                            <code>#<?= $user['id'] ?></code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                    <?= mb_substr($user['full_name'], 0, 1) ?>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($user['full_name']) ?></div>
                                    <div class="text-muted small">@<?= htmlspecialchars($user['username']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($user['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                    <?= htmlspecialchars($user['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">
                                    <i class="fas fa-minus me-1"></i>
                                    ندارد
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= getRoleColor($user['role']) ?>">
                                <i class="<?= getRoleIcon($user['role']) ?> me-1"></i>
                                <?= $roles[$user['role']] ?? $user['role'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?= $user['status'] ?>" id="status-<?= $user['id'] ?>">
                                <i class="fas fa-<?= $user['status'] === 'active' ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                <?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?>
                            </span>
                        </td>
                        <td>
                            <div class="user-tags" id="user-tags-<?= $user['id'] ?>">
                                <?php if (!empty($user['tags'])): ?>
                                    <?php 
                                    $userTags = json_decode($user['tags'], true) ?: [];
                                    foreach ($userTags as $tagId):
                                        $tag = array_filter($tags, function($t) use ($tagId) { return $t['id'] == $tagId; });
                                        $tag = reset($tag);
                                        if ($tag):
                                    ?>
                                    <span class="tag-badge" style="
                                        background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>);
                                        color: <?= $tag['text_color'] ?>;
                                        padding: 2px 6px;
                                        border-radius: 8px;
                                        font-size: 10px;
                                        margin: 1px;
                                        display: inline-block;
                                    "><?= htmlspecialchars($tag['title']) ?></span>
                                    <?php endif; endforeach; ?>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-primary ms-1" 
                                        onclick="showAssignTagsModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['full_name']) ?>')"
                                        title="تخصیص تگ">
                                    <i class="fas fa-plus" style="font-size: 10px;"></i>
                                </button>
                            </div>
                        </td>
                        <td class="text-muted persian-num">
                            <?php if ($user['last_login']): ?>
                                <?= jdate('Y/m/d H:i', strtotime($user['last_login'])) ?>
                            <?php else: ?>
                                هرگز
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="/?route=users&action=show&id=<?= $user['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="مشاهده جزئیات">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                    <button type="button" 
                                            class="btn btn-outline-<?= $user['status'] === 'active' ? 'warning' : 'success' ?> btn-sm" 
                                            onclick="toggleUserStatus(<?= $user['id'] ?>)"
                                            title="<?= $user['status'] === 'active' ? 'غیرفعال کردن' : 'فعال کردن' ?>">
                                        <i class="fas fa-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                            title="حذف کاربر">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- لیست موبایل -->
            <div class="mobile-list" id="mobileUsersList">
                <?php foreach ($users as $user): ?>
                <div class="mobile-list-item">
                    <div class="mobile-item-main">
                        <div class="mobile-item-title"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="mobile-item-meta">
                            <span class="badge bg-<?= getRoleColor($user['role']) ?>"><?= $roles[$user['role']] ?? $user['role'] ?></span>
                            • <span class="status-badge <?= $user['status'] ?>"><?= $user['status'] === 'active' ? 'فعال' : 'غیرفعال' ?></span>
                        </div>
                        <div class="mobile-item-date">@<?= htmlspecialchars($user['username']) ?></div>
                    </div>
                    <div class="mobile-item-actions">
                        <a href="/?route=users&action=show&id=<?= $user['id'] ?>" class="btn-icon" title="مشاهده">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                        <button class="btn-icon" onclick="toggleUserStatus(<?= $user['id'] ?>)" title="تغییر وضعیت">
                            <i class="fas fa-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- پیام خالی -->
            <div style="text-align: center; padding: var(--space-8); color: var(--gray-500);">
                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: var(--space-4); opacity: 0.5;"></i>
                <h5>هیچ کاربری یافت نشد</h5>
                <p>هنوز کاربری در سیستم ثبت نشده است یا کاربری با این فیلترها وجود ندارد</p>
                <a href="/?route=users&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    ایجاد کاربر جدید
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
                <div class="task-item" onclick="location.href='/?route=users&action=create'" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-plus" style="color: var(--primary); margin-left: var(--space-2);"></i>
                        ایجاد کاربر جدید
                    </span>
                </div>
                
                <div class="task-item" onclick="exportUsers()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-download" style="color: var(--info); margin-left: var(--space-2);"></i>
                        خروجی Excel
                    </span>
                </div>
                
                <div class="task-item" onclick="refreshUserList()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-sync-alt" style="color: var(--success); margin-left: var(--space-2);"></i>
                        به‌روزرسانی لیست
                    </span>
                </div>
            </div>
        </div>

        <!-- آمار نقش‌ها -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-chart-pie"></i>
                    توزیع نقش‌ها
                </div>
            </div>
            <div class="panel-body">
                <?php 
                $roleCounts = [];
                foreach ($users ?? [] as $user) {
                    $roleCounts[$user['role']] = ($roleCounts[$user['role']] ?? 0) + 1;
                }
                ?>
                <?php foreach ($roles as $roleKey => $roleLabel): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">
                        <i class="<?= getRoleIcon($roleKey) ?> me-1"></i>
                        <?= $roleLabel ?>
                    </span>
                    <span style="font-weight: 600; color: var(--<?= getRoleColor($roleKey) ?>);"><?= $roleCounts[$roleKey] ?? 0 ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- مدال تخصیص تگ -->
<div class="modal fade" id="assignTagsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تخصیص تگ به کاربر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignTagsForm">
                    <input type="hidden" id="assignUserId" name="user_id">
                    <p>انتخاب تگ‌ها برای کاربر: <strong id="assignUserName"></strong></p>
                    
                    <div class="tags-grid" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($tags ?? [] as $tag): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="<?= $tag['id'] ?>" 
                                   id="tag-<?= $tag['id'] ?>" name="tag_ids[]">
                            <label class="form-check-label d-flex align-items-center" for="tag-<?= $tag['id'] ?>">
                                <span class="tag-preview me-2" 
                                      style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                             color: <?= $tag['text_color'] ?>;">
                                    <?= htmlspecialchars($tag['title']) ?>
                                </span>
                                <small class="text-muted"><?= $tag['usage_count'] ?> استفاده</small>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="button" class="btn btn-primary" onclick="assignTags()">تخصیص تگ‌ها</button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Advanced Search System for Users
 */
class UsersAdvancedSearch {
    constructor() {
        this.apiUrl = '/?route=users&action=api';
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
        this.totalUsersCount = document.getElementById('totalUsersCount');
        this.usersTableBody = document.getElementById('usersTableBody');
        this.mobileUsersList = document.getElementById('mobileUsersList');
        
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
            this.showLoading();
            
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
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.data, data.total, query, data.search_terms);
            } else {
                throw new Error(data.error || 'Search failed');
            }
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Search error:', error);
                this.showError('خطا در جستجو');
            }
        } finally {
            this.hideLoading();
            this.currentRequest = null;
        }
    }
    
    displayResults(results, total, query, searchTerms) {
        // Update statistics
        this.updateSearchStats(total, query, searchTerms);
        
        if (results.length === 0) {
            this.showNoResults();
            return;
        }
        
        // Render results
        this.renderResults(results);
        this.hideNoResults();
    }
    
    updateSearchStats(total, query, searchTerms) {
        this.searchResultCount.textContent = total.toLocaleString('fa-IR');
        this.totalUsersCount.textContent = total;
        
        if (query && searchTerms && searchTerms.length > 0) {
            this.searchTermsDisplay.innerHTML = ` برای "<strong>${query}</strong>" (${searchTerms.length} کلمه)`;
            this.searchStats.style.display = 'block';
        } else {
            this.searchStats.style.display = 'none';
        }
    }
    
    renderResults(results) {
        // Similar to tags implementation but for users
        let desktopHtml = '';
        let mobileHtml = '';
        
        results.forEach(user => {
            desktopHtml += this.renderUserRow(user);
            mobileHtml += this.renderMobileUserCard(user);
        });
        
        this.usersTableBody.innerHTML = desktopHtml;
        this.mobileUsersList.innerHTML = mobileHtml;
        
        // Re-bind event listeners for new content
        this.bindResultEvents();
    }
    
    renderUserRow(user) {
        const fullName = user.full_name_highlighted || user.full_name;
        const username = user.username_highlighted || user.username;
        const email = user.email_highlighted || user.email;
        
        return `
            <tr class="result-item" data-id="${user.id}">
                <td class="text-center">
                    <code>#${user.id}</code>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.875rem;">
                            ${user.full_name.substr(0, 1)}
                        </div>
                        <div>
                            <div class="fw-semibold">${fullName}</div>
                            <div class="text-muted small">@${username}</div>
                        </div>
                    </div>
                </td>
                <td>
                    ${user.email ? `<a href="mailto:${user.email}">${email}</a>` : '<span class="text-muted">ندارد</span>'}
                </td>
                <td>
                    <span class="badge bg-${this.getRoleColor(user.role)}">
                        ${user.role_label || user.role}
                    </span>
                </td>
                <td>
                    <span class="status-badge ${user.status}">
                        ${user.status === 'active' ? 'فعال' : 'غیرفعال'}
                    </span>
                </td>
                <td>-</td>
                <td>${user.last_login_formatted || 'هرگز'}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="/?route=users&action=show&id=${user.id}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    }
    
    renderMobileUserCard(user) {
        return `
            <div class="mobile-list-item">
                <div class="mobile-item-main">
                    <div class="mobile-item-title">${user.full_name_highlighted || user.full_name}</div>
                    <div class="mobile-item-meta">
                        <span class="badge bg-${this.getRoleColor(user.role)}">${user.role_label || user.role}</span>
                    </div>
                    <div class="mobile-item-date">@${user.username_highlighted || user.username}</div>
                </div>
                <div class="mobile-item-actions">
                    <a href="/?route=users&action=show&id=${user.id}" class="btn-icon">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        `;
    }
    
    getRoleColor(role) {
        const colors = {
            'admin': 'danger',
            'manager': 'primary',
            'accountant': 'info',
            'user': 'secondary'
        };
        return colors[role] || 'secondary';
    }
    
    // Other methods similar to tags implementation...
    clearSearch() {
        this.searchInput.value = '';
        this.toggleClearButton(false);
        this.performSearch('');
        this.searchInput.focus();
    }
    
    toggleClearButton(show) {
        this.clearButton.style.display = show ? 'block' : 'none';
    }
    
    showLoading() {
        this.loadingIndicator.style.display = 'block';
        this.resultsContainer.style.opacity = '0.5';
    }
    
    hideLoading() {
        this.loadingIndicator.style.display = 'none';
        this.resultsContainer.style.opacity = '1';
    }
    
    showNoResults() {
        this.noResultsMessage.style.display = 'block';
        this.resultsContainer.style.display = 'none';
    }
    
    hideNoResults() {
        this.noResultsMessage.style.display = 'none';
        this.resultsContainer.style.display = 'block';
    }
    
    loadInitialData() {
        this.performSearch('');
    }
    
    bindResultEvents() {
        // Re-bind any dynamic events
    }
    
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

// User Management Functions
function toggleUserStatus(userId) {
    if (!confirm('آیا از تغییر وضعیت این کاربر اطمینان دارید؟')) {
        return;
    }
    
    fetch(`/?route=users&action=toggleStatus&id=${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در تغییر وضعیت کاربر');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function deleteUser(userId, username) {
    if (!confirm(`آیا از حذف کاربر "${username}" اطمینان دارید؟`)) {
        return;
    }
    
    fetch(`/?route=users&action=delete&id=${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'خطا در حذف کاربر');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function showAssignTagsModal(userId, userName) {
    document.getElementById('assignUserId').value = userId;
    document.getElementById('assignUserName').textContent = userName;
    
    // Clear previous selections
    const checkboxes = document.querySelectorAll('#assignTagsModal input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    const modal = new bootstrap.Modal(document.getElementById('assignTagsModal'));
    modal.show();
}

function assignTags() {
    const form = document.getElementById('assignTagsForm');
    const formData = new FormData(form);
    
    fetch('/?route=users&action=assignTags', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignTagsModal'));
            modal.hide();
            location.reload();
        } else {
            alert(data.message || 'خطا در تخصیص تگ‌ها');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

function exportUsers() {
    window.open('/?route=users&action=export', '_blank');
}

function refreshUserList() {
    location.reload();
}

// Initialize search system
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('searchInput')) {
        window.usersSearch = new UsersAdvancedSearch();
        console.log('✅ Users Advanced Search System initialized');
    }
});
</script>

<?php
// Load main layout footer
require_once(APP_PATH . 'views/layouts/footer.php');
?> 