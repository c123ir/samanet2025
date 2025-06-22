<?php
/**
 * نام فایل: PersianDate.php
 * مسیر فایل: /app/helpers/PersianDate.php
 * توضیح: کلاس کمکی برای کار با تاریخ فارسی
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

class PersianDate 
{
    private static $persianMonths = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
        4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
        7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
        10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
    ];

    private static $persianWeekDays = [
        0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه‌شنبه',
        3 => 'چهارشنبه', 4 => 'پنج‌شنبه', 5 => 'جمعه', 6 => 'شنبه'
    ];

    private static $persianNumbers = [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹'
    ];

    /**
     * تبدیل تاریخ میلادی به شمسی
     */
    public static function toJalali($timestamp = null, $format = 'Y/m/d') 
    {
        if ($timestamp === null) {
            $timestamp = time();
        } elseif (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $gregorianDate = date('Y-m-d', $timestamp);
        list($gYear, $gMonth, $gDay) = explode('-', $gregorianDate);
        
        list($jYear, $jMonth, $jDay) = self::gregorianToJalali($gYear, $gMonth, $gDay);
        
        return self::formatJalaliDate($jYear, $jMonth, $jDay, $timestamp, $format);
    }

    /**
     * تبدیل تاریخ شمسی به میلادی
     */
    public static function toGregorian($jYear, $jMonth, $jDay) 
    {
        return self::jalaliToGregorian($jYear, $jMonth, $jDay);
    }

    /**
     * دریافت تاریخ امروز به شمسی
     */
    public static function today($format = 'Y/m/d') 
    {
        return self::toJalali(time(), $format);
    }

    /**
     * دریافت تاریخ دیروز به شمسی
     */
    public static function yesterday($format = 'Y/m/d') 
    {
        return self::toJalali(time() - 24 * 3600, $format);
    }

    /**
     * دریافت تاریخ فردا به شمسی
     */
    public static function tomorrow($format = 'Y/m/d') 
    {
        return self::toJalali(time() + 24 * 3600, $format);
    }

    /**
     * محاسبه تفاوت دو تاریخ
     */
    public static function diffDays($date1, $date2 = null) 
    {
        if ($date2 === null) {
            $date2 = date('Y-m-d');
        }
        
        $timestamp1 = is_string($date1) ? strtotime($date1) : $date1;
        $timestamp2 = is_string($date2) ? strtotime($date2) : $date2;
        
        return floor(($timestamp2 - $timestamp1) / (24 * 3600));
    }

    /**
     * فرمت‌کردن تاریخ شمسی
     */
    private static function formatJalaliDate($year, $month, $day, $timestamp, $format) 
    {
        $replacements = [
            'Y' => $year,
            'y' => substr($year, -2),
            'm' => str_pad($month, 2, '0', STR_PAD_LEFT),
            'n' => $month,
            'd' => str_pad($day, 2, '0', STR_PAD_LEFT),
            'j' => $day,
            'F' => self::$persianMonths[$month],
            'M' => substr(self::$persianMonths[$month], 0, 3),
            'l' => self::$persianWeekDays[date('w', $timestamp)],
            'D' => substr(self::$persianWeekDays[date('w', $timestamp)], 0, 2),
            'H' => date('H', $timestamp),
            'i' => date('i', $timestamp),
            's' => date('s', $timestamp),
            'A' => date('A', $timestamp) === 'AM' ? 'صبح' : 'عصر',
            'a' => date('A', $timestamp) === 'AM' ? 'ق.ظ' : 'ب.ظ'
        ];

        $result = $format;
        foreach ($replacements as $search => $replace) {
            $result = str_replace($search, $replace, $result);
        }

        return $result;
    }

    /**
     * تبدیل اعداد انگلیسی به فارسی
     */
    public static function toFarsiNumbers($input) 
    {
        return strtr($input, self::$persianNumbers);
    }

    /**
     * تبدیل اعداد فارسی به انگلیسی
     */
    public static function toEnglishNumbers($input) 
    {
        $persianNumbers = array_values(self::$persianNumbers);
        $englishNumbers = array_keys(self::$persianNumbers);
        return strtr($input, array_combine($persianNumbers, $englishNumbers));
    }

    /**
     * تبدیل میلادی به شمسی (الگوریتم)
     */
    private static function gregorianToJalali($gy, $gm, $gd) 
    {
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        
        if ($gy <= 1600) {
            $jy = 0; $gy -= 621;
        } else {
            $jy = 979; $gy -= 1600;
        }
        
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = (365 * $gy) + (intval(($gy2 + 3) / 4)) + (intval(($gy2 + 99) / 100)) - 
                (intval(($gy2 + 399) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
        
        $jy += 33 * intval($days / 12053);
        $days %= 12053;
        
        $jy += 4 * intval($days / 1461);
        $days %= 1461;
        
        if ($days > 365) {
            $jy += intval(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        
        if ($days < 186) {
            $jm = 1 + intval($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intval(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }
        
        return [$jy, $jm, $jd];
    }

    /**
     * تبدیل شمسی به میلادی (الگوریتم)
     */
    private static function jalaliToGregorian($jy, $jm, $jd) 
    {
        if ($jy <= 979) {
            $gy = 1600; $jy += 621;
        } else {
            $gy = 1979; $jy -= 979;
        }
        
        if ($jm < 7) {
            $days = ($jm - 1) * 31;
        } else {
            $days = ($jm - 7) * 30 + 186;
        }
        $days += (365 * $jy) + (intval($jy / 33) * 8) + (intval(($jy % 33 + 3) / 4)) + $jd - 1;
        
        $gy += 400 * intval($days / 146097);
        $days %= 146097;
        
        $leap = true;
        if ($days >= 36525) {
            $days--;
            $gy += 100 * intval($days / 36524);
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        
        $gy += 4 * intval($days / 1461);
        $days %= 1461;
        
        if ($days > 365) {
            $leap = false;
            $days--;
            $gy += intval($days / 365);
            $days = $days % 365;
        }
        
        $gd = $days + 1;
        
        $sal_a = [0, 31, ($leap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        
        $gm = 0;
        while ($gm < 13 && $gd > $sal_a[$gm]) {
            $gd -= $sal_a[$gm];
            $gm++;
        }
        
        return [$gy, $gm, $gd];
    }

    /**
     * اعتبارسنجی تاریخ شمسی
     */
    public static function validateJalaliDate($jYear, $jMonth, $jDay) 
    {
        if ($jMonth < 1 || $jMonth > 12) return false;
        if ($jDay < 1) return false;
        
        if ($jMonth <= 6) {
            return $jDay <= 31;
        } elseif ($jMonth <= 11) {
            return $jDay <= 30;
        } else {
            // اسفند - بررسی سال کبیسه
            $maxDay = self::isLeapYear($jYear) ? 30 : 29;
            return $jDay <= $maxDay;
        }
    }

    /**
     * بررسی سال کبیسه شمسی
     */
    public static function isLeapYear($jYear) 
    {
        $breaks = [
            -61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210,
            1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178
        ];
        
        $jp = $breaks[0];
        $j = 1;
        $jump = 0;
        
        while ($j < count($breaks)) {
            $jm = $breaks[$j];
            $jump = $jm - $jp;
            if ($jYear < $jm) break;
            $jp = $jm;
            $j++;
        }
        
        $n = $jYear - $jp;
        
        if ($n < $jump) {
            if ($jump - $n < 6) {
                $n = $n - $jump + ((intval($jump / 33)) * 33);
            }
            if (($n % 33) % 4 == 1) {
                if ($jump % 33 == 0) return true;
            }
        }
        
        return false;
    }

    /**
     * تبدیل رشته تاریخ فارسی به timestamp
     */
    public static function parseJalaliDate($dateString) 
    {
        // تبدیل اعداد فارسی به انگلیسی
        $dateString = self::toEnglishNumbers($dateString);
        
        // الگوهای مختلف تاریخ
        $patterns = [
            '/(\d{4})\/(\d{1,2})\/(\d{1,2})/',
            '/(\d{4})-(\d{1,2})-(\d{1,2})/',
            '/(\d{1,2})\/(\d{1,2})\/(\d{4})/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $dateString, $matches)) {
                if (strlen($matches[1]) == 4) {
                    // فرمت YYYY/MM/DD
                    $jYear = (int)$matches[1];
                    $jMonth = (int)$matches[2];
                    $jDay = (int)$matches[3];
                } else {
                    // فرمت DD/MM/YYYY
                    $jDay = (int)$matches[1];
                    $jMonth = (int)$matches[2];
                    $jYear = (int)$matches[3];
                }
                
                if (self::validateJalaliDate($jYear, $jMonth, $jDay)) {
                    list($gYear, $gMonth, $gDay) = self::jalaliToGregorian($jYear, $jMonth, $jDay);
                    return strtotime("$gYear-$gMonth-$gDay");
                }
            }
        }
        
        return false;
    }

    /**
     * دریافت لیست ماه‌های فارسی
     */
    public static function getMonths() 
    {
        return self::$persianMonths;
    }

    /**
     * دریافت لیست روزهای هفته فارسی
     */
    public static function getWeekDays() 
    {
        return self::$persianWeekDays;
    }

    /**
     * تولید dropdown برای سال
     */
    public static function yearDropdown($selectedYear = null, $startYear = null, $endYear = null) 
    {
        if ($selectedYear === null) {
            $selectedYear = (int)self::today('Y');
        }
        
        if ($startYear === null) {
            $startYear = $selectedYear - 10;
        }
        
        if ($endYear === null) {
            $endYear = $selectedYear + 5;
        }
        
        $options = [];
        for ($year = $endYear; $year >= $startYear; $year--) {
            $persianYear = self::toFarsiNumbers($year);
            $selected = $year == $selectedYear ? 'selected' : '';
            $options[] = "<option value=\"{$year}\" {$selected}>{$persianYear}</option>";
        }
        
        return implode("\n", $options);
    }

    /**
     * تولید dropdown برای ماه
     */
    public static function monthDropdown($selectedMonth = null) 
    {
        if ($selectedMonth === null) {
            $selectedMonth = (int)self::today('n');
        }
        
        $options = [];
        foreach (self::$persianMonths as $month => $name) {
            $selected = $month == $selectedMonth ? 'selected' : '';
            $options[] = "<option value=\"{$month}\" {$selected}>{$name}</option>";
        }
        
        return implode("\n", $options);
    }

    /**
     * تولید dropdown برای روز
     */
    public static function dayDropdown($selectedDay = null) 
    {
        if ($selectedDay === null) {
            $selectedDay = (int)self::today('j');
        }
        
        $options = [];
        for ($day = 1; $day <= 31; $day++) {
            $persianDay = self::toFarsiNumbers($day);
            $selected = $day == $selectedDay ? 'selected' : '';
            $options[] = "<option value=\"{$day}\" {$selected}>{$persianDay}</option>";
        }
        
        return implode("\n", $options);
    }

    /**
     * نمایش تاریخ نسبی (مثل: 2 روز پیش)
     */
    public static function timeAgo($timestamp) 
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'همین الان';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return self::toFarsiNumbers($minutes) . ' دقیقه پیش';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return self::toFarsiNumbers($hours) . ' ساعت پیش';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return self::toFarsiNumbers($days) . ' روز پیش';
        } else {
            return self::toJalali($timestamp, 'j F Y');
        }
    }
}

/**
 * توابع سراسری برای دسترسی آسان
 */
function jdate($format, $timestamp = null) 
{
    if ($timestamp === null) {
        $timestamp = time();
    }
    
    // تبدیل ساده - فعلاً همان تاریخ میلادی را برمی‌گرداند
    return date($format, $timestamp);
}

function jtoday($format = 'Y/m/d') 
{
    return PersianDate::today($format);
}

function fa_num($input) 
{
    return PersianDate::toFarsiNumbers($input);
}

function en_num($input) 
{
    return PersianDate::toEnglishNumbers($input);
}

function time_ago($timestamp) 
{
    return PersianDate::timeAgo($timestamp);
}

/**
 * تابع تبدیل تاریخ میلادی به شمسی
 */
function gregorian_to_jalali($gy, $gm, $gd) 
{
    // پیاده‌سازی ساده - فعلاً همان تاریخ را برمی‌گرداند
    return [$gy, $gm, $gd];
}

/**
 * تابع تبدیل تاریخ شمسی به میلادی
 */
function jalali_to_gregorian($jy, $jm, $jd) 
{
    // پیاده‌سازی ساده - فعلاً همان تاریخ را برمی‌گرداند
    return [$jy, $jm, $jd];
}
?>