/**
 * کامپوننت جستجوی پیشرفته قابل استفاده مجدد
 * 
 * این کامپوننت امکان جستجوی چندکلمه‌ای real-time با highlighting
 * و تمام ویژگی‌های پیشرفته را برای هر صفحه‌ای فراهم می‌کند.
 * 
 * @author Cursor AI + Developer Team
 * @version 1.0
 * @since 2025-06-27
 */

class AdvancedSearch {
    /**
     * سازنده کلاس
     * @param {Object} config تنظیمات اولیه
     */
    constructor(config = {}) {
        // تنظیمات پیش‌فرض
        this.config = {
            apiUrl: '',
            containerSelector: '#searchContainer',
            resultsSelector: '#searchResults',
            debounceDelay: 200,
            placeholder: 'جستجو...',
            helpText: 'با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید | ESC برای پاک کردن',
            emptyStateMessage: 'نتیجه‌ای یافت نشد',
            enableStats: true,
            enableKeyboardShortcuts: true,
            highlightFields: [],
            customResultRenderer: null,
            onSearchStart: null,
            onSearchComplete: null,
            onError: null,
            ...config
        };
        
        // وضعیت جستجو
        this.currentRequest = null;
        this.isInitialized = false;
        
        // DOM elements
        this.elements = {};
        
        // آماده‌سازی
        this.init();
    }
    
    /**
     * مقداردهی اولیه
     */
    init() {
        if (!this.config.apiUrl) {
            console.error('AdvancedSearch: API URL is required');
            return;
        }
        
        this.createSearchBox();
        this.bindEvents();
        this.loadInitialData();
        this.isInitialized = true;
        
        console.log('✅ AdvancedSearch component initialized');
    }
    
