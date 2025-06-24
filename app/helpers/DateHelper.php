<?php
/**
 * نام فایل: DateHelper.php
 * مسیر: /app/helpers/DateHelper.php
 * توضیح: کلاس کمکی برای تبدیل تاریخ میلادی به شمسی
 * تاریخ ایجاد: 1404/10/15
 */

class DateHelper
{
    /**
     * تبدیل تاریخ میلادی به شمسی
     * 
     * @param string $format فرمت خروجی
     * @param string|int $timestamp تایم استمپ یا تاریخ
     * @return string تاریخ شمسی
     */
    public static function jdate($format, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        
        return self::gregorianToJalali($format, $timestamp);
    }
    
    /**
     * تبدیل تاریخ گرگوری به جلالی
     */
    private static function gregorianToJalali($format, $timestamp)
    {
        $gdate = getdate($timestamp);
        $gy = $gdate['year'];
        $gm = $gdate['mon'];
        $gd = $gdate['mday'];
        $gh = $gdate['hours'];
        $gi = $gdate['minutes'];
        $gs = $gdate['seconds'];
        
        // تبدیل به جلالی
        list($jy, $jm, $jd) = self::gregorianToJalaliArray($gy, $gm, $gd);
        
        // ماه‌های فارسی
        $jmonths = [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
            4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
            7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
            10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
        ];
        
        // روزهای فارسی
        $jdays = [
            0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه‌شنبه',
            3 => 'چهارشنبه', 4 => 'پنج‌شنبه', 5 => 'جمعه', 6 => 'شنبه'
        ];
        
        $weekday = $gdate['wday'];
        
        // جایگزینی فرمت‌ها
        $format = str_replace('Y', $jy, $format);
        $format = str_replace('m', sprintf('%02d', $jm), $format);
        $format = str_replace('d', sprintf('%02d', $jd), $format);
        $format = str_replace('H', sprintf('%02d', $gh), $format);
        $format = str_replace('i', sprintf('%02d', $gi), $format);
        $format = str_replace('s', sprintf('%02d', $gs), $format);
        $format = str_replace('n', $jm, $format);
        $format = str_replace('j', $jd, $format);
        $format = str_replace('F', $jmonths[$jm], $format);
        $format = str_replace('l', $jdays[$weekday], $format);
        
        return $format;
    }
    
    /**
     * تبدیل تاریخ گرگوری به آرایه جلالی
     */
    private static function gregorianToJalaliArray($gy, $gm, $gd)
    {
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        
        if ($gy <= 1600) {
            $jy = 0;
            $gy -= 621;
        } else {
            $jy = 979;
            $gy -= 1600;
        }
        
        if ($gm > 2) {
            $gy2 = $gy + 1;
        } else {
            $gy2 = $gy;
        }
        
        $days = (365 * $gy) + ((int)($gy2 / 4)) - ((int)($gy2 / 100)) + ((int)($gy2 / 400)) - 80 + $gd + $g_d_m[$gm - 1];
        
        $jy += 33 * ((int)($days / 12053));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        
        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + (int)(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }
        
        return [$jy, $jm, $jd];
    }
}

/**
 * تابع سراسری برای استفاده آسان
 */
if (!function_exists('jdate')) {
    function jdate($format, $timestamp = null)
    {
        return DateHelper::jdate($format, $timestamp);
    }
} 