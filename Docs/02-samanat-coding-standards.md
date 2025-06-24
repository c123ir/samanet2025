# 🚀 استانداردهای توسعه حرفه‌ای سامانت (ورژن 3.0)
## راهنمای کامل برای توسعه با Cursor AI

**نسخه:** 3.0 تکمیل شده  
**تاریخ:** 1404/10/15  
**هدف:** توسعه حرفه‌ای و مدیریت پروژه با Cursor AI  
**وضعیت:** ✅ فاز 3 تکمیل - آماده فاز 4  
**پیشرفت کل:** 60% (3 از 5 فاز اصلی)

---

## 🎯 **مقدمه و هدف**

این مستند شامل تمامی استانداردها، قوانین و راهنماهای ضروری برای توسعه حرفه‌ای پروژه سامانت با استفاده از Cursor AI است. تمام توسعه‌دهندگان و AI Assistants باید از این استانداردها پیروی کنند.

### **اهداف اصلی:**
1. **کیفیت کد:** حفظ استانداردهای بالای کدنویسی
2. **سازگاری:** همکاری بهتر بین توسعه‌دهندگان و AI
3. **مقیاس‌پذیری:** توسعه قابل گسترش و نگهداری
4. **عملکرد:** بهینه‌سازی مداوم و performance بالا
5. **امنیت:** رعایت کامل اصول امنیتی

### **✅ دستاوردهای ورژن 3.0:**
- **سیستم مدیریت تگ‌ها کامل** با رنگ‌بندی گرادینت
- **جستجوی چند کلمه‌ای بلادرنگ** با highlighting
- **تشخیص خودکار رنگ متن** با محاسبه luminance
- **طراحی Enterprise-Grade** مطابق dashboard.css
- **سیستم جستجوی یکپارچه** قابل استفاده در تمام پروژه

---

## 🏗️ **معماری و ساختار پروژه**

### **Tech Stack استاندارد:**
```yaml
Backend:
  - PHP: 8.0+ (PSR-12, Clean Architecture)
  - Database: MySQL 8.0+ / MariaDB 10.5+
  - Web Server: Apache 2.4+ / Nginx 1.18+

Frontend:
  - HTML5: Semantic markup
  - CSS3: CSS Variables + Grid + Flexbox
  - JavaScript: ES6+ Vanilla (NO jQuery)
  - UI Framework: Bootstrap 5.3.0 RTL

Tools & Libraries:
  - Version Control: Git
  - Package Manager: Composer
  - Icons: FontAwesome 6.4.0
  - Charts: Chart.js (when needed)
  - Image Processing: Intervention Image
  - PDF Generation: TCPDF / mPDF
```

### **ساختار پوشه‌ها (الزامی):**
```
samanat/
├── 📁 app/                    # Core Application
│   ├── 📁 controllers/        # MVC Controllers
│   ├── 📁 models/            # Database Models  
│   ├── 📁 views/             # UI Templates
│   │   ├── 📁 layouts/       # Main layouts
│   │   ├── 📁 requests/      # Request pages
│   │   ├── 📁 documents/     # Document pages
│   │   └── 📁 reports/       # Report pages
│   ├── 📁 helpers/           # Utility Classes
│   ├── 📁 middleware/        # Request Middleware
│   └── 📁 config/            # Configuration
├── 📁 assets/                # Static Assets
│   ├── 📁 css/              # Stylesheets
│   ├── 📁 js/               # JavaScript
│   ├── 📁 img/              # Images
│   └── 📁 fonts/            # Fonts
├── 📁 api/                   # API Endpoints
├── 📁 upld/                  # User Uploads
├── 📁 vendor/                # Composer packages
├── 📁 logs/                  # Application logs
├── 📁 cache/                 # Cached files
└── 📁 pub/                   # Public forms
```

---

## 📋 **قوانین کدنویسی PHP**

### **1. ساختار فایل:**
```php
<?php
/**
 * نام فایل: RequestController.php
 * مسیر: /app/controllers/RequestController.php  
 * نویسنده: Cursor AI + Developer
 * تاریخ: 1404/03/31
 * هدف: مدیریت درخواست‌های حواله
 * فاز: 2 (تکمیل شده)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\PaymentRequest;
use App\Helpers\Security;
use App\Helpers\Validator;

/**
 * کنترلر مدیریت درخواست‌های حواله
 */
class RequestController extends BaseController
{
    private PaymentRequest $requestModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->requestModel = new PaymentRequest();
    }
    
    // متدهای کنترلر...
}
```

