<?php
/**
 * صفحه تنظیمات سیستم
 */
?>

<!-- MANDATORY: Stats Row -->
<div class="stats-row">
    <div class="stat-card-pro">
        <div class="stat-label">کل کاربران</div>
        <div class="stat-value"><?= $stats['total_users'] ?></div>
        <div class="stat-change positive">
            <i class="fas fa-users"></i>
            <span>ثبت‌نام‌شده</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">کل درخواست‌ها</div>
        <div class="stat-value"><?= $stats['total_requests'] ?></div>
        <div class="stat-change positive">
            <i class="fas fa-file-alt"></i>
            <span>در سیستم</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">برچسب‌ها</div>
        <div class="stat-value"><?= $stats['total_tags'] ?></div>
        <div class="stat-change positive">
            <i class="fas fa-tags"></i>
            <span>فعال</span>
        </div>
    </div>
    
    <div class="stat-card-pro">
        <div class="stat-label">فضای مصرفی</div>
        <div class="stat-value"><?= $stats['disk_usage'] ?></div>
        <div class="stat-change neutral">
            <i class="fas fa-hdd"></i>
            <span>استفاده شده</span>
        </div>
    </div>
</div>

<!-- Grid اصلی -->
<div class="dashboard-grid">
    <!-- ستون اصلی -->
    <div class="main-column">
        <!-- تنظیمات عمومی -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-cog"></i>
                    تنظیمات عمومی
                </h2>
                <button class="btn-icon" onclick="saveSettings()" title="ذخیره تنظیمات">
                    <i class="fas fa-save"></i>
                </button>
            </div>
            
            <div style="padding: var(--space-6);">
                <form id="settingsForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نام سایت</label>
                            <input type="text" class="form-control" name="site_name" 
                                   value="<?= htmlspecialchars($settings['site_name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نسخه سیستم</label>
                            <input type="text" class="form-control" name="site_version" 
                                   value="<?= htmlspecialchars($settings['site_version']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">حداکثر اندازه آپلود</label>
                            <input type="text" class="form-control" name="max_upload_size" 
                                   value="<?= htmlspecialchars($settings['max_upload_size']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">زمان انقضای نشست</label>
                            <input type="text" class="form-control" name="session_timeout" 
                                   value="<?= htmlspecialchars($settings['session_timeout']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">زبان پیش‌فرض</label>
                            <select class="form-control" name="default_language">
                                <option value="fa" <?= $settings['default_language'] === 'فارسی' ? 'selected' : '' ?>>فارسی</option>
                                <option value="en">انگلیسی</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">منطقه زمانی</label>
                            <input type="text" class="form-control" name="timezone" 
                                   value="<?= htmlspecialchars($settings['timezone']) ?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- اطلاعات سیستم -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-info-circle"></i>
                    اطلاعات سیستم
                </h2>
            </div>
            
            <table class="data-table">
                <tbody>
                    <tr>
                        <td><strong>نسخه PHP</strong></td>
                        <td><?= $stats['php_version'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>نسخه MySQL</strong></td>
                        <td><?= $stats['mysql_version'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>وضعیت سیستم</strong></td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                فعال
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>آخرین پشتیبان‌گیری</strong></td>
                        <td><?= $stats['last_backup'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>فضای مصرفی</strong></td>
                        <td><?= $stats['disk_usage'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ستون جانبی -->
    <div class="side-column">
        <!-- عملیات سیستم -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-tools"></i>
                    عملیات سیستم
                </div>
            </div>
            <div class="panel-body">
                <div class="task-item" onclick="createBackup()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-download" style="color: var(--primary); margin-left: var(--space-2);"></i>
                        ایجاد نسخه پشتیبان
                    </span>
                </div>
                
                <div class="task-item" onclick="clearCache()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-trash" style="color: var(--warning); margin-left: var(--space-2);"></i>
                        پاکسازی حافظه موقت
                    </span>
                </div>
                
                <div class="task-item" onclick="viewLogs()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-file-alt" style="color: var(--info); margin-left: var(--space-2);"></i>
                        مشاهده لاگ‌ها
                    </span>
                </div>
                
                <div class="task-item" onclick="systemStatus()" style="cursor: pointer;">
                    <span class="task-text">
                        <i class="fas fa-heartbeat" style="color: var(--success); margin-left: var(--space-2);"></i>
                        بررسی وضعیت سیستم
                    </span>
                </div>
            </div>
        </div>

        <!-- آمار عملکرد -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-chart-line"></i>
                    آمار عملکرد
                </div>
            </div>
            <div class="panel-body">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">زمان پاسخ میانگین</span>
                    <span style="font-weight: 600; color: var(--success);">0.2s</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; border-bottom: 1px solid var(--gray-100);">
                    <span style="font-size: 12px; color: var(--gray-600);">استفاده از حافظه</span>
                    <span style="font-weight: 600; color: var(--warning);">45%</span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0;">
                    <span style="font-size: 12px; color: var(--gray-600);">استفاده از CPU</span>
                    <span style="font-weight: 600; color: var(--info);">23%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function saveSettings() {
    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);
    
    // نمایش loading
    const saveBtn = document.querySelector('[onclick="saveSettings()"]');
    const originalIcon = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch('/?route=settings&action=update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('تنظیمات با موفقیت ذخیره شد', 'success');
        } else {
            showAlert('خطا در ذخیره تنظیمات', 'error');
        }
    })
    .catch(error => {
        showAlert('خطا در ارتباط با سرور', 'error');
    })
    .finally(() => {
        saveBtn.innerHTML = originalIcon;
    });
}

function createBackup() {
    if (confirm('آیا می‌خواهید نسخه پشتیبان ایجاد کنید؟')) {
        window.open('/?route=settings&action=backup', '_blank');
        showAlert('درحال ایجاد نسخه پشتیبان...', 'info');
    }
}

function clearCache() {
    if (confirm('آیا می‌خواهید حافظه موقت پاک شود؟')) {
        fetch('/?route=settings&action=clearCache', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            showAlert(data.message || 'حافظه موقت پاک شد', data.success ? 'success' : 'error');
        });
    }
}

function viewLogs() {
    window.open('/?route=settings&action=logs', '_blank');
}

function systemStatus() {
    showAlert('سیستم در وضعیت عادی کار می‌کند', 'success');
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1000;
        max-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}
</script> 