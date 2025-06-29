<?php
/**
 * Router برای سرور محلی PHP
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// اگر فایل واقعی موجود است، آن را برگردان
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// تنظیم route برای index.php
$route = ltrim($uri, '/');
if (!empty($route)) {
    $_GET['route'] = $route;
    $_SERVER['QUERY_STRING'] = "route={$route}";
}

// همه درخواست‌ها را به index.php ارسال کن
require_once __DIR__ . '/index.php'; 