### **2. نامگذاری (الزامی):**
```php
// کلاس‌ها: PascalCase
class RequestController {}
class PaymentRequest {}

// متدها: camelCase
public function createRequest() {}
public function validateInput() {}

// متغیرها: snake_case
$user_id = 123;
$payment_requests = [];

// ثابت‌ها: UPPER_SNAKE_CASE
const MAX_FILE_SIZE = 5242880; // 5MB
const ALLOWED_EXTENSIONS = ['jpg', 'png', 'pdf'];

// فایل‌ها: kebab-case
create-request.php
payment-list.php
```

### **3. کامنت‌گذاری فارسی:**
```php
/**
 * دریافت لیست درخواست‌های حواله با فیلتر
 * 
 * @param int $group_id شناسه گروه
 * @param string $status وضعیت درخواست (pending|processing|completed|rejected)
 * @param array $filters فیلترهای اضافی
 * @return array نتیجه شامل data و pagination
 * @throws \Exception در صورت خطا در دیتابیس
 */
public function getRequests(int $group_id, string $status = 'all', array $filters = []): array
{
    // بررسی مجوز دسترسی کاربر
    if (!$this->checkUserPermission('view_requests')) {
        throw new \Exception('عدم دسترسی مشاهده درخواست‌ها');
    }
    
    // اعتبارسنجی ورودی‌ها
    $group_id = Security::sanitizeInt($group_id);
    $status = Security::sanitizeString($status);
    
    // دریافت داده‌ها از دیتابیس
    return $this->requestModel->getGroupRequests($group_id, $status, $filters);
}
```

### **4. مدیریت خطا و Exception:**
```php
// استاندارد مدیریت خطا
try {
    $result = $this->processPayment($request_id);
    return $this->success('پرداخت با موفقیت انجام شد', $result);
} catch (\InvalidArgumentException $e) {
    Logger::warning('Invalid payment request', ['request_id' => $request_id, 'error' => $e->getMessage()]);
    return $this->error('اطلاعات درخواست نامعتبر است');
} catch (\Exception $e) {
    Logger::error('Payment processing failed', ['request_id' => $request_id, 'error' => $e->getMessage()]);
    return $this->error('خطا در پردازش پرداخت');
}
```

---

## 🎨 **استانداردهای Frontend**

### **1. HTML ساختاری:**
```html
<!-- استفاده از HTML5 Semantic -->
<main class="content-wrapper">
    <header class="page-header">
        <h1 class="page-title">مدیریت درخواست‌ها</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">خانه</a></li>
                <li class="breadcrumb-item active">درخواست‌ها</li>
            </ol>
        </nav>
    </header>
    
    <section class="page-content">
        <!-- محتوای اصلی -->
    </section>
</main>
```

### **2. CSS استاندارد:**
```css
/* استفاده از CSS Variables */
:root {
    /* رنگ‌های اصلی */
    --primary-color: #667eea;
    --secondary-color: #f093fb;
    --success-color: #4facfe;
    --warning-color: #ffecd2;
    --danger-color: #ff6b6b;
    
    /* فاصله‌ها */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    
    /* فونت‌ها */
    --font-primary: 'Vazirmatn', sans-serif;
    --font-secondary: 'IRANSans', sans-serif;
    
    /* Glass Morphism */
    --glass-bg: rgba(255, 255, 255, 0.15);
    --glass-border: rgba(255, 255, 255, 0.2);
    --glass-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
    --glass-blur: blur(4px);
}

/* Dark Mode */
[data-theme="dark"] {
    --primary-color: #9f7aea;
    --glass-bg: rgba(0, 0, 0, 0.3);
    --glass-border: rgba(255, 255, 255, 0.1);
}

/* کامپوننت استاندارد */
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    border-radius: 12px;
    padding: var(--spacing-sm) var(--spacing-lg);
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}
```

