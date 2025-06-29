<?php
/**
 * نام فایل: Utilities.php
 * مسیر فایل: /app/helpers/Utilities.php
 * توضیح: توابع کمکی عمومی سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تابع fa_num() از PersianDate.php استفاده می‌شود

// تابع en_num() از PersianDate.php استفاده می‌شود

/**
 * فرمت کردن مبلغ به صورت فارسی
 * @param int|float $amount
 * @param bool $show_rial
 * @return string
 */
function format_currency($amount, $show_rial = true) {
    $formatted = number_format((float)$amount);
    $formatted = fa_num($formatted);
    
    return $show_rial ? $formatted . ' ریال' : $formatted;
}

/**
 * دریافت رنگ وضعیت درخواست
 * @param string $status
 * @return string
 */
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'approved' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger',
        'rejected' => 'danger',
        'expired' => 'secondary'
    ];
    
    return $colors[$status] ?? 'secondary';
}

/**
 * دریافت رنگ اولویت
 * @param string $priority
 * @return string
 */
function getPriorityColor($priority) {
    $colors = [
        'low' => 'success',
        'normal' => 'secondary',
        'high' => 'warning',
        'urgent' => 'danger'
    ];
    
    return $colors[$priority] ?? 'secondary';
}

/**
 * دریافت متن وضعیت درخواست
 * @param string $status
 * @return string
 */
function getStatusLabel($status) {
    $labels = [
        'pending' => 'در انتظار',
        'processing' => 'در حال بررسی',
        'approved' => 'تایید شده',
        'completed' => 'تکمیل شده',
        'cancelled' => 'لغو شده',
        'rejected' => 'رد شده',
        'expired' => 'منقضی شده'
    ];
    
    return $labels[$status] ?? 'نامشخص';
}

/**
 * دریافت متن اولویت
 * @param string $priority
 * @return string
 */
function getPriorityLabel($priority) {
    $labels = [
        'low' => 'کم',
        'normal' => 'عادی',
        'high' => 'بالا',
        'urgent' => 'فوری'
    ];
    
    return $labels[$priority] ?? 'عادی';
}

/**
 * دریافت ایکون وضعیت درخواست
 * @param string $status
 * @return string
 */
function getStatusIcon($status) {
    $icons = [
        'pending' => 'fas fa-clock',
        'processing' => 'fas fa-sync-alt',
        'approved' => 'fas fa-check',
        'completed' => 'fas fa-check-circle',
        'cancelled' => 'fas fa-ban',
        'rejected' => 'fas fa-times-circle',
        'expired' => 'fas fa-hourglass-end'
    ];
    
    return $icons[$status] ?? 'fas fa-circle';
}

/**
 * دریافت ایکون اولویت
 * @param string $priority
 * @return string
 */
function getPriorityIcon($priority) {
    $icons = [
        'low' => 'fas fa-arrow-down',
        'normal' => 'fas fa-minus',
        'high' => 'fas fa-arrow-up',
        'urgent' => 'fas fa-exclamation-triangle'
    ];
    
    return $icons[$priority] ?? 'fas fa-minus';
}

/**
 * کوتاه کردن متن
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate_text($text, $length = 50, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

/**
 * فرمت کردن اندازه فایل
 * @param int $bytes
 * @return string
 */
function format_file_size($bytes) {
    if ($bytes == 0) return '۰ بایت';
    
    $units = ['بایت', 'کیلوبایت', 'مگابایت', 'گیگابایت', 'ترابایت'];
    $power = floor(log($bytes) / log(1024));
    $size = round($bytes / pow(1024, $power), 2);
    
    return fa_num($size) . ' ' . $units[$power];
}

/**
 * بررسی فرمت ایمیل
 * @param string $email
 * @return bool
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * بررسی شماره موبایل ایرانی
 * @param string $mobile
 * @return bool
 */
function is_valid_mobile($mobile) {
    $mobile = en_num(trim($mobile));
    $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
    // حذف +98 یا 0098 از ابتدا
    if (substr($mobile, 0, 3) == '098') {
        $mobile = substr($mobile, 3);
    } elseif (substr($mobile, 0, 2) == '98') {
        $mobile = substr($mobile, 2);
    } elseif (substr($mobile, 0, 1) == '0') {
        $mobile = substr($mobile, 1);
    }
    
    // بررسی فرمت شماره موبایل
    return preg_match('/^9[0-9]{9}$/', $mobile);
}

/**
 * فرمت کردن شماره موبایل
 * @param string $mobile
 * @return string
 */
