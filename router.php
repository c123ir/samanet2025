<?php
/**
 * Router برای سرور محلی PHP
 */

// اگر فایل موجود باشد، آن را برگردان
$file = __DIR__ . $_SERVER['REQUEST_URI'];
if (is_file($file)) {
    return false;
}

// همه درخواست‌ها را به index.php ارسال کن
require_once __DIR__ . '/index.php';
?> 