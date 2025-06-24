<?php
/**
 * نام فایل: index.php
 * مسیر فایل: /index.php
 * توضیح: فایل اصلی ورودی سامانت
 * تاریخ ایجاد: 1404/03/31
 */

// تنظیمات پایه
error_reporting(E_ALL);
ini_set('display_errors', 1);

// بارگذاری تنظیمات
require_once 'app/config/app.php';
require_once 'app/config/database.php';
require_once 'app/config/security.php';

// شروع session بعد از تنظیمات (اگر شروع نشده باشد)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// بارگذاری کلاس‌های اصلی
require_once 'app/controllers/BaseController.php';
require_once 'app/helpers/Security.php';
require_once 'app/helpers/PersianDate.php';
require_once 'app/helpers/Utilities.php';
require_once 'app/helpers/DateHelper.php';

// بارگذاری مدل‌ها
require_once 'app/models/Database.php';
require_once 'app/models/User.php';
require_once 'app/models/Group.php';
require_once 'app/models/PaymentRequest.php';
require_once 'app/models/Document.php';
require_once 'app/models/Tag.php';

// دریافت route از URL
$route = $_GET['route'] ?? '';
$route = Security::sanitizeInput($route);

// بررسی احراز هویت برای root route
if (empty($route)) {
    // اگر کاربر وارد شده باشد، به dashboard برو
    if (Security::isLoggedIn()) {
        $route = 'dashboard';
    } else {
        // اگر وارد نشده، به صفحه login برو
        $route = 'login';
    }
}

// مسیریابی ساده
$routes = [
    'login' => 'AuthController@login',
    'auth/login' => 'AuthController@login',
    'logout' => 'AuthController@logout',
    'auth/logout' => 'AuthController@logout',
    'dashboard' => 'DashboardController@index',
    // requests را حذف کردیم تا از routing پیچیده استفاده شود
    'users' => 'UserController@index',
    'users/create' => 'UserController@create',
];

// اجرای کنترلر مناسب
if (isset($routes[$route])) {
    $handler = explode('@', $routes[$route]);
    $controller = $handler[0];
    $method = $handler[1];
    
    require_once "app/controllers/{$controller}.php";
    $controllerInstance = new $controller();
    $controllerInstance->$method();
} else {
    // بررسی route های پیچیده با پارامتر
    $routeParts = explode('/', $route);
    $handled = false;
    
    // مسیریابی users با پارامتر
    if (count($routeParts) >= 3 && $routeParts[0] === 'users') {
        $action = $routeParts[1];
        $id = intval($routeParts[2]);
        
        require_once "app/controllers/UserController.php";
        $controller = new UserController();
        
        switch ($action) {
            case 'show':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $controller->show($id);
                    $handled = true;
                }
                break;
            case 'toggleStatus':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->toggleStatus($id);
                    $handled = true;
                }
                break;
        }
    }
    
    // مسیریابی requests با پارامتر
    if ($routeParts[0] === 'requests') {
        $action = $_GET['action'] ?? 'index'; // اگر action نباشد، به index برو
        
        require_once "app/controllers/RequestController.php";
        $controller = new RequestController();
        
        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $controller->create();
                    $handled = true;
                }
                break;
            case 'store':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->store();
                    $handled = true;
                }
                break;
            case 'show':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $controller->show();
                    $handled = true;
                }
                break;
            case 'approve':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->approve();
                    $handled = true;
                }
                break;
            case 'reject':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->reject();
                    $handled = true;
                }
                break;
            case 'complete':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->complete();
                    $handled = true;
                }
                break;
            case 'index':
            default:
                $controller->index();
                $handled = true;
        }
    }
    
    // مسیریابی tags با پارامتر
    if ($routeParts[0] === 'tags') {
        $action = $_GET['action'] ?? 'index';
        
        require_once "app/controllers/TagController.php";
        $controller = new TagController();
        
        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $controller->create();
                    $handled = true;
                }
                break;
            case 'store':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->store();
                    $handled = true;
                }
                break;
            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $controller->edit();
                    $handled = true;
                }
                break;
            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                    $handled = true;
                }
                break;
            case 'delete':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->delete();
                    $handled = true;
                }
                break;
            case 'api':
                $controller->api();
                $handled = true;
                break;
            case 'preview':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->preview();
                    $handled = true;
                }
                break;
            case 'randomGradient':
                $controller->randomGradient();
                $handled = true;
                break;
            case 'index':
            default:
                $controller->index();
                $handled = true;
        }
    }
    
    // اگر route پیدا نشد، صفحه 404
    if (!$handled) {
        http_response_code(404);
        echo "صفحه یافت نشد";
    }
}
?>