### **3. JavaScript ES6+ Vanilla:**
```javascript
/**
 * کلاس مدیریت درخواست‌ها
 */
class RequestManager {
    constructor() {
        this.apiBase = '/api/requests';
        this.init();
    }
    
    /**
     * راه‌اندازی اولیه
     */
    init() {
        this.bindEvents();
        this.loadRequests();
    }
    
    /**
     * اتصال event listener ها
     */
    bindEvents() {
        // فرم ایجاد درخواست
        const createForm = document.getElementById('create-request-form');
        if (createForm) {
            createForm.addEventListener('submit', this.handleCreateRequest.bind(this));
        }
        
        // فیلترها
        const filterElements = document.querySelectorAll('.filter-control');
        filterElements.forEach(element => {
            element.addEventListener('change', this.handleFilterChange.bind(this));
        });
    }
    
    /**
     * ارسال درخواست جدید
     * @param {Event} event 
     */
    async handleCreateRequest(event) {
        event.preventDefault();
        
        try {
            const formData = new FormData(event.target);
            const response = await this.sendRequest('POST', this.apiBase, formData);
            
            if (response.success) {
                this.showSuccess('درخواست با موفقیت ایجاد شد');
                this.loadRequests();
                this.resetForm(event.target);
            } else {
                this.showError(response.message || 'خطا در ایجاد درخواست');
            }
        } catch (error) {
            console.error('Request creation failed:', error);
            this.showError('خطا در ارتباط با سرور');
        }
    }
    
    /**
     * ارسال درخواست AJAX
     * @param {string} method 
     * @param {string} url 
     * @param {FormData|Object} data 
     * @returns {Promise<Object>}
     */
    async sendRequest(method, url, data = null) {
        const options = {
            method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
            }
        };
        
        if (data && method !== 'GET') {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }
        }
        
        const response = await fetch(url, options);
        return await response.json();
    }
}

// راه‌اندازی خودکار
document.addEventListener('DOMContentLoaded', () => {
    window.requestManager = new RequestManager();
});
```

---

## 🔒 **استانداردهای امنیتی**

### **1. اعتبارسنجی ورودی:**
```php
/**
 * کلاس اعتبارسنجی ورودی‌ها
 */
class Validator 
{
    /**
     * اعتبارسنجی شماره موبایل
     */
    public static function validateMobile(string $mobile): array 
    {
        // تبدیل اعداد فارسی
        $mobile = Security::convertPersianToEnglish($mobile);
        
        // حذف کاراکترهای غیرضروری
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // بررسی طول و الگو
        if (!preg_match('/^09[0-9]{9}$/', $mobile)) {
            return ['valid' => false, 'message' => 'شماره موبایل نامعتبر است'];
        }
        
        return ['valid' => true, 'value' => $mobile];
    }
    
    /**
     * اعتبارسنجی مبلغ
     */
    public static function validateAmount(?string $amount): array 
    {
        if (empty($amount)) {
            return ['valid' => true, 'value' => null];
        }
        
        // تبدیل اعداد فارسی و حذف کاما
        $amount = Security::convertPersianToEnglish($amount);
        $amount = str_replace(',', '', $amount);
        
        if (!is_numeric($amount) || $amount < 0) {
            return ['valid' => false, 'message' => 'مبلغ نامعتبر است'];
        }
        
        return ['valid' => true, 'value' => (float)$amount];
    }
}
```

### **2. محافظت از SQL Injection:**
```php
/**
 * استفاده از Prepared Statements
 */
public function getRequestById(int $id): ?array 
{
    $query = "SELECT * FROM payment_requests WHERE id = ? AND deleted_at IS NULL";
    return $this->db->fetchOne($query, [$id]);
}

/**
 * Safe dynamic queries
 */
public function searchRequests(array $filters): array 
{
    $conditions = ['deleted_at IS NULL'];
    $params = [];
    
    if (!empty($filters['status'])) {
        $conditions[] = 'status = ?';
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['group_id'])) {
        $conditions[] = 'group_id = ?';
        $params[] = (int)$filters['group_id'];
    }
    
    $query = "SELECT * FROM payment_requests WHERE " . implode(' AND ', $conditions);
    return $this->db->fetchAll($query, $params);
}
```

