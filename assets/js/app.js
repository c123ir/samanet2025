/*
نام فایل: app.js
مسیر فایل: /assets/js/app.js
توضیح: فایل JavaScript اصلی سامانت
تاریخ ایجاد: 1404/03/31
نویسنده: توسعه‌دهنده سامانت
*/

// ========================================
// متغیرهای گلوبال
// ========================================
window.Samanat = window.Samanat || {};

const App = {
    // تنظیمات اصلی
    config: {
        baseUrl: window.location.origin,
        apiUrl: window.location.origin + '/api',
        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        debug: true,
        version: '1.0.0'
    },

    // کش داده‌ها
    cache: new Map(),

    // تایمرهای فعال
    timers: new Map(),

    // وضعیت اپلیکیشن
    state: {
        isOnline: navigator.onLine,
        currentUser: null,
        notifications: [],
        theme: 'auto'
    }
};

// ========================================
// توابع کمکی (Utility Functions)
// ========================================

const Utils = {
    /**
     * تبدیل اعداد فارسی به انگلیسی
     */
    toEnglishNumbers(str) {
        const persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
        const arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];
        
        for (let i = 0; i < 10; i++) {
            str = str.toString()
                .replace(persianNumbers[i], i)
                .replace(arabicNumbers[i], i);
        }
        return str;
    },

    /**
     * تبدیل اعداد انگلیسی به فارسی
     */
    toPersianNumbers(str) {
        const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
        return str.toString().replace(/\d/g, function(digit) {
            return persianDigits[digit];
        });
    },

    /**
     * فرمت کردن مبلغ
     */
    formatCurrency(amount, showRial = true) {
        const formatted = parseInt(amount).toLocaleString('fa-IR');
        return showRial ? formatted + ' ریال' : formatted;
    },

    /**
     * فرمت کردن تاریخ
     */
    formatDate(date, format = 'Y/m/d') {
        if (!date) return '';
        
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hour = String(d.getHours()).padStart(2, '0');
        const minute = String(d.getMinutes()).padStart(2, '0');
        
        return format
            .replace('Y', Utils.toPersianNumbers(year))
            .replace('m', Utils.toPersianNumbers(month))
            .replace('d', Utils.toPersianNumbers(day))
            .replace('H', Utils.toPersianNumbers(hour))
            .replace('i', Utils.toPersianNumbers(minute));
    },

    /**
     * ایجاد UUID
     */
    generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    /**
     * دریافت extension فایل
     */
    getFileExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    },

    /**
     * بررسی نوع فایل تصویر
     */
    isImageFile(filename) {
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return imageExtensions.includes(Utils.getFileExtension(filename));
    },

    /**
     * تبدیل بایت به اندازه خوانا
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '۰ بایت';
        
        const k = 1024;
        const sizes = ['بایت', 'کیلوبایت', 'مگابایت', 'گیگابایت'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return Utils.toPersianNumbers(parseFloat((bytes / Math.pow(k, i)).toFixed(2))) + ' ' + sizes[i];
    },

    /**
     * کپی متن در کلیپبورد
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            Notifications.success('متن کپی شد');
            return true;
        } catch (err) {
            console.error('خطا در کپی:', err);
            // روش جایگزین برای مرورگرهای قدیمی
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            Notifications.success('متن کپی شد');
            return true;
        }
    },

    /**
     * دیبانس تابع
     */
    debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    },

    /**
     * تراتل تابع
     */
    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
};

// ========================================
// مدیریت درخواست‌های AJAX
// ========================================

