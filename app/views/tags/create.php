<?php
/**
 * نام فایل: create.php
 * مسیر فایل: /app/views/tags/create.php
 * توضیح: صفحه ایجاد تگ جدید - Enterprise Grade (مطابق dashboard.css)
 * تاریخ ایجاد: 1404/10/15
 * نسخه: 2.0 بهبود یافته (بدون tag_code و پیش‌نمایش بهتر)
 */

// تنظیم متغیرهای صفحه
$page_title = $title ?? 'ایجاد تگ جدید';
$page_description = 'ایجاد تگ جدید با گرادینت سفارشی';
$active_menu = 'tags';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}

// گرادینت پیش‌فرض
$randomGradient = $random_gradient ?? [
    'color_start' => '#667eea',
    'color_end' => '#764ba2',
    'text_color' => '#ffffff'
];
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
                <i class="fas fa-plus me-3"></i>
                ایجاد تگ جدید
            </h1>
        </div>
        <div class="header-actions">
            <a href="<?= url('tags') ?>" class="btn btn-secondary btn-modern hover-lift">
                <i class="fas fa-arrow-right me-2"></i>
                بازگشت
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
        <div class="create-tag-container">
            <!-- پیش‌نمایش زنده در بالای فرم -->
            <div class="live-preview-section">
                <div class="preview-title">
                    <i class="fas fa-eye me-2"></i>
                    پیش‌نمایش زنده
                </div>
                <div class="preview-container">
                    <div id="tag-preview" class="tag-preview-live" 
                         style="background: linear-gradient(135deg, <?= $randomGradient['color_start'] ?>, <?= $randomGradient['color_end'] ?>); 
                                color: <?= $randomGradient['text_color'] ?>;">
                        نمونه تگ
                    </div>
                </div>
            </div>

            <!-- فرم ایجاد تگ -->
            <div class="form-section">
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-plus-circle me-2"></i>
                            اطلاعات تگ جدید
                        </h2>
                    </div>

                    <div class="table-body" style="padding: var(--space-6);">
                        <form method="POST" action="<?= url('tags?action=store') ?>" id="create-tag-form" class="tag-form">
                            
                            <!-- فیلد عنوان -->
                            <div class="form-group">
                                <label for="title" class="form-label required">
                                    <i class="fas fa-tag me-2"></i>
                                    عنوان تگ
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       placeholder="مثال: فوری، *, !, کم‌اولویت، A" 
                                       required
                                       maxlength="100">
                                <small class="form-text">
                                    حداکثر 100 کاراکتر • تگ‌های تک کاراکتری بصورت دایره نمایش داده می‌شوند
                                </small>
                            </div>

                            <!-- انتخاب رنگ‌ها -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="color_start" class="form-label required">
                                            <i class="fas fa-palette me-2"></i>
                                            رنگ شروع گرادینت
                                        </label>
                                        <div class="color-input-group">
                                            <input type="color" 
                                                   class="form-control color-picker" 
                                                   id="color_start" 
                                                   name="color_start" 
                                                   value="<?= $randomGradient['color_start'] ?>"
                                                   required>
                                            <input type="text" 
                                                   class="form-control color-text" 
                                                   id="color_start_text"
                                                   value="<?= strtoupper($randomGradient['color_start']) ?>"
                                                   placeholder="#FF5733" 
                                                   pattern="^#[A-Fa-f0-9]{6}$">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="color_end" class="form-label required">
                                            <i class="fas fa-palette me-2"></i>
                                            رنگ پایان گرادینت
                                        </label>
                                        <div class="color-input-group">
                                            <input type="color" 
                                                   class="form-control color-picker" 
                                                   id="color_end" 
                                                   name="color_end" 
                                                   value="<?= $randomGradient['color_end'] ?>"
                                                   required>
                                            <input type="text" 
                                                   class="form-control color-text" 
                                                   id="color_end_text"
                                                   value="<?= strtoupper($randomGradient['color_end']) ?>"
                                                   placeholder="#C70039" 
                                                   pattern="^#[A-Fa-f0-9]{6}$">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- گالری قالب‌ها -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-swatchbook me-2"></i>
                                    قالب‌های آماده
                                </label>
                                <div class="gradient-gallery">
                                    <div class="gradient-item" data-start="#667eea" data-end="#764ba2" title="Purple Blue">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#f093fb" data-end="#f5576c" title="Pink Red">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #f093fb, #f5576c);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#4facfe" data-end="#00f2fe" title="Blue Cyan">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #4facfe, #00f2fe);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#43e97b" data-end="#38f9d7" title="Green Teal">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #43e97b, #38f9d7);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#fa709a" data-end="#fee140" title="Pink Yellow">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #fa709a, #fee140);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#ff9a9e" data-end="#fecfef" title="Coral Pink">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #ff9a9e, #fecfef);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#ffecd2" data-end="#fcb69f" title="Peach">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #ffecd2, #fcb69f);"></div>
                                    </div>
                                    <div class="gradient-item" data-start="#a8edea" data-end="#fed6e3" title="Aqua Pink">
                                        <div class="gradient-preview" style="background: linear-gradient(135deg, #a8edea, #fed6e3);"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- دکمه‌های عملیات -->
                            <div class="form-actions">
                                <button type="button" 
                                        id="random-gradient-btn" 
                                        class="btn btn-outline-primary btn-modern">
                                    <i class="fas fa-dice me-2"></i>
                                    گرادینت تصادفی
                                </button>
                                <div class="form-actions-main">
                                    <a href="<?= url('tags') ?>" class="btn btn-secondary btn-modern">
                                        <i class="fas fa-times me-2"></i>
                                        انصراف
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-modern">
                                        <i class="fas fa-save me-2"></i>
                                        ذخیره تگ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* استایل‌های خاص صفحه ایجاد تگ */