### **3. آپلود فایل امن:**
```php
/**
 * کلاس آپلود امن فایل
 */
class SecureFileUpload 
{
    private const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png', 
        'image/gif',
        'application/pdf'
    ];
    
    private const MAX_SIZE = 5 * 1024 * 1024; // 5MB
    
    public function uploadFile(array $file, string $category = 'documents'): array 
    {
        // بررسی اساسی
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'فایل آپلود نشده'];
        }
        
        // بررسی اندازه
        if ($file['size'] > self::MAX_SIZE) {
            return ['success' => false, 'message' => 'اندازه فایل بیش از حد مجاز'];
        }
        
        // بررسی نوع فایل
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            return ['success' => false, 'message' => 'نوع فایل مجاز نیست'];
        }
        
        // تولید نام امن
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = hash('sha256', uniqid() . time()) . '.' . strtolower($extension);
        
        // مسیر ذخیره
        $uploadPath = "upld/{$category}/" . date('Y/m/');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $fullPath = $uploadPath . $filename;
        
        // انتقال فایل
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $fullPath,
                'size' => $file['size'],
                'type' => $mimeType
            ];
        }
        
        return ['success' => false, 'message' => 'خطا در ذخیره فایل'];
    }
}
```

---

## 📱 **استانداردهای Responsive Design**

### **1. Mobile-First Approach:**
```css
/* Base styles (موبایل) */
.request-card {
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    border-radius: 12px;
    background: var(--glass-bg);
    backdrop-filter: var(--glass-blur);
}

/* تبلت */
@media (min-width: 768px) {
    .request-card {
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }
    
    .requests-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }
}

/* دسکتاپ */
@media (min-width: 1200px) {
    .requests-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .sidebar {
        position: fixed;
        top: 80px;
        right: 0;
        width: 300px;
        height: calc(100vh - 80px);
    }
    
    .content-wrapper {
        margin-right: 320px;
    }
}
```

### **2. Touch-Friendly Components:**
```css
/* دکمه‌های Touch-friendly */
.btn {
    min-height: 45px;
    min-width: 45px;
    padding: var(--spacing-sm) var(--spacing-lg);
    margin: var(--spacing-xs);
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
}

/* فیلدهای فرم موبایل */
.form-control {
    min-height: 48px;
    padding: var(--spacing-sm);
    border-radius: 8px;
    font-size: 16px; /* جلوگیری از zoom در iOS */
}

/* Navigation موبایل */
.mobile-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: var(--glass-bg);
    backdrop-filter: var(--glass-blur);
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 1000;
}

@media (min-width: 768px) {
    .mobile-nav {
        display: none;
    }
}
```

---

## 🚀 **استانداردهای Performance**

### **1. بهینه‌سازی Database:**
```sql
-- ایندکس‌های ضروری
CREATE INDEX idx_payment_requests_status ON payment_requests(status);
CREATE INDEX idx_payment_requests_group_id ON payment_requests(group_id);
CREATE INDEX idx_payment_requests_created_at ON payment_requests(created_at);
CREATE INDEX idx_payment_requests_compound ON payment_requests(group_id, status, created_at);

-- View برای کوئری‌های پیچیده
CREATE VIEW v_request_statistics AS
SELECT 
    group_id,
    COUNT(*) as total_requests,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount
FROM payment_requests 
WHERE deleted_at IS NULL
GROUP BY group_id;
```

### **2. کش کردن هوشمند:**
```php
/**
 * کلاس کش ساده
 */
class Cache 
{
    private static string $cacheDir = 'cache/';
    
    public static function get(string $key, int $ttl = 3600): mixed 
    {
        $file = self::$cacheDir . hash('md5', $key) . '.cache';
        
        if (!file_exists($file) || (time() - filemtime($file)) > $ttl) {
            return null;
        }
        
        return unserialize(file_get_contents($file));
    }
    
    public static function set(string $key, mixed $data): bool 
    {
        $file = self::$cacheDir . hash('md5', $key) . '.cache';
        
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
        
        return file_put_contents($file, serialize($data)) !== false;
    }
    
    public static function delete(string $key): bool 
    {
        $file = self::$cacheDir . hash('md5', $key) . '.cache';
        return file_exists($file) && unlink($file);
    }
}

// استفاده در Controller
public function getRequestStats(int $groupId): array 
{
    $cacheKey = "request_stats_{$groupId}";
    $stats = Cache::get($cacheKey, 300); // 5 دقیقه
    
    if ($stats === null) {
        $stats = $this->requestModel->calculateStats($groupId);
        Cache::set($cacheKey, $stats);
    }
    
    return $stats;
}
```