function format_mobile($mobile) {
    $mobile = en_num(trim($mobile));
    $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
    // حذف +98 یا 0098 از ابتدا
    if (substr($mobile, 0, 3) == '098') {
        $mobile = substr($mobile, 3);
    } elseif (substr($mobile, 0, 2) == '98') {
        $mobile = substr($mobile, 2);
    } elseif (substr($mobile, 0, 1) == '0') {
        $mobile = substr($mobile, 1);
    }
    
    if (strlen($mobile) == 10 && substr($mobile, 0, 1) == '9') {
        return fa_num('0' . $mobile);
    }
    
    return fa_num($mobile);
}

/**
 * تولید کد تصادفی
 * @param int $length
 * @param bool $numbers_only
 * @return string
 */
function generate_code($length = 6, $numbers_only = true) {
    if ($numbers_only) {
        $characters = '0123456789';
    } else {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    }
    
    $code = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, $max)];
    }
    
    return $code;
}

/**
 * ایجاد شماره مرجع منحصر به فرد
 * @param string $prefix
 * @return string
 */
function generate_reference_number($prefix = 'REQ') {
    $timestamp = time();
    $random = generate_code(4);
    
    return $prefix . $timestamp . $random;
}

/**
 * محاسبه درصد
 * @param float $part
 * @param float $total
 * @param int $decimals
 * @return float
 */
function calculate_percentage($part, $total, $decimals = 2) {
    if ($total == 0) return 0;
    
    return round(($part / $total) * 100, $decimals);
}

// توابع تاریخی از PersianDate.php استفاده می‌شوند

// توابع asset() و url() از app.php استفاده می‌شوند

/**
 * دریافت IP کاربر
 * @return string
 */
function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

/**
 * دریافت User Agent کاربر
 * @return string
 */
function get_user_agent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

/**
 * بررسی درخواست AJAX
 * @return bool
 */
function is_ajax_request() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * ارسال پاسخ JSON
 * @param array $data
 * @param int $status_code
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * پاکسازی HTML
 * @param string $html
 * @return string
 */
function clean_html($html) {
    return htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8');
}

/**
 * فرمت کردن مبلغ با کوتاه‌سازی (K, M, B)
 * @param int|float $amount
 * @return string
 */
function formatAmount($amount) {
    if ($amount >= 1000000000) {
        return number_format($amount / 1000000000, 1) . 'B';
    } elseif ($amount >= 1000000) {
        return number_format($amount / 1000000, 1) . 'M';
    } elseif ($amount >= 1000) {
        return number_format($amount / 1000, 1) . 'K';
    } else {
        return number_format($amount);
    }
}

/**
 * دریافت کلاس رنگ badge براساس وضعیت
 * @param string $status
 * @return string
 */
function getBadgeClass($status) {
    switch ($status) {
        case 'completed':
            return 'text-success';
        case 'pending':
            return 'text-warning';
        case 'processing':
            return 'text-info';
        case 'approved':
            return 'text-primary';
        case 'rejected':
        case 'cancelled':
            return 'text-danger';
        default:
            return 'text-muted';
    }
}

/**
 * دریافت رنگ نقش کاربر
 * @param string $role
 * @return string
 */
function getRoleColor($role) {
    switch ($role) {
        case 'admin':
            return 'danger';
        case 'manager':
            return 'warning';
        case 'user':
            return 'primary';
        case 'guest':
            return 'secondary';
        default:
            return 'info';
    }
}

/**
 * دریافت ایکون نقش کاربر
 * @param string $role
 * @return string
 */
function getRoleIcon($role) {
    switch ($role) {
        case 'admin':
            return 'fas fa-crown';
        case 'manager':
            return 'fas fa-user-tie';
        case 'user':
            return 'fas fa-user';
        case 'guest':
            return 'fas fa-user-clock';
        default:
            return 'fas fa-user-circle';
    }
}

/**
 * debug output
 * @param mixed $data
 * @param bool $die
 */
function dd($data, $die = true) {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: monospace; font-size: 12px; line-height: 1.4; color: #333; text-align: left; direction: ltr;">';
    if (is_array($data) || is_object($data)) {
        print_r($data);
    } else {
        var_dump($data);
    }
    echo '</pre>';
    
    if ($die) {
        exit;
    }
}

/**
 * log errors
 * @param string $message
 * @param string $level
 */
function log_error($message, $level = 'ERROR') {
    $log_file = __DIR__ . '/../../storage/logs/error.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

/**
 * دریافت کلاس badge براساس تعداد استفاده
 * @param int $usageCount
 * @return string
 */
function getUsageBadgeClass($usageCount) {
    if ($usageCount >= 10) {
        return 'success';
    } elseif ($usageCount >= 5) {
        return 'primary';
    } elseif ($usageCount >= 1) {
        return 'warning';
    } else {
        return 'secondary';
    }
}
