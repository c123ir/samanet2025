<?php
/**
 * نام فایل: create.php
 * مسیر فایل: /app/views/users/create.php
 * توضیح: فرم ایجاد کاربر جدید - طراحی flat و responsive
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */
?>

<!-- Page Header -->
<div class="flat-header mb-4">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h1 class="flat-title">
                    <span class="flat-icon">➕</span>
                    ایجاد کاربر جدید
                </h1>
                <p class="flat-subtitle">
                    اضافه کردن کاربر جدید به سیستم
                </p>
            </div>
            <div>
                <a href="<?= url('users') ?>" class="btn-flat btn-flat-secondary">
                    <span class="btn-icon">🔙</span>
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Create User Form -->
<div class="form-container">
    <div class="flat-card">
        <div class="flat-card-header">
            <h6 class="flat-card-title">
                <span class="card-icon">👤</span>
                اطلاعات کاربر جدید
            </h6>
        </div>
        <div class="flat-card-body">
            <form method="POST" action="<?= url('users/create') ?>" id="createUserForm" class="flat-form">
                <div class="form-grid">
                    <!-- نام کاربری -->
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <span class="label-icon">👤</span>
                            نام کاربری
                            <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-input" 
                               id="username" 
                               name="username" 
                               placeholder="نام کاربری منحصر به فرد"
                               value="<?= htmlspecialchars($old_data['username'] ?? '') ?>"
                               required>
                        <div class="form-help">فقط حروف انگلیسی، اعداد و خط زیر مجاز است</div>
                    </div>

                    <!-- رمز عبور -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <span class="label-icon">🔒</span>
                            رمز عبور
                            <span class="required">*</span>
                        </label>
                        <div class="password-input-group">
                            <input type="password" 
                                   class="form-input" 
                                   id="password" 
                                   name="password" 
                                   placeholder="رمز عبور قوی"
                                   required>
                            <button class="password-toggle" 
                                    type="button" 
                                    onclick="togglePassword('password')">
                                <span id="password-icon">👁️</span>
                            </button>
                        </div>
                        <div class="form-help">حداقل 8 کاراکتر شامل حرف، عدد و علامت</div>
                    </div>

                    <!-- نام کامل -->
                    <div class="form-group">
                        <label for="full_name" class="form-label">
                            <span class="label-icon">📛</span>
                            نام کامل
                            <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-input" 
                               id="full_name" 
                               name="full_name" 
                               placeholder="نام و نام خانوادگی"
                               value="<?= htmlspecialchars($old_data['full_name'] ?? '') ?>"
                               required>
                    </div>

                    <!-- ایمیل -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <span class="label-icon">📧</span>
                            آدرس ایمیل
                        </label>
                        <input type="email" 
                               class="form-input" 
                               id="email" 
                               name="email" 
                               placeholder="example@domain.com"
                               value="<?= htmlspecialchars($old_data['email'] ?? '') ?>">
                    </div>

                    <!-- شماره موبایل -->
                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <span class="label-icon">📱</span>
                            شماره موبایل
                        </label>
                        <input type="tel" 
                               class="form-input number-input" 
                               id="phone" 
                               name="phone" 
                               placeholder="09123456789"
                               maxlength="11"
                               data-persian-convert="true"
                               value="<?= htmlspecialchars($old_data['phone'] ?? '') ?>">
                        <div class="form-help">مثال: 09123456789</div>
                    </div>

                    <!-- نقش کاربری -->
                    <div class="form-group">
                        <label for="role" class="form-label">
                            <span class="label-icon">🏷️</span>
                            نقش کاربری
                            <span class="required">*</span>
                        </label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">انتخاب نقش...</option>
                            <?php foreach ($roles as $roleKey => $roleLabel): ?>
                                <option value="<?= $roleKey ?>" 
                                        <?= ($old_data['role'] ?? '') === $roleKey ? 'selected' : '' ?>>
                                    <?= $roleLabel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- وضعیت کاربر -->
                <div class="form-section">
                    <label class="section-label">
                        <span class="label-icon">⚡</span>
                        وضعیت کاربر
                    </label>
                    <div class="radio-group">
                        <label class="radio-item radio-success">
                            <input type="radio" 
                                   name="status" 
                                   value="active" 
                                   <?= ($old_data['status'] ?? 'active') === 'active' ? 'checked' : '' ?>>
                            <span class="radio-check"></span>
                            <span class="radio-icon">✅</span>
                            <span class="radio-text">فعال</span>
                        </label>
                        <label class="radio-item radio-warning">
                            <input type="radio" 
                                   name="status" 
                                   value="inactive"
                                   <?= ($old_data['status'] ?? '') === 'inactive' ? 'checked' : '' ?>>
                            <span class="radio-check"></span>
                            <span class="radio-icon">⏸️</span>
                            <span class="radio-text">غیرفعال</span>
                        </label>
                    </div>
                </div>

                <!-- دکمه‌های عملیات -->
                <div class="form-actions">
                    <div class="form-note">
                        <span class="note-icon">ℹ️</span>
                        فیلدهای ستاره‌دار الزامی هستند
                    </div>
                    <div class="action-buttons">
                        <a href="<?= url('users') ?>" class="btn-flat btn-flat-secondary">
                            <span class="btn-icon">❌</span>
                            انصراف
                        </a>
                        <button type="submit" class="btn-flat btn-flat-primary">
                            <span class="btn-icon">💾</span>
                            ایجاد کاربر
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ========================================
   Flat Form Design CSS
