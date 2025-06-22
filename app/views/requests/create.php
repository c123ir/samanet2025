<?php
/**
 * نام فایل: create.php
 * مسیر فایل: /app/views/requests/create.php
 * توضیح: فرم ایجاد درخواست حواله جدید
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تنظیم متغیرهای صفحه
$page_title = $title ?? 'ایجاد درخواست حواله جدید';
$page_description = 'فرم ایجاد درخواست حواله با امکانات پیشرفته';
$active_menu = 'requests';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}
?>

<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    ایجاد درخواست حواله جدید
                </h1>
                <p class="page-subtitle text-muted">
                    لطفاً اطلاعات درخواست حواله را با دقت تکمیل کنید
                </p>
            </div>
            <div>
                <a href="?route=requests" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Main Form -->
    <div class="row">
        <div class="col-12">
            <div class="flat-card">
                <div class="card-body p-4">
                    <form id="requestForm" method="POST" action="?route=requests&action=store" class="needs-validation" novalidate>
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                        <!-- اطلاعات اصلی درخواست -->
                        <div class="form-section mb-5">
                            <h3 class="section-title">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                اطلاعات اصلی درخواست
                            </h3>
                            <div class="section-divider"></div>

                            <div class="row g-3">
                                <!-- عنوان درخواست -->
                                <div class="col-md-8">
                                    <label for="title" class="form-label required">عنوان درخواست</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="title" 
                                           name="title" 
                                           placeholder="مثال: پرداخت حقوق فروردین ماه"
                                           required
                                           maxlength="200">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان درخواست را وارد کنید
                                    </div>
                                    <div class="form-text">حداکثر ۲۰۰ کاراکتر</div>
                                </div>

                                <!-- اولویت -->
                                <div class="col-md-4">
                                    <label for="priority" class="form-label">اولویت</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <?php foreach ($priorities as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= $value === 'normal' ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- توضیحات -->
                                <div class="col-12">
                                    <label for="description" class="form-label">توضیحات تکمیلی</label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="3"
                                              placeholder="توضیحات اضافی در مورد این درخواست..."></textarea>
                                    <div class="form-text">اختیاری - توضیحات بیشتر درباره درخواست</div>
                                </div>

                                <!-- مبلغ -->
                                <div class="col-md-6">
                                    <label for="amount" class="form-label required">مبلغ (ریال)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control number-input" 
                                               id="amount" 
                                               name="amount" 
                                               placeholder="۱۰۰۰۰۰۰"
                                               data-persian-convert="true"
                                               required>
                                        <span class="input-group-text">ریال</span>
                                    </div>
                                    <div class="invalid-feedback">
                                        لطفاً مبلغ درخواست را وارد کنید
                                    </div>
                                    <div class="amount-words text-muted small mt-1"></div>
                                </div>

                                <!-- دسته‌بندی -->
                                <div class="col-md-6">
                                    <label for="category" class="form-label">دسته‌بندی</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">انتخاب دسته‌بندی...</option>
                                        <?php foreach ($categories as $value => $label): ?>
                                            <option value="<?= $value ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- تاریخ سررسید -->
                                <div class="col-md-6">
                                    <label for="due_date" class="form-label">تاریخ سررسید</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="due_date" 
                                           name="due_date">
                                    <div class="form-text">اختیاری - تاریخ مطلوب برای پرداخت</div>
                                </div>

                                <!-- درخواست فوری -->
                                <div class="col-md-6">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_urgent" 
                                               name="is_urgent"
                                               value="1">
                                        <label class="form-check-label" for="is_urgent">
                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                            درخواست فوری
                                        </label>
                                    </div>
                                    <div class="form-text">برای درخواست‌های ضروری علامت بزنید</div>
                                </div>
                            </div>
                        </div>

                        <!-- اطلاعات حساب مقصد -->
                        <div class="form-section mb-5">
                            <h3 class="section-title">
                                <i class="fas fa-university text-success me-2"></i>
                                اطلاعات حساب مقصد
                            </h3>
                            <div class="section-divider"></div>

                            <div class="row g-3">
                                <!-- نام صاحب حساب -->
                                <div class="col-md-6">
                                    <label for="account_holder" class="form-label required">نام صاحب حساب</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="account_holder" 
                                           name="account_holder" 
                                           placeholder="نام و نام خانوادگی صاحب حساب"
                                           required
                                           maxlength="100">
                                    <div class="invalid-feedback">
                                        لطفاً نام صاحب حساب را وارد کنید
                                    </div>
                                </div>

                                <!-- بانک -->
                                <div class="col-md-6">
                                    <label for="bank_name" class="form-label required">بانک</label>
                                    <select class="form-select" id="bank_name" name="bank_name" required>
                                        <option value="">انتخاب بانک...</option>
                                        <?php foreach ($banks as $value => $label): ?>
                                            <option value="<?= $value ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        لطفاً بانک را انتخاب کنید
                                    </div>
                                </div>

                                <!-- شماره حساب -->
                                <div class="col-md-6">
                                    <label for="account_number" class="form-label required">شماره حساب</label>
                                    <input type="text" 
                                           class="form-control number-input" 
                                           id="account_number" 
                                           name="account_number" 
                                           placeholder="۱۲۳۴۵۶۷۸۹۰۱۲۳۴۵۶"
                                           data-persian-convert="true"
                                           required
                                           maxlength="20">
                                    <div class="invalid-feedback">
                                        لطفاً شماره حساب را وارد کنید
                                    </div>
                                    <div class="form-text">شماره حساب بدون خط تیره</div>
                                </div>

                                <!-- شماره کارت -->
                                <div class="col-md-6">
                                    <label for="card_number" class="form-label">شماره کارت</label>
                                    <input type="text" 
                                           class="form-control number-input" 
                                           id="card_number" 
                                           name="card_number" 
                                           placeholder="۱۲۳۴-۵۶۷۸-۹۰۱۲-۳۴۵۶"
                                           data-persian-convert="true"
                                           maxlength="19">
                                    <div class="form-text">اختیاری - شماره کارت ۱۶ رقمی</div>
                                </div>

                                <!-- شماره شبا -->
                                <div class="col-12">
                                    <label for="iban" class="form-label">شماره شبا</label>
                                    <div class="input-group">
                                        <span class="input-group-text">IR</span>
                                        <input type="text" 
                                               class="form-control number-input" 
                                               id="iban" 
                                               name="iban" 
                                               placeholder="۱۲۳۴۵۶۷۸۹۰۱۲۳۴۵۶۷۸۹۰۱۲۳۴"
                                               data-persian-convert="true"
                                               maxlength="24">
                                    </div>
                                    <div class="form-text">اختیاری - شماره شبا ۲۴ رقمی بدون IR</div>
                                </div>
                            </div>
                        </div>

                        <!-- تگ‌ها و اطلاعات اضافی -->
                        <div class="form-section mb-5">
                            <h3 class="section-title">
                                <i class="fas fa-tags text-info me-2"></i>
                                اطلاعات اضافی
                            </h3>
                            <div class="section-divider"></div>

                            <div class="row g-3">
                                <!-- تگ‌ها -->
                                <div class="col-12">
                                    <label for="tags" class="form-label">تگ‌ها</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="tags" 
                                           name="tags" 
                                           placeholder="حقوق، فروردین، کارمندان">
                                    <div class="form-text">
                                        تگ‌ها را با کاما جدا کنید - برای جستجوی بهتر
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- دکمه‌های عملیات -->
                        <div class="form-actions">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                        <i class="fas fa-times me-2"></i>
                                        انصراف
                                    </button>
                                </div>
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2">
                                        <i class="fas fa-undo me-2"></i>
                                        پاک کردن فرم
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        ثبت درخواست
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript مخصوص این صفحه -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('requestForm');
    const amountInput = document.getElementById('amount');
    const amountWordsDiv = document.querySelector('.amount-words');
    const cardNumberInput = document.getElementById('card_number');
    
    // تبدیل خودکار اعداد فارسی
    const numberInputs = document.querySelectorAll('.number-input');
    numberInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = window.Samanet.toEnglishNumbers(e.target.value);
            
            // مخصوص شماره کارت - فرمت ۴ رقمی
            if (e.target.id === 'card_number') {
                value = value.replace(/\D/g, '');
                value = value.replace(/(\d{4})(?=\d)/g, '$1-');
                if (value.length > 19) {
                    value = value.substring(0, 19);
                }
            }
            // مخصوص مبلغ - حذف کاراکترهای غیرعددی
            else if (e.target.id === 'amount') {
                value = value.replace(/\D/g, '');
                // اضافه کردن کاما برای هزارگان
                if (value) {
                    const formatted = parseInt(value).toLocaleString('fa-IR');
                    e.target.value = formatted;
                    updateAmountWords(parseInt(value));
                    return;
                }
            }
            // سایر فیلدهای عددی
            else {
                value = value.replace(/\D/g, '');
            }
            
            e.target.value = value;
        });
    });
    
    // تبدیل مبلغ به حروف
    function updateAmountWords(amount) {
        if (amount && amount > 0) {
            const words = numberToWords(amount);
            amountWordsDiv.textContent = words + ' ریال';
            amountWordsDiv.classList.add('text-success');
        } else {
            amountWordsDiv.textContent = '';
            amountWordsDiv.classList.remove('text-success');
        }
    }
    
    // تابع ساده تبدیل عدد به حروف
    function numberToWords(num) {
        if (num < 1000) return num + ' ریال';
        if (num < 1000000) return Math.floor(num / 1000) + ' هزار ریال';
        if (num < 1000000000) return Math.floor(num / 1000000) + ' میلیون ریال';
        return Math.floor(num / 1000000000) + ' میلیارد ریال';
    }
    
    // اعتبارسنجی فرم
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            
            // فوکوس روی اولین فیلد خطا
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
        
        // نمایش loading
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال ثبت...';
        submitBtn.disabled = true;
        
        // ارسال فرم
        form.submit();
    });
    
    // Reset form
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        amountWordsDiv.textContent = '';
        amountWordsDiv.classList.remove('text-success');
    });
    
    // Auto-resize textarea
    const textarea = document.getElementById('description');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});
</script>

<style>
/* استایل‌های مخصوص این صفحه */
.form-section {
    position: relative;
    padding: 1.5rem;
    background: var(--glass-bg);
    border-radius: 15px;
    backdrop-filter: var(--glass-blur);
    border: 1px solid var(--glass-border);
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color), transparent);
    margin-bottom: 1.5rem;
    border-radius: 1px;
}

.form-label.required::after {
    content: " *";
    color: var(--danger-color);
    font-weight: bold;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 1px solid var(--glass-border);
    background: var(--glass-bg);
    backdrop-filter: var(--glass-blur);
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: rgba(255, 255, 255, 0.1);
}

.form-actions {
    padding: 2rem 1.5rem 1rem;
    background: var(--glass-bg);
    border-radius: 15px;
    backdrop-filter: var(--glass-blur);
    border: 1px solid var(--glass-border);
}

.btn {
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.amount-words {
    font-style: italic;
    transition: all 0.3s ease;
}

.invalid-feedback {
    display: block;
    font-size: 0.875rem;
}

.was-validated .form-control:invalid {
    border-color: var(--danger-color);
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3E%3Ccircle cx='6' cy='6' r='4.5'/%3E%3Cpath d='M5.8 3.6h.4L6 6.5z'/%3E%3Ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:valid {
    border-color: var(--success-color);
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2328a745' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/* Animation for form sections */
.form-section {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-section {
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
    
    .form-actions {
        padding: 1.5rem 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        width: 100%;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
