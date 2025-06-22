<?php
/**
 * نام فایل: database.php
 * مسیر فایل: /app/config/database.php
 * توضیح: تنظیمات اتصال به دیتابیس
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تنظیمات دیتابیس
define('DB_HOST', 'localhost');
define('DB_NAME', 'samanat_db');
define('DB_USER', 'root');
define('DB_PASS', '123');
define('DB_CHARSET', 'utf8mb4');

// تنظیمات PDO
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_persian_ci"
]);

/**
 * کلاس مدیریت دیتابیس
 */
class DatabaseConfig 
{
    private static $instance = null;
    private $connection;

    private function __construct() 
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
        } catch (PDOException $e) {
            die("خطا در اتصال به دیتابیس: " . $e->getMessage());
        }
    }

    /**
     * دریافت نمونه Singleton از کلاس
     */
    public static function getInstance() 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * دریافت اتصال دیتابیس
     */
    public function getConnection() 
    {
        return $this->connection;
    }

    /**
     * جلوگیری از کلون کردن
     */
    private function __clone() {}

    /**
     * جلوگیری از unserialize
     */
    public function __wakeup() {}
}

/**
 * تابع سراسری برای دریافت اتصال دیتابیس
 */
function getDB() 
{
    return DatabaseConfig::getInstance()->getConnection();
}
?>