const Ajax = {
    /**
     * تنظیمات پیش‌فرض
     */
    defaults: {
        timeout: 30000,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    },

    /**
     * ارسال درخواست GET
     */
    async get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    },

    /**
     * ارسال درخواست POST
     */
    async post(url, data = {}, options = {}) {
        return this.request(url, { 
            ...options, 
            method: 'POST', 
            data 
        });
    },

    /**
     * ارسال درخواست PUT
     */
    async put(url, data = {}, options = {}) {
        return this.request(url, { 
            ...options, 
            method: 'PUT', 
            data 
        });
    },

    /**
     * ارسال درخواست DELETE
     */
    async delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    },

    /**
     * ارسال درخواست اصلی
     */
    async request(url, options = {}) {
        const config = {
            ...this.defaults,
            ...options,
            headers: {
                ...this.defaults.headers,
                ...options.headers
            }
        };

        // اضافه کردن CSRF token
        if (App.config.csrf && ['POST', 'PUT', 'DELETE'].includes(config.method)) {
            config.headers['X-CSRF-TOKEN'] = App.config.csrf;
        }

        // آماده‌سازی بدنه درخواست
        let body = null;
        if (config.data) {
            if (config.data instanceof FormData) {
                body = config.data;
                delete config.headers['Content-Type']; // مرورگر خودش تنظیم می‌کند
            } else {
                body = JSON.stringify(config.data);
            }
        }

        try {
            Loading.show();
            
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), config.timeout);

            const response = await fetch(url, {
                method: config.method,
                headers: config.headers,
                body,
                signal: controller.signal,
                credentials: 'same-origin'
            });

            clearTimeout(timeoutId);
            Loading.hide();

            // بررسی وضعیت پاسخ
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            // تجزیه پاسخ JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                return { success: true, data, response };
            }

            return { success: true, data: await response.text(), response };

        } catch (error) {
            Loading.hide();
            
            // مدیریت خطاهای مختلف
            if (error.name === 'AbortError') {
                throw new Error('درخواست به دلیل طولانی شدن لغو شد');
            }
            
            if (!navigator.onLine) {
                throw new Error('اتصال اینترنت قطع است');
            }

            throw error;
        }
    }
};

// ========================================
// مدیریت Loading
// ========================================

const Loading = {
    element: null,
    counter: 0,

    /**
     * راه‌اندازی اولیه
     */
    init() {
        if (!this.element) {
            this.createElement();
        }
    },

    /**
     * ایجاد المان loading
     */
    createElement() {
        this.element = document.createElement('div');
        this.element.id = 'global-loading';
        this.element.className = 'loading-overlay';
        this.element.innerHTML = `
            <div class="loading-content">
                <div class="spinner"></div>
                <div class="loading-text">در حال بارگذاری...</div>
            </div>
        `;
        this.element.style.display = 'none';
        document.body.appendChild(this.element);
    },

    /**
     * نمایش loading
     */
    show(text = 'در حال بارگذاری...') {
        this.init();
        this.counter++;
        
        const textElement = this.element.querySelector('.loading-text');
        if (textElement) {
            textElement.textContent = text;
        }
        
        this.element.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    },

    /**
     * مخفی کردن loading
     */
    hide() {
        this.counter = Math.max(0, this.counter - 1);
        
        if (this.counter === 0 && this.element) {
            this.element.style.display = 'none';
            document.body.style.overflow = '';
        }
    },

    /**
     * مخفی کردن فوری
     */
    forceHide() {
        this.counter = 0;
        if (this.element) {
            this.element.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
};

// ========================================
// مدیریت Notifications
// ========================================

const Notifications = {
    container: null,
    notifications: [],

    /**
     * راه‌اندازی اولیه
     */
    init() {
        if (!this.container) {
            this.createContainer();
        }
    },

    /**
     * ایجاد کانتینر notifications
     */
    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'notifications-container';
        this.container.className = 'toast-container position-fixed top-0 end-0 p-3';
        this.container.style.zIndex = '9999';
        document.body.appendChild(this.container);
    },

    /**
     * نمایش notification موفقیت
     */
    success(message, title = 'موفق', options = {}) {
        return this.show(message, 'success', title, options);
    },

    /**
     * نمایش notification خطا
     */
    error(message, title = 'خطا', options = {}) {
        return this.show(message, 'error', title, options);
    },

    /**
     * نمایش notification هشدار
     */
    warning(message, title = 'هشدار', options = {}) {
        return this.show(message, 'warning', title, options);
    },

    /**
     * نمایش notification اطلاعات
     */
    info(message, title = 'اطلاعات', options = {}) {
        return this.show(message, 'info', title, options);
    },

    /**
     * نمایش notification
     */
    show(message, type = 'info', title = null, options = {}) {
        this.init();

        const config = {
            autoHide: true,
            delay: 5000,
            closable: true,
            ...options
        };

        const id = Utils.generateUUID();
        const toast = this.createToast(id, message, type, title, config);
        
        this.container.appendChild(toast);
        this.notifications.push({ id, element: toast, config });

        // انیمیشن ورود
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // حذف خودکار
        if (config.autoHide) {
            setTimeout(() => {
                this.hide(id);
            }, config.delay);
        }

        return id;
    },

    /**
     * ایجاد toast element
     */
    createToast(id, message, type, title, config) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${this.getBootstrapClass(type)} border-0`;
        toast.setAttribute('data-notification-id', id);
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-${this.getIcon(type)} me-2"></i>
                        <div>
                            ${title ? `<strong>${title}</strong><br>` : ''}
                            ${message}
                        </div>
                    </div>
                </div>
                ${config.closable ? `
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                            onclick="Notifications.hide('${id}')"></button>
                ` : ''}
            </div>
        `;

        return toast;
    },

    /**
     * مخفی کردن notification
     */
    hide(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (!notification) return;

        notification.element.classList.remove('show');
        
        setTimeout(() => {
            if (notification.element.parentNode) {
                notification.element.parentNode.removeChild(notification.element);
            }
            this.notifications = this.notifications.filter(n => n.id !== id);
        }, 300);
    },

    /**
     * مخفی کردن تمام notifications
     */
    hideAll() {
        this.notifications.forEach(notification => {
            this.hide(notification.id);
        });
    },

    /**
     * دریافت کلاس Bootstrap
     */
    getBootstrapClass(type) {
        const classes = {
            success: 'success',
            error: 'danger',
            warning: 'warning',
            info: 'info'
        };
        return classes[type] || 'info';
    },

    /**
     * دریافت آیکون
     */
    getIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
};

