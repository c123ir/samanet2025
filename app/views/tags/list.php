<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/tags/list.php
 * توضیح: صفحه مدیریت تگ‌ها - Enterprise Grade (مطابق dashboard.css)
 * تاریخ ایجاد: 1404/10/15
 * نسخه: 1.0 حرفه‌ای (دقیقاً مطابق dashboard.css)
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
$popularTags = $popularTags ?? [];
$filters = $filters ?? [];
?>

<div class="dashboard-pro">
    <!-- Flash Messages (الزامی) -->
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show glass-card hover-lift" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; border-radius: 16px;">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Professional Header (الزامی) -->
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="header-title">
                <i class="fas fa-tags me-3"></i>
                مدیریت تگ‌ها
            </h1>
        </div>
        <div class="header-actions">
            <a href="<?= url('tags?action=create') ?>" class="btn btn-primary btn-modern hover-lift">
                <i class="fas fa-plus me-2"></i>
                ایجاد تگ جدید
            </a>
            <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            <div class="user-profile" title="نام کاربر">
                ک
            </div>
        </div>
    </header>

    <!-- محتوای اصلی -->
    <div class="dashboard-content">
        <!-- Professional Stats Row (الزامی) -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل تگ‌ها</div>
                <div class="stat-value"><?= number_format($stats['total_tags'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-tags"></i>
                    <span>فعال در سیستم</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">در حال استفاده</div>
                <div class="stat-value"><?= number_format($stats['used_tags'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-chart-line"></i>
                    <span>تگ فعال</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">بدون استفاده</div>
                <div class="stat-value"><?= number_format($stats['unused_tags'] ?? 0) ?></div>
                <div class="stat-change negative">
                    <i class="fas fa-circle"></i>
                    <span>غیرفعال</span>
                </div>
            </div>
            
            <div class="stat-card-pro">
                <div class="stat-label">بیشترین استفاده</div>
                <div class="stat-value"><?= number_format($stats['max_usage'] ?? 0) ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-crown"></i>
                    <span>محبوب‌ترین</span>
                </div>
            </div>
        </div>

        <!-- Professional Grid Layout (الزامی) -->
        <div class="dashboard-grid">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Search Section -->
                <div class="search-section">
                    <div class="search-container">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" 
                                   id="tag-search" 
                                   class="search-input" 
                                   placeholder="جستجو در تگ‌ها... (مثال: فوری قرمز، 19 تایید، مهم 1404)"
                                   autocomplete="off">
                            <button id="clear-search" class="clear-search-btn" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="search-stats">
                            <span id="search-results-count"></span>
                            <small class="search-help">
                                <i class="fas fa-info-circle me-1"></i>
                                جستجوی چند کلمه‌ای: کلمات را با فاصله جدا کنید | ESC برای پاک کردن
                            </small>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-list me-2"></i>
                            لیست تگ‌ها
                            <span class="badge bg-primary ms-2" id="total-tags-count"><?= count($tags) ?></span>
                        </h2>
                        <div class="table-header-actions">
                            <button class="btn-icon" onclick="refreshTags()" title="بروزرسانی">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>شناسه</th>
                                <th>عنوان</th>
                                <th>پیش‌نمایش</th>
                                <th>تعداد استفاده</th>
                                <th>ایجاد کننده</th>
                                <th>تاریخ ایجاد</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tags)): ?>
                                <?php foreach ($tags as $tag): ?>
                                    <tr>
                                        <td>
                                            <code class="badge bg-secondary">#<?= $tag['id'] ?></code>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($tag['title']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="tag-preview" 
                                                  style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                         color: <?= $tag['text_color'] ?>; 
                                                         padding: 4px 12px; 
                                                         border-radius: 20px; 
                                                         font-size: 12px; 
                                                         font-weight: 500; 
                                                         white-space: nowrap;"
                                                  title="<?= htmlspecialchars($tag['title']) ?>">
                                                <?= mb_strlen($tag['title']) > 20 ? mb_substr($tag['title'], 0, 20) . '...' : htmlspecialchars($tag['title']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="usage-count badge bg-<?= $tag['usage_count'] > 0 ? 'success' : 'secondary' ?>">
                                                <?= $tag['usage_count'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($tag['creator_name'] ?? 'نامشخص') ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?= jdate('Y/m/d H:i', strtotime($tag['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?= url('tags?action=edit&id=' . $tag['id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="ویرایش">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($tag['usage_count'] == 0): ?>
                                                    <button onclick="deleteTag(<?= $tag['id'] ?>)" 
                                                            class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">هیچ تگی یافت نشد</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Mobile List -->
                    <div class="mobile-list">
                        <?php if (!empty($tags)): ?>
                            <?php foreach ($tags as $tag): ?>
                                <div class="mobile-list-item">
                                    <div class="mobile-item-main">
                                        <div class="mobile-item-header">
                                            <code class="badge bg-secondary me-2">#<?= $tag['id'] ?></code>
                                            <span class="mobile-item-title"><?= htmlspecialchars($tag['title']) ?></span>
                                        </div>
                                        <div class="mobile-item-content">
                                            <div class="mobile-tag-display">
                                                <span class="tag-preview-mobile <?= mb_strlen($tag['title']) === 1 ? 'single-char' : '' ?>" 
                                                      style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                             color: <?= $tag['text_color'] ?>;"
                                                      title="<?= htmlspecialchars($tag['title']) ?>">
                                                    <?= mb_strlen($tag['title']) > 15 ? mb_substr($tag['title'], 0, 15) . '...' : htmlspecialchars($tag['title']) ?>
                                                </span>
                                            </div>
                                            <div class="mobile-item-meta">
                                                <span class="usage-count badge bg-<?= $tag['usage_count'] > 0 ? 'success' : 'secondary' ?>">
                                                    <?= $tag['usage_count'] ?> استفاده
                                                </span>
                                                <small class="text-muted ms-2">
                                                    <?= jdate('Y/m/d', strtotime($tag['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="mobile-item-actions">
                                            <a href="<?= url('tags?action=edit&id=' . $tag['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" title="ویرایش">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($tag['usage_count'] == 0): ?>
                                                <button onclick="deleteTag(<?= $tag['id'] ?>)" 
                                                        class="btn btn-sm btn-outline-danger ms-2" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                <p class="text-muted">هیچ تگی یافت نشد</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Side Column -->
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
                                <div class="task-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="tag-preview" 
                                              style="background: linear-gradient(135deg, <?= $tag['color_start'] ?>, <?= $tag['color_end'] ?>); 
                                                     color: <?= $tag['text_color'] ?>; 
                                                     padding: 2px 8px; 
                                                     border-radius: 12px; 
                                                     font-size: 11px; 
                                                     font-weight: 500;">
                                            <?= mb_strlen($tag['title']) > 15 ? mb_substr($tag['title'], 0, 15) . '...' : htmlspecialchars($tag['title']) ?>
                                        </span>
                                        <small class="text-muted"><?= $tag['usage_count'] ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <small class="text-muted">هنوز تگی استفاده نشده</small>
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
                        <div class="d-grid gap-2">
                            <a href="<?= url('tags?action=create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>ایجاد تگ جدید
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Theme system handled by theme-system.js - no local override needed

// Progressive animation for stats cards
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card-pro');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease-out forwards';
    });
    
    // Table hover effects
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Initialize search functionality
    initializeTagSearch();
});

// Initialize Tag Search
function initializeTagSearch() {
    const searchInput = document.getElementById('tag-search');
    const clearButton = document.getElementById('clear-search');
    const searchStats = document.getElementById('search-results-count');
    const totalTagsCount = document.getElementById('total-tags-count');
    
    let searchTimeout;
    let originalTagsCount = parseInt(totalTagsCount.textContent);
    
    // Search input event
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        // Show/hide clear button
        if (query.length > 0) {
            clearButton.style.display = 'flex';
        } else {
            clearButton.style.display = 'none';
        }
        
        // Debounce search
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 200);
    });
    
    // Clear search
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        clearButton.style.display = 'none';
        performSearch('');
        searchInput.focus();
    });
    
    // Enter key to focus on first result
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstVisibleRow = document.querySelector('.data-table tbody tr:not(.table-row-hidden)');
            if (firstVisibleRow) {
                firstVisibleRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstVisibleRow.classList.add('search-result-highlight');
                setTimeout(() => {
                    firstVisibleRow.classList.remove('search-result-highlight');
                }, 600);
            }
        }
        
        // ESC key to clear search when focused on input
        if (e.key === 'Escape') {
            e.preventDefault();
            clearSearchAndBlur();
        }
    });
    
    // Global ESC key to clear search from anywhere
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchInput.value && document.activeElement !== searchInput) {
            e.preventDefault();
            clearSearchOnly();
        }
    });
    
    // Helper function: Clear search and blur input
    function clearSearchAndBlur() {
        searchInput.value = '';
        clearButton.style.display = 'none';
        performSearch('');
        searchInput.blur();
    }
    
    // Helper function: Clear search only (don't focus)
    function clearSearchOnly() {
        searchInput.value = '';
        clearButton.style.display = 'none';
        performSearch('');
    }
}

