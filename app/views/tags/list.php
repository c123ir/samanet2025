<?php
/**
 * نام فایل: list.php
 * مسیر فایل: /app/views/tags/list.php
 * توضیح: صفحه مدیریت برچسب‌ها - طراحی Enterprise حرفه‌ای + ماژول جستجوی پیشرفته
 * تاریخ بازطراحی: 1404/10/18
 * نسخه: 4.2 Enterprise + Advanced Search - سازگار با layout اصلی
 */

// Load helper functions
require_once APP_PATH . 'helpers/Utilities.php';

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

// محاسبه آمار
$totalTags = $stats['total_tags'] ?? count($tags);
$usedTags = $stats['used_tags'] ?? array_filter($tags, fn($tag) => ($tag['usage_count'] ?? 0) > 0);
$unusedTags = $stats['unused_tags'] ?? array_filter($tags, fn($tag) => ($tag['usage_count'] ?? 0) == 0);
$maxUsage = $stats['max_usage'] ?? max(array_column($tags, 'usage_count') ?: [0]);

// محاسبه آمار ماه جاری
$currentMonth = date('Y-m');
$monthlyTags = array_filter($tags, fn($tag) => date('Y-m', strtotime($tag['created_at'] ?? 'now')) === $currentMonth);
$thisMonthCount = count($monthlyTags);
$lastMonthCount = max(1, $totalTags - $thisMonthCount); // جلوگیری از تقسیم بر صفر
$monthlyGrowth = round((($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100, 1);
?>

<!-- Include CSS Files -->
<link rel="stylesheet" href="<?= url('assets/css/pages/tags.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/advanced-search.css') ?>">

<!-- Flash Messages -->
<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" 
         style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Page Header Actions -->
<div class="page-header-actions">
    <a href="<?= url('tags?action=create') ?>" class="btn btn-primary btn-sm" title="برچسب جدید">
        <i class="fas fa-plus"></i>
        <span>جدید</span>
    </a>
</div>

<!-- ردیف آمارهای فشرده -->
<div class="stats-row">
    <div class="stat-card-pro">
        <div class="stat-label">کل برچسب‌ها</div>
        <div class="stat-value"><?= number_format($totalTags) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-tags"></i>
            <span>ایجاد شده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">در حال استفاده</div>
        <div class="stat-value"><?= is_array($usedTags) ? count($usedTags) : $usedTags ?></div>
        <div class="stat-change positive">
            <i class="fas fa-chart-line"></i>
            <span>فعال</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">بدون استفاده</div>
        <div class="stat-value"><?= is_array($unusedTags) ? count($unusedTags) : $unusedTags ?></div>
        <div class="stat-change neutral">
            <i class="fas fa-circle"></i>
            <span>غیرفعال</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">بیشترین استفاده</div>
        <div class="stat-value"><?= number_format($maxUsage) ?></div>
        <div class="stat-change positive">
            <i class="fas fa-crown"></i>
            <span>محبوب</span>
        </div>
    </div>
</div>

<!-- ماژول جستجوی پیشرفته -->
<div class="table-container mb-4">
    <div class="table-header">
        <h2 class="table-title">
            <i class="fas fa-search"></i>
            جستجوی پیشرفته
        </h2>
    </div>
    <div class="card-body">
        <div id="searchContainer">
            <!-- AdvancedSearch Component اینجا ایجاد می‌شود -->
        </div>
    </div>
</div>

<!-- Grid اصلی -->
<div class="dashboard-grid">
    <!-- ستون اصلی -->
    <div class="main-column">
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-tags"></i>
                    لیست برچسب‌ها
                    <span class="badge badge-primary ms-2" id="totalTagsCount"><?= count($tags) ?></span>
                </h2>
                <div class="table-actions">
                    <a href="<?= url('tags?action=create') ?>" class="btn-icon btn-primary" title="برچسب جدید">
                        <i class="fas fa-plus"></i>
                    </a>
                    <button class="btn-icon" onclick="exportTags()" title="صادرات">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-icon" onclick="refreshTagList()" title="بروزرسانی">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <div id="searchResults">
                <!-- نتایج جستجو اینجا نمایش داده می‌شوند -->
                <!-- جدول دسکتاپ -->
                <table class="data-table">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>عنوان</th>
                        <th>پیش‌نمایش</th>
                        <th>تعداد استفاده</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tags)): ?>
                        <?php foreach ($tags as $tag): ?>
                            <tr>
                                <td>
                                    <code class="text-muted">#<?= $tag['id'] ?></code>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-name"><?= htmlspecialchars($tag['title'] ?? '') ?></div>
                                        <div class="user-email text-muted">
                                            <?= htmlspecialchars($tag['creator_name'] ?? 'سیستم') ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="tag-preview-badge" 
                                          style="background: linear-gradient(135deg, <?= $tag['color_start'] ?? '#667eea' ?>, <?= $tag['color_end'] ?? '#764ba2' ?>); 
                                                 color: <?= $tag['text_color'] ?? '#ffffff' ?>;"
                                          title="<?= htmlspecialchars($tag['title'] ?? '') ?>">
                                        <?= htmlspecialchars(mb_substr($tag['title'] ?? '', 0, 10)) ?><?= mb_strlen($tag['title'] ?? '') > 10 ? '...' : '' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= getUsageBadgeClass($tag['usage_count'] ?? 0) ?>">
                                        <?= number_format($tag['usage_count'] ?? 0) ?> استفاده
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= ($tag['usage_count'] ?? 0) > 0 ? 'success' : 'secondary' ?>">
                                        <?= ($tag['usage_count'] ?? 0) > 0 ? 'فعال' : 'غیرفعال' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <div class="date-primary"><?= jdate('Y/m/d', strtotime($tag['created_at'] ?? 'now')) ?></div>
                                        <div class="date-secondary text-muted"><?= jdate('H:i', strtotime($tag['created_at'] ?? 'now')) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="<?= url('tags?action=edit&id=' . $tag['id']) ?>" 
                                           class="btn-icon" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn-icon text-danger" 
                                                onclick="deleteTag(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['title'] ?? '') ?>')" 
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn-icon text-info" 
                                                onclick="duplicateTag(<?= $tag['id'] ?>)" 
                                                title="کپی">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">هنوز برچسبی ایجاد نشده</h5>
                                    <p class="text-muted">برای شروع، اولین برچسب خود را ایجاد کنید</p>
                                    <a href="<?= url('tags?action=create') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>
                                        ایجاد برچسب جدید
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- لیست موبایل -->
            <div class="mobile-list">
                <?php if (!empty($tags)): ?>
                    <?php foreach ($tags as $tag): ?>
                        <div class="mobile-list-item">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title"><?= htmlspecialchars($tag['title'] ?? '') ?></div>
                                <div class="mobile-item-meta">
                                    <span class="tag-preview-badge small" 
                                          style="background: linear-gradient(135deg, <?= $tag['color_start'] ?? '#667eea' ?>, <?= $tag['color_end'] ?? '#764ba2' ?>); 
                                                 color: <?= $tag['text_color'] ?? '#ffffff' ?>;">
                                        <?= htmlspecialchars(mb_substr($tag['title'] ?? '', 0, 8)) ?>
                                    </span>
                                    • <span class="badge badge-<?= getUsageBadgeClass($tag['usage_count'] ?? 0) ?>"><?= number_format($tag['usage_count'] ?? 0) ?></span>
                                    • <?= htmlspecialchars($tag['creator_name'] ?? 'سیستم') ?>
                                </div>
                                <div class="mobile-item-date"><?= jdate('Y/m/d H:i', strtotime($tag['created_at'] ?? 'now')) ?></div>
                            </div>
                            <div class="mobile-item-actions">
                                <a href="<?= url('tags?action=edit&id=' . $tag['id']) ?>" class="btn-icon" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-icon text-danger" onclick="deleteTag(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['title'] ?? '') ?>')" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">هنوز برچسبی ایجاد نشده</h5>
                        <p class="text-muted">برای شروع، اولین برچسب خود را ایجاد کنید</p>
                        <a href="<?= url('tags?action=create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            ایجاد برچسب جدید
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            </div> <!-- End searchResults -->
        </div>
    </div>

    <!-- ستون جانبی -->
    <div class="side-column">
        <!-- فیلترهای سریع -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-filter"></i>
                    فیلترهای سریع
                </div>
            </div>
            <div class="panel-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm filter-btn active" data-filter="all">
                        همه برچسب‌ها
                    </button>
                    <button class="btn btn-outline-success btn-sm filter-btn" data-filter="used">
                        در حال استفاده
                    </button>
                    <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="unused">
                        بدون استفاده
                    </button>
                    <button class="btn btn-outline-info btn-sm filter-btn" data-filter="popular">
                        محبوب‌ترین
                    </button>
                    <button class="btn btn-outline-warning btn-sm filter-btn" data-filter="recent">
                        جدیدترین
                    </button>
                </div>
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
                        <div class="quick-action-title">ایجاد برچسب</div>
                        <div class="quick-action-desc">افزودن برچسب جدید</div>
                    </div>
                </div>
                
                <div class="quick-action" onclick="exportTags()">
                    <div class="quick-action-icon bg-success">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">صادرات لیست</div>
                        <div class="quick-action-desc">خروجی Excel</div>
                    </div>
                </div>
                
                <div class="quick-action" onclick="bulkOperations()">
                    <div class="quick-action-icon bg-warning">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">عملیات گروهی</div>
                        <div class="quick-action-desc">مدیریت چندگانه</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- آمار ماهانه -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-chart-line"></i>
                    آمار این ماه
                </div>
                <span class="panel-badge"><?= $thisMonthCount ?></span>
            </div>
            <div class="panel-body">
                <div class="stat-row">
                    <div class="stat-item">
                        <div class="stat-value text-primary"><?= number_format($thisMonthCount) ?></div>
                        <div class="stat-label">برچسب ایجاد شده</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value text-<?= $monthlyGrowth >= 0 ? 'success' : 'danger' ?>">
                            <?= $monthlyGrowth >= 0 ? '+' : '' ?><?= $monthlyGrowth ?>%
                        </div>
                        <div class="stat-label">رشد نسبت به ماه قبل</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <i class="fas fa-calendar text-muted"></i>
                    <span>ماه جاری: <?= jdate('F Y') ?></span>
                </div>
            </div>
        </div>

        <!-- محبوب‌ترین برچسب‌ها -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-fire"></i>
                    محبوب‌ترین برچسب‌ها
                </div>
                <span class="panel-badge"><?= count($popularTags) ?></span>
            </div>
            <div class="panel-body">
                <?php if (!empty($popularTags)): ?>
                    <?php foreach (array_slice($popularTags, 0, 5) as $index => $tag): ?>
                        <div class="popular-tag-item">
                            <div class="rank-badge"><?= $index + 1 ?></div>
                            <span class="tag-preview-mini" 
                                  style="background: linear-gradient(135deg, <?= $tag['color_start'] ?? '#667eea' ?>, <?= $tag['color_end'] ?? '#764ba2' ?>); 
                                         color: <?= $tag['text_color'] ?? '#ffffff' ?>;">
                                <?= htmlspecialchars(mb_substr($tag['title'] ?? '', 0, 8)) ?>
                            </span>
                            <small class="text-muted"><?= number_format($tag['usage_count'] ?? 0) ?> بار</small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state text-center">
                        <i class="fas fa-tags text-muted"></i>
                        <p class="text-muted">هنوز آماری موجود نیست</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="<?= url('assets/js/advanced-search.js') ?>"></script>