// ========================================
// مدیریت Modal
// ========================================

const Modal = {
    /**
     * نمایش modal تایید
     */
    confirm(message, title = 'تایید', options = {}) {
        return new Promise((resolve) => {
            const config = {
                confirmText: 'تایید',
                cancelText: 'لغو',
                type: 'warning',
                ...options
            };

            const modalId = 'confirm-modal-' + Date.now();
            const modalHtml = `
                <div class="modal fade" id="${modalId}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-${this.getIcon(config.type)} me-2"></i>
                                    ${title}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">${message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    ${config.cancelText}
                                </button>
                                <button type="button" class="btn btn-${this.getBootstrapClass(config.type)}" 
                                        onclick="Modal.resolveConfirm('${modalId}', true)">
                                    ${config.confirmText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modalElement = document.getElementById(modalId);
            const modal = new bootstrap.Modal(modalElement);

            // ذخیره resolver
            modalElement._resolver = resolve;

            // نمایش modal
            modal.show();

            // حذف modal پس از بسته شدن
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
            });
        });
    },

    /**
     * حل کردن promise تایید
     */
    resolveConfirm(modalId, result) {
        const modalElement = document.getElementById(modalId);
        if (modalElement && modalElement._resolver) {
            modalElement._resolver(result);
            bootstrap.Modal.getInstance(modalElement).hide();
        }
    },

    /**
     * نمایش modal اطلاعات
     */
    alert(message, title = 'اطلاعات', type = 'info') {
        const modalId = 'alert-modal-' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-${this.getIcon(type)} me-2"></i>
                                ${title}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                تایید
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement);

        modal.show();

        modalElement.addEventListener('hidden.bs.modal', () => {
            modalElement.remove();
        });
    },

    /**
     * دریافت آیکون
     */
    getIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle',
            question: 'question-circle'
        };
        return icons[type] || 'info-circle';
    },

    /**
     * دریافت کلاس Bootstrap
     */
    getBootstrapClass(type) {
        const classes = {
            success: 'success',
            error: 'danger',
            warning: 'warning',
            info: 'info',
            question: 'primary'
        };
        return classes[type] || 'primary';
    }
};

// ========================================
// مدیریت Form ها
// ========================================

const FormHandler = {
    /**
     * راه‌اندازی اولیه فرم‌ها
     */
    init() {
        this.setupFormValidation();
        this.setupFileUploads();
        this.setupNumberInputs();
        this.setupDateInputs();
    },

    /**
     * تنظیم اعتبارسنجی فرم‌ها
     */
    setupFormValidation() {
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('needs-validation')) {
                e.preventDefault();
                e.stopPropagation();
                
                if (this.validateForm(e.target)) {
                    this.submitForm(e.target);
                }
                
                e.target.classList.add('was-validated');
            }
        });
    },

    /**
     * اعتبارسنجی فرم
     */
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                isValid = false;
                this.showFieldError(input, input.validationMessage);
            } else {
                this.clearFieldError(input);
            }
        });

        // اعتبارسنجی‌های سفارشی
        isValid = this.customValidation(form) && isValid;

        return isValid;
    },

    /**
     * اعتبارسنجی‌های سفارشی
     */
    customValidation(form) {
        let isValid = true;

        // بررسی تطابق پسورد
        const password = form.querySelector('input[name="password"]');
        const confirmPassword = form.querySelector('input[name="password_confirmation"]');
        
        if (password && confirmPassword && password.value !== confirmPassword.value) {
            this.showFieldError(confirmPassword, 'رمز عبور و تایید آن مطابقت ندارند');
            isValid = false;
        }

        // بررسی شماره موبایل
        const mobileInputs = form.querySelectorAll('input[type="tel"]');
        mobileInputs.forEach(input => {
            if (input.value && !this.validateMobile(input.value)) {
                this.showFieldError(input, 'فرمت شماره موبایل صحیح نیست');
                isValid = false;
            }
        });

        // بررسی ایمیل
        const emailInputs = form.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            if (input.value && !this.validateEmail(input.value)) {
                this.showFieldError(input, 'فرمت ایمیل صحیح نیست');
                isValid = false;
            }
        });

        return isValid;
    },

    /**
     * ارسال فرم
     */
    async submitForm(form) {
        const formData = new FormData(form);
        const url = form.action || window.location.href;
        const method = form.method || 'POST';

        try {
            Loading.show('در حال ارسال...');
            
            const response = await Ajax.request(url, {
                method: method.toUpperCase(),
                data: formData
            });

            if (response.success && response.data.success) {
                Notifications.success(response.data.message || 'عملیات با موفقیت انجام شد');
                
                // ریدایرکت در صورت وجود
                if (response.data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = response.data.redirect_url;
                    }, 1000);
                } else {
                    form.reset();
                    form.classList.remove('was-validated');
                }
            } else {
                throw new Error(response.data.message || 'خطا در ارسال فرم');
            }
        } catch (error) {
            console.error('خطا در ارسال فرم:', error);
            Notifications.error(error.message || 'خطا در ارسال فرم');
        } finally {
            Loading.hide();
        }
    },

    /**
     * نمایش خطای فیلد
     */
    showFieldError(input, message) {
        this.clearFieldError(input);
        
        input.classList.add('is-invalid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        
        input.parentNode.appendChild(feedback);
    },

    /**
     * پاک کردن خطای فیلد
     */
    clearFieldError(input) {
        input.classList.remove('is-invalid');
        
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    },

    /**
     * تنظیم آپلود فایل‌ها
     */
    setupFileUploads() {
        document.addEventListener('change', (e) => {
            if (e.target.type === 'file') {
                this.handleFileSelect(e.target);
            }
        });

        // Drag & Drop
        document.addEventListener('dragover', (e) => {
            e.preventDefault();
            const dropZone = e.target.closest('.upload-area');
            if (dropZone) {
                dropZone.classList.add('dragover');
            }
        });

        document.addEventListener('dragleave', (e) => {
            const dropZone = e.target.closest('.upload-area');
            if (dropZone && !dropZone.contains(e.relatedTarget)) {
                dropZone.classList.remove('dragover');
            }
        });

        document.addEventListener('drop', (e) => {
            e.preventDefault();
            const dropZone = e.target.closest('.upload-area');
            if (dropZone) {
                dropZone.classList.remove('dragover');
                const fileInput = dropZone.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.files = e.dataTransfer.files;
                    this.handleFileSelect(fileInput);
                }
            }
        });
    },

    /**
     * پردازش انتخاب فایل
     */
    handleFileSelect(input) {
        const files = Array.from(input.files);
        
        if (files.length === 0) return;

        // بررسی تعداد فایل‌ها
        const maxFiles = parseInt(input.dataset.maxFiles) || 10;
        if (files.length > maxFiles) {
            Notifications.warning(`حداکثر ${Utils.toPersianNumbers(maxFiles)} فایل قابل انتخاب است`);
            return;
        }

        // بررسی اندازه فایل‌ها
        const maxSize = parseInt(input.dataset.maxSize) || 5 * 1024 * 1024; // 5MB
        const oversizedFiles = files.filter(file => file.size > maxSize);
        
        if (oversizedFiles.length > 0) {
            Notifications.warning(`اندازه فایل‌ها نباید بیش از ${Utils.formatFileSize(maxSize)} باشد`);
            return;
        }

        // بررسی نوع فایل‌ها
        const allowedTypes = input.dataset.allowedTypes?.split(',') || [];
        if (allowedTypes.length > 0) {
            const invalidFiles = files.filter(file => {
                const extension = Utils.getFileExtension(file.name);
                return !allowedTypes.includes(extension);
            });
            
            if (invalidFiles.length > 0) {
                Notifications.warning(`فقط فایل‌های ${allowedTypes.join(', ')} مجاز هستند`);
                return;
            }
        }

        // نمایش پیش‌نمایش فایل‌ها
        this.showFilePreview(input, files);
    },

    /**
     * نمایش پیش‌نمایش فایل‌ها
     */
    showFilePreview(input, files) {
        const previewContainer = input.parentNode.querySelector('.file-preview') || 
                                 this.createPreviewContainer(input);
        
        previewContainer.innerHTML = '';

        files.forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item d-flex align-items-center p-2 mb-2 border rounded';
            
            if (Utils.isImageFile(file.name)) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" class="file-thumbnail me-3" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${file.name}</div>
                            <small class="text-muted">${Utils.formatFileSize(file.size)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="FormHandler.removeFile(this, ${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewItem.innerHTML = `
                    <i class="fas fa-file me-3 fs-3 text-muted"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${file.name}</div>
                        <small class="text-muted">${Utils.formatFileSize(file.size)}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="FormHandler.removeFile(this, ${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }
            
            previewContainer.appendChild(previewItem);
        });
    },

    /**
     * ایجاد کانتینر پیش‌نمایش
     */
    createPreviewContainer(input) {
        const container = document.createElement('div');
        container.className = 'file-preview mt-3';
        input.parentNode.appendChild(container);
        return container;
    },

    /**
     * حذف فایل از لیست
     */
    removeFile(button, index) {
        const previewItem = button.closest('.file-preview-item');
        const input = previewItem.closest('.form-group').querySelector('input[type="file"]');
        
        // حذف فایل از لیست
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        
        files.forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        input.files = dt.files;
        previewItem.remove();
    },

    /**
     * تنظیم input های عددی
     */
    setupNumberInputs() {
        document.addEventListener('input', (e) => {
            const target = e.target;
            
            // فیلدهای شماره موبایل، تلفن، کد پستی و اعداد
            if (target.type === 'tel' || 
                target.name === 'phone' || 
                target.name === 'mobile' || 
                target.name === 'postal_code' ||
                target.name === 'code' ||
                target.classList.contains('persian-number') ||
                target.classList.contains('number-input')) {
                
                // تبدیل اعداد فارسی به انگلیسی
                const convertedValue = Utils.toEnglishNumbers(target.value);
                if (convertedValue !== target.value) {
                    target.value = convertedValue;
                }
            }
            
            // فیلدهای پسورد عددی  
            if (target.type === 'password' && target.value.match(/[۰-۹٠-٩]/)) {
                target.value = Utils.toEnglishNumbers(target.value);
            }
            
            // فیلدهای مبلغ
            if (target.classList.contains('currency-input')) {
                // فرمت کردن مبلغ
                const value = Utils.toEnglishNumbers(target.value.replace(/[^\d]/g, ''));
                target.value = value ? parseInt(value).toLocaleString('fa-IR') : '';
            }
        });

        // همچنین در paste event
        document.addEventListener('paste', (e) => {
            const target = e.target;
            
            if (target.type === 'tel' || 
                target.name === 'phone' || 
                target.name === 'mobile' || 
                target.name === 'postal_code' ||
                target.name === 'code' ||
                target.type === 'password' ||
                target.classList.contains('persian-number') ||
                target.classList.contains('number-input')) {
                
                setTimeout(() => {
                    target.value = Utils.toEnglishNumbers(target.value);
                }, 10);
            }
        });
    },

    /**
     * تنظیم input های تاریخ
     */
    setupDateInputs() {
        document.addEventListener('focus', (e) => {
            if (e.target.classList.contains('persian-date')) {
                // نمایش تقویم فارسی (می‌توان کتابخانه تقویم اضافه کرد)
                console.log('تقویم فارسی برای:', e.target);
            }
        });
    },

    /**
     * اعتبارسنجی شماره موبایل
     */
    validateMobile(mobile) {
        const cleaned = Utils.toEnglishNumbers(mobile.replace(/[^\d]/g, ''));
        return /^(09|9)[0-9]{9}$/.test(cleaned);
    },

    /**
     * اعتبارسنجی ایمیل
     */
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
};

