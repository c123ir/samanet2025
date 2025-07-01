/**
 * نام فایل: requests-advanced-search.js
 * مسیر: /assets/js/requests-advanced-search.js
 * توضیح: منطق جستجوی پیشرفته و فیلترینگ برای صفحه درخواست‌ها
 * تاریخ ایجاد: 1404/10/31
 * نسخه: 2.0 Enterprise Grade
 */

class RequestsAdvancedSearch {
    constructor() {
        this.apiUrl = '/?route=requests&action=api';
        this.debounceDelay = 300;
        this.currentRequest = null;
        this.currentPage = 1;
        this.totalPages = 1;
        this.isLoading = false;
        
        // DOM elements
        this.searchInput = document.getElementById('searchInput');
        this.clearButton = document.getElementById('clearSearch');
        this.statusFilter = document.getElementById('statusFilter');
        this.priorityFilter = document.getElementById('priorityFilter');
        this.applyFiltersBtn = document.getElementById('applyFilters');
        this.resetFiltersBtn = document.getElementById('resetFilters');
        this.clearAllFiltersBtn = document.getElementById('clearAllFilters');
        this.resultsContainer = document.getElementById('resultsContainer');
        this.loadingIndicator = document.getElementById('loadingIndicator');
        this.noResultsMessage = document.getElementById('noResultsMessage');
        this.searchResultsInfo = document.getElementById('searchResultsInfo');
        this.searchResultsCount = document.getElementById('searchResultsCount');
        this.searchTermsDisplay = document.getElementById('searchTermsDisplay');
        this.searchTerms = document.getElementById('searchTerms');
        this.totalRequestsCount = document.getElementById('totalRequestsCount');
        this.requestsTableBody = document.getElementById('requestsTableBody');
        this.mobileCardsContainer = document.getElementById('mobileCardsContainer');
        this.desktopTable = document.getElementById('desktopTable');
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateClearButton();
        this.checkInitialFilters();
        this.setupKeyboardShortcuts();
    }
    