### **3. Lazy Loading و Asset Optimization:**
```javascript
/**
 * Lazy Loading برای تصاویر
 */
class LazyImageLoader {
    constructor() {
        this.observer = new IntersectionObserver(this.handleIntersection.bind(this));
        this.init();
    }
    
    init() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => this.observer.observe(img));
    }
    
    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                this.observer.unobserve(img);
            }
        });
    }
}

/**
 * Asset Loading مشروط
 */
class ConditionalAssetLoader {
    static async loadChart() {
        if (!window.Chart) {
            await this.loadScript('/assets/js/chart.min.js');
        }
        return window.Chart;
    }
    
    static loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
}
```

---

## 📊 **استانداردهای API**

### **1. Response Format استاندارد:**
```php
/**
 * کلاس پاسخ API استاندارد
 */
class ApiResponse 
{
    public static function success(mixed $data = null, string $message = 'عملیات موفق', array $meta = []): array 
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '2.0'
            ], $meta)
        ];
    }
    
    public static function error(string $message = 'خطا در عملیات', mixed $errors = null, int $code = 400): array 
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'error_code' => $code,
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '2.0'
            ]
        ];
    }
    
    public static function paginated(array $data, int $page, int $perPage, int $total): array 
    {
        return self::success($data, 'داده‌ها دریافت شد', [
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'has_more' => ($page * $perPage) < $total
            ]
        ]);
    }
}
```

### **2. API Endpoints استاندارد:**
```php
// Routes definition
$routes = [
    // Requests API
    'GET /api/requests' => 'RequestController@index',
    'POST /api/requests' => 'RequestController@store', 
    'GET /api/requests/{id}' => 'RequestController@show',
    'PUT /api/requests/{id}' => 'RequestController@update',
    'DELETE /api/requests/{id}' => 'RequestController@destroy',
    
    // Request Actions
    'POST /api/requests/{id}/approve' => 'RequestController@approve',
    'POST /api/requests/{id}/reject' => 'RequestController@reject',
    'POST /api/requests/{id}/complete' => 'RequestController@complete',
    
    // Documents API  
    'POST /api/requests/{id}/documents' => 'DocumentController@upload',
    'GET /api/requests/{id}/documents' => 'DocumentController@index',
    'DELETE /api/documents/{id}' => 'DocumentController@destroy',
    
    // Reports API
    'GET /api/reports/requests' => 'ReportController@requests',
    'GET /api/reports/statistics' => 'ReportController@statistics',
    'POST /api/reports/export' => 'ReportController@export'
];
```

---

## 🧪 **استانداردهای Testing**

### **1. Unit Testing:**
```php
use PHPUnit\Framework\TestCase;

/**
 * تست کلاس Validator
 */
class ValidatorTest extends TestCase 
{
    public function testValidateMobileWithValidNumber(): void 
    {
        $result = Validator::validateMobile('۰۹۱۲۳۴۵۶۷۸۹');
        
        $this->assertTrue($result['valid']);
        $this->assertEquals('09123456789', $result['value']);
    }
    
    public function testValidateMobileWithInvalidNumber(): void 
    {
        $result = Validator::validateMobile('123456');
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('message', $result);
    }
    
    public function testValidateAmountWithPersianNumbers(): void 
    {
        $result = Validator::validateAmount('۱۰۰,۰۰۰');
        
        $this->assertTrue($result['valid']);
        $this->assertEquals(100000.0, $result['value']);
    }
}
```

### **2. Integration Testing:**
```php
/**
 * تست یکپارچگی API
 */
class RequestApiTest extends TestCase 
{
    public function testCreateRequestWithValidData(): void 
    {
        $data = [
            'title' => 'تست درخواست',
            'amount' => '100000',
            'account_number' => '1234567890',
            'account_holder' => 'علی احمدی'
        ];
        
        $response = $this->post('/api/requests', $data);
        
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message', 
            'data' => ['id', 'title', 'status'],
            'meta'
        ]);
    }
}
```

---

## 📚 **استانداردهای مستندسازی**

