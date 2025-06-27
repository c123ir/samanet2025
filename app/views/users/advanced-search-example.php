<?php
/**
 * نمونه عملی: صفحه کاربران با سیستم جستجوی پیشرفته
 * 
 * این فایل نشان می‌دهد چطور با استفاده از AdvancedSearch Helper
 * می‌توان در کمترین کد ممکن، جستجوی حرفه‌ای پیاده‌سازی کرد.
 */

$pageTitle = 'کاربران - جستجوی پیشرفته';
include_once '../layouts/header.php';
?>

<link rel="stylesheet" href="<?= url('assets/css/bootstrap-dashboard.css') ?>">
<link rel="stylesheet" href="<?= url('assets/css/advanced-search.css') ?>">

<div class="dashboard-pro">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-content">
            <h1 class="header-title">مدیریت کاربران</h1>
        </div>
        <div class="header-actions">
            <button class="theme-toggle" onclick="toggleTheme()" title="تغییر تم">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
            <div class="user-profile" title="مدیر سیستم">
                م
            </div>
        </div>
    </header>

    <!-- محتوای اصلی -->
    <div class="dashboard-content">
        <!-- آمار کلی -->
        <div class="stats-row">
            <div class="stat-card-pro">
                <div class="stat-label">کل کاربران</div>
                <div class="stat-value">1,234</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>12% این ماه</span>
                </div>
            </div>
            <div class="stat-card-pro">
                <div class="stat-label">کاربران فعال</div>
                <div class="stat-value">1,089</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>8% این ماه</span>
                </div>
            </div>
            <div class="stat-card-pro">
                <div class="stat-label">کاربران جدید</div>
                <div class="stat-value">45</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>این هفته</span>
                </div>
            </div>
            <div class="stat-card-pro">
                <div class="stat-label">آنلاین</div>
                <div class="stat-value">23</div>
                <div class="stat-change">
                    <i class="fas fa-circle text-success"></i>
                    <span>در حال حاضر</span>
                </div>
            </div>
        </div>

        <!-- Grid اصلی -->
        <div class="dashboard-grid">
            <!-- ستون اصلی -->
            <div class="main-column">
                <!-- جعبه جستجوی پیشرفته -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div id="searchContainer">
                            <!-- AdvancedSearch Component اینجا ایجاد می‌شود -->
                        </div>
                    </div>
                </div>

                <!-- جدول کاربران -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-users me-2"></i>
                            لیست کاربران
                            <span class="badge bg-secondary ms-2" id="totalUsersCount">0</span>
                        </h2>
                        <div class="d-flex gap-2">
                            <button class="btn-icon" title="افزودن کاربر" onclick="window.location.href='add-user.php'">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn-icon" title="خروجی Excel">
                                <i class="fas fa-file-excel"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="searchResults">
                        <!-- نتایج جستجو اینجا نمایش داده می‌شوند -->
                    </div>
                </div>
            </div>

            <!-- ستون جانبی -->
            <div class="side-column">
                <!-- پنل فیلترهای سریع -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-filter"></i>
                            فیلترهای سریع
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="all">
                                همه کاربران
                            </button>
                            <button class="btn btn-outline-success btn-sm filter-btn" data-filter="active">
                                کاربران فعال
                            </button>
                            <button class="btn btn-outline-warning btn-sm filter-btn" data-filter="inactive">
                                کاربران غیرفعال
                            </button>
                            <button class="btn btn-outline-info btn-sm filter-btn" data-filter="admins">
                                مدیران
                            </button>
                            <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="recent">
                                اعضای جدید
                            </button>
                        </div>
                    </div>
                </div>

                <!-- پنل آمار تفصیلی -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-chart-pie"></i>
                            آمار تفصیلی
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">نقش‌های کاربری</small>
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 60%"></div>
                            </div>
                            <small class="text-muted">کاربر عادی: 60%</small>
                        </div>
                        <div class="mb-3">
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                            </div>
                            <small class="text-muted">مدیر: 30%</small>
                        </div>
                        <div class="mb-3">
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: 10%"></div>
                            </div>
                            <small class="text-muted">سوپر ادمین: 10%</small>
                        </div>
                    </div>
                </div>

                <!-- پنل عملیات سریع -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fas fa-bolt"></i>
                            عملیات سریع
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus me-2"></i>
                                افزودن کاربر
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-envelope me-2"></i>
                                ارسال پیام گروهی
                            </button>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-shield-alt me-2"></i>
                                مدیریت مجوزها
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= url('assets/js/advanced-search.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // راه‌اندازی AdvancedSearch برای کاربران
    const usersSearch = new AdvancedSearch({
        // تنظیمات اصلی
        apiUrl: '<?= url('users?action=api') ?>',
        containerSelector: '#searchContainer',
        resultsSelector: '#searchResults',
        
        // تنظیمات UI
        placeholder: 'جستجو در کاربران... (مثال: احمد مدیر، تهران فعال، 1403)',
        helpText: 'با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید | ESC برای پاک کردن',
        enableStats: true,
        enableKeyboardShortcuts: true,
        
        // فیلدهای highlighting
        highlightFields: ['full_name', 'email', 'username'],
        
        // Renderer سفارشی برای کاربران
        customResultRenderer: function(results) {
            if (!results || results.length === 0) {
                return '<div class="text-center p-4 text-muted">هیچ کاربری یافت نشد</div>';
            }
            
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">آواتار</th>
                                <th>نام کامل</th>
                                <th>نام کاربری</th>
                                <th>ایمیل</th>
                                <th>نقش</th>
                                <th class="text-center">وضعیت</th>
                                <th>آخرین ورود</th>
                                <th class="text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            results.forEach(user => {
                const statusBadge = user.status === 'active' 
                    ? '<span class="badge bg-success">فعال</span>'
                    : '<span class="badge bg-secondary">غیرفعال</span>';
                    
                const roleBadge = this.getRoleBadge(user.role);
                const avatar = user.avatar 
                    ? `<img src="${user.avatar}" class="rounded-circle" width="32" height="32">`
                    : `<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; font-weight: 600;">${user.full_name ? user.full_name.charAt(0) : 'U'}</div>`;
                
                html += `
                    <tr class="result-item" data-id="${user.id}">
                        <td class="text-center">${avatar}</td>
                        <td>
                            <strong>${user.full_name_highlighted || user.full_name || 'نامشخص'}</strong>
                        </td>
                        <td>
                            <code>${user.username_highlighted || user.username}</code>
                        </td>
                        <td>
                            <small class="text-muted">${user.email_highlighted || user.email || '-'}</small>
                        </td>
                        <td>${roleBadge}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td>
                            <small class="text-muted">${this.formatDate(user.last_login)}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="edit-user.php?id=${user.id}" class="btn btn-outline-primary" title="ویرایش">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="viewUser(${user.id})" class="btn btn-outline-info" title="مشاهده">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${user.status === 'active' ? 
                                    `<button onclick="deactivateUser(${user.id})" class="btn btn-outline-warning" title="غیرفعال">
                                        <i class="fas fa-ban"></i>
                                    </button>` :
                                    `<button onclick="activateUser(${user.id})" class="btn btn-outline-success" title="فعال">
                                        <i class="fas fa-check"></i>
                                    </button>`
                                }
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            return html;
        },
        
        // Callbacks
        onSearchStart: function(query) {
            console.log('🔍 جستجوی کاربران شروع شد:', query);
        },
        
        onSearchComplete: function(data, query) {
            console.log('✅ جستجو تکمیل شد:', data.total, 'کاربر یافت شد');
            
            // به‌روزرسانی شمارنده
            document.getElementById('totalUsersCount').textContent = data.total;
        },
        
        onError: function(error, query) {
            console.error('❌ خطا در جستجوی کاربران:', error);
            
            // نمایش پیام خطا
            this.elements.resultsContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h6>خطا در جستجو</h6>
                    <p class="mb-0">لطفاً دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.</p>
                </div>
            `;
        }
    });
    
    // Helper method برای نمایش نقش کاربر
    usersSearch.getRoleBadge = function(role) {
        const roles = {
            'super_admin': '<span class="badge bg-danger">سوپر ادمین</span>',
            'admin': '<span class="badge bg-warning">مدیر</span>',
            'manager': '<span class="badge bg-info">مدیر بخش</span>',
            'user': '<span class="badge bg-primary">کاربر</span>'
        };
        return roles[role] || '<span class="badge bg-secondary">نامشخص</span>';
    };
    
    // فیلترهای سریع
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // حذف active از همه دکمه‌ها
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            
            // اضافه کردن active به دکمه کلیک شده
            this.classList.add('active');
            
            // اعمال فیلتر (این قسمت باید با API تنظیم شود)
            console.log('فیلتر اعمال شد:', filter);
            
            // می‌توانید API URL را به‌روزرسانی کنید
            usersSearch.updateConfig({
                apiUrl: `<?= url('users?action=api') ?>&filter=${filter}`
            });
            
            // جستجوی مجدد
            usersSearch.performSearch(usersSearch.elements.searchInput.value);
        });
    });
    
    // دسترسی global برای debugging
    window.usersSearchSystem = usersSearch;
    
    console.log('✅ سیستم جستجوی پیشرفته کاربران راه‌اندازی شد');
});

