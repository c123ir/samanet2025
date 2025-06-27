<?php
/**
 * نام فایل: create.php
 * مسیر فایل: /app/views/tags/create.php
 * توضیح: صفحه ایجاد تگ جدید - Bootstrap 5 Modern Design
 * تاریخ ایجاد: 1404/10/15
 * نسخه: 3.0 مدرن
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
            <h1 class="h3 mb-1 text-gray-800">ایجاد تگ جدید</h1>
            <p class="text-muted mb-0">ایجاد تگ جدید با گرادینت سفارشی</p>
        </div>
        <div>
            <a href="<?= url('tags') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>بازگشت
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row justify-content-center p-3">
        <div class="col-xl-8 col-lg-10">
            <!-- Live Preview -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-eye text-primary me-2"></i>
                        پیش‌نمایش زنده
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    <div id="tag-preview" 
                         class="d-inline-flex align-items-center justify-content-center"
                         style="background: linear-gradient(135deg, <?= $randomGradient['color_start'] ?>, <?= $randomGradient['color_end'] ?>); 
                                color: <?= $randomGradient['text_color'] ?>;
                                padding: 8px 20px;
                                border-radius: 20px;
                                font-size: 14px;
                                font-weight: 600;
                                min-width: 80px;
                                min-height: 36px;">
                        نمونه تگ
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <small>تگ شما اینطور نمایش داده خواهد شد</small>
                    </p>
                </div>
            </div>

            <!-- Create Form -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        اطلاعات تگ جدید
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= url('tags?action=store') ?>" id="create-tag-form">
                        
                        <!-- عنوان تگ -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">
                                <i class="fas fa-tag text-secondary me-2"></i>
                                عنوان تگ <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="title" 
                                   name="title" 
                                   placeholder="مثال: فوری، اولویت بالا، ویژه"
                                   required
                                   maxlength="100">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                حداکثر 100 کاراکتر • تگ‌های تک کاراکتری بصورت دایره نمایش داده می‌شوند
                            </div>
                        </div>

                        <!-- انتخاب رنگ‌ها -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="color_start" class="form-label fw-bold">
                                        <i class="fas fa-palette text-primary me-2"></i>
                                        رنگ شروع گرادینت <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="color" 
                                               class="form-control form-control-color" 
                                               id="color_start" 
                                               name="color_start" 
                                               value="<?= $randomGradient['color_start'] ?>"
                                               style="width: 60px;"
                                               required>
                                        <input type="text" 
                                               class="form-control text-uppercase font-monospace" 
                                               id="color_start_text"
                                               value="<?= strtoupper($randomGradient['color_start']) ?>"
                                               placeholder="#667EEA"
                                               maxlength="7">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="color_end" class="form-label fw-bold">
                                        <i class="fas fa-palette text-info me-2"></i>
                                        رنگ پایان گرادینت <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="color" 
                                               class="form-control form-control-color" 
                                               id="color_end" 
                                               name="color_end" 
                                               value="<?= $randomGradient['color_end'] ?>"
                                               style="width: 60px;"
                                               required>
                                        <input type="text" 
                                               class="form-control text-uppercase font-monospace" 
                                               id="color_end_text"
                                               value="<?= strtoupper($randomGradient['color_end']) ?>"
                                               placeholder="#764BA2"
                                               maxlength="7">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- قالب‌های آماده -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-swatchbook text-warning me-2"></i>
                                قالب‌های آماده
                            </label>
                            <div class="gradient-templates">
                                <div class="row g-2">
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#667eea" data-end="#764ba2" title="Purple Blue">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#f093fb" data-end="#f5576c" title="Pink Red">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #f093fb, #f5576c);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#4facfe" data-end="#00f2fe" title="Blue Cyan">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #4facfe, #00f2fe);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#43e97b" data-end="#38f9d7" title="Green Teal">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #43e97b, #38f9d7);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#fa709a" data-end="#fee140" title="Pink Yellow">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #fa709a, #fee140);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#ff9a9e" data-end="#fecfef" title="Coral Pink">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #ff9a9e, #fecfef);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#ffecd2" data-end="#fcb69f" title="Peach">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #ffecd2, #fcb69f);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#a8edea" data-end="#fed6e3" title="Aqua Pink">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #a8edea, #fed6e3);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#ff6b6b" data-end="#feca57" title="Red Orange">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #ff6b6b, #feca57);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#5f27cd" data-end="#341f97" title="Purple Dark">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #5f27cd, #341f97);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#54a0ff" data-end="#2e86de" title="Blue Ocean">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #54a0ff, #2e86de);"></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-md-2">
                                        <div class="template-item" data-start="#5f27cd" data-end="#00d2d3" title="Purple Cyan">
                                            <div class="template-preview" style="background: linear-gradient(135deg, #5f27cd, #00d2d3);"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i class="fas fa-click me-1"></i>
                                روی هر قالب کلیک کنید تا به‌کار گرفته شود
                            </div>
                        </div>

                        <!-- دکمه‌های عملیات -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <button type="button" id="random-gradient-btn" class="btn btn-outline-secondary">
                                <i class="fas fa-dice me-2"></i>گرادینت تصادفی
                            </button>
                            
                            <div class="d-flex gap-2">
                                <a href="<?= url('tags') ?>" class="btn btn-light">
                                    <i class="fas fa-times me-2"></i>انصراف
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>ذخیره تگ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* استایل‌های سفارشی */
.form-control-color {
    border: 1px solid #ced4da !important;
    border-radius: 6px !important;
}