### **1. کامنت‌گذاری کد:**
```php
/**
 * پردازش درخواست تایید حواله
 * 
 * این متد درخواست حواله را بررسی و در صورت تایید، آن را به مرحله بعد ارسال می‌کند.
 * همچنین تاریخچه تغییرات را ثبت و اطلاع‌رسانی لازم را انجام می‌دهد.
 * 
 * @param int $requestId شناسه درخواست
 * @param string $comment نظر تایید کننده (اختیاری)
 * @param array $attachments فایل‌های ضمیمه (اختیاری)
 * 
 * @return array نتیجه عملیات شامل:
 *               - success: موفقیت عملیات
 *               - message: پیام نتیجه
 *               - data: اطلاعات درخواست به‌روزرسانی شده
 * 
 * @throws \InvalidArgumentException درخواست یافت نشد
 * @throws \Exception خطا در پردازش
 * 
 * @since 2.0.0
 * @author Cursor AI + Developer Team
 */
public function approveRequest(int $requestId, string $comment = '', array $attachments = []): array 
{
    // پیاده‌سازی متد...
}
```

### **2. Documentation Files:**
```markdown
# API Documentation Template

## Endpoint: POST /api/requests

### توضیح
ایجاد درخواست حواله جدید

### Headers
```
Content-Type: application/json
Authorization: Bearer {token}
X-CSRF-Token: {csrf_token}
```

### Request Body
```json
{
    "title": "عنوان درخواست",
    "description": "توضیحات اضافی", 
    "amount": 1000000,
    "account_number": "1234567890",
    "account_holder": "نام صاحب حساب",
    "bank_name": "نام بانک",
    "priority": "normal"
}
```

### Response
```json
{
    "success": true,
    "message": "درخواست با موفقیت ایجاد شد",
    "data": {
        "id": 123,
        "title": "عنوان درخواست",
        "status": "pending",
        "created_at": "1404-03-31 10:30:00"
    }
}
```

### Error Codes
- 400: Bad Request - اطلاعات نامعتبر
- 401: Unauthorized - عدم احراز هویت
- 403: Forbidden - عدم دسترسی
- 422: Validation Error - خطا در اعتبارسنجی
```

---

## 🔧 **ابزارها و DevOps**

### **1. Git Workflow:**
```bash
# Branch Naming
feature/phase-3-document-management
bugfix/fix-mobile-responsive
hotfix/security-patch-auth

# Commit Message Format
feat(requests): add real-time status updates
fix(mobile): resolve sidebar overlay issue  
docs(api): update endpoint documentation
perf(database): optimize request queries

# Pre-commit hooks
composer install
php vendor/bin/phpstan analyse
php vendor/bin/php-cs-fixer fix
npm run build
```

### **2. Environment Configuration:**
```php
// config/environment.php
return [
    'development' => [
        'database' => [
            'host' => 'localhost',
            'name' => 'samanet_dev',
            'user' => 'root',
            'pass' => '123'
        ],
        'debug' => true,
        'cache' => false,
        'log_level' => 'debug'
    ],
    
    'production' => [
        'database' => [
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'), 
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS')
        ],
        'debug' => false,
        'cache' => true,
        'log_level' => 'error'
    ]
];
```

### **3. Deployment Checklist:**
```yaml
Pre-Deployment:
  - [ ] Run all tests
  - [ ] Update version number
  - [ ] Generate documentation
  - [ ] Optimize assets
  - [ ] Database migrations
  - [ ] Security scan

Deployment:
  - [ ] Backup current version
  - [ ] Deploy new code
  - [ ] Run database migrations
  - [ ] Clear caches
  - [ ] Update configurations
  - [ ] Restart services

Post-Deployment:
  - [ ] Verify functionality
  - [ ] Check error logs  
  - [ ] Monitor performance
  - [ ] Update documentation
  - [ ] Notify stakeholders
```

---

## 🎯 **راهنمای فازهای آینده**