// توابع کمکی
function viewUser(userId) {
    // نمایش modal یا redirect به صفحه جزئیات
    window.location.href = `view-user.php?id=${userId}`;
}

function activateUser(userId) {
    if (confirm('آیا از فعال‌سازی این کاربر اطمینان دارید؟')) {
        // AJAX call برای فعال‌سازی
        fetch('<?= url('users?action=activate') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // رفرش جستجو
                window.usersSearchSystem.performSearch(
                    window.usersSearchSystem.elements.searchInput.value
                );
            } else {
                alert(data.message || 'خطا در فعال‌سازی کاربر');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('خطا در ارتباط با سرور');
        });
    }
}

function deactivateUser(userId) {
    if (confirm('آیا از غیرفعال‌سازی این کاربر اطمینان دارید؟')) {
        // مشابه activateUser ولی برای غیرفعال‌سازی
        console.log('غیرفعال‌سازی کاربر:', userId);
    }
}
</script>

<style>
/* استایل‌های اضافی برای این صفحه */
.filter-btn.active {
    background-color: var(--primary, #5E3AEE) !important;
    border-color: var(--primary, #5E3AEE) !important;
    color: white !important;
}

.result-item:hover {
    background-color: var(--gray-50, #f9fafb) !important;
}

.btn-group .btn {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.result-item:hover .btn-group .btn {
    opacity: 1;
}

/* Dark theme support */
[data-theme="dark"] .result-item:hover {
    background-color: var(--gray-200, #374151) !important;
}
</style>

<?php include_once '../layouts/footer.php'; ?> 