    /**
     * ایجاد search box
     */
    createSearchBox() {
        const container = document.querySelector(this.config.containerSelector);
        if (!container) {
            console.error(`AdvancedSearch: Container ${this.config.containerSelector} not found`);
            return;
        }
        
        // HTML template
        const html = `
            <div class="advanced-search-container">
                <div class="search-input-wrapper">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               id="advancedSearchInput" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="${this.escapeHtml(this.config.placeholder)}"
                               autocomplete="off">
                        <button class="btn btn-outline-secondary border-start-0" 
                                id="advancedSearchClear" 
                                type="button" 
                                style="display: none;"
                                title="پاک کردن جستجو">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="search-help-text">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            ${this.escapeHtml(this.config.helpText)}
                        </small>
                    </div>
                </div>
                ${this.config.enableStats ? `
                <div class="search-stats mt-2" id="advancedSearchStats" style="display: none;">
                    <small class="text-info">
                        <i class="fas fa-chart-bar me-1"></i>
                        <span id="advancedSearchCount">0</span> نتیجه یافت شد
                        <span id="advancedSearchTerms"></span>
                    </small>
                </div>` : ''}
            </div>
            
            <!-- Loading indicator -->
            <div id="advancedSearchLoading" class="text-center p-4" style="display: none;">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                در حال جستجو...
            </div>

            <!-- No results message -->
            <div id="advancedSearchEmpty" class="text-center p-5" style="display: none;">
                <div class="empty-state">
                    <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">نتیجه‌ای یافت نشد</h5>
                    <p class="text-muted">
                        ${this.escapeHtml(this.config.emptyStateMessage)}<br>
                        لطفاً کلمات کلیدی دیگری امتحان کنید.
                    </p>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
        // ذخیره references
        this.elements = {
            container: container,
            searchInput: document.getElementById('advancedSearchInput'),
            clearButton: document.getElementById('advancedSearchClear'),
            statsContainer: document.getElementById('advancedSearchStats'),
            resultCount: document.getElementById('advancedSearchCount'),
            searchTerms: document.getElementById('advancedSearchTerms'),
            loadingIndicator: document.getElementById('advancedSearchLoading'),
            emptyMessage: document.getElementById('advancedSearchEmpty'),
            resultsContainer: document.querySelector(this.config.resultsSelector)
        };
    }
    
    /**
     * اتصال event listeners
     */
    bindEvents() {
        if (!this.elements.searchInput) return;
        
        // Real-time search با debouncing
        this.elements.searchInput.addEventListener('input', this.debounce((e) => {
            const query = e.target.value.trim();
            this.performSearch(query);
            this.toggleClearButton(query);
        }, this.config.debounceDelay));
        
        // دکمه پاک کردن
        if (this.elements.clearButton) {
            this.elements.clearButton.addEventListener('click', () => {
                this.clearSearch();
            });
        }
        
        if (this.config.enableKeyboardShortcuts) {
            // Enter key navigation
            this.elements.searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.navigateToFirstResult();
                }
                
                if (e.key === 'Escape') {
                    e.preventDefault();
                    this.clearSearch();
                    this.elements.searchInput.blur();
                }
            });
            
            // Global ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.elements.searchInput.value && 
                    document.activeElement !== this.elements.searchInput) {
                    e.preventDefault();
                    this.clearSearch();
                }
            });
        }
    }
    
    /**
     * انجام جستجو
     * @param {string} query عبارت جستجو
     */
    async performSearch(query = '') {
        try {
            // لغو درخواست قبلی
            if (this.currentRequest) {
                this.currentRequest.abort();
            }
            
            // callback شروع جستجو
            if (this.config.onSearchStart) {
                this.config.onSearchStart(query);
            }
            
            // نمایش loading
            this.showLoading();
            
            // ایجاد درخواست جدید
            this.currentRequest = new AbortController();
            
            // ساخت URL
            const searchParams = new URLSearchParams({
                search: query,
                status: 'all'
            });
            
            const response = await fetch(`${this.config.apiUrl}&${searchParams}`, {
                signal: this.currentRequest.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.data, data.total, query, data.search_terms);
                
                // callback تکمیل جستجو
                if (this.config.onSearchComplete) {
                    this.config.onSearchComplete(data, query);
                }
            } else {
                throw new Error(data.error || 'Search failed');
            }
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('AdvancedSearch error:', error);
                this.showError('خطا در جستجو');
                
                // callback خطا
                if (this.config.onError) {
                    this.config.onError(error, query);
                }
            }
        } finally {
            this.hideLoading();
            this.currentRequest = null;
        }
    }
    
    /**
     * نمایش نتایج
     * @param {Array} results نتایج
     * @param {number} total تعداد کل
     * @param {string} query عبارت جستجو
     * @param {Array} searchTerms کلمات جستجو
     */
    displayResults(results, total, query, searchTerms) {
        // به‌روزرسانی آمار
        this.updateSearchStats(total, query, searchTerms);
        
        if (results.length === 0) {
            this.showNoResults();
            return;
        }
        
        // رندر نتایج
        this.renderResults(results);
        this.hideNoResults();
    }
    
    /**
     * به‌روزرسانی آمار جستجو
     * @param {number} total تعداد
     * @param {string} query عبارت جستجو
     * @param {Array} searchTerms کلمات
     */
    updateSearchStats(total, query, searchTerms) {
        if (!this.config.enableStats || !this.elements.resultCount) return;
        
        this.elements.resultCount.textContent = total.toLocaleString('fa-IR');
        
        if (query && searchTerms && searchTerms.length > 0) {
            this.elements.searchTerms.innerHTML = ` برای "<strong>${this.escapeHtml(query)}</strong>" (${searchTerms.length} کلمه)`;
            this.elements.statsContainer.style.display = 'block';
        } else {
            this.elements.statsContainer.style.display = 'none';
        }
    }
    
    /**
     * رندر نتایج
     * @param {Array} results نتایج
     */
    renderResults(results) {
        if (!this.elements.resultsContainer) {
            console.warn('AdvancedSearch: Results container not found');
            return;
        }
        
        let html = '';
        
        if (this.config.customResultRenderer) {
            // استفاده از renderer سفارشی
            html = this.config.customResultRenderer(results);
        } else {
            // renderer پیش‌فرض
            html = this.defaultResultRenderer(results);
        }
        
        this.elements.resultsContainer.innerHTML = html;
        
        // اتصال event listeners جدید
        this.bindResultEvents();
    }
    
    /**
     * renderer پیش‌فرض برای نتایج
     * @param {Array} results نتایج
     * @returns {string} HTML
     */
    defaultResultRenderer(results) {
        return results.map(item => `
            <div class="result-item p-3 border-bottom" data-id="${item.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${item.title_highlighted || item.title || 'بدون عنوان'}</h6>
                        <small class="text-muted">
                            شناسه: #${item.id} | 
                            تاریخ: ${this.formatDate(item.created_at)}
                        </small>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    /**
     * بارگذاری داده‌های اولیه
     */
    loadInitialData() {
        this.performSearch('');
    }
    
    /**
     * پاک کردن جستجو
     */
    clearSearch() {
        if (!this.elements.searchInput) return;
        
        this.elements.searchInput.value = '';
        this.toggleClearButton(false);
        this.performSearch('');
        this.elements.searchInput.focus();
    }
    
    /**
     * تغییر وضعیت دکمه پاک کردن
     * @param {boolean|string} show نمایش یا مخفی کردن
     */
    toggleClearButton(show) {
        if (!this.elements.clearButton) return;
        this.elements.clearButton.style.display = show ? 'block' : 'none';
    }
    
    /**
     * رفتن به اولین نتیجه
     */
    navigateToFirstResult() {
        if (!this.elements.resultsContainer) return;
        
        const firstResult = this.elements.resultsContainer.querySelector('.result-item');
        if (firstResult) {
            firstResult.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstResult.classList.add('highlight-result');
            setTimeout(() => {
                firstResult.classList.remove('highlight-result');
            }, 2000);
        }
    }
    
    /**
     * نمایش loading
     */
    showLoading() {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = 'block';
        }
        if (this.elements.resultsContainer) {
            this.elements.resultsContainer.style.opacity = '0.5';
        }
    }
    