// Perform search function
function performSearch(query) {
    const searchStats = document.getElementById('search-results-count');
    const totalTagsCount = document.getElementById('total-tags-count');
    
    // Get all table rows and mobile items
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    const mobileItems = document.querySelectorAll('.mobile-list-item');
    
    let visibleCount = 0;
    
    if (query === '') {
        // Show all items
        tableRows.forEach(row => {
            row.classList.remove('table-row-hidden');
            clearHighlights(row);
        });
        
        mobileItems.forEach(item => {
            item.classList.remove('mobile-item-hidden');
            clearHighlights(item);
        });
        
        visibleCount = tableRows.length - (document.querySelector('.data-table .empty-state') ? 1 : 0);
        searchStats.textContent = '';
        totalTagsCount.textContent = visibleCount;
    } else {
        // Parse multi-word search - تقسیم کوئری به کلمات جداگانه
        const searchTerms = query.trim().split(/\s+/).filter(term => term.length > 0);
        
        // Search in table rows
        tableRows.forEach(row => {
            const isVisible = searchInRowMultiWord(row, searchTerms);
            if (isVisible) {
                row.classList.remove('table-row-hidden');
                visibleCount++;
            } else {
                row.classList.add('table-row-hidden');
            }
        });
        
        // Search in mobile items
        mobileItems.forEach(item => {
            const isVisible = searchInMobileItemMultiWord(item, searchTerms);
            if (isVisible) {
                item.classList.remove('mobile-item-hidden');
            } else {
                item.classList.add('mobile-item-hidden');
            }
        });
        
        // Update stats
        if (visibleCount === 0) {
            searchStats.innerHTML = `<span style="color: var(--danger);">هیچ نتیجه‌ای یافت نشد برای "${query}"</span>`;
        } else {
            const termText = searchTerms.length > 1 ? `${searchTerms.length} کلمه` : 'کلمه';
            searchStats.innerHTML = `<span style="color: var(--success);">${visibleCount} نتیجه برای ${termText}: "${query}"</span>`;
        }
        
        totalTagsCount.textContent = visibleCount;
    }
    
    // Show "no results" message if needed
    toggleNoResultsMessage(visibleCount === 0 && query !== '');
}