// ========================================
// مدیریت صفحه‌بندی
// ========================================

const Pagination = {
    /**
     * راه‌اندازی صفحه‌بندی AJAX
     */
    init() {
        document.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('.pagination a[data-page]');
            if (paginationLink) {
                e.preventDefault();
                this.loadPage(paginationLink.dataset.page, paginationLink.dataset.url);
            }
        });
    },

    /**
     * بارگذاری صفحه جدید
     */
    async loadPage(page, url) {
        try {
            Loading.show('بارگذاری صفحه...');
            
            const params = new URLSearchParams(window.location.search);
            params.set('page', page);
            
            const response = await Ajax.get(url + '?' + params.toString());
            
            if (response.success) {
                // به‌روزرسانی محتوا
                const contentContainer = document.querySelector('[data-pagination-content]');
                if (contentContainer && response.data.html) {
                    contentContainer.innerHTML = response.data.html;
                }
                
                // به‌روزرسانی URL
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('page', page);
                window.history.pushState(null, '', newUrl);
            }
        } catch (error) {
            console.error('خطا در بارگذاری صفحه:', error);
            Notifications.error('خطا در بارگذاری صفحه');
        } finally {
            Loading.hide();
        }
    }
};

// ========================================
// مدیریت جستجو
// ========================================

