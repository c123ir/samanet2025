<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/tags/list.php
 * توضیح: صفحه مدیریت تگ‌ها - نسخه بهینه شده
 * تاریخ بازطراحی: 1404/10/17
 * نسخه: 3.0 یکپارچه
 */

// تنظیم متغیرهای صفحه
$page_title = 'مدیریت تگ‌ها';
$page_subtitle = 'ایجاد، ویرایش و مدیریت تگ‌های سیستم';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}

// پردازش آمار
$stats = $stats ?? [];
$tags = $tags ?? [];
$popularTags = $popular_tags ?? [];

// Load main layout
require_once(APP_PATH . 'views/layouts/main.php');
?>

<!-- Main Dashboard Content -->
<div class="dashboard-pro">
    <!-- محتوای اصلی -->
    <div class="dashboard-content">
        <!-- Flash Messages -->
        <?php if (isset($flash) && $flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" 
                 style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
                <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ردیف آمارهای اصلی -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل تگ‌ها</div>
                <div class="stat-value"><?= fa_num($stats['total_tags'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-tags"></i>
                    <span>ایجاد شده</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">در حال استفاده</div>
                <div class="stat-value"><?= fa_num($stats['used_tags'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-chart-line"></i>
                    <span>فعال</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">بدون استفاده</div>
                <div class="stat-value"><?= fa_num($stats['unused_tags'] ?? 0) ?></div>
                <div class="stat-change neutral">
                    <i class="fas fa-circle"></i>
                    <span>غیرفعال</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">بیشترین استفاده</div>
                <div class="stat-value"><?= fa_num($stats['max_usage'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-crown"></i>
                    <span>محبوب</span>
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
                <!-- لیست تگ‌ها -->
                <div class="table-container" id="resultsContainer">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-list"></i>
                            لیست تگ‌ها
                            <span class="badge badge-primary ms-2" id="totalTagsCount"><?= count($tags) ?></span>
                        </h2>
                        <div class="table-actions">
                            <a href="<?= url('tags?action=create') ?>" class="btn-icon btn-primary" title="تگ جدید">
                                <i class="fas fa-plus"></i>
                            </a>
                            <button class="btn-icon" onclick="exportTags()" title="دانلود لیست">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="btn-icon" onclick="refreshTagList()" title="بروزرسانی">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- جدول دسکتاپ -->
                    <table class="data-table">
                        <thead>
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
                            <?php foreach ($tags as $tag): ?>
                                <tr>
                                    <td class="text-center">
                                        <code>#<?= $tag['id'] ?></code>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($tag['title']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="tag-preview" 
                                              style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                     color: <?= $tag['text_color'] ?>;"
                                              title="<?= htmlspecialchars($tag['title']) ?>">
                                            <?= htmlspecialchars($tag['title']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $tag['usage_count'] > 0 ? 'success' : 'secondary' ?>">
                                            <?= $tag['usage_count'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($tag['creator_name'] ?? 'سیستم') ?></td>
                                    <td><?= jdate('Y/m/d', strtotime($tag['created_at'])) ?></td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="<?= url('tags?action=edit&id=' . $tag['id']) ?>" 
                                               class="btn-icon" title="ویرایش">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-icon text-danger" 
                                                    onclick="deleteTag(<?= $tag['id'] ?>)" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- لیست موبایل -->
                    <div class="mobile-list" id="mobileTagsList">
                        <?php foreach ($tags as $tag): ?>
                            <div class="mobile-list-item">
                                <div class="mobile-item-main">
                                    <div class="mobile-item-title"><?= htmlspecialchars($tag['title']) ?></div>
                                    <div class="mobile-item-meta">
                                        <span class="tag-preview small" 
                                              style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                     color: <?= $tag['text_color'] ?>;">
                                            <?= htmlspecialchars($tag['title']) ?>
                                        </span>
                                        • <span class="badge badge-<?= $tag['usage_count'] > 0 ? 'success' : 'secondary' ?>"><?= $tag['usage_count'] ?> استفاده</span>
                                    </div>
                                    <div class="mobile-item-date"><?= jdate('Y/m/d', strtotime($tag['created_at'])) ?></div>
                                </div>
                                <div class="mobile-item-actions">
                                    <a href="<?= url('tags?action=edit&id=' . $tag['id']) ?>" class="btn-icon" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn-icon text-danger" onclick="deleteTag(<?= $tag['id'] ?>)" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ستون جانبی -->
            <div class="side-column">
                <!-- محبوب‌ترین تگ‌ها -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-fire"></i>
                            محبوب‌ترین تگ‌ها
                        </div>
                        <span class="panel-badge"><?= count($popularTags) ?></span>
                    </div>
                    <div class="panel-body">
                        <?php if (!empty($popularTags)): ?>
                            <?php foreach (array_slice($popularTags, 0, 5) as $tag): ?>
                                <div class="popular-tag-item">
                                    <span class="tag-preview" 
                                          style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                 color: <?= $tag['text_color'] ?>;">
                                        <?= htmlspecialchars($tag['title']) ?>
                                    </span>
                                    <small class="text-muted"><?= $tag['usage_count'] ?> بار</small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-tags"></i>
                                <p>هنوز تگی استفاده نشده</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- عملیات سریع -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-bolt"></i>
                            عملیات سریع
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="quick-action" onclick="location.href='<?= url('tags?action=create') ?>'">
                            <div class="quick-action-icon bg-primary">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">ایجاد تگ جدید</div>
                                <div class="quick-action-desc">افزودن تگ به سیستم</div>
                            </div>
                        </div>
                        
                        <div class="quick-action" onclick="exportTags()">
                            <div class="quick-action-icon bg-success">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">دانلود لیست</div>
                                <div class="quick-action-desc">خروجی Excel تگ‌ها</div>
                            </div>
                        </div>
                        
                        <div class="quick-action" onclick="refreshTagList()">
                            <div class="quick-action-icon bg-info">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div class="quick-action-content">
                                <div class="quick-action-title">به‌روزرسانی</div>
                                <div class="quick-action-desc">بارگذاری مجدد لیست</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        this.mobileTagsList = document.getElementById('mobileTagsList');
        
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
        let desktopHtml = '';
        let mobileHtml = '';
        
        results.forEach(tag => {
            desktopHtml += this.renderTagRow(tag);
            mobileHtml += this.renderMobileTagCard(tag);
        });
        
        this.tagsTableBody.innerHTML = desktopHtml;
        this.mobileTagsList.innerHTML = mobileHtml;
        
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
                    <span class="tag-preview" 
                          style="background: linear-gradient(135deg, ${tag.color_start}, ${tag.color_end}); 
                                 color: ${tag.text_color};"
                          title="${tag.title}">
                        ${tag.title}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge badge-${tag.usage_count > 0 ? 'success' : 'secondary'}">
                        ${tag.usage_count}
                    </span>
                </td>
                <td>${creatorName}</td>
                <td>${tag.created_at_formatted}</td>
                <td class="text-center">
                    <div class="table-actions">
                        <a href="<?= url('tags?action=edit&id=') ?>${tag.id}" 
                           class="btn-icon" title="ویرایش">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn-icon text-danger" 
                                onclick="deleteTag(${tag.id})" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
    
    renderMobileTagCard(tag) {
        const title = tag.title_highlighted || tag.title;
        
        return `
            <div class="mobile-list-item result-item" data-id="${tag.id}">
                <div class="mobile-item-main">
                    <div class="mobile-item-title">${title}</div>
                    <div class="mobile-item-meta">
                        <span class="tag-preview small" 
                              style="background: linear-gradient(135deg, ${tag.color_start}, ${tag.color_end}); 
                                     color: ${tag.text_color};">
                            ${tag.title}
                        </span>
                        • <span class="badge badge-${tag.usage_count > 0 ? 'success' : 'secondary'}">${tag.usage_count} استفاده</span>
                    </div>
                    <div class="mobile-item-date">${tag.created_at_formatted}</div>
                </div>
                <div class="mobile-item-actions">
                    <a href="<?= url('tags?action=edit&id=') ?>${tag.id}" class="btn-icon" title="ویرایش">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn-icon text-danger" onclick="deleteTag(${tag.id})" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
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
        const firstResult = this.resultsContainer.querySelector('.result-item');
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
        // Add any additional event listeners for result items
        const resultItems = this.resultsContainer.querySelectorAll('.result-item');
        resultItems.forEach(item => {
            // Example: hover effects, click handlers, etc.
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

// Global Functions
function deleteTag(tagId) {
    if (confirmDelete('آیا از حذف این تگ اطمینان دارید؟')) {
        // AJAX delete request
        fetch('<?= url('tags?action=delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: tagId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('تگ با موفقیت حذف شد', 'success');
                window.searchSystem.performSearch(window.searchSystem.searchInput.value);
            } else {
                showAlert(data.message || 'خطا در حذف تگ', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('خطا در ارتباط با سرور', 'error');
        });
    }
}

function exportTags() {
    window.open('<?= url('tags?action=export') ?>', '_blank');
}

function refreshTagList() {
    if (window.searchSystem) {
        window.searchSystem.performSearch(window.searchSystem.searchInput.value);
        showAlert('لیست تگ‌ها بروزرسانی شد', 'success');
    }
}

// Initialize search system
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('searchInput')) {
        window.searchSystem = new TagsAdvancedSearch();
        console.log('✅ Tags Advanced Search System initialized');
    }
    
    // انیمیشن‌های تدریجی برای کارت‌های آمار
    const statCards = document.querySelectorAll('.stat-card-pro');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
});
</script>

<?php
// Load main layout footer
require_once(APP_PATH . 'views/layouts/footer.php');
?> 