// Search in table row - Multi-word search
function searchInRowMultiWord(row, searchTerms) {
    clearHighlights(row);
    
    // Skip empty state row
    if (row.querySelector('.empty-state')) {
        return false;
    }
    
    const searchableElements = row.querySelectorAll('td');
    
    // Get all text content from searchable cells (skip actions column)
    const allText = Array.from(searchableElements)
        .slice(0, -1) // Remove last column (actions)
        .map(cell => cell.textContent.toLowerCase())
        .join(' ');
    
    // Check if ALL search terms are found in the text
    const allTermsFound = searchTerms.every(term => 
        allText.includes(term.toLowerCase())
    );
    
    if (allTermsFound) {
        // Highlight each term in each cell
        searchableElements.forEach((cell, index) => {
            // Skip actions column
            if (index === searchableElements.length - 1) return;
            
            const textNodes = getTextNodes(cell);
            textNodes.forEach(textNode => {
                let highlightedText = textNode.textContent;
                
                // Apply highlighting for each search term
                searchTerms.forEach(term => {
                    const regex = new RegExp(escapeRegex(term), 'gi');
                    highlightedText = highlightedText.replace(regex, `<span class="search-highlight">$&</span>`);
                });
                
                if (highlightedText !== textNode.textContent) {
                    const span = document.createElement('span');
                    span.innerHTML = highlightedText;
                    textNode.parentNode.replaceChild(span, textNode);
                }
            });
        });
        
        return true;
    }
    
    return false;
}