const Search = {
    searchTimeout: null,
    
    /**
     * راه‌اندازی جستجو
     */
    init() {
        const searchInputs = document.querySelectorAll('[data-search]');
        searchInputs.forEach(input => {
            input.addEventListener('input', Utils.debounce((e) => {
                this.performSearch(e.target);
            }, 500));
        });
    },

    /**
     * انجام جستجو
     */
    async performSearch(input) {
        const query = input.value.trim();
        const url = input.dataset.search;
        const target = input.dataset.target;
        
        if (!query || query.length < 2) {
            return;
        }

        try {
            const response = await Ajax.get(url, {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: { q: query }
            });

            if (response.success && target) {
                const targetElement = document.querySelector(target);
                if (targetElement) {
                    targetElement.innerHTML = response.data.html || '';
                }
            }
        } catch (error) {
            console.error('خطا در جستجو:', error);
        }
    }
};

// ========================================
// مدیریت Theme - Integration با Theme System
// ========================================

const Theme = {
    /**
     * راه‌اندازی اولیه - فقط sync با theme system
     */
    init() {
        // Theme system در theme-system.js handle می‌شود
        if (window.SamanetTheme) {
            App.state.theme = window.SamanetTheme.getCurrentTheme();
            
            // گوش دادن به تغییرات theme
            document.addEventListener('themeChanged', (e) => {
                App.state.theme = e.detail.theme;
            });
        }
    },

    /**
     * دریافت theme فعلی
     */
    getCurrentTheme() {
        return window.SamanetTheme ? window.SamanetTheme.getCurrentTheme() : 'light';
    },

    /**
     * تنظیم theme - proxy به theme system
     */
    setTheme(theme) {
        if (window.SamanetTheme) {
            window.SamanetTheme.setTheme(theme);
        }
    },

    /**
     * بررسی dark mode
     */
    isDarkMode() {
        return window.SamanetTheme ? window.SamanetTheme.isDarkMode() : false;
    }
};