    bindEvents() {
        // Search input with debounce
        let searchTimeout;
        this.searchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.currentPage = 1; // Reset to first page on new search
                this.performSearch();
            }, this.debounceDelay);
            this.updateClearButton();
        });
        
        // Clear search button
        this.clearButton?.addEventListener('click', () => {
            this.searchInput.value = '';
            this.updateClearButton();
            this.currentPage = 1;
            this.performSearch();
        });
        
        // Filter change events
        this.statusFilter?.addEventListener('change', () => {
            this.currentPage = 1;
            this.performSearch();
        });
        
        this.priorityFilter?.addEventListener('change', () => {
            this.currentPage = 1;
            this.performSearch();
        });
        
        // Filter buttons
        this.applyFiltersBtn?.addEventListener('click', () => {
            this.currentPage = 1;
            this.performSearch();
        });
        
        this.resetFiltersBtn?.addEventListener('click', () => this.resetAllFilters());
        this.clearAllFiltersBtn?.addEventListener('click', () => this.resetAllFilters());
        
        // Enter key support
        this.searchInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.currentPage = 1;
                this.performSearch();
            }
        });
        
        // Form submit prevention
        const searchForm = this.searchInput?.closest('form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.currentPage = 1;
                this.performSearch();
            });
        }
    }
    
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+F or Cmd+F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                this.searchInput?.focus();
            }
            
            // Escape to clear search
            if (e.key === 'Escape' && document.activeElement === this.searchInput) {
                this.resetAllFilters();
            }
        });
    }
    
    updateClearButton() {
        if (!this.clearButton) return;
        
        if (this.searchInput?.value.trim()) {
            this.clearButton.classList.add('show');
        } else {
            this.clearButton.classList.remove('show');
        }
    }
    
    checkInitialFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('search') || urlParams.get('status') || urlParams.get('priority')) {
            // Set values from URL
            if (this.searchInput) this.searchInput.value = urlParams.get('search') || '';
            if (this.statusFilter) this.statusFilter.value = urlParams.get('status') || '';
            if (this.priorityFilter) this.priorityFilter.value = urlParams.get('priority') || '';
            
            this.updateClearButton();
            this.performSearch();
        }
    }
    
    async performSearch() {
        if (this.isLoading) return;
        
        try {
            // Cancel previous request
            if (this.currentRequest) {
                this.currentRequest.abort();
            }
            
            // Show loading
            this.showLoading();
            this.isLoading = true;
            
            // Collect search parameters
            const searchData = {
                search: this.searchInput?.value.trim() || '',
                status: this.statusFilter?.value || '',
                priority: this.priorityFilter?.value || '',
                page: this.currentPage,
                per_page: 20
            };
            
            // Update URL without refresh
            this.updateUrl(searchData);
            
            // Create request
            this.currentRequest = new AbortController();
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                body: JSON.stringify(searchData),
                signal: this.currentRequest.signal
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.displayResults(result.data || [], result.meta || {});
                this.updateSearchInfo(searchData, result.meta || {});
                this.updatePagination(result.meta || {});
            } else {
                this.showError(result.message || 'خطا در دریافت نتایج');
            }
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Search error:', error);
                this.showError('خطا در ارتباط با سرور');
            }
        } finally {
            this.hideLoading();
            this.isLoading = false;
            this.currentRequest = null;
        }
    }
    
    updateUrl(searchData) {
        const url = new URL(window.location);
        
        // Remove existing search params
        url.searchParams.delete('search');
        url.searchParams.delete('status');
        url.searchParams.delete('priority');
        url.searchParams.delete('page');
        
        // Add new params
        if (searchData.search) url.searchParams.set('search', searchData.search);
        if (searchData.status) url.searchParams.set('status', searchData.status);
        if (searchData.priority) url.searchParams.set('priority', searchData.priority);
        if (searchData.page > 1) url.searchParams.set('page', searchData.page);
        
        // Update URL without refresh
        window.history.replaceState({}, '', url);
    }
    
    displayResults(requests, meta) {
        // Update desktop table
        this.updateDesktopTable(requests);
        
        // Update mobile cards
        this.updateMobileCards(requests);
        
        // Update counters
        if (this.totalRequestsCount) {
            this.totalRequestsCount.textContent = requests.length;
        }
        
        // Show/hide no results
        if (requests.length === 0) {
            this.showNoResults();
        } else {
            this.hideNoResults();
        }
        
        // Scroll to top of results on new search
        if (this.currentPage === 1) {
            this.resultsContainer?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    
    updateDesktopTable(requests) {
        if (!this.requestsTableBody) return;
        
        if (requests.length === 0) {
            this.requestsTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-search mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                            <div>نتیجه‌ای یافت نشد</div>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        this.requestsTableBody.innerHTML = requests.map(request => this.createTableRow(request)).join('');
    }
    
    updateMobileCards(requests) {
        if (!this.mobileCardsContainer) return;
        
        if (requests.length === 0) {
            this.mobileCardsContainer.innerHTML = `
                <div class="no-results show">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="no-results-title">نتیجه‌ای یافت نشد</div>
                    <div class="no-results-message">لطفاً کلمات کلیدی یا فیلترهای مختلفی امتحان کنید</div>
                </div>
            `;
            return;
        }
        
        this.mobileCardsContainer.innerHTML = requests.map(request => this.createMobileCard(request)).join('');
    }
    
    createTableRow(request) {
        const statusMap = {
            'pending': { class: 'bg-warning text-dark', text: 'در انتظار' },
            'processing': { class: 'bg-info text-white', text: 'در حال بررسی' },
            'completed': { class: 'bg-success text-white', text: 'تکمیل شده' },
            'rejected': { class: 'bg-danger text-white', text: 'رد شده' }
        };
        
        const priorityMap = {
            'urgent': { class: 'bg-danger text-white', text: 'فوری', icon: 'fas fa-exclamation-triangle' },
            'high': { class: 'bg-warning text-dark', text: 'بالا', icon: 'fas fa-arrow-up' },
            'normal': { class: 'bg-primary text-white', text: 'معمولی', icon: 'fas fa-minus' },
            'low': { class: 'bg-secondary text-white', text: 'پایین', icon: 'fas fa-arrow-down' }
        };
        
        const status = statusMap[request.status] || { class: 'bg-secondary text-white', text: 'نامشخص' };
        const priority = priorityMap[request.priority || 'normal'] || priorityMap['normal'];
        
        // Use highlighted versions if available
        const title = request.title_highlighted || this.escapeHtml(request.title);
        const accountHolder = request.account_holder_highlighted || this.escapeHtml(request.account_holder);
        const referenceNumber = request.reference_number_highlighted || this.escapeHtml(request.reference_number || '#' + request.id);
        
        return `
            <tr data-request-id="${request.id}" class="table-row-animated">
                <td class="text-center">
                    <code class="request-id">#${request.id}</code>
                </td>
                <td>
                    <span class="text-primary fw-bold">${referenceNumber}</span>
                </td>
                <td>
                    <div class="fw-bold">${title}</div>
                    ${request.description ? `<small class="text-muted">${this.escapeHtml(request.description.substring(0, 50))}...</small>` : ''}
                </td>
                <td>
                    <span class="text-success fw-bold persian-num">
                        ${this.formatNumber(request.amount)} ریال
                    </span>
                </td>
                <td>
                    <div>${accountHolder}</div>
                    ${request.account_number ? `<small class="text-muted">${this.escapeHtml(request.account_number)}</small>` : ''}
                </td>
                <td>
                    <span class="badge ${status.class}">${status.text}</span>
                </td>
                <td>
                    <span class="badge ${priority.class}">
                        <i class="${priority.icon} me-1"></i>
                        ${priority.text}
                    </span>
                </td>
                <td>
                    <div class="small">${request.created_at_jalali || new Date().toLocaleDateString('fa-IR')}</div>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="?route=requests&action=show&id=${request.id}" 
                           class="btn btn-outline-primary btn-sm" title="مشاهده">
                            <i class="fas fa-eye"></i>
                        </a>
                        ${request.status === 'pending' ? `
                        <a href="?route=requests&action=edit&id=${request.id}" 
                           class="btn btn-outline-info btn-sm" title="ویرایش">
                            <i class="fas fa-edit"></i>
                        </a>` : ''}
                        <button type="button" 
                                class="btn btn-outline-success btn-sm" 
                                onclick="approveRequest(${request.id})" 
                                title="تایید">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
    
    createMobileCard(request) {
        const statusMap = {
            'pending': 'در انتظار',
            'processing': 'در حال بررسی', 
            'completed': 'تکمیل شده',
            'rejected': 'رد شده'
        };
        
        const priorityMap = {
            'urgent': { text: 'فوری', icon: 'fas fa-exclamation-triangle' },
            'high': { text: 'بالا', icon: 'fas fa-arrow-up' },
            'normal': { text: 'معمولی', icon: 'fas fa-minus' },
            'low': { text: 'پایین', icon: 'fas fa-arrow-down' }
        };
        
        const priority = priorityMap[request.priority || 'normal'] || priorityMap['normal'];
        
        // Use highlighted versions if available
        const title = request.title_highlighted || this.escapeHtml(request.title);
        const accountHolder = request.account_holder_highlighted || this.escapeHtml(request.account_holder);
        const referenceNumber = request.reference_number_highlighted || this.escapeHtml(request.reference_number || '#' + request.id);
        
        return `
            <div class="request-card animate-fade-in" data-request-id="${request.id}">
                <div class="request-card-header">
                    <div class="request-card-id">#${request.id}</div>
                    <div class="request-card-status ${request.status || 'pending'}">
                        ${statusMap[request.status] || 'نامشخص'}
                    </div>
                </div>
                
                <div class="request-card-title">
                    ${title}
                </div>
                
                <div class="request-card-meta">
                    <div class="request-card-meta-item">
                        <div class="request-card-meta-label">مرجع</div>
                        <div class="request-card-meta-value">${referenceNumber}</div>
                    </div>
                    
                    <div class="request-card-meta-item">
                        <div class="request-card-meta-label">مبلغ</div>
                        <div class="request-card-meta-value request-card-amount">
                            ${this.formatNumber(request.amount)} ریال
                        </div>
                    </div>
                    
                    <div class="request-card-meta-item">
                        <div class="request-card-meta-label">صاحب حساب</div>
                        <div class="request-card-meta-value">${accountHolder}</div>
                    </div>
                    
                    <div class="request-card-meta-item">
                        <div class="request-card-meta-label">اولویت</div>
                        <div class="request-card-meta-value">
                            <span class="request-card-priority ${request.priority || 'normal'}">
                                <i class="${priority.icon}"></i> ${priority.text}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="request-card-actions">
                    <a href="?route=requests&action=show&id=${request.id}" 
                       class="request-card-btn primary">
                        <i class="fas fa-eye"></i>
                        مشاهده
                    </a>
                    ${request.status === 'pending' ? `
                    <a href="?route=requests&action=edit&id=${request.id}" 
                       class="request-card-btn">
                        <i class="fas fa-edit"></i>
                        ویرایش
                    </a>` : ''}
                    <button type="button" 
                            class="request-card-btn" 
                            onclick="approveRequest(${request.id})">
                        <i class="fas fa-check"></i>
                        تایید
                    </button>
                </div>
            </div>
        `;
    }
    
    updateSearchInfo(searchData, meta) {
        if (!this.searchResultsInfo) return;
        
        const hasFilters = searchData.search || searchData.status || searchData.priority;
        
        if (hasFilters) {
            this.searchResultsInfo.style.display = 'flex';
            if (this.searchResultsCount) {
                this.searchResultsCount.textContent = this.formatNumber(meta.total || 0);
            }
            
            if (searchData.search && this.searchTermsDisplay && this.searchTerms) {
                this.searchTermsDisplay.style.display = 'inline';
                this.searchTerms.textContent = searchData.search;
            } else if (this.searchTermsDisplay) {
                this.searchTermsDisplay.style.display = 'none';
            }
        } else {
            this.searchResultsInfo.style.display = 'none';
        }
    }
    
    updatePagination(meta) {
        this.currentPage = meta.page || 1;
        this.totalPages = meta.last_page || 1;
        
        // Create pagination if more than one page
        if (this.totalPages > 1) {
            this.createPagination(meta);
        } else {
            this.removePagination();
        }
    }
    
    createPagination(meta) {
        // Remove existing pagination
        this.removePagination();
        
        // Create pagination container
        const paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container d-flex justify-content-center mt-4';
        paginationContainer.id = 'paginationContainer';
        
        const pagination = document.createElement('nav');
        pagination.setAttribute('aria-label', 'صفحه‌بندی نتایج');
        
        const paginationList = document.createElement('ul');
        paginationList.className = 'pagination';
        
        // Previous button
        if (this.currentPage > 1) {
            paginationList.appendChild(this.createPaginationItem('قبلی', this.currentPage - 1, false));
        }
        
        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            paginationList.appendChild(this.createPaginationItem(i, i, i === this.currentPage));
        }
        
        // Next button
        if (this.currentPage < this.totalPages) {
            paginationList.appendChild(this.createPaginationItem('بعدی', this.currentPage + 1, false));
        }
        
        pagination.appendChild(paginationList);
        paginationContainer.appendChild(pagination);
        
        // Add to DOM
        this.resultsContainer?.appendChild(paginationContainer);
    }
    
    createPaginationItem(text, page, isActive) {
        const li = document.createElement('li');
        li.className = `page-item ${isActive ? 'active' : ''}`;
        
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = text;
        
        if (!isActive) {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = page;
                this.performSearch();
            });
        }
        
        li.appendChild(a);
        return li;
    }
    
    removePagination() {
        const existingPagination = document.getElementById('paginationContainer');
        if (existingPagination) {
            existingPagination.remove();
        }
    }
    
    resetAllFilters() {
        if (this.searchInput) this.searchInput.value = '';
        if (this.statusFilter) this.statusFilter.value = '';
        if (this.priorityFilter) this.priorityFilter.value = '';
        
        this.updateClearButton();
        this.currentPage = 1;
        this.performSearch();
    }
    
    showLoading() {
        if (this.loadingIndicator) {
            this.loadingIndicator.classList.add('show');
        }
        this.hideNoResults();
        
        if (this.desktopTable) this.desktopTable.style.opacity = '0.5';
        if (this.mobileCardsContainer) this.mobileCardsContainer.style.opacity = '0.5';
        
        // Disable form elements
        [this.searchInput, this.statusFilter, this.priorityFilter, this.applyFiltersBtn, this.resetFiltersBtn]
            .forEach(element => {
                if (element) element.disabled = true;
            });
    }
    
    hideLoading() {
        if (this.loadingIndicator) {
            this.loadingIndicator.classList.remove('show');
        }
        
        if (this.desktopTable) this.desktopTable.style.opacity = '1';
        if (this.mobileCardsContainer) this.mobileCardsContainer.style.opacity = '1';
        
        // Enable form elements
        [this.searchInput, this.statusFilter, this.priorityFilter, this.applyFiltersBtn, this.resetFiltersBtn]
            .forEach(element => {
                if (element) element.disabled = false;
            });
    }
    
    showNoResults() {
        if (this.noResultsMessage) {
            this.noResultsMessage.classList.add('show');
        }
    }
    
    hideNoResults() {
        if (this.noResultsMessage) {
            this.noResultsMessage.classList.remove('show');
        }
    }
    
    showError(message) {
        console.error('Search error:', message);
        
        // Create toast notification
        this.showToast(message, 'error');
    }
    
    showToast(message, type = 'info') {
        // Create toast container if not exists
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `toast show`;
        toast.setAttribute('role', 'alert');
        
        const typeClasses = {
            'error': 'text-bg-danger',
            'success': 'text-bg-success',
            'warning': 'text-bg-warning',
            'info': 'text-bg-info'
        };
        
        toast.classList.add(typeClasses[type] || typeClasses.info);
        
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">اعلان</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${this.escapeHtml(message)}
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
        
        // Manual close
        const closeBtn = toast.querySelector('.btn-close');
        closeBtn?.addEventListener('click', () => toast.remove());
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }
    
    formatNumber(num) {
        return new Intl.NumberFormat('fa-IR').format(num || 0);
    }
    
    // Public method to trigger search from outside
    search(term = '') {
        if (this.searchInput) {
            this.searchInput.value = term;
        }
        this.updateClearButton();
        this.currentPage = 1;
        this.performSearch();
    }
    
    // Public method to set filters
    setFilters(filters = {}) {
        if (filters.status && this.statusFilter) {
            this.statusFilter.value = filters.status;
        }
        if (filters.priority && this.priorityFilter) {
            this.priorityFilter.value = filters.priority;
        }
        this.currentPage = 1;
        this.performSearch();
    }
}

// Export for use in other scripts
window.RequestsAdvancedSearch = RequestsAdvancedSearch;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.requestsSearch = new RequestsAdvancedSearch();
    console.log('✅ Advanced Search System Initialized');
});

// Helper functions for existing functionality
function approveRequest(requestId) {
    if (confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        // Implementation for approval
        console.log('Approving request:', requestId);
        // You can add actual API call here
    }
}

function exportRequests() {
    console.log('Exporting requests...');
    // Implementation for export
}

function refreshRequestList() {
    if (window.requestsSearch) {
        window.requestsSearch.currentPage = 1;
        window.requestsSearch.performSearch();
    } else {
        location.reload();
    }
}

// Quick filter functions for external use
function filterByStatus(status) {
    if (window.requestsSearch) {
        window.requestsSearch.setFilters({ status });
    }
}

function filterByPriority(priority) {
    if (window.requestsSearch) {
        window.requestsSearch.setFilters({ priority });
    }
}

function searchRequests(term) {
    if (window.requestsSearch) {
        window.requestsSearch.search(term);
    }
} 