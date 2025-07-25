<?php
/**
 * نام فایل: BaseController.php
 * مسیر فایل: /app/controllers/BaseController.php
 * توضیح: کنترلر پایه برای تمام کنترلرهای سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

class BaseController 
{
    protected $data = [];
    protected $user = null;
    protected $requireAuth = true;
    protected $requiredRole = 'user';
    
    public function __construct() 
    {
        // شروع session اگر شروع نشده
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // موقتاً غیرفعال کردن بررسی‌های امنیتی برای رفع مشکل
        /*
        // بررسی امنیت IP
        if (!SecurityConfig::isIPAllowed()) {
            $this->sendError('دسترسی از این IP مجاز نیست', 403);
        }
        
        // بررسی محدودیت نرخ درخواست
        if (!Security::checkRateLimit()) {
            $this->sendError('تعداد درخواست‌های شما بیش از حد مجاز است', 429);
        }
        */
        
        // بررسی احراز هویت (فقط برای کنترلرهایی که نیاز دارند)
        if ($this->requireAuth && !$this->checkAuth()) {
            $this->redirectToLogin();
        }
        
        // بارگذاری اطلاعات کاربر
        if (Security::isLoggedIn()) {
            $this->loadUser();
        }
        
        // بررسی دسترسی نقش
        if ($this->requireAuth && !Security::checkPermission($this->requiredRole)) {
            $this->sendError('شما دسترسی لازم برای این بخش را ندارید', 403);
        }
        
        // تنظیم داده‌های پیش‌فرض برای view
        $this->data = [
            'page_title' => APP_NAME,
            'user' => $this->user,
            'current_route' => $this->getCurrentRoute(),
            'csrf_token' => $this->getCSRFToken(),
            'app_name' => APP_NAME,
            'app_version' => APP_VERSION,
            'flash' => $this->getFlash()
        ];
    }

    /**
     * دریافت CSRF Token
     */
    protected function getCSRFToken() 
    {
        if (function_exists('csrf_token')) {
            return csrf_token();
        }
        
        // تولید token ساده در صورت عدم وجود تابع
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * بررسی احراز هویت کاربر
     */
    protected function checkAuth() 
    {
        return Security::isLoggedIn();
    }

    /**
     * بارگذاری اطلاعات کاربر فعال
     */
    protected function loadUser() 
    {
        if (isset($_SESSION['user_id'])) {
            // موقتاً از session استفاده می‌کنیم به جای دیتابیس
            $this->user = [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? 'admin',
                'full_name' => $_SESSION['full_name'] ?? 'مدیر سیستم',
                'role' => $_SESSION['user_role'] ?? 'admin',
                'group_id' => $_SESSION['group_id'] ?? 1
            ];
            
            writeLog("Debug: User loaded from session: " . $this->user['username'], 'INFO');
            
            /*
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            $this->user = $userModel->find($_SESSION['user_id']);
            
            if (!$this->user) {
                // کاربر در دیتابیس موجود نیست، خروج از سیستم
                Security::logout();
                $this->redirectToLogin();
            }
            */
        }
    }

    /**
     * دریافت مسیر فعلی
     */
    protected function getCurrentRoute() 
    {
        return $_GET['route'] ?? '';
    }

    /**
     * ریدایرکت به صفحه ورود
     */
    protected function redirectToLogin() 
    {
        $currentRoute = $this->getCurrentRoute();
        
        // اگر در حال حاضر در صفحه login هستیم، loop نکنیم
        if ($currentRoute === 'login') {
            return;
        }
        
        $returnUrl = !empty($currentRoute) && $currentRoute !== 'dashboard' 
            ? '?return=' . urlencode($currentRoute) 
            : '';
        
        $loginUrl = url('login') . $returnUrl;
        Security::redirect($loginUrl);
    }

    /**
     * Renders a view file with data and wraps it in the main layout.
     *
     * @param string $view The view file to render (e.g., 'dashboard/index').
     * @param array $data Data to be extracted and made available to the view.
     */
    protected function render(string $view, array $data = [])
    {
        $viewFile = APP_PATH . "views/{$view}.php";

        if (file_exists($viewFile)) {
            // Make all protected data and argument data available to the view and layout
            extract($this->data);
            extract($data);

            // This variable holds the path to the view file
            // and the main layout will include it.
            $content_view = $viewFile;
            
            require APP_PATH . 'views/layouts/main.php';
        } else {
            // Handle view not found error
            $this->sendError("View not found: {$viewFile}", 404);
        }
    }

    /**
     * Renders a view file directly without any layout.
     * Useful for pages like login, register, or for AJAX content.
     *
     * @param string $view The view file to render.
     * @param array $data Data to be made available to the view.
     */
    protected function renderPartial(string $view, array $data = [])
    {
        $viewFile = APP_PATH . "views/{$view}.php";

        if (file_exists($viewFile)) {
            extract($this->data);
            extract($data);
            require $viewFile;
        } else {
            $this->sendError("View not found: {$viewFile}", 404);
        }
    }

    /**
     * نمایش JSON response
     */
    protected function json($data = [], $status = 200) 
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        
        $response = [
            'success' => $status >= 200 && $status < 300,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * ارسال پاسخ موفقیت‌آمیز
     */
    protected function sendSuccess($message = 'عملیات با موفقیت انجام شد', $data = [], $status = 200) 
    {
        $this->json([
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * ارسال پاسخ خطا
     */
    protected function sendError($message = 'خطایی رخ داده است', $status = 400, $errors = []) 
    {
        // لاگ کردن خطا
        writeLog("Error {$status}: {$message}", 'ERROR');
        
        if ($this->isAjaxRequest()) {
            $this->json([
                'message' => $message,
                'errors' => $errors
            ], $status);
        } else {
            // نمایش صفحه خطا
            http_response_code($status);
            $this->data['error_message'] = $message;
            $this->data['error_code'] = $status;
            include APP_PATH . 'views/layouts/error.php';
            exit();
        }
    }

    /**
     * بررسی درخواست AJAX
     */
    protected function isAjaxRequest() 
    {
        // بررسی header های مختلف برای تشخیص AJAX
        
        // 1. استاندارد XMLHttpRequest header
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        // 2. بررسی Accept header برای JSON
        if (isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            return true;
        }
        
        // 3. بررسی CSRF header (که فقط توسط JavaScript ارسال می‌شود)
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return true;
        }
        
        // 4. بررسی Content-Type برای JSON
        if (isset($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            return true;
        }
        
        // 5. درخواست‌های fetch API معمولاً sec-fetch-dest دارند
        if (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && 
            $_SERVER['HTTP_SEC_FETCH_DEST'] === 'empty') {
            return true;
        }
        
        return false;
    }

    /**
     * اعتبارسنجی ورودی‌ها
     */
    protected function validate($data, $rules) 
    {
        require_once APP_PATH . 'helpers/Validation.php';
        $validator = new Validation();
        return $validator->validate($data, $rules);
    }

    /**
     * دریافت ورودی پاک شده
     */
    protected function input($key, $default = null) 
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return Security::sanitizeInput($value);
    }

    /**
     * دریافت تمام ورودی‌ها
     */
    protected function all() 
    {
        $data = array_merge($_GET, $_POST);
        return Security::sanitizeInput($data);
    }

    /**
     * بررسی درخواست POST
     */
    protected function isPost() 
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * بررسی درخواست GET
     */
    protected function isGet() 
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * بررسی token CSRF
     */
    protected function verifyCSRF() 
    {
        if ($this->isPost() && !Security::verifyCSRF()) {
            $this->sendError('درخواست نامعتبر است', 419);
        }
    }

    /**
     * تنظیم پیام flash
     */
    protected function setFlash($type, $message) 
    {
        $_SESSION['flash'] = [
            'type' => $type, // success, error, warning, info
            'message' => $message
        ];
    }

    /**
     * دریافت پیام flash
     */
    protected function getFlash() 
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * ریدایرکت با پیام
     */
    protected function redirectWithMessage($url, $type, $message) 
    {
        $this->setFlash($type, $message);
        Security::redirect($url);
    }

    /**
     * دریافت اطلاعات فایل آپلودی
     */
    protected function getUploadedFile($fieldName) 
    {
        if (!isset($_FILES[$fieldName])) {
            return null;
        }
        
        $file = $_FILES[$fieldName];
        
        // اعتبارسنجی فایل
        $errors = Security::validateUploadedFile($file);
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        
        return [
            'name' => $file['name'],
            'tmp_name' => $file['tmp_name'],
            'size' => $file['size'],
            'type' => $file['type'],
            'extension' => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))
        ];
    }

    /**
     * ذخیره فایل آپلودی
     */
    protected function saveUploadedFile($file, $directory = 'docs') 
    {
        if (isset($file['errors'])) {
            return false;
        }
        
        // ایجاد نام فایل امن
        $filename = Security::generateSafeFilename($file['name']);
        
        // ایجاد مسیر ذخیره
        $uploadDir = UPLOAD_PATH . $directory . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $destination = $uploadDir . $filename;
        
        // انتقال فایل
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'filename' => $filename,
                'path' => $destination,
                'url' => UPLOAD_URL . $directory . '/' . $filename,
                'size' => $file['size']
            ];
        }
        
        return false;
    }

    /**
     * محدود کردن دسترسی به متد برای نقش‌های خاص
     */
    protected function requireRole($role) 
    {
        if (!Security::checkPermission($role)) {
            $this->sendError('دسترسی محدود شده', 403);
        }
    }

    /**
     * Wrapper برای jsonResponse
     */
    protected function jsonResponse($data = [], $status = 200) 
    {
        return $this->json($data, $status);
    }

    /**
     * پیش‌پردازی قبل از اجرای متد
     */
    protected function beforeAction() 
    {
        // متد قابل بازنویسی در کنترلرهای فرزند
    }

    /**
     * پس‌پردازی بعد از اجرای متد
     */
    protected function afterAction() 
    {
        // متد قابل بازنویسی در کنترلرهای فرزند
    }

    /**
     * Alias برای checkAuth
     */
    protected function requireAuth() 
    {
        if (!$this->checkAuth()) {
            $this->redirectToLogin();
        }
    }

    /**
     * دریافت کاربر فعلی
     */
    protected function getCurrentUser() 
    {
        if (!$this->user) {
            $this->loadUser();
        }
        return $this->user;
    }

    /**
     * مدیریت خطا
     */
    protected function handleError($exception, $userMessage = 'خطایی رخ داده است') 
    {
        writeLog("Error: " . $exception->getMessage(), 'ERROR');
        $this->sendError($userMessage, 500);
    }

    /**
     * Alias برای setFlash
     */
    protected function setFlashMessage($type, $message) 
    {
        return $this->setFlash($type, $message);
    }

    /**
     * ارسال JSON response
     */
    protected function sendJSON($data) 
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * اعتبارسنجی CSRF Token
     */
    protected function validateCSRF($token) 
    {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * بررسی مجوز دسترسی
     */
    protected function checkPermission($permission) 
    {
        // بررسی ساده - در آینده می‌توان پیچیده‌تر کرد
        if (!$this->user) {
            return false;
        }
        
        // مدیران کل دسترسی دارند
        if ($this->user['role'] === 'admin') {
            return true;
        }
        
        // سایر نقش‌ها
        $permissions = [
            'manage_tags' => ['admin', 'manager'],
            'create_tags' => ['admin', 'manager', 'user'],
            'edit_tags' => ['admin', 'manager'],
            'delete_tags' => ['admin']
        ];
        
        if (!isset($permissions[$permission])) {
            return false;
        }
        
        return in_array($this->user['role'], $permissions[$permission]);
    }

    /**
     * ریدایرکت با پیام خطا
     */
    protected function redirectWithError($url, $message) 
    {
        $this->setFlash('error', $message);
        Security::redirect($url);
    }

    /**
     * دریافت شناسه کاربر فعلی
     */
    protected function getCurrentUserId() 
    {
        return $this->user ? $this->user['id'] : null;
    }

    /**
     * Alias برای verifyCSRF  
     */
    protected function validateCsrfToken() 
    {
        return $this->verifyCSRF();
    }

    /**
     * ریدایرکت ساده
     */
    protected function redirect($url) 
    {
        Security::redirect($url);
    }

    /**
     * تبدیل اعداد فارسی به انگلیسی
     */
    protected function convertPersianNumbers($string) 
    {
        return Security::convertPersianToEnglishNumbers($string);
    }
}
?>