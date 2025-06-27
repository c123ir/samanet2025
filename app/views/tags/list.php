<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/tags/list.php
 * توضیح: صفحه مدیریت تگ‌ها - Enterprise Grade (مطابق dashboard.css و users.css)
 * تاریخ ایجاد: 1404/10/15
 * نسخه: 2.0 حرفه‌ای
 */

// تنظیم متغیرهای صفحه
$page_title = $title ?? 'مدیریت تگ‌ها';
$page_description = 'ایجاد، ویرایش و مدیریت تگ‌های سیستم';
$active_menu = 'tags';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}

// پردازش آمار
$stats = $stats ?? [];
$tags = $tags ?? [];
$popularTags = $popular_tags ?? [];
$filters = $filters ?? [];
?>

<!-- Container -->
<div class="container-fluid p-0">
    <!-- Flash Messages -->
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show mx-3 mt-3" role="alert">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center p-3 pb-0">
        <div>
            <h1 class="h3 mb-1 text-gray-800">مدیریت تگ‌ها</h1>
            <p class="text-muted mb-0">ایجاد و مدیریت تگ‌های سیستم</p>
        </div>
        <div>
            <a href="<?= url('tags?action=create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>ایجاد تگ جدید
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 p-3">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1 opacity-75">کل تگ‌ها</div>
                            <div class="h5 mb-0"><?= number_format($stats['total_tags'] ?? 0) ?></div>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1 opacity-75">در حال استفاده</div>
                            <div class="h5 mb-0"><?= number_format($stats['used_tags'] ?? 0) ?></div>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1 opacity-75">بدون استفاده</div>
                            <div class="h5 mb-0"><?= number_format($stats['unused_tags'] ?? 0) ?></div>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1 opacity-75">بیشترین استفاده</div>
                            <div class="h5 mb-0"><?= number_format($stats['max_usage'] ?? 0) ?></div>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-crown fa-2x"></i>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

    <!-- Main Content -->
    <div class="row g-3 p-3 pt-0">
            <!-- Main Column -->
        <div class="col-xl-9">
            <!-- Advanced Search Section -->
            <div class="card mb-3">
                <div class="card-body">
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
                                       placeholder="جستجو در تگ‌ها... (مثال: فوری قرمز، 19 استفاده، سیستم 1404)"
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

            <!-- Tags Table -->
            <div class="card" id="resultsContainer">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        لیست تگ‌ها
                        <span class="badge bg-secondary ms-2" id="totalTagsCount"><?= count($tags) ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tagsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 70px;">شناسه</th>
                                <th>عنوان</th>
                                        <th style="width: 200px;">پیش‌نمایش</th>
                                        <th class="text-center" style="width: 120px;">تعداد استفاده</th>
                                <th>ایجاد کننده</th>
                                <th>تاریخ ایجاد</th>
                                        <th class="text-center" style="width: 100px;">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody id="tagsTableBody">
                                    <!-- نتایج به‌صورت داینامیک لود می‌شوند -->
                        </tbody>
                    </table>

                    </div>
                    </div>
                </div>
            </div>

        <!-- Sidebar -->
        <div class="col-xl-3">
            <!-- Popular Tags -->
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-fire text-danger me-2"></i>
                            محبوب‌ترین تگ‌ها
                    </h6>
                        </div>
                <div class="card-body">
                        <?php if (!empty($popularTags)): ?>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach (array_slice($popularTags, 0, 5) as $tag): ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge" 
                                              style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                     color: <?= $tag['text_color'] ?>; 
                                                 padding: 4px 12px; 
                                                 font-size: 12px;">
                                        <?= htmlspecialchars($tag['title']) ?>
                                        </span>
                                    <small class="text-muted"><?= $tag['usage_count'] ?> بار</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0 text-center">
                            <small>هنوز تگی استفاده نشده</small>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                            عملیات سریع
                    </h6>
                        </div>
                <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= url('tags?action=create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>ایجاد تگ جدید
                            </a>
                        <button onclick="exportTags()" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download me-2"></i>دانلود لیست
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Advanced Search System Styling */
.search-container {
    margin-bottom: var(--space-4, 1rem);
}

.search-input-wrapper {
    position: relative;
}

.search-input-wrapper .input-group-text {
    background: var(--gray-50, #f9fafb);
    border-color: var(--gray-200, #e5e7eb);
}

.search-input-wrapper .form-control {
    border-color: var(--gray-200, #e5e7eb);
    transition: all 0.15s ease;
}

.search-input-wrapper .form-control:focus {
    border-color: var(--primary, #5E3AEE);
    box-shadow: 0 0 0 0.2rem rgba(94, 58, 238, 0.1);
}

.search-help-text {
    margin-top: 0.5rem;
}

.search-stats {
    padding: 0.5rem 0.75rem;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 0.5rem;
    border-left: 3px solid #0d6efd;
}

/* Search highlighting */
mark {
    background: #fff3cd !important;
    color: #856404 !important;
    padding: 1px 2px !important;
    border-radius: 2px !important;
    font-weight: 500 !important;
}

[data-theme="dark"] mark {
    background: #664d03 !important;
    color: #ffed4a !important;
}

/* Empty state styling */
.empty-state {
    padding: 2rem;
}

.empty-state i {
    opacity: 0.5;
}

/* Loading state */
#loadingIndicator {
    color: var(--gray-600, #6b7280);
}

/* Result highlighting */
.highlight-result {
    background: rgba(94, 58, 238, 0.1) !important;
    border: 2px solid var(--primary, #5E3AEE) !important;
    border-radius: 0.75rem !important;
    transition: all 0.3s ease !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .search-input-wrapper {
        margin-bottom: 0.75rem;
    }
    
    .search-help-text {
        font-size: 0.75rem;
    }
    
    .search-input-wrapper .form-control {
        font-size: 16px; /* Prevent zoom on iOS */
        padding: 12px;
    }
}
</style>

<script>
/**
 * Advanced Search System for Tags
 */
class TagsAdvancedSearch {
    constructor() {
        this.apiUrl = '<?= url('tags?action=api') ?>';
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
        this.totalTagsCount = document.getElementById('totalTagsCount');
        this.tagsTableBody = document.getElementById('tagsTableBody');
        
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
                search: query,
                status: 'all'
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
        this.totalTagsCount.textContent = total;
        
        if (query && searchTerms && searchTerms.length > 0) {
            this.searchTermsDisplay.innerHTML = ` برای "<strong>${query}</strong>" (${searchTerms.length} کلمه)`;
            this.searchStats.style.display = 'block';
        } else {
            this.searchStats.style.display = 'none';
        }
    }
    
    renderResults(results) {
        let html = '';
        
        results.forEach(tag => {
            html += this.renderTagRow(tag);
        });
        
        this.tagsTableBody.innerHTML = html;
        
        // Re-bind event listeners for new content
        this.bindResultEvents();
    }
    
    renderTagRow(tag) {
        const title = tag.title_highlighted || tag.title;
        const creatorName = tag.creator_name_highlighted || tag.creator_name || 'نامشخص';
        
        return `
            <tr class="result-item" data-id="${tag.id}">
                <td class="text-center">
                    <code>#${tag.id}</code>
                </td>
                <td>
                    <strong>${title}</strong>
                </td>
                <td>
                    <span class="badge" 
                          style="background: linear-gradient(135deg, ${tag.color_start}, ${tag.color_end}); 
                                 color: ${tag.text_color}; 
                                 padding: 6px 16px; 
                                 font-size: 13px; 
                                 font-weight: 500;"
                          title="${tag.title}">
                        ${tag.title}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge bg-${tag.usage_count > 0 ? 'success' : 'secondary'}">
                        ${tag.usage_count}
                    </span>
                </td>
                <td>${creatorName}</td>
                <td>
                    <small class="text-muted">
                        ${this.formatDate(tag.created_at)}
                    </small>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="<?= url('tags?action=edit&id=') ?>${tag.id}" 
                           class="btn btn-outline-primary" title="ویرایش">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${tag.usage_count == 0 ? `
                            <button onclick="deleteTag(${tag.id})" 
                                    class="btn btn-outline-danger" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }
    
    formatDate(dateString) {
        // Convert to Jalali date format
        const date = new Date(dateString);
        const persianDate = new Intl.DateTimeFormat('fa-IR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
        return persianDate;
    }
    
    loadInitialData() {
        this.performSearch('');
    }
    
    clearSearch() {
        this.searchInput.value = '';
        this.toggleClearButton(false);
        this.performSearch('');
        this.searchInput.focus();
    }
    
    toggleClearButton(show) {
        this.clearButton.style.display = show ? 'block' : 'none';
    }
    
    navigateToFirstResult() {
        const firstResult = this.tagsTableBody.querySelector('.result-item');
        if (firstResult) {
            firstResult.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstResult.classList.add('highlight-result');
            setTimeout(() => {
                firstResult.classList.remove('highlight-result');
            }, 2000);
        }
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
    
    showError(message) {
        console.error(message);
        this.hideLoading();
    }
    
    bindResultEvents() {
        // Add hover effects for result items
        const resultItems = this.tagsTableBody.querySelectorAll('.result-item');
        resultItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.01)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
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

// Initialize Advanced Search System
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('searchInput')) {
        window.tagsSearch = new TagsAdvancedSearch();
        console.log('✅ Advanced Tags Search System initialized');
    }
});

// حذف تگ
function deleteTag(id) {
    if (!confirm('آیا از حذف این تگ اطمینان دارید؟')) {
        return;
    }
    
    fetch('<?= url('tags?action=delete') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh search instead of reloading page
            if (window.tagsSearch) {
                window.tagsSearch.performSearch(window.tagsSearch.searchInput.value);
            } else {
                location.reload();
            }
        } else {
            alert(data.message || 'خطا در حذف تگ');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در ارتباط با سرور');
    });
}

// دانلود لیست تگ‌ها
function exportTags() {
    alert('این قابلیت به زودی اضافه می‌شود');
}
</script> 