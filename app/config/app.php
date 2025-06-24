<?php
/**
 * نام فایل: app.php
 * مسیر فایل: /app/config/app.php
 * توضیح: تنظیمات کلی برنامه سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تنظیمات کلی برنامه
define('APP_NAME', 'سامانت');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production
define('APP_DEBUG', true);
define('APP_TIMEZONE', 'Asia/Tehran');
define('APP_LANG', 'fa');

// تنظیمات URL
define('BASE_URL', 'http://localhost:3110/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOAD_URL', BASE_URL . 'upld/');
define('API_URL', BASE_URL . 'api/');
define('PUBLIC_URL', BASE_URL . 'pub/');

// تنظیمات مسیر فایل‌ها
define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('UPLOAD_PATH', ROOT_PATH . 'upld/');
define('CACHE_PATH', ROOT_PATH . 'cache/');
define('LOG_PATH', ROOT_PATH . 'logs/');

// تنظیمات امنیتی
define('SESSION_LIFETIME', 7200); // 2 ساعت
define('CSRF_TOKEN_NAME', '_token');
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 دقیقه

// تنظیمات آپلود فایل
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);
define('WATERMARK_TEXT', 'سامانت - ' . date('Y/m/d'));

// تنظیمات تقویم
define('DEFAULT_DATE_FORMAT', 'Y/m/d');
define('DEFAULT_DATETIME_FORMAT', 'Y/m/d H:i');
define('PERSIAN_MONTHS', [
    1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
    4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
    7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
    10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
]);

// نقش‌های کاربری
define('USER_ROLES', [
    'admin' => 'مدیر سیستم',
    'manager' => 'مدیر حسابداری', 
    'accountant' => 'حسابدار',
    'user' => 'کاربر عادی'
]);

// وضعیت‌های درخواست
define('REQUEST_STATUS', [
    'pending' => 'در انتظار',
    'processing' => 'در حال پردازش',
    'completed' => 'تکمیل شده',
    'cancelled' => 'لغو شده'
]);

// اولویت‌های درخواست
define('REQUEST_PRIORITIES', [
    'low' => 'کم',
    'normal' => 'معمولی', 
    'high' => 'بالا',
    'urgent' => 'فوری'
]);

// تنظیم منطقه زمانی
date_default_timezone_set(APP_TIMEZONE);

// تنظیمات خطا بر اساس محیط
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/**
 * تابع دریافت تنظیمات برنامه
 */
function config($key, $default = null) 
{
    $configs = [
        'app.name' => APP_NAME,
        'app.version' => APP_VERSION,
        'app.env' => APP_ENV,
        'app.debug' => APP_DEBUG,
        'app.timezone' => APP_TIMEZONE,
        'app.lang' => APP_LANG,
        'upload.max_size' => MAX_FILE_SIZE,
        'upload.image_types' => ALLOWED_IMAGE_TYPES,
        'upload.doc_types' => ALLOWED_DOC_TYPES,
    ];
    
    return isset($configs[$key]) ? $configs[$key] : $default;
}

/**
 * تابع دریافت URL کامل
 */
function url($path = '') 
{
    if (empty($path)) {
        return BASE_URL;
    }
    
    // حذف slash ابتدایی
    $path = ltrim($path, '/');
    
    // اگر مسیر شامل index.php باشد، URL کامل را برگردان
    if (strpos($path, 'index.php') === 0) {
        return BASE_URL . $path;
    }
    
    // بررسی اینکه آیا path قبلاً پارامتر دارد یا نه
    if (strpos($path, '?') !== false) {
        // اگر پارامتر دارد، تبدیل کن
        $pathParts = explode('?', $path, 2);
        $route = $pathParts[0];
        $params = $pathParts[1];
        return BASE_URL . 'index.php?route=' . $route . '&' . $params;
    } else {
        // در غیر این صورت، route query parameter اضافه کن
        return BASE_URL . 'index.php?route=' . $path;
    }
}

/**
 * تابع دریافت مسیر asset
 */
function asset($path) 
{
    return ASSETS_URL . ltrim($path, '/');
}

/**
 * تابع لاگ کردن
 */
function writeLog($message, $level = 'INFO') 
{
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents(LOG_PATH . 'app.log', $logMessage, FILE_APPEND | LOCK_EX);
}
?>