======================================== */

/* Container */
.form-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Header */
.flat-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,0.05);
}

.flat-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.flat-icon {
    font-size: 1.8rem;
    opacity: 0.8;
}

.flat-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

/* Buttons */
.btn-flat {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-flat-primary {
    background: #667eea;
    color: white;
}

.btn-flat-primary:hover {
    background: #5a6fd8;
    transform: translateY(-1px);
    color: white;
}

.btn-flat-secondary {
    background: #6c757d;
    color: white;
}

.btn-flat-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
    color: white;
}

.btn-icon {
    font-size: 1.1em;
}

/* Cards */
.flat-card {
    background: white;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.08);
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.flat-card-header {
    background: #f8f9fa;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.flat-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-icon {
    font-size: 1.2rem;
    opacity: 0.7;
}

.flat-card-body {
    padding: 2rem;
}

/* Form */
.flat-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.label-icon {
    font-size: 1.1rem;
    opacity: 0.7;
}

.required {
    color: #ef4444;
    font-weight: 700;
}

.form-input, .form-select {
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: white;
    outline: none;
    font-family: inherit;
}

.form-input:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

/* Validation States */
.form-input.is-valid {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important;
}

.form-input.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.8rem;
    color: #ef4444;
    font-weight: 500;
}

.valid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.8rem;
    color: #10b981;
    font-weight: 500;
}

.form-input::placeholder {
    color: #9ca3af;
}