// ========================================
// مدیریت کش
// ========================================

const Cache = {
    /**
     * ذخیره در کش
     */
    set(key, data, ttl = 3600000) { // 1 ساعت پیش‌فرض
        const item = {
            data,
            expiry: Date.now() + ttl
        };
        App.cache.set(key, item);
        
        // ذخیره در localStorage برای ماندگاری
        try {
            localStorage.setItem('samanat_cache_' + key, JSON.stringify(item));
        } catch (e) {
            console.warn('خطا در ذخیره کش:', e);
        }
    },

    /**
     * دریافت از کش
     */
    get(key) {
        let item = App.cache.get(key);
        
        // اگر در memory نیست، از localStorage بخوان
        if (!item) {
            try {
                const stored = localStorage.getItem('samanat_cache_' + key);
                if (stored) {
                    item = JSON.parse(stored);
                    App.cache.set(key, item);
                }
            } catch (e) {
                console.warn('خطا در خواندن کش:', e);
            }
        }
        
        if (!item) return null;
        
        // بررسی انقضا
        if (Date.now() > item.expiry) {
            this.delete(key);
            return null;
        }
        
        return item.data;
    },

    /**
     * حذف از کش
     */
    delete(key) {
        App.cache.delete(key);
        localStorage.removeItem('samanat_cache_' + key);
    },

    /**
     * پاک کردن کش
     */
    clear() {
        App.cache.clear();
        
        // حذف از localStorage
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('samanat_cache_')) {
                localStorage.removeItem(key);
            }
        });
    }
};