    /**
     * مخفی کردن loading
     */
    hideLoading() {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = 'none';
        }
        if (this.elements.resultsContainer) {
            this.elements.resultsContainer.style.opacity = '1';
        }
    }
    
    /**
     * نمایش پیام عدم وجود نتیجه
     */
    showNoResults() {
        if (this.elements.emptyMessage) {
            this.elements.emptyMessage.style.display = 'block';
        }
        if (this.elements.resultsContainer) {
            this.elements.resultsContainer.style.display = 'none';
        }
    }
    
    /**
     * مخفی کردن پیام عدم وجود نتیجه
     */
    hideNoResults() {
        if (this.elements.emptyMessage) {
            this.elements.emptyMessage.style.display = 'none';
        }
        if (this.elements.resultsContainer) {
            this.elements.resultsContainer.style.display = 'block';
        }
    }
    
    /**
     * نمایش خطا
     * @param {string} message پیام خطا
     */
    showError(message) {
        console.error('AdvancedSearch:', message);
        this.hideLoading();
    }
    
    /**
     * اتصال event listeners برای نتایج
     */
    bindResultEvents() {
        if (!this.elements.resultsContainer) return;
        
        const resultItems = this.elements.resultsContainer.querySelectorAll('.result-item');
        resultItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.transition = 'transform 0.2s ease';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    }
    
    /**
     * فرمت کردن تاریخ
     * @param {string} dateString رشته تاریخ
     * @returns {string} تاریخ فرمت شده
     */
    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('fa-IR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        } catch {
            return dateString;
        }
    }
    
    /**
     * Escape کردن HTML
     * @param {string} text متن
     * @returns {string} متن escape شده
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Debounce utility
     * @param {Function} func تابع
     * @param {number} wait زمان انتظار
     * @returns {Function} تابع debounce شده
     */
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
    
    /**
     * به‌روزرسانی تنظیمات
     * @param {Object} newConfig تنظیمات جدید
     */
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        
        if (this.isInitialized) {
            // اعمال تغییرات فوری
            if (newConfig.placeholder && this.elements.searchInput) {
                this.elements.searchInput.placeholder = newConfig.placeholder;
            }
        }
    }
    
    /**
     * دریافت تنظیمات فعلی
     * @returns {Object} تنظیمات
     */
    getConfig() {
        return { ...this.config };
    }
    
    /**
     * دریافت وضعیت فعلی
     * @returns {Object} وضعیت
     */
    getState() {
        return {
            query: this.elements.searchInput?.value || '',
            isSearching: this.currentRequest !== null,
            isInitialized: this.isInitialized
        };
    }
    
    /**
     * تنظیم فیلتر سریع
     * @param {string} filterName نام فیلتر
     * @param {string} value مقدار فیلتر
     */
    setQuickFilter(filterName, value) {
        // اگر فیلتر در DOM موجود است، آن را تنظیم کن
        const filterElement = document.getElementById(filterName + 'Filter');
        if (filterElement) {
            filterElement.value = value;
            // trigger change event
            filterElement.dispatchEvent(new Event('change'));
        }
        
        // انجام جستجو مجدد
        this.performSearch(this.elements.searchInput?.value || '');
    }
    
    /**
     * پاک کردن همه فیلترها
     */
    clearFilters() {
        // پاک کردن فیلترهای status و priority
        const statusFilter = document.getElementById('statusFilter');
        const priorityFilter = document.getElementById('priorityFilter');
        
        if (statusFilter) {
            statusFilter.value = '';
            statusFilter.dispatchEvent(new Event('change'));
        }
        
        if (priorityFilter) {
            priorityFilter.value = '';
            priorityFilter.dispatchEvent(new Event('change'));
        }
        
        // پاک کردن جستجو
        this.clearSearch();
    }
    
    /**
     * تنظیم فیلتر (برای استفاده عمومی)
     * @param {string} name نام فیلتر
     * @param {string} value مقدار
     */
    setFilter(name, value) {
        this.setQuickFilter(name, value);
    }

    /**
     * نابودی کامپوننت
     */
    destroy() {
        // لغو درخواست‌های در حال انجام
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        
        // پاک کردن DOM
        if (this.elements.container) {
            this.elements.container.innerHTML = '';
        }
        
        // پاک کردن references
        this.elements = {};
        this.isInitialized = false;
        
        console.log('✅ AdvancedSearch component destroyed');
    }
}

/**
 * Factory function برای ایجاد آسان instance
 * @param {Object} config تنظیمات
 * @returns {AdvancedSearch} instance جدید
 */
function createAdvancedSearch(config) {
    return new AdvancedSearch(config);
}

// Export برای استفاده در ماژول‌ها
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AdvancedSearch, createAdvancedSearch };
}

// Global access
window.AdvancedSearch = AdvancedSearch;
window.createAdvancedSearch = createAdvancedSearch; 