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
                        <input type="hidden" name="_token" value="<?= $csrf_token ?>">

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
                                    <label for="amount" class="form-label">مبلغ (ریال)</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control number-input" 
                                               id="amount" 
                                               name="amount" 
                                               placeholder="۱،۰۰۰،۰۰۰"
                                               data-persian-convert="true"
                                               maxlength="20">
                                        <span class="input-group-text">ریال</span>
                                    </div>
                                    <div class="form-text">اختیاری - مبلغ درخواست</div>
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
                                    <label for="account_holder" class="form-label">نام صاحب حساب</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="account_holder" 
                                           name="account_holder" 
                                           placeholder="نام و نام خانوادگی صاحب حساب"
                                           maxlength="100">
                                    <div class="form-text">اختیاری - نام صاحب حساب</div>
                                </div>

                                <!-- بانک -->
                                <div class="col-md-6">
                                    <label for="bank_name" class="form-label">بانک</label>
                                    <select class="form-select" id="bank_name" name="bank_name">
                                        <option value="">انتخاب بانک...</option>
                                        <?php foreach ($banks as $value => $label): ?>
                                            <option value="<?= $value ?>"><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">اختیاری - نام بانک</div>
                                </div>

                                <!-- شماره حساب -->
                                <div class="col-md-6">
                                    <label for="account_number" class="form-label">شماره حساب</label>
                                    <input type="text" 
                                           class="form-control number-input" 
                                           id="account_number" 
                                           name="account_number" 
                                           placeholder="۱۲۳۴۵۶۷۸۹۰۱۲۳۴۵۶"
                                           data-persian-convert="true"
                                           maxlength="20">
                                    <div class="form-text">اختیاری - شماره حساب بدون خط تیره</div>
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
    
    // Debug: چک کردن وجود المان‌ها
    console.log('🔍 Amount input exists:', !!amountInput);
    console.log('🔍 Amount words div exists:', !!amountWordsDiv);
    
    if (!amountInput) {
        console.error('❌ Amount input field not found!');
        return;
    }
    
    // تست اولیه - Event listener ساده
    amountInput.addEventListener('keyup', function(e) {
        console.log('⌨️ Keyup event triggered:', e.target.value);
    });
    
    // Event listener مخصوص فیلد مبلغ - ساده و قابل اعتماد
    amountInput.addEventListener('input', function(e) {
        console.log('🚀 Input event triggered! Value:', e.target.value); // Debug
        
        // تبدیل اعداد فارسی به انگلیسی (fallback)
        let value = e.target.value;
        
        // تبدیل اعداد فارسی
        value = value.replace(/[۰-۹]/g, function(match) {
            return String.fromCharCode(match.charCodeAt(0) - '۰'.charCodeAt(0) + '0'.charCodeAt(0));
        });
        
        console.log('💰 After Persian conversion:', value); // Debug
        
        // حذف همه کاراکترهای غیرعددی
        const cleanValue = value.replace(/[^\d]/g, '');
        console.log('🧹 Clean value:', cleanValue); // Debug
        
        if (cleanValue && cleanValue !== '') {
            // فرمت با کاما
            const formatted = addCommas(cleanValue);
            console.log('✨ Formatted:', formatted); // Debug
            
            // تنظیم مقدار بدون cursor positioning
            e.target.value = formatted;
            
            // بروزرسانی متن توضیحی
            updateAmountWords(parseInt(cleanValue));
        } else {
            e.target.value = '';
            updateAmountWords(0);
        }
    });
    
    // Event listener های اضافی برای فیلد amount
    amountInput.addEventListener('blur', function(e) {
        if (e.target.value) {
            // اطمینان از فرمت صحیح هنگام خروج از فیلد
            const cleanValue = e.target.value.replace(/\D/g, '');
            if (cleanValue) {
                e.target.value = formatNumberWithCommas(cleanValue);
                updateAmountWords(parseInt(cleanValue));
            }
        }
    });
    
    // انتخاب متن هنگام focus
    amountInput.addEventListener('focus', function(e) {
        // انتخاب تمام متن برای تایپ راحت‌تر
        setTimeout(() => {
            e.target.select();
        }, 10);
    });
    
    // تبدیل خودکار اعداد فارسی برای سایر فیلدها
    const numberInputs = document.querySelectorAll('.number-input:not(#amount)');
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
            // سایر فیلدهای عددی
            else {
                value = value.replace(/\D/g, '');
            }
            
            e.target.value = value;
        });
    });
    
    // تابع ساده اضافه کردن کاما
    function addCommas(num) {
        console.log('🔧 Adding commas to:', num); // Debug
        
        if (!num || num === '' || num === '0') {
            return '';
        }
        
        // تبدیل به رشته و حذف کاماهای قبلی
        const str = num.toString().replace(/,/g, '');
        
        // اضافه کردن کاما از راست به چپ
        return str.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // تابع قدیمی نگه داشتن برای سازگاری
    function formatNumberWithCommas(number) {
        return addCommas(number);
    }
    

    
    // حذف کاما از عدد برای ارسال به سرور
    function removeCommas(numberStr) {
        return numberStr.replace(/,/g, '');
    }
    
    // تبدیل مبلغ به حروف
    function updateAmountWords(amount) {
        if (amount && amount > 0) {
            const words = numberToWords(amount);
            amountWordsDiv.textContent = words;
            amountWordsDiv.classList.add('text-success');
        } else {
            amountWordsDiv.textContent = '';
            amountWordsDiv.classList.remove('text-success');
        }
    }
    
    // تابع بهبود یافته تبدیل عدد به حروف
    function numberToWords(num) {
        if (!num || num === 0) return '';
        
        const persianNumbers = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
        
        if (num < 1000) {
            return formatNumberWithCommas(num) + ' ریال';
        } else if (num < 1000000) {
            const thousands = Math.floor(num / 1000);
            const remainder = num % 1000;
            
            if (remainder === 0) {
                return formatNumberWithCommas(thousands) + ' هزار ریال';
            } else {
                return formatNumberWithCommas(thousands) + ' هزار و ' + formatNumberWithCommas(remainder) + ' ریال';
            }
        } else if (num < 1000000000) {
            const millions = Math.floor(num / 1000000);
            return formatNumberWithCommas(millions) + ' میلیون ریال';
        } else if (num < 1000000000000) {
            const billions = Math.floor(num / 1000000000);
            return formatNumberWithCommas(billions) + ' میلیارد ریال';
        } else {
            return 'مبلغ بسیار زیاد!';
        }
    }
    
    // اعتبارسنجی فرم
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('🚀 Form submit event triggered');
        
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            console.log('❌ Form validation failed');
            
            // فوکوس روی اولین فیلد خطا
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                console.log('🎯 Focus on invalid field:', firstInvalid.name);
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
        
        console.log('✅ Form validation passed');
        
        // حذف کاما از فیلد مبلغ قبل از ارسال
        const amountField = document.getElementById('amount');
        if (amountField && amountField.value) {
            amountField.value = removeCommas(amountField.value);
        }
        
        // جمع‌آوری تمام داده‌های فرم برای logging
        const formData = new FormData(form);
        const formObject = {};
        for (let [key, value] of formData.entries()) {
            formObject[key] = value;
        }
        console.log('📋 Form data to be submitted:', formObject);
        
        // نمایش loading
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>در حال ثبت...';
        submitBtn.disabled = true;
        console.log('⏳ Submit button set to loading state');
        
        // ارسال فرم با AJAX
        console.log('📤 Submitting form to:', form.action);
        submitFormAjax(form, submitBtn, originalText);
    });
    
    // تابع ارسال فرم با AJAX
    async function submitFormAjax(form, submitBtn, originalText) {
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('📥 Response status:', response.status);
            
            const result = await response.json();
            console.log('📥 Response data:', result);
            
            if (result.success) {
                // حذف کلاس was-validated برای جلوگیری از beforeunload
                form.classList.remove('was-validated');
                
                // بازنشانی دکمه submit
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>درخواست ثبت شد';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                
                // نمایش پیام موفقیت
                showSuccessMessage('درخواست با موفقیت ایجاد شد!');
                
                // ریدایرکت بعد از 2 ثانیه (زمان بیشتر برای نمایش پیام)
                setTimeout(() => {
                    // Smooth transition
                    document.body.style.opacity = '0.7';
                    document.body.style.transition = 'opacity 0.3s ease';
                    
                    setTimeout(() => {
                        if (result.redirect_url) {
                            window.location.href = result.redirect_url;
                        } else {
                            window.location.href = '?route=requests';
                        }
                    }, 300);
                }, 2000);
            } else {
                // نمایش پیام خطا
                showErrorMessage(result.message || 'خطا در ایجاد درخواست');
                
                // بازگردانی فرمت فیلد amount
                if (amountField && amountField.value) {
                    amountField.value = formatNumberWithCommas(amountField.value);
                }
                
                // بازگردانی دکمه
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            
        } catch (error) {
            console.error('❌ Error submitting form:', error);
            showErrorMessage('خطا در ارسال درخواست');
            
            // بازگردانی فرمت فیلد amount
            if (amountField && amountField.value) {
                amountField.value = formatNumberWithCommas(amountField.value);
            }
            
            // بازگردانی دکمه
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }
    
    // نمایش پیام موفقیت
    function showSuccessMessage(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // اضافه کردن به ابتدای صفحه
        const contentWrapper = document.querySelector('.content-wrapper');
        contentWrapper.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Scroll به بالا
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // نمایش پیام خطا
    function showErrorMessage(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // اضافه کردن به ابتدای صفحه
        const contentWrapper = document.querySelector('.content-wrapper');
        contentWrapper.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Scroll به بالا
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Reset form
    form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        amountWordsDiv.textContent = '';
        amountWordsDiv.classList.remove('text-success');
        
        // پاک کردن فیلد amount
        setTimeout(() => {
            const amountField = document.getElementById('amount');
            if (amountField) {
                amountField.value = '';
            }
        }, 10);
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

/* استایل مخصوص فیلد amount */
#amount {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    text-align: left;
    direction: ltr;
    font-size: 1.1rem;
}

#amount:focus {
    font-size: 1.2rem;
    transition: font-size 0.2s ease;
}

.amount-words {
    font-weight: 500;
    padding: 0.5rem;
    border-radius: 8px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    margin-top: 0.5rem;
    min-height: 2.5rem;
    display: flex;
    align-items: center;
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
