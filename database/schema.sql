-- نام فایل: schema.sql  
-- مسیر فایل: /database/schema.sql
-- توضیح: ساختار دیتابیس پروژه سامانت
-- تاریخ ایجاد: 1404/03/31
-- نویسنده: توسعه‌دهنده سامانت

USE samanat_db;

-- =========================================
-- جدول گروه‌های کاری
-- =========================================
CREATE TABLE IF NOT EXISTS `user_groups` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT 'نام گروه',
    `description` TEXT COMMENT 'توضیحات گروه',
    `permissions` JSON COMMENT 'مجوزهای گروه',
    `parent_id` INT(11) UNSIGNED NULL COMMENT 'گروه والد',
    `created_by` INT(11) UNSIGNED NOT NULL COMMENT 'ایجادکننده',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_parent_id` (`parent_id`),
    INDEX `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='گروه‌های کاری';

-- =========================================
-- جدول کاربران سیستم
-- =========================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL COMMENT 'نام کاربری',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'رمز عبور هش شده',
    `full_name` VARCHAR(100) NOT NULL COMMENT 'نام و نام خانوادگی',
    `email` VARCHAR(150) UNIQUE NULL COMMENT 'ایمیل',
    `phone` VARCHAR(15) UNIQUE NULL COMMENT 'شماره تلفن',
    `role` ENUM('admin', 'manager', 'employee', 'viewer') DEFAULT 'employee' COMMENT 'نقش کاربر',
    `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'شناسه گروه',
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active' COMMENT 'وضعیت کاربر',
    `avatar` VARCHAR(255) NULL COMMENT 'آواتار کاربر',
    `last_login` TIMESTAMP NULL COMMENT 'آخرین ورود',
    `login_attempts` TINYINT(3) DEFAULT 0 COMMENT 'تعداد تلاش‌های ورود',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`group_id`) REFERENCES `user_groups`(`id`) ON DELETE RESTRICT,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`),
    INDEX `idx_phone` (`phone`),
    INDEX `idx_group_id` (`group_id`),
    INDEX `idx_role` (`role`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='کاربران سیستم';

-- =========================================
-- جدول درخواست‌های حواله
-- =========================================
CREATE TABLE IF NOT EXISTS `payment_requests` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference_number` VARCHAR(20) UNIQUE NOT NULL COMMENT 'شماره مرجع',
    `requester_id` INT(11) UNSIGNED NOT NULL COMMENT 'درخواست‌کننده',
    `group_id` INT(11) UNSIGNED NOT NULL COMMENT 'گروه مربوطه',
    `title` VARCHAR(200) NOT NULL COMMENT 'عنوان درخواست',
    `description` TEXT COMMENT 'توضیحات درخواست',
    `amount` DECIMAL(15,2) NOT NULL COMMENT 'مبلغ درخواست',
    `account_number` VARCHAR(30) NOT NULL COMMENT 'شماره حساب',
    `account_holder` VARCHAR(100) NOT NULL COMMENT 'نام صاحب حساب',
    `bank_name` VARCHAR(50) NOT NULL COMMENT 'نام بانک',
    `iban` VARCHAR(26) NULL COMMENT 'شماره شبا',
    `card_number` VARCHAR(19) NULL COMMENT 'شماره کارت',
    `priority` ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal' COMMENT 'اولویت',
    `status` ENUM('pending', 'processing', 'approved', 'completed', 'rejected', 'cancelled') DEFAULT 'pending' COMMENT 'وضعیت',
    `category` VARCHAR(50) NULL COMMENT 'دسته‌بندی',
    `tags` TEXT NULL COMMENT 'تگ‌ها',
    `assigned_to` INT(11) UNSIGNED NULL COMMENT 'محول شده به',
    `approved_by` INT(11) UNSIGNED NULL COMMENT 'تایید شده توسط',
    `approved_at` TIMESTAMP NULL COMMENT 'تاریخ تایید',
    `completed_by` INT(11) UNSIGNED NULL COMMENT 'تکمیل شده توسط',
    `completed_at` TIMESTAMP NULL COMMENT 'تاریخ تکمیل',
    `rejected_by` INT(11) UNSIGNED NULL COMMENT 'رد شده توسط',
    `rejected_at` TIMESTAMP NULL COMMENT 'تاریخ رد',
    `rejection_reason` TEXT NULL COMMENT 'دلیل رد',
    `due_date` DATE NULL COMMENT 'تاریخ سررسید',
    `is_urgent` BOOLEAN DEFAULT FALSE COMMENT 'فوری بودن',
    `payment_date` DATE NULL COMMENT 'تاریخ پرداخت',
    `payment_reference` VARCHAR(100) NULL COMMENT 'مرجع پرداخت',
    `notes` TEXT NULL COMMENT 'یادداشت‌ها',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`requester_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`group_id`) REFERENCES `user_groups`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`completed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`rejected_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_reference_number` (`reference_number`),
    INDEX `idx_requester_id` (`requester_id`),
    INDEX `idx_group_id` (`group_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_due_date` (`due_date`),
    INDEX `idx_amount` (`amount`),
    FULLTEXT `idx_search` (`title`, `description`, `account_holder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='درخواست‌های حواله';

-- =========================================
-- Triggers برای شماره مرجع خودکار
-- =========================================
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS `payment_requests_reference_trigger` 
BEFORE INSERT ON `payment_requests` 
FOR EACH ROW
BEGIN
    IF NEW.reference_number IS NULL OR NEW.reference_number = '' THEN
        SET NEW.reference_number = CONCAT('REQ', YEAR(NOW()), LPAD(MONTH(NOW()), 2, '0'), LPAD(DAY(NOW()), 2, '0'), LPAD(CONNECTION_ID(), 4, '0'));
    END IF;
END$$

DELIMITER ;

-- =========================================
-- داده‌های پیش‌فرض
-- =========================================

-- گروه پیش‌فرض
INSERT IGNORE INTO `user_groups` (`id`, `name`, `description`, `permissions`, `created_by`) VALUES 
(1, 'مدیران سیستم', 'گروه مدیران اصلی سیستم', JSON_ARRAY('admin', 'manage_users', 'manage_groups', 'manage_requests', 'view_reports'), 1);

-- کاربر ادمین پیش‌فرض
-- رمز عبور: admin123 (هش شده با PASSWORD_DEFAULT)
INSERT IGNORE INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `phone`, `role`, `group_id`, `status`) VALUES 
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدیر سیستم', 'admin@samanat.local', '09123456789', 'admin', 1, 'active');

-- اعلان تکمیل
SELECT 'Database schema created successfully!' as message;