.form-help {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Password Input */
.password-input-group {
    position: relative;
    display: flex;
}

.password-input-group .form-input {
    padding-left: 3rem;
    flex: 1;
}

.password-toggle {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    z-index: 2;
}

.password-toggle:hover {
    background: rgba(0,0,0,0.05);
}

/* Form Section */
.form-section {
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
}

.section-label {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

/* Radio Group */
.radio-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.radio-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    background: white;
}

.radio-item:hover {
    border-color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.radio-item input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.radio-check {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    position: relative;
}

.radio-check::after {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: white;
    transform: scale(0);
    transition: transform 0.2s ease;
}

.radio-icon {
    font-size: 1.5rem;
}

.radio-text {
    font-weight: 600;
    font-size: 0.95rem;
}

/* Radio States */
.radio-success {
    color: #065f46;
}

.radio-success:hover {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.05);
}

.radio-success input:checked + .radio-check {
    background: #10b981;
    border-color: #10b981;
}

.radio-success input:checked + .radio-check::after {
    transform: scale(1);
}

.radio-warning {
    color: #92400e;
}

.radio-warning:hover {
    border-color: #f59e0b;
    background: rgba(245, 158, 11, 0.05);
}

.radio-warning input:checked + .radio-check {
    background: #f59e0b;
    border-color: #f59e0b;
}

.radio-warning input:checked + .radio-check::after {
    transform: scale(1);
}

/* Selected Radio State */
.radio-item.selected {
    background: rgba(102, 126, 234, 0.05);
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: between;
    align-items: center;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.form-note {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-size: 0.9rem;
    flex: 1;
}

.note-icon {
    font-size: 1.1rem;
    opacity: 0.7;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-shrink: 0;
}

/* Password Strength */
.password-strength {
    margin-top: 0.75rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.05);
}

.strength-bar {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-weak { background: #ef4444; width: 25%; }
.strength-fair { background: #f59e0b; width: 50%; }
.strength-good { background: #3b82f6; width: 75%; }
.strength-strong { background: #10b981; width: 100%; }

.strength-text {
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .flat-header {
        padding: 1.5rem;
    }
    
    .flat-title {
        font-size: 1.5rem;
    }
    
    .flat-card-body {
        padding: 1.5rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    
    .radio-group {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .action-buttons {
        width: 100%;
        justify-content: center;
    }
    
    .btn-flat {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .form-container {
        padding: 0 0.5rem;
    }
    
    .flat-header {
        padding: 1rem;
    }
    
    .flat-title {
        font-size: 1.3rem;
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .flat-card-body {
        padding: 1rem;
    }
    
    .form-input, .form-select {
        padding: 0.875rem;
    }
    
    .radio-item {
        padding: 0.875rem;
        text-align: center;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

</style>

<script>
// تغییر نمایش رمز عبور
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '🙈';
    } else {
        input.type = 'password';
        icon.textContent = '👁️';
    }
}

// بررسی قدرت رمز عبور
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// اعتبارسنجی فرم
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const fullName = document.getElementById('full_name').value.trim();
    const email = document.getElementById('email').value.trim();
    let phone = document.getElementById('phone').value.trim();
    
    // تبدیل نهایی اعداد فارسی به انگلیسی قبل از ارسال
    if (phone) {
        phone = window.Samanet.toEnglishNumbers(phone).replace(/\D/g, '');
        document.getElementById('phone').value = phone;
    }
    
    let errors = [];
    
    // بررسی نام کاربری
    if (!username) {
        errors.push('نام کاربری الزامی است');
    } else if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
        errors.push('نام کاربری باید بین 3 تا 20 کاراکتر و شامل حروف انگلیسی، اعداد و خط زیر باشد');
    }
    
    // بررسی رمز عبور
    if (!password) {
        errors.push('رمز عبور الزامی است');
    } else if (password.length < 8) {
        errors.push('رمز عبور باید حداقل 8 کاراکتر باشد');
    }
    
    // بررسی نام کامل
    if (!fullName) {
        errors.push('نام کامل الزامی است');
    }
    
    // بررسی ایمیل
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('فرمت ایمیل صحیح نیست');
    }
    
    // بررسی شماره موبایل
    if (phone) {
        // تبدیل اعداد فارسی به انگلیسی
        const cleanPhone = window.Samanet.toEnglishNumbers(phone).replace(/\D/g, '');
        if (!/^09[0-9]{9}$/.test(cleanPhone)) {
            errors.push('فرمت شماره موبایل صحیح نیست. مثال: 09123456789');
        }
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        showAlert(errors.join('<br>'), 'error');
        return false;
    }
    
    // نمایش loading
    showLoading();
});

// فرمت کردن شماره موبایل
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value;
    
    // تبدیل اعداد فارسی به انگلیسی
    value = window.Samanet.toEnglishNumbers(value);
    
    // حذف همه کاراکترهای غیر عددی
    value = value.replace(/\D/g, '');
    
    // محدود کردن به 11 رقم
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    
    // فرمت ساده بدون خط تیره
    e.target.value = value;
});

// تبدیل فوری اعداد فارسی در کلیک و paste
document.getElementById('phone').addEventListener('paste', function(e) {
    setTimeout(() => {
        let value = e.target.value;
        value = window.Samanet.toEnglishNumbers(value);
        value = value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        e.target.value = value;
    }, 10);
});

// نمایش پیام راهنما برای شماره موبایل
document.getElementById('phone').addEventListener('focus', function(e) {
    const helpDiv = e.target.nextElementSibling;
    if (helpDiv && helpDiv.classList.contains('form-help')) {
        helpDiv.style.color = '#667eea';
        helpDiv.innerHTML = '💡 می‌توانید با کیبورد فارسی یا انگلیسی تایپ کنید';
    }
});

document.getElementById('phone').addEventListener('blur', function(e) {
    const helpDiv = e.target.nextElementSibling;
    if (helpDiv && helpDiv.classList.contains('form-help')) {
        helpDiv.style.color = '#6b7280';
        helpDiv.innerHTML = 'مثال: 09123456789';
    }
});

// بررسی validation در هنگام تایپ
document.getElementById('phone').addEventListener('input', function(e) {
    const value = e.target.value;
    const phoneGroup = e.target.closest('.form-group');
    const existingError = phoneGroup.querySelector('.invalid-feedback');
    
    if (existingError) {
        existingError.remove();
        e.target.classList.remove('is-invalid');
    }
    
    if (value.length > 0) {
        const cleanPhone = window.Samanet.toEnglishNumbers(value).replace(/\D/g, '');
        if (cleanPhone.length === 11 && /^09[0-9]{9}$/.test(cleanPhone)) {
            e.target.classList.add('is-valid');
            e.target.classList.remove('is-invalid');
        } else if (cleanPhone.length > 3) {
            e.target.classList.remove('is-valid');
        }
    } else {
        e.target.classList.remove('is-valid', 'is-invalid');
    }
});

// اضافه کردن نشانگر قدرت رمز عبور
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = checkPasswordStrength(password);
    
    // حذف نشانگر قبلی
    const existingStrength = e.target.parentNode.parentNode.querySelector('.password-strength');
    if (existingStrength) {
        existingStrength.remove();
    }
    
    if (password.length > 0) {
        const strengthDiv = document.createElement('div');
        strengthDiv.className = 'password-strength';
        
        const strengthBar = document.createElement('div');
        strengthBar.className = 'strength-bar';
        
        const strengthFill = document.createElement('div');
        strengthFill.className = 'strength-fill';
        
        let strengthText = '';
        let strengthClass = '';
        
        switch (strength) {
            case 0:
            case 1:
                strengthText = 'ضعیف';
                strengthClass = 'strength-weak';
                break;
            case 2:
                strengthText = 'متوسط';
                strengthClass = 'strength-fair';
                break;
            case 3:
            case 4:
                strengthText = 'خوب';
                strengthClass = 'strength-good';
                break;
            case 5:
                strengthText = 'قوی';
                strengthClass = 'strength-strong';
                break;
        }
        
        strengthFill.className += ' ' + strengthClass;
        strengthBar.appendChild(strengthFill);
        strengthDiv.appendChild(strengthBar);
        
        const strengthLabel = document.createElement('div');
        strengthLabel.className = 'strength-text';
        strengthLabel.textContent = 'قدرت رمز عبور: ' + strengthText;
        strengthDiv.appendChild(strengthLabel);
        
        e.target.parentNode.parentNode.appendChild(strengthDiv);
    }
});

// بهبود تجربه کاربری در موبایل
document.addEventListener('DOMContentLoaded', function() {
    // فوکوس بر روی اولین فیلد
    const firstInput = document.getElementById('username');
    if (firstInput && window.innerWidth > 768) {
        firstInput.focus();
    }
    
    // انیمیشن برای radio buttons
    const radioItems = document.querySelectorAll('.radio-item');
    radioItems.forEach(item => {
        item.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                
                // حذف کلاس selected از همه آیتم‌ها
                radioItems.forEach(r => r.classList.remove('selected'));
                
                // اضافه کردن کلاس selected به آیتم انتخاب شده
                this.classList.add('selected');
            }
        });
    });
    
    // بررسی آیتم انتخاب شده اولیه
    const checkedRadio = document.querySelector('input[name="status"]:checked');
    if (checkedRadio) {
        checkedRadio.closest('.radio-item').classList.add('selected');
    }
});

// نمایش پیام تایید هنگام خروج از صفحه بدون ذخیره
let formChanged = false;

document.getElementById('createUserForm').addEventListener('input', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'تغییرات شما ذخیره نشده است. آیا مطمئن هستید؟';
        return e.returnValue;
    }
});

// حذف تایید هنگام submit موفق
document.getElementById('createUserForm').addEventListener('submit', function() {
    formChanged = false;
});
</script>
 