.template-item {
    cursor: pointer;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s ease;
    border: 2px solid transparent;
    aspect-ratio: 1;
}

.template-item:hover {
    transform: scale(1.05);
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.template-item.active {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
}

.template-preview {
    width: 100%;
    height: 100%;
    border-radius: 6px;
}

#tag-preview {
    transition: all 0.3s ease;
}

.gradient-templates {
    margin-top: 0.5rem;
}

/* تگ دایره‌ای برای یک کاراکتر */
.single-char {
    border-radius: 50% !important;
    width: 40px !important;
    height: 40px !important;
    padding: 0 !important;
    font-size: 16px !important;
    font-weight: bold !important;
}

/* Responsive */
@media (max-width: 768px) {
    .gradient-templates .row {
        --bs-gutter-x: 0.5rem;
    }
    
    .template-item {
        margin-bottom: 0.5rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.gap-2 {
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر فرم
    const titleInput = document.getElementById('title');
    const colorStartPicker = document.getElementById('color_start');
    const colorEndPicker = document.getElementById('color_end');
    const colorStartText = document.getElementById('color_start_text');
    const colorEndText = document.getElementById('color_end_text');
    const tagPreview = document.getElementById('tag-preview');
    const randomBtn = document.getElementById('random-gradient-btn');
    const templateItems = document.querySelectorAll('.template-item');

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
        if (isValidHex(this.value)) {
            colorStartPicker.value = this.value;
            updatePreview();
        }
    });

    colorEndText.addEventListener('input', function() {
        if (isValidHex(this.value)) {
            colorEndPicker.value = this.value;
            updatePreview();
        }
    });

    // تغییر عنوان
    titleInput.addEventListener('input', updatePreview);

    // انتخاب قالب آماده
    templateItems.forEach(item => {
        item.addEventListener('click', function() {
            const startColor = this.dataset.start;
            const endColor = this.dataset.end;
            
            colorStartPicker.value = startColor;
            colorEndPicker.value = endColor;
            colorStartText.value = startColor.toUpperCase();
            colorEndText.value = endColor.toUpperCase();
            
            // حذف active از همه و اضافه به انتخاب شده
            templateItems.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            updatePreview();
        });
    });

    // گرادینت تصادفی
    randomBtn.addEventListener('click', function() {
        const randomGradient = generateRandomGradient();
        
        colorStartPicker.value = randomGradient.start;
        colorEndPicker.value = randomGradient.end;
        colorStartText.value = randomGradient.start.toUpperCase();
        colorEndText.value = randomGradient.end.toUpperCase();
        
        // حذف active از همه قالب‌ها
        templateItems.forEach(t => t.classList.remove('active'));
        
        updatePreview();
    });

    // تابع بروزرسانی پیش‌نمایش
    function updatePreview() {
        const title = titleInput.value || 'نمونه تگ';
        const startColor = colorStartPicker.value;
        const endColor = colorEndPicker.value;
        const textColor = getOptimalTextColor(startColor, endColor);
        
        tagPreview.style.background = `linear-gradient(135deg, ${startColor}, ${endColor})`;
        tagPreview.style.color = textColor;
        tagPreview.textContent = title;
        
        // تگ دایره‌ای برای یک کاراکتر
        if (title.length === 1) {
            tagPreview.classList.add('single-char');
        } else {
            tagPreview.classList.remove('single-char');
        }
    }

    // تولید گرادینت تصادفی
    function generateRandomGradient() {
        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
            '#F8C471', '#82E0AA', '#F1948A', '#85C1E9', '#F4D03F'
        ];
        
        const start = colors[Math.floor(Math.random() * colors.length)];
        let end;
        do {
            end = colors[Math.floor(Math.random() * colors.length)];
        } while (end === start);
        
        return { start, end };
    }

    // بررسی معتبر بودن کد رنگ hex
    function isValidHex(hex) {
        return /^#[0-9A-F]{6}$/i.test(hex);
    }

    // تعیین رنگ بهینه متن
    function getOptimalTextColor(startColor, endColor) {
        // محاسبه میانگین luminance دو رنگ
        const startLuminance = getLuminance(startColor);
        const endLuminance = getLuminance(endColor);
        const avgLuminance = (startLuminance + endLuminance) / 2;
        
        return avgLuminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    // محاسبه luminance رنگ
    function getLuminance(hex) {
        const rgb = hexToRgb(hex);
        if (!rgb) return 0;
        
        const [r, g, b] = [rgb.r, rgb.g, rgb.b].map(c => {
            c = c / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        
        return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    }

    // تبدیل hex به RGB
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    // بروزرسانی اولیه
    updatePreview();
});
</script> 