<script>
// فعال‌سازی ماژول جستجوی پیشرفته
document.addEventListener('DOMContentLoaded', function() {
    // ایجاد instance جستجوی پیشرفته
    apiUrl: '<?= url('api/tags.php?action=search') ?>',
    window.tagsSearchManager = new AdvancedSearch({
        containerSelector: '#searchContainer',
        resultsSelector: '#searchResults',
        placeholder: 'جستجو در برچسب‌ها...',
        helpText: 'نام برچسب، توضیحات یا نام ایجادکننده را جستجو کنید | ESC برای پاک کردن',
        emptyStateMessage: 'هیچ برچسبی با این مشخصات یافت نشد',
        enableStats: true,
        enableKeyboardShortcuts: true,
        highlightFields: ['title', 'description', 'creator_name'],
        customResultRenderer: function(results) {
            // رندر سفارشی برای نتایج برچسب‌ها
            if (!results || results.length === 0) {
                return `
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">هیچ برچسبی یافت نشد</h5>
                        <p class="text-muted">لطفاً کلمات کلیدی دیگری امتحان کنید</p>
                    </div>
                `;
            }
            
            // رندر جدول دسکتاپ
            const tableHTML = `
                <table class="data-table">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>عنوان</th>
                        <th>پیش‌نمایش</th>
                        <th>تعداد استفاده</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    ${results.map(tag => `
                        <tr>
                            <td>
                                <code class="text-muted">#${tag.id}</code>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">${tag.title_highlighted || tag.title || ''}</div>
                                    <div class="user-email text-muted">
                                        ${tag.creator_name_highlighted || tag.creator_name || 'سیستم'}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="tag-preview-badge" 
                                      style="background: linear-gradient(135deg, ${tag.color_start || '#667eea'}, ${tag.color_end || '#764ba2'}); 
                                             color: ${tag.text_color || '#ffffff'};"
                                      title="${tag.title || ''}">
                                    ${(tag.title || '').substring(0, 10)}${(tag.title || '').length > 10 ? '...' : ''}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-${getUsageBadgeClass(tag.usage_count || 0)}">
                                    ${(tag.usage_count || 0).toLocaleString('fa-IR')} استفاده
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-${(tag.usage_count || 0) > 0 ? 'success' : 'secondary'}">
                                    ${(tag.usage_count || 0) > 0 ? 'فعال' : 'غیرفعال'}
                                </span>
                            </td>
                            <td>
                                <div class="date-info">
                                    <div class="date-primary">${formatJalaliDate(tag.created_at)}</div>
                                    <div class="date-secondary text-muted">${formatJalaliTime(tag.created_at)}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= url('tags?action=edit&id=') ?>${tag.id}" class="btn-icon" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn-icon text-danger" onclick="deleteTag(${tag.id}, '${tag.title || ''}')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn-icon text-info" onclick="duplicateTag(${tag.id})" title="کپی">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
                </table>
                
                <!-- لیست موبایل -->
                <div class="mobile-list">
                    ${results.map(tag => `
                        <div class="mobile-list-item">
                            <div class="mobile-item-main">
                                <div class="mobile-item-title">${tag.title_highlighted || tag.title || ''}</div>
                                <div class="mobile-item-meta">
                                    <span class="tag-preview-badge small" 
                                          style="background: linear-gradient(135deg, ${tag.color_start || '#667eea'}, ${tag.color_end || '#764ba2'}); 
                                                 color: ${tag.text_color || '#ffffff'};">
                                        ${(tag.title || '').substring(0, 8)}
                                    </span>
                                    • <span class="badge badge-${getUsageBadgeClass(tag.usage_count || 0)}">${(tag.usage_count || 0).toLocaleString('fa-IR')}</span>
                                    • ${tag.creator_name || 'سیستم'}
                                </div>
                                <div class="mobile-item-date">${formatJalaliDateTime(tag.created_at)}</div>
                            </div>
                            <div class="mobile-item-actions">
                                <a href="<?= url('tags?action=edit&id=') ?>${tag.id}" class="btn-icon" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-icon text-danger" onclick="deleteTag(${tag.id}, '${tag.title || ''}')" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            
            return tableHTML;
        },
        onSearchStart: function(query) {
            console.log('شروع جستجو:', query);
        },
        onSearchComplete: function(data, query) {
            console.log('جستجو تکمیل شد:', data.total + ' نتیجه');
            // به‌روزرسانی شمارنده
            const totalCounter = document.getElementById('totalTagsCount');
            if (totalCounter) {
                totalCounter.textContent = data.total || 0;
            }
        },
        onError: function(error, query) {
            console.error('خطا در جستجو:', error);
        }
    });
    
    // فیلترهای سریع
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // حذف active از همه دکمه‌ها
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            // اضافه کردن active به دکمه فعلی
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            // اعمال فیلتر
            applyQuickFilter(filter);
        });
    });
    
    console.log('✅ Tags page with Advanced Search initialized');
});

// Helper functions
function getUsageBadgeClass(usageCount) {
    if (usageCount === 0) return 'secondary';
    if (usageCount < 5) return 'info';
    if (usageCount < 20) return 'warning';
    return 'success';
}

function formatJalaliDate(dateString) {
    // ساده‌سازی شده - باید با کتابخانه تاریخ شمسی پیاده‌سازی شود
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fa-IR');
    } catch {
        return dateString;
    }
}

function formatJalaliTime(dateString) {
    try {
        const date = new Date(dateString);
        return date.toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });
    } catch {
        return '';
    }
}

function formatJalaliDateTime(dateString) {
    return formatJalaliDate(dateString) + ' ' + formatJalaliTime(dateString);
}

function applyQuickFilter(filter) {
    // پیاده‌سازی فیلتر سریع
    console.log('اعمال فیلتر:', filter);
    // TODO: پیاده‌سازی لاجیک فیلتر
}

// عملیات برچسب‌ها
function deleteTag(id, title) {
    if (confirm(`آیا مطمئن هستید که می‌خواهید برچسب "${title}" را حذف کنید؟`)) {
        // TODO: پیاده‌سازی حذف
        console.log('حذف برچسب:', id);
    }
}

function duplicateTag(id) {
    // TODO: پیاده‌سازی کپی
    console.log('کپی برچسب:', id);
}

function exportTags() {
    // TODO: پیاده‌سازی صادرات
    console.log('صادرات برچسب‌ها');
}

function refreshTagList() {
    if (window.tagsSearchManager) {
        window.tagsSearchManager.loadInitialData();
    }
}

function bulkOperations() {
    // TODO: پیاده‌سازی عملیات گروهی
    console.log('عملیات گروهی');
}
</script>

<?php
// Helper function for usage badge class
if (!function_exists('getUsageBadgeClass')) {
    function getUsageBadgeClass($usageCount) {
        if ($usageCount === 0) return 'secondary';
        if ($usageCount < 5) return 'info';
        if ($usageCount < 20) return 'warning';
        return 'success';
    }
}
?> 