// ========================================
// مدیریت اتصال اینترنت
// ========================================

const NetworkStatus = {
    /**
     * راه‌اندازی اولیه
     */
    init() {
        window.addEventListener('online', () => {
            App.state.isOnline = true;
            Notifications.success('اتصال اینترنت برقرار شد');
            this.syncOfflineData();
        });

        window.addEventListener('offline', () => {
            App.state.isOnline = false;
            Notifications.warning('اتصال اینترنت قطع شد');
        });
    },

    /**
     * همگام‌سازی داده‌های آفلاین
     */
    async syncOfflineData() {
        const offlineData = localStorage.getItem('samanat_offline_data');
        if (offlineData) {
            try {
                const data = JSON.parse(offlineData);
                // پردازش داده‌های آفلاین
                console.log('همگام‌سازی داده‌های آفلاین:', data);
                localStorage.removeItem('samanat_offline_data');
            } catch (e) {
                console.error('خطا در همگام‌سازی:', e);
            }
        }
    }
};

// ========================================
// مدیریت کیبورد میانبرها
// ========================================

const KeyboardShortcuts = {
    shortcuts: new Map(),

    /**
     * راه‌اندازی اولیه
     */
    init() {
        document.addEventListener('keydown', (e) => {
            this.handleKeyDown(e);
        });

        // میانبرهای پیش‌فرض
        this.register('ctrl+s', () => {
            e.preventDefault();
            const activeForm = document.querySelector('form:focus-within');
            if (activeForm) {
                activeForm.dispatchEvent(new Event('submit'));
            }
        });

        this.register('escape', () => {
            // بستن modal ها و dropdown ها
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                bootstrap.Modal.getInstance(openModal).hide();
            }
        });
    },

    /**
     * ثبت میانبر جدید
     */
    register(combination, callback) {
        this.shortcuts.set(combination.toLowerCase(), callback);
    },

    /**
     * پردازش فشردن کلید
     */
    handleKeyDown(e) {
        const combination = this.getCombination(e);
        const callback = this.shortcuts.get(combination);
        
        if (callback) {
            callback(e);
        }
    },

    /**
     * دریافت ترکیب کلیدها
     */
    getCombination(e) {
        const keys = [];
        
        if (e.ctrlKey) keys.push('ctrl');
        if (e.altKey) keys.push('alt');
        if (e.shiftKey) keys.push('shift');
        
        // بررسی وجود e.key قبل از استفاده
        if (e.key && typeof e.key === 'string') {
            keys.push(e.key.toLowerCase());
        }
        
        return keys.join('+');
    }
};

// ========================================
// راه‌اندازی اولیه اپلیکیشن
// ========================================

class SamanatApp {
    constructor() {
        this.isInitialized = false;
    }

    /**
     * راه‌اندازی اپلیکیشن
     */
    async init() {
        if (this.isInitialized) return;

        console.log('🚀 راه‌اندازی سامانت...');

        try {
            // راه‌اندازی ماژول‌ها
            Theme.init();
            Loading.init();
            Notifications.init();
            FormHandler.init();
            Pagination.init();
            Search.init();
            NetworkStatus.init();
            KeyboardShortcuts.init();

            // تنظیمات AJAX
            this.setupAjaxDefaults();

            // Event listeners کلی
            this.setupGlobalEventListeners();

            // بارگذاری داده‌های اولیه
            await this.loadInitialData();

            this.isInitialized = true;
            console.log('✅ سامانت آماده است');

            // ارسال رویداد آماده بودن
            document.dispatchEvent(new CustomEvent('samanat:ready'));

        } catch (error) {
            console.error('❌ خطا در راه‌اندازی سامانت:', error);
            Notifications.error('خطا در بارگذاری اپلیکیشن');
        }
    }