.create-tag-container {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

/* پیش‌نمایش زنده */
.live-preview-section {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: var(--space-6);
    text-align: center;
}

.preview-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-4);
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80px;
}

.tag-preview-live {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    border-radius: 24px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
    min-width: 48px;
    min-height: 48px;
    word-wrap: break-word;
    max-width: 300px;
}

/* تگ دایره‌ای برای یک کاراکتر */
.tag-preview-live.single-char {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    padding: 0;
    font-size: 18px;
    font-weight: bold;
}

/* فرم */
.form-section {
    width: 100%;
}

.form-group {
    margin-bottom: var(--space-5);
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--space-2);
}

.form-label.required::after {
    content: ' *';
    color: var(--danger);
}

.form-control {
    width: 100%;
    padding: var(--space-3);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    font-size: 14px;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(94, 58, 238, 0.1);
}

.form-text {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: var(--space-1);
}

/* Color Picker */
.color-input-group {
    display: flex;
    gap: var(--space-3);
    align-items: center;
}

.color-picker {
    width: 60px !important;
    height: 48px !important;
    border: none !important;
    border-radius: var(--radius-md) !important;
    cursor: pointer;
    padding: 0 !important;
}

.color-text {
    flex: 1;
    font-family: var(--font-mono) !important;
    text-transform: uppercase;
    font-size: 14px;
}

/* Gradient Gallery */
.gradient-gallery {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-3);
    margin-top: var(--space-2);
}

.gradient-item {
    cursor: pointer;
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: var(--transition);
    border: 2px solid transparent;
}

.gradient-item:hover {
    transform: scale(1.05);
    border-color: var(--primary);
}

.gradient-item.active {
    border-color: var(--primary);
    box-shadow: var(--shadow-md);
}

.gradient-preview {
    width: 100%;
    height: 50px;
    border-radius: var(--radius-sm);
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: var(--space-4);
    border-top: 1px solid var(--gray-200);
    margin-top: var(--space-6);
}

.form-actions-main {
    display: flex;
    gap: var(--space-3);
}

/* Dark Mode */
[data-theme="dark"] .live-preview-section,
[data-theme="dark"] .form-section .table-container {
    background: var(--gray-100);
    border-color: var(--gray-200);
}

[data-theme="dark"] .preview-title,
[data-theme="dark"] .form-label {
    color: var(--gray-800);
}

[data-theme="dark"] .form-text {
    color: var(--gray-600);
}

[data-theme="dark"] .form-control {
    background: var(--gray-200);
    border-color: var(--gray-300);
    color: var(--gray-800);
}

[data-theme="dark"] .form-control:focus {
    border-color: var(--primary);
    background: var(--gray-100);
}

/* Mobile Responsive */
@media (max-width: 767px) {
    .create-tag-container {
        margin: 0;
        gap: var(--space-4);
    }
    
    .live-preview-section {
        padding: var(--space-4);
    }
    
    .gradient-gallery {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-2);
    }
    
    .color-input-group {
        flex-direction: column;
        gap: var(--space-2);
    }
    
    .color-picker {
        width: 100% !important;
    }
    
    .form-actions {
        flex-direction: column;
        gap: var(--space-3);
        align-items: stretch;
    }
    
    .form-actions-main {
        justify-content: center;
    }
    
    .tag-preview-live {
        font-size: 14px;
        padding: 10px 20px;
    }
    
    .tag-preview-live.single-char {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .gradient-gallery {
        grid-template-columns: 1fr;
    }
    
    .form-actions-main {
        flex-direction: column;
    }
}
</style>

<script>
// Theme system handled by theme-system.js - no local override needed