// Search in mobile item - Multi-word search
function searchInMobileItemMultiWord(item, searchTerms) {
    clearHighlights(item);
    
    const searchableElements = item.querySelectorAll('.mobile-item-title, .badge, .tag-preview-mobile, .text-muted');
    
    // Get all text content from searchable elements
    const allText = Array.from(searchableElements)
        .map(element => element.textContent.toLowerCase())
        .join(' ');
    
    // Check if ALL search terms are found in the text
    const allTermsFound = searchTerms.every(term => 
        allText.includes(term.toLowerCase())
    );
    
    if (allTermsFound) {
        // Highlight each term in each element
        searchableElements.forEach(element => {
            const textNodes = getTextNodes(element);
            textNodes.forEach(textNode => {
                let highlightedText = textNode.textContent;
                
                // Apply highlighting for each search term
                searchTerms.forEach(term => {
                    const regex = new RegExp(escapeRegex(term), 'gi');
                    highlightedText = highlightedText.replace(regex, `<span class="search-highlight">$&</span>`);
                });
                
                if (highlightedText !== textNode.textContent) {
                    const span = document.createElement('span');
                    span.innerHTML = highlightedText;
                    textNode.parentNode.replaceChild(span, textNode);
                }
            });
        });
        
        return true;
    }
    
    return false;
}

// Search in table row (keep old function as backup)
function searchInRow(row, searchRegex, originalQuery) {
    clearHighlights(row);
    
    // Skip empty state row
    if (row.querySelector('.empty-state')) {
        return false;
    }
    
    let found = false;
    const searchableElements = row.querySelectorAll('td');
    
    searchableElements.forEach((cell, index) => {
        // Skip actions column
        if (index === searchableElements.length - 1) return;
        
        const originalText = cell.textContent;
        
        if (searchRegex.test(originalText)) {
            found = true;
            // Highlight matches
            const textNodes = getTextNodes(cell);
            textNodes.forEach(textNode => {
                if (searchRegex.test(textNode.textContent)) {
                    const span = document.createElement('span');
                    span.innerHTML = textNode.textContent.replace(searchRegex, `<span class="search-highlight">$&</span>`);
                    textNode.parentNode.replaceChild(span, textNode);
                }
            });
        }
    });
    
    return found;
}

// Get text nodes from element
function getTextNodes(element) {
    const textNodes = [];
    const walker = document.createTreeWalker(
        element,
        NodeFilter.SHOW_TEXT,
        null,
        false
    );
    
    let node;
    while (node = walker.nextNode()) {
        if (node.textContent.trim()) {
            textNodes.push(node);
        }
    }
    
    return textNodes;
}

// Clear highlights
function clearHighlights(element) {
    const highlights = element.querySelectorAll('.search-highlight');
    highlights.forEach(highlight => {
        const parent = highlight.parentNode;
        parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
        parent.normalize();
    });
}

// Escape regex special characters
function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Toggle no results message
function toggleNoResultsMessage(show) {
    let noResultsMsg = document.querySelector('.no-results-message');
    
    if (show) {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.innerHTML = `
                <i class="fas fa-search"></i>
                <p>هیچ تگی با این جستجو یافت نشد</p>
                <small>کلمات کلیدی دیگری امتحان کنید</small>
            `;
            
            // Add to both table and mobile list
            const tableBody = document.querySelector('.data-table tbody');
            const mobileList = document.querySelector('.mobile-list');
            
            if (tableBody) {
                const tr = document.createElement('tr');
                tr.className = 'no-results-row';
                const td = document.createElement('td');
                td.colSpan = 7;
                td.appendChild(noResultsMsg.cloneNode(true));
                tr.appendChild(td);
                tableBody.appendChild(tr);
            }
            
            if (mobileList) {
                const div = document.createElement('div');
                div.className = 'no-results-mobile';
                div.appendChild(noResultsMsg);
                mobileList.appendChild(div);
            }
        }
    } else {
        // Remove no results messages
        const noResultsRows = document.querySelectorAll('.no-results-row, .no-results-mobile');
        noResultsRows.forEach(row => row.remove());
    }
}

// تابع حذف تگ
function deleteTag(tagId) {
    if (!confirm('آیا از حذف این تگ اطمینان دارید؟')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', tagId);
    formData.append('csrf_token', '<?= $csrf_token ?>');
    
    fetch('<?= url('tags?action=delete') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('خطا:', error);
        showAlert('خطا در ارتباط با سرور', 'error');
    });
}

// تابع بروزرسانی تگ‌ها
function refreshTags() {
    location.reload();
}

// تابع نمایش alert
function showAlert(message, type = 'info') {
    const alertClass = type === 'error' ? 'danger' : type;
    const alertHtml = `
        <div class="alert alert-${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // حذف خودکار پس از 3 ثانیه
    setTimeout(function() {
        const alert = document.querySelector('.alert:last-child');
        if (alert) alert.remove();
    }, 3000);
}
</script> 