    /**
     * تنظیمات پیش‌فرض AJAX
     */
    setupAjaxDefaults() {
        // تنظیم CSRF token در هدر درخواست‌های fetch
        if (App.config.csrf) {
            // CSRF token در Ajax کلاس handle می‌شود
            console.log('CSRF token تنظیم شد:', App.config.csrf);
        }
    }

    /**
     * Event listeners کلی
     */
    setupGlobalEventListeners() {
        // کلیک خارج از منو برای بستن
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.sidebar') && !e.target.closest('.navbar-toggler')) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // پیش‌گیری از ارسال دوباره فرم
        window.addEventListener('beforeunload', (e) => {
            const forms = document.querySelectorAll('form.was-validated');
            if (forms.length > 0) {
                e.preventDefault();
                e.returnValue = 'تغییرات ذخیره نشده دارید. آیا مطمئن هستید؟';
            }
        });

        // به‌روزرسانی آخرین فعالیت
        let activityTimer;
        const updateActivity = () => {
            clearTimeout(activityTimer);
            activityTimer = setTimeout(() => {
                if (App.state.isOnline) {
                    Ajax.post('/dashboard/update-activity').catch(() => {});
                }
            }, 5000);
        };

        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, updateActivity, { passive: true });
        });
    }

    /**
     * بارگذاری داده‌های اولیه
     */
    async loadInitialData() {
        // بارگذاری اطلاعات کاربر فعال
        const userData = Cache.get('current_user');
        if (userData) {
            App.state.currentUser = userData;
        }

        // بارگذاری تنظیمات
        const settings = Cache.get('app_settings');
        if (settings) {
            Object.assign(App.config, settings);
        }
    }

    /**
     * متدهای عمومی API
     */
    
    // دسترسی به ماژول‌ها
    get utils() { return Utils; }
    get ajax() { return Ajax; }
    get loading() { return Loading; }
    get notifications() { return Notifications; }
    get modal() { return Modal; }
    get cache() { return Cache; }
    get theme() { return window.SamanetTheme || Theme; }

    // متدهای کمکی
    formatCurrency(amount) { return Utils.formatCurrency(amount); }
    formatDate(date, format) { return Utils.formatDate(date, format); }
    toPersianNumbers(str) { return Utils.toPersianNumbers(str); }
    toEnglishNumbers(str) { return Utils.toEnglishNumbers(str); }

    // نمایش پیام‌ها
    success(message, title, options) { return Notifications.success(message, title, options); }
    error(message, title, options) { return Notifications.error(message, title, options); }
    warning(message, title, options) { return Notifications.warning(message, title, options); }
    info(message, title, options) { return Notifications.info(message, title, options); }

    // Modal ها
    confirm(message, title, options) { return Modal.confirm(message, title, options); }
    alert(message, title, type) { return Modal.alert(message, title, type); }
}

// ========================================
// Export برای استفاده سراسری
// ========================================

// ایجاد instance اصلی
window.Samanat = new SamanatApp();

// راه‌اندازی پس از بارگذاری DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.Samanat.init();
    });
} else {
    window.Samanat.init();
}

// Export برای ماژول‌ها
window.SamanatUtils = Utils;
window.SamanatAjax = Ajax;
window.SamanatNotifications = Notifications;
window.SamanatModal = Modal;
window.SamanatCache = Cache;

// ========================================
// تنظیمات اضافی Bootstrap (بدون jQuery)
// ========================================

// راه‌اندازی Bootstrap components بدون jQuery
document.addEventListener('DOMContentLoaded', function() {
    // فعال‌سازی tooltip ها
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // فعال‌سازی popover ها
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // انیمیشن smooth scroll
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href^="#"]');
        if (link) {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1);
            const target = document.getElementById(targetId);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ========================================
// پایان فایل
// ========================================

console.log('📦 Samanat JavaScript Framework Loaded Successfully');
console.log('🔗 Version:', App.config?.version || '1.0.0');
console.log('🌐 Base URL:', App.config?.baseUrl || window.location.origin);