document.addEventListener('DOMContentLoaded', function() {
    // عناصر فرم
    const form = document.getElementById('create-tag-form');
    const titleInput = document.getElementById('title');
    const colorStartPicker = document.getElementById('color_start');
    const colorEndPicker = document.getElementById('color_end');
    const colorStartText = document.getElementById('color_start_text');
    const colorEndText = document.getElementById('color_end_text');
    const tagPreview = document.getElementById('tag-preview');
    const randomBtn = document.getElementById('random-gradient-btn');
    
    // همگام‌سازی color picker و text input
    colorStartPicker.addEventListener('change', function() {
        colorStartText.value = this.value.toUpperCase();
        updatePreview();
    });
    
    colorEndPicker.addEventListener('change', function() {
        colorEndText.value = this.value.toUpperCase();
        updatePreview();
    });
    
    colorStartText.addEventListener('input', function() {
        if (/^#[A-Fa-f0-9]{6}$/.test(this.value)) {
            colorStartPicker.value = this.value;
            updatePreview();
        }
    });
    
    colorEndText.addEventListener('input', function() {
        if (/^#[A-Fa-f0-9]{6}$/.test(this.value)) {
            colorEndPicker.value = this.value;
            updatePreview();
        }
    });
    
    // بروزرسانی پیش‌نمایش با تغییر عنوان
    titleInput.addEventListener('input', updatePreview);
    
    // گرادینت تصادفی
    randomBtn.addEventListener('click', function() {
        fetch('<?= url('tags?action=randomGradient') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    colorStartPicker.value = data.data.color_start;
                    colorEndPicker.value = data.data.color_end;
                    colorStartText.value = data.data.color_start.toUpperCase();
                    colorEndText.value = data.data.color_end.toUpperCase();
                    updatePreview();
                }
            })
            .catch(error => console.error('خطا:', error));
    });
    
    // کلیک روی آیتم‌های گالری
    document.querySelectorAll('.gradient-item').forEach(item => {
        item.addEventListener('click', function() {
            // حذف کلاس active از همه
            document.querySelectorAll('.gradient-item').forEach(el => el.classList.remove('active'));
            
            // اضافه کردن کلاس active به آیتم کلیک شده
            this.classList.add('active');
            
            const startColor = this.dataset.start;
            const endColor = this.dataset.end;
            
            colorStartPicker.value = startColor;
            colorEndPicker.value = endColor;
            colorStartText.value = startColor.toUpperCase();
            colorEndText.value = endColor.toUpperCase();
            
            updatePreview();
        });
    });
    
    // تابع بروزرسانی پیش‌نمایش
    function updatePreview() {
        const title = titleInput.value.trim() || 'نمونه تگ';
        const colorStart = colorStartPicker.value;
        const colorEnd = colorEndPicker.value;
        
        // محاسبه رنگ متن بهینه
        const textColor = getOptimalTextColor(colorStart, colorEnd);
        
        // بروزرسانی پیش‌نمایش
        tagPreview.style.background = `linear-gradient(135deg, ${colorStart}, ${colorEnd})`;
        tagPreview.style.color = textColor;
        
        // بررسی طول متن برای تگ دایره‌ای
        if (title.length === 1) {
            tagPreview.classList.add('single-char');
            tagPreview.textContent = title;
        } else {
            tagPreview.classList.remove('single-char');
            // محدود کردن طول نمایش
            tagPreview.textContent = title.length > 20 ? title.substring(0, 20) + '...' : title;
        }
    }
    
    // محاسبه رنگ متن بهینه
    function getOptimalTextColor(colorStart, colorEnd) {
        const luminanceStart = getLuminance(colorStart);
        const luminanceEnd = getLuminance(colorEnd);
        const averageLuminance = (luminanceStart + luminanceEnd) / 2;
        
        return averageLuminance > 0.5 ? '#1f2937' : '#ffffff';
    }
    
    // محاسبه روشنایی رنگ
    function getLuminance(hex) {
        const r = parseInt(hex.substr(1, 2), 16) / 255;
        const g = parseInt(hex.substr(3, 2), 16) / 255;
        const b = parseInt(hex.substr(5, 2), 16) / 255;
        
        const rLum = r <= 0.03928 ? r / 12.92 : Math.pow((r + 0.055) / 1.055, 2.4);
        const gLum = g <= 0.03928 ? g / 12.92 : Math.pow((g + 0.055) / 1.055, 2.4);
        const bLum = b <= 0.03928 ? b / 12.92 : Math.pow((b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * rLum + 0.7152 * gLum + 0.0722 * bLum;
    }
    
    // اولیه‌سازی پیش‌نمایش
    updatePreview();
    
    // ارسال فرم
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // بررسی اعتبار
        if (!titleInput.value.trim()) {
            showAlert('عنوان تگ الزامی است', 'error');
            return;
        }
        
        // ارسال فرم
        this.submit();
    });
});

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