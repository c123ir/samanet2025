<?php
/**
 * نام فایل: Security.php
 * مسیر فایل: /app/helpers/Security.php
 * توضیح: کلاس کمکی امنیت و اعتبارسنجی
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

class Security 
{
    /**
     * پاکسازی ورودی کاربر
     */
    public static function sanitizeInput($input) 
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // حذف فضاهای اضافی
            $input = trim($input);
            
            // حذف تگ‌های HTML مخرب
            $input = strip_tags($input);
            
            // تبدیل کاراکترهای خاص به HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            
            return $input;
        }
        
        return $input;
    }

    /**
     * اعتبارسنجی ایمیل
     */
    public static function validateEmail($email) 
    {
        $email = self::sanitizeInput($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * تبدیل اعداد فارسی به انگلیسی
     */
    public static function convertPersianToEnglishNumbers($input)
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        // تبدیل اعداد فارسی
        $input = str_replace($persianNumbers, $englishNumbers, $input);
        
        // تبدیل اعداد عربی (اگر وجود داشته باشد)
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $input = str_replace($arabicNumbers, $englishNumbers, $input);
        
        return $input;
    }

    /**
     * اعتبارسنجی شماره موبایل ایران
     */
    public static function validateMobile($mobile) 
    {
        $mobile = self::sanitizeInput($mobile);
        
        // تبدیل اعداد فارسی/عربی به انگلیسی
        $mobile = self::convertPersianToEnglishNumbers($mobile);
        
        // حذف کاراکترهای غیرعددی
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // بررسی فرمت شماره موبایل ایران
        $pattern = '/^(09|9)[0-9]{9}$/';
        return preg_match($pattern, $mobile);
    }

    /**
     * اعتبارسنجی نام کاربری
     */
    public static function validateUsername($username) 
    {
        $username = self::sanitizeInput($username);
        
        // فقط حروف انگلیسی، اعداد و خط زیر
        $pattern = '/^[a-zA-Z0-9_]{3,20}$/';
        return preg_match($pattern, $username);
    }

    /**
     * اعتبارسنجی پسورد
     */
    public static function validatePassword($password) 
    {
        $errors = [];
        
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'پسورد باید حداقل ' . PASSWORD_MIN_LENGTH . ' کاراکتر باشد';
        }
        
        if (strlen($password) > 50) {
            $errors[] = 'پسورد نباید بیش از ۵۰ کاراکتر باشد';
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
     * رمزگذاری پسورد
     */
    public static function hashPassword($password) 
    {
        return password_hash($password, PASSWORD_ALGORITHM);
    }

    /**
     * بررسی پسورد
     */
    public static function verifyPassword($password, $hash) 
    {
        return password_verify($password, $hash);
    }

    /**
     * تولید رمز عبور تصادفی قوی
     */
    public static function generateRandomPassword($length = 8) 
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';
        
        // اطمینان از وجود حداقل یکی از هر نوع کاراکتر
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // تکمیل باقی کاراکترها
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // مخلوط کردن کاراکترها
        return str_shuffle($password);
    }

    /**
     * تولید Token تصادفی
     */
    public static function generateToken($length = 32) 
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * اعتبارسنجی فایل آپلودی
     */
    public static function validateUploadedFile($file) 
    {
        $errors = [];
        
        // بررسی وجود فایل
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = 'فایلی انتخاب نشده است';
            return $errors;
        }
        
        // بررسی خطا در آپلود
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'اندازه فایل بیش از حد مجاز است';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'آپلود فایل ناتمام است';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errors[] = 'پوشه موقت موجود نیست';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errors[] = 'امکان نوشتن فایل وجود ندارد';
                    break;
                default:
                    $errors[] = 'خطا در آپلود فایل';
            }
            return $errors;
        }
        
        // بررسی اندازه فایل
        if ($file['size'] > MAX_FILE_SIZE) {
            $maxSizeMB = MAX_FILE_SIZE / (1024 * 1024);
            $errors[] = "اندازه فایل نباید بیش از {$maxSizeMB} مگابایت باشد";
        }
        
        // بررسی نوع فایل
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
        
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'نوع فایل مجاز نیست. فرمت‌های مجاز: ' . implode(', ', $allowedTypes);
        }
        
        // بررسی MIME Type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($mimeType, $allowedMimes)) {
            $errors[] = 'نوع فایل نامعتبر است';
        }
        
        return $errors;
    }

    /**
     * تولید نام فایل امن
     */
    public static function generateSafeFilename($originalName) 
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        
        // پاکسازی نام فایل
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);
        $filename = substr($filename, 0, 50); // محدود کردن طول نام
        
        // اضافه کردن timestamp برای یکتا بودن
        $timestamp = time();
        $randomString = substr(md5(uniqid()), 0, 8);
        
        return $filename . '_' . $timestamp . '_' . $randomString . '.' . $extension;
    }

    /**
     * بررسی دسترسی کاربر
     */
    public static function checkPermission($requiredRole, $userRole = null) 
    {
        if (!$userRole && isset($_SESSION['user_role'])) {
            $userRole = $_SESSION['user_role'];
        }
        
        $roleHierarchy = [
            'admin' => 4,
            'manager' => 3,
            'accountant' => 2,
            'user' => 1
        ];
        
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }

    /**
     * بررسی ورود کاربر
     */
    public static function isLoggedIn() 
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * خروج از سیستم
     */
    public static function logout() 
    {
        // پاک کردن تمام session ها
        $_SESSION = [];
        
        // حذف کوکی session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // تخریب session
        session_destroy();
    }

    /**
     * ریدایرکت امن
     */
    public static function redirect($url, $statusCode = 302) 
    {
        writeLog("Debug: Security::redirect called with URL: $url", 'INFO');
        
        // بررسی اینکه URL محلی باشد
        $parsedUrl = parse_url($url);
        $currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost:3110';
        
        writeLog("Debug: Parsed URL host: " . ($parsedUrl['host'] ?? 'none'), 'INFO');
        writeLog("Debug: Current host: $currentHost", 'INFO');
        
        // اگر URL مشکوک باشد، به login redirect کن (نه dashboard)
        if (isset($parsedUrl['host'])) {
            // مقایسه host بدون در نظر گیری پورت
            $parsedHost = $parsedUrl['host'];
            $currentHostOnly = explode(':', $currentHost)[0]; // حذف پورت
            
            if ($parsedHost !== $currentHostOnly) {
                writeLog("Debug: URL host mismatch ($parsedHost vs $currentHostOnly), redirecting to login instead", 'INFO');
                $url = url('login');
            } else {
                writeLog("Debug: Host match confirmed", 'INFO');
            }
        }
        
        // اطمینان از اینکه URL کامل است
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            writeLog("Debug: URL not valid, adding base URL: $url", 'INFO');
            $url = url($url); // اضافه کردن base URL
        }
        
        writeLog("Debug: Final redirect URL: $url", 'INFO');
        
        header("Location: $url", true, $statusCode);
        exit();
    }

    /**
     * محافظت در برابر CSRF
     */
    public static function verifyCSRF($token = null) 
    {
        if (!$token) {
            $token = $_POST[CSRF_TOKEN_NAME] ?? $_GET[CSRF_TOKEN_NAME] ?? null;
        }
        
        if (!$token) {
            return false;
        }
        
        return SecurityConfig::verifyCSRFToken($token);
    }

    /**
     * محدودسازی نرخ درخواست (Rate Limiting)
     */
    public static function checkRateLimit($identifier = null) 
    {
        // موقتاً غیرفعال برای تست
        return true;
        
        /*
        if (!$identifier) {
            $identifier = SecurityConfig::getClientIP();
        }
        
        $key = 'rate_limit_' . md5($identifier);
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        // پاک کردن درخواست‌های قدیمی
        $_SESSION[$key] = array_filter($_SESSION[$key], function($time) use ($now) {
            return ($now - $time) < RATE_LIMIT_WINDOW;
        });
        
        // بررسی محدودیت
        if (count($_SESSION[$key]) >= RATE_LIMIT_REQUESTS) {
            return false;
        }
        
        // اضافه کردن درخواست جدید
        $_SESSION[$key][] = $now;
        
        return true;
        */
    }

    /**
     * فیلتر کردن ورودی برای جستجو
     */
    public static function sanitizeSearchTerm($term) 
    {
        $term = self::sanitizeInput($term);
        
        // حذف کاراکترهای ویژه SQL
        $term = str_replace(['%', '_'], ['\%', '\_'], $term);
        
        // محدود کردن طول جستجو
        $term = substr($term, 0, 100);
        
        return $term;
    }

    /**
     * بررسی معتبر بودن session
     */
    public static function isValidSession(): bool 
    {
        // شروع session اگر هنوز شروع نشده
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // بررسی وجود کاربر در session
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
            return false;
        }

        // بررسی timeout - افزایش timeout به 2 ساعت
        $timeout = 7200; // 2 ساعت = 7200 ثانیه (قبلاً احتمالاً کمتر بوده)
        
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                // Session expired
                self::destroySession();
                return false;
            }
        }

        // تمدید last_activity برای هر درخواست معتبر
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * حذف کامل session
     */
    public static function destroySession(): void 
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}
?>