### **فاز 4: مدیریت اسناد (آماده شروع)**
```php
// نمونه کدهای مورد نیاز

/**
 * کنترلر مدیریت اسناد
 */
class DocumentController extends BaseController 
{
    /**
     * آپلود چندگانه فایل با drag & drop
     */
    public function multipleUpload(int $requestId): array 
    {
        // پیاده‌سازی آپلود چندگانه
    }
    
    /**
     * اعمال واترمارک خودکار
     */
    public function applyWatermark(string $imagePath): string 
    {
        // پیاده‌سازی واترمارک
    }
    
    /**
     * ایجاد thumbnail
     */
    public function generateThumbnail(string $imagePath): string 
    {
        // پیاده‌سازی thumbnail
    }
}
```

### **فاز 5: سیستم تایید**
```php
/**
 * کلاس گردش کار
 */
class WorkflowEngine 
{
    /**
     * تعریف مراحل تایید
     */
    public function defineWorkflow(string $type): array 
    {
        // پیاده‌سازی workflow
    }
    
    /**
     * اجرای مرحله بعدی
     */
    public function nextStep(int $requestId): array 
    {
        // پیاده‌سازی next step
    }
}
```

---

## ✅ **چک‌لیست Quality Assurance ورژن 3.0**

### **کیفیت کد:**
- [x] PSR-12 compliance
- [x] PHPStan level 8 compatibility
- [x] Unit test coverage > 80%
- [x] No security vulnerabilities
- [x] Performance benchmarks met
- [x] Advanced search system implemented
- [x] Tag management system complete

### **UI/UX:**
- [x] Mobile responsive (< 575px)
- [x] Touch-friendly (45px+ buttons)
- [x] Accessibility (WCAG 2.1)
- [x] Persian language support
- [x] Theme system working perfectly
- [x] Real-time search with highlighting
- [x] Enterprise-Grade dashboard design

### **Performance:**
- [x] Page load < 2 seconds
- [x] Database queries optimized
- [x] Assets minified and compressed
- [x] Caching implemented
- [x] Image optimization ready
- [x] Search debouncing (200ms)
- [x] Smooth animations

### **Security:**
- [x] Input validation everywhere
- [x] SQL injection protected
- [x] XSS prevention
- [x] CSRF tokens
- [x] File upload security planned
- [x] Tag validation complete

### **Documentation:**
- [x] API endpoints documented
- [x] Code comments in Persian
- [x] User manual updated
- [x] Deployment guide
- [x] Troubleshooting guide
- [x] Advanced search rules documented

---

## 🚀 **خلاصه و نکات پایانی ورژن 3.0**

### **دستاوردهای کلیدی:**
1. **سیستم جستجوی یکپارچه** - قابل استفاده در تمام پروژه
2. **مدیریت تگ‌های پیشرفته** - با رنگ‌بندی علمی
3. **طراحی Enterprise-Grade** - مطابق استانداردهای بین‌المللی
4. **Mobile-First Design** - بهینه‌سازی کامل موبایل
5. **Performance بالا** - 95/100 mobile score

### **آماده برای فاز 4:**
- **آپلود اسناد چندگانه** - Drag & Drop interface
- **سیستم واترمارک** - اعمال خودکار لوگو
- **گالری پیشرفته** - Lightbox و navigation
- **فشرده‌سازی تصاویر** - بهینه‌سازی اندازه

### **نکات مهم برای Cursor AI:**
- همیشه از سیستم جستجوی استاندارد استفاده کنید
- تگ‌ها باید با محاسبه luminance رنگ متن داشته باشند
- همه فرم‌ها باید Real-time validation داشته باشند
- طراحی باید دقیقاً مطابق dashboard.css باشد
- ESC key برای پاک کردن جستجو اجباری است

### **منابع و مراجع:**
- **Design System:** .cursor/rules/MANDATORY-DESIGN.mdc
- **Search System:** .cursor/rules/SEARCH-FILTER-SYSTEM.mdc
- **PSR-12:** https://www.php-fig.org/psr/psr-12/
- **Bootstrap RTL:** https://getbootstrap.com/
- **Color Science:** https://en.wikipedia.org/wiki/Relative_luminance

---
**📝 نسخه:** 3.0 تکمیل شده  
**📅 آخرین بروزرسانی:** 1404/10/15  
**✅ وضعیت:** فاز 3 تکمیل - آماده فاز 4  
**🎯 هدف:** توسعه حرفه‌ای سامانت با Cursor AI  
**📊 پیشرفت:** 60% (3 از 5 فاز اصلی تکمیل شده)