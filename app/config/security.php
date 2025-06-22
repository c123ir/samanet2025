<?php
/**
 * نام فایل: security.php
 * مسیر فایل: /app/config/security.php
 * توضیح: تنظیمات امنیتی سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// کلیدهای امنیتی
define('ENCRYPTION_KEY', 'Samanat_2024_Secret_Key_Very_Strong');
define('JWT_SECRET', 'Samanat_JWT_Secret_2024_Persian');
define('HASH_ALGORITHM', 'sha256');
define('PASSWORD_ALGORITHM', PASSWORD_BCRYPT);

// تنظیمات Session
define('SESSION_NAME', 'SAMANAT_SESSION');
define('SESSION_SECURE', false); // true در production با HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// تنظیمات CSRF
define('CSRF_TOKEN_LENGTH', 32);
define('CSRF_TOKEN_EXPIRE', 3600); // 1 ساعت

// تنظیمات Rate Limiting
define('RATE_LIMIT_REQUESTS', 100); // تعداد درخواست در دقیقه
define('RATE_LIMIT_WINDOW', 60); // پنجره زمانی (ثانیه)

// IP های مجاز (در صورت نیاز) - خالی یعنی همه مجاز
define('ALLOWED_IPS', []);

// IP های مسدود
define('BLOCKED_IPS', [
    // '192.168.1.100'
]);

/**
 * کلاس امنیت
 */
class SecurityConfig 
{
    /**
     * رمزگذاری متن
     */
    public static function encrypt($data) 
    {
        $key = ENCRYPTION_KEY;
        $method = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
        $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * رمزگشایی متن
     */
    public static function decrypt($data) 
    {
        $key = ENCRYPTION_KEY;
        $method = 'AES-256-CBC';
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, $method, $key, 0, $iv);
    }

    /**
     * تولید CSRF Token
     */
    public static function generateCSRFToken() 
    {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) ||
            (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * بررسی CSRF Token
     */
    public static function verifyCSRFToken($token) 
    {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        if ((time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * پاکسازی ورودی از کاراکترهای مخرب
     */
    public static function sanitizeInput($input) 
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        // حذف فضاهای اضافی
        $input = trim($input);
        
        // حذف تگ‌های HTML
        $input = strip_tags($input);
        
        // تبدیل کاراکترهای خاص
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }

    /**
     * بررسی قدرت پسورد
     */
    public static function validatePassword($password) 
    {
        $errors = [];
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'پسورد باید حداقل ' . PASSWORD_MIN_LENGTH . ' کاراکتر باشد';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'پسورد باید شامل حداقل یک عدد باشد';
        }
        
        if (!preg_match('/[a-zA-Z]/', $password)) {
            $errors[] = 'پسورد باید شامل حداقل یک حرف انگلیسی باشد';
        }
        
        return empty($errors) ? true : $errors;
    }

    /**
     * بررسی IP مجاز
     */
    public static function isIPAllowed($ip = null) 
    {
        $ip = $ip ?: self::getClientIP();
        
        // بررسی IP های مسدود
        if (in_array($ip, BLOCKED_IPS)) {
            return false;
        }
        
        // اگر لیست IP های مجاز خالی باشد، همه مجاز هستند
        if (empty(ALLOWED_IPS)) {
            return true;
        }
        
        return in_array($ip, ALLOWED_IPS);
    }

    /**
     * دریافت IP کلاینت
     */
    public static function getClientIP() 
    {
        $ip_headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * ثبت تلاش ورود ناموفق
     */
    public static function logFailedAttempt($username, $ip = null) 
    {
        $ip = $ip ?: self::getClientIP();
        $timestamp = date('Y-m-d H:i:s');
        $message = "Failed login attempt for user: {$username} from IP: {$ip}";
        
        writeLog($message, 'WARNING');
        
        // ذخیره در session برای محدودسازی
        $key = 'failed_attempts_' . $ip;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        $_SESSION[$key][] = time();
        
        // پاک کردن تلاش‌های قدیمی
        $_SESSION[$key] = array_filter($_SESSION[$key], function($time) {
            return (time() - $time) < LOGIN_LOCKOUT_TIME;
        });
    }

    /**
     * بررسی محدودیت تلاش ورود
     */
    public static function isLoginLocked($ip = null) 
    {
        $ip = $ip ?: self::getClientIP();
        $key = 'failed_attempts_' . $ip;
        
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        // پاک کردن تلاش‌های قدیمی
        $_SESSION[$key] = array_filter($_SESSION[$key], function($time) {
            return (time() - $time) < LOGIN_LOCKOUT_TIME;
        });
        
        return count($_SESSION[$key]) >= MAX_LOGIN_ATTEMPTS;
    }

    /**
     * پاک کردن تلاش‌های ناموفق
     */
    public static function clearFailedAttempts($ip = null) 
    {
        $ip = $ip ?: self::getClientIP();
        $key = 'failed_attempts_' . $ip;
        unset($_SESSION[$key]);
    }
}

// تنظیم session فقط اگر session شروع نشده باشد
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.name', SESSION_NAME);
    ini_set('session.cookie_httponly', SESSION_HTTPONLY);
    ini_set('session.cookie_secure', SESSION_SECURE);
    ini_set('session.cookie_samesite', SESSION_SAMESITE);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
}

/**
 * تابع سراسری برای دریافت CSRF Token
 */
function csrf_token() 
{
    return SecurityConfig::generateCSRFToken();
}

/**
 * تابع سراسری برای ایجاد فیلد CSRF
 */
function csrf_field() 
{
    $token = csrf_token();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}
?>