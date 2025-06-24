# 🗄️ راهنمای Import دیتابیس سامانت ورژن 3.0

**نسخه:** 3.0.0  
**تاریخ:** 1404/10/15  
**وضعیت:** ✅ تکمیل شده - شامل سیستم تگ‌ها  
**پیشرفت:** 60% (3 از 5 فاز اصلی)

---

## 🎯 **مقدمه**

این راهنما برای import کردن دیتابیس کامل سامانت ورژن 3.0 است که شامل:
- ✅ جداول هسته سیستم
- ✅ جدول درخواست‌های حواله
- ✅ جدول سیستم تگ‌ها (جدید)
- ✅ داده‌های نمونه کامل

---

## 📋 **مراحل Import**

### **1. آماده‌سازی دیتابیس**

```sql
-- ایجاد دیتابیس جدید
CREATE DATABASE IF NOT EXISTS `samanat_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- انتخاب دیتابیس
USE `samanat_db`;
```

### **2. ایجاد جداول اصلی**

#### **جدول کاربران:**
```sql
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `full_name` varchar(100) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `phone` varchar(15) DEFAULT NULL,
    `role` enum('admin','manager','user') DEFAULT 'user',
    `group_id` int(11) DEFAULT NULL,
    `status` enum('active','inactive') DEFAULT 'active',
    `last_login` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **جدول گروه‌های کاری:**
```sql
CREATE TABLE IF NOT EXISTS `user_groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `permissions` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **جدول درخواست‌های حواله:**
```sql
CREATE TABLE IF NOT EXISTS `payment_requests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `requester_id` int(11) NOT NULL,
    `title` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `amount` decimal(15,2) DEFAULT NULL,
    `account_number` varchar(50) DEFAULT NULL,
    `account_holder` varchar(100) DEFAULT NULL,
    `bank_name` varchar(100) DEFAULT NULL,
    `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
    `status` enum('pending','processing','completed','rejected') DEFAULT 'pending',
    `group_id` int(11) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_requester` (`requester_id`),
    KEY `idx_status` (`status`),
    KEY `idx_priority` (`priority`),
    KEY `idx_group` (`group_id`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **✨ جدول سیستم تگ‌ها (جدید در ورژن 3.0):**
```sql
CREATE TABLE IF NOT EXISTS `tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(100) NOT NULL,
    `color_start` varchar(7) NOT NULL DEFAULT '#667eea',
    `color_end` varchar(7) NOT NULL DEFAULT '#764ba2',
    `text_color` varchar(7) NOT NULL DEFAULT '#ffffff',
    `usage_count` int(11) DEFAULT 0,
    `created_by` int(11) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_title` (`title`),
    KEY `idx_usage` (`usage_count`),
    KEY `idx_creator` (`created_by`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **3. ایجاد داده‌های نمونه**

#### **کاربران پیش‌فرض:**
```sql
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `phone`, `role`, `group_id`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدیر سیستم', 'admin@samanat.local', '09123456789', 'admin', 1, 'active'),
('manager1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'احمد محمدی', 'ahmad@samanat.local', '09123456788', 'manager', 1, 'active'),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'علی احمدی', 'ali@samanat.local', '09123456787', 'user', 2, 'active'),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'فاطمه رضایی', 'fateme@samanat.local', '09123456786', 'user', 2, 'active');
```

#### **گروه‌های کاری:**
```sql
INSERT INTO `user_groups` (`name`, `description`, `permissions`) VALUES
('مدیریت', 'گروه مدیریت اصلی سیستم', 'all'),
('حسابداری', 'گروه واحد حسابداری', 'requests,reports'),
('عملیات', 'گروه عملیات و پردازش', 'requests'),
('بازرسی', 'گروه بازرسی و کنترل', 'view,approve');
```

#### **✨ تگ‌های نمونه (جدید در ورژن 3.0):**
```sql
INSERT INTO `tags` (`title`, `color_start`, `color_end`, `text_color`, `usage_count`, `created_by`) VALUES
('فوری', '#ff6b6b', '#ee5a24', '#ffffff', 8, 1),
('مهم', '#ffa502', '#ff6348', '#ffffff', 5, 1),
('تایید شده', '#2ed573', '#1e90ff', '#ffffff', 12, 1),
('در انتظار', '#ffa502', '#ff7675', '#ffffff', 3, 1),
('تکمیل شده', '#00d2d3', '#54a0ff', '#ffffff', 15, 1),
('لغو شده', '#747d8c', '#57606f', '#ffffff', 2, 1),
('HIGH', '#667eea', '#764ba2', '#ffffff', 7, 1),
('*', '#f093fb', '#f5576c', '#ffffff', 4, 1),
('!', '#4facfe', '#00f2fe', '#ffffff', 6, 1),
('بررسی', '#a8edea', '#fed6e3', '#333333', 3, 1),
('A', '#fd79a8', '#fdcb6e', '#ffffff', 2, 1),
('کم‌اولویت', '#ddd', '#bbb', '#333333', 1, 1),
('VIP', '#6c5ce7', '#a29bfe', '#ffffff', 9, 1),
('عادی', '#74b9ff', '#0984e3', '#ffffff', 4, 1),
('ویژه', '#e17055', '#d63031', '#ffffff', 3, 1);
```

#### **درخواست‌های نمونه:**
```sql
INSERT INTO `payment_requests` (`requester_id`, `title`, `description`, `amount`, `account_number`, `account_holder`, `bank_name`, `priority`, `status`, `group_id`) VALUES
(1, 'پرداخت حقوق کارکنان', 'پرداخت حقوق ماه جاری کارکنان واحد فروش', 25000000.00, '1234567890', 'شرکت نمونه', 'ملی ایران', 'high', 'pending', 1),
(2, 'خرید تجهیزات اداری', 'خرید میز و صندلی برای دفتر جدید', 5500000.00, '0987654321', 'فروشگاه مبل سازان', 'پاسارگاد', 'normal', 'processing', 2),
(3, 'پرداخت اجاره', 'اجاره ماهانه دفتر مرکزی', 8000000.00, '1122334455', 'علی محمدی', 'ملت', 'high', 'completed', 1),
(4, 'خرید لوازم‌التحریر', 'خرید کاغذ و لوازم‌التحریر برای سه ماه', 1200000.00, '5566778899', 'فروشگاه پیروز', 'صادرات', 'low', 'pending', 2),
(1, 'پرداخت قبض برق', 'قبض برق دفتر مرکزی', 850000.00, '9988776655', 'شرکت توزیع برق', 'ملی ایران', 'normal', 'completed', 1),
(3, 'بیمه کارکنان', 'پرداخت حق بیمه کارکنان', 3200000.00, '4433221100', 'سازمان تامین اجتماعی', 'ملی ایران', 'high', 'processing', 1),
(2, 'خرید نرم‌افزار', 'خرید لایسنس نرم‌افزار حسابداری', 12000000.00, '7788990011', 'شرکت فناوری', 'پاسارگاد', 'normal', 'pending', 2),
(4, 'تعمیر ماشین', 'تعمیر خودروی شرکت', 2100000.00, '6655443322', 'تعمیرگاه مهر', 'ملت', 'low', 'completed', 2);
```

---

## 🔧 **تنظیمات اضافی**

### **Foreign Key Constraints:**
```sql
-- اضافه کردن Foreign Keys
ALTER TABLE `payment_requests` 
ADD CONSTRAINT `fk_payment_requester` 
FOREIGN KEY (`requester_id`) REFERENCES `users`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `payment_requests` 
ADD CONSTRAINT `fk_payment_group` 
FOREIGN KEY (`group_id`) REFERENCES `user_groups`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `tags` 
ADD CONSTRAINT `fk_tag_creator` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users` 
ADD CONSTRAINT `fk_user_group` 
FOREIGN KEY (`group_id`) REFERENCES `user_groups`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;
```

### **Indexes برای بهبود Performance:**
```sql
-- Indexes اضافی برای جستجوی بهتر
CREATE INDEX `idx_requests_compound` ON `payment_requests` (`group_id`, `status`, `created_at`);
CREATE INDEX `idx_tags_search` ON `tags` (`title`, `deleted_at`);
CREATE FULLTEXT INDEX `idx_requests_search` ON `payment_requests` (`title`, `description`);
CREATE FULLTEXT INDEX `idx_tags_fulltext` ON `tags` (`title`);
```

---

## 📊 **بررسی موفقیت Import**

### **تست اتصال و داده‌ها:**
```sql
-- بررسی تعداد رکوردها
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'user_groups', COUNT(*) FROM user_groups
UNION ALL
SELECT 'payment_requests', COUNT(*) FROM payment_requests
UNION ALL
SELECT 'tags', COUNT(*) FROM tags;

-- بررسی تگ‌های محبوب
SELECT title, usage_count, color_start, color_end 
FROM tags 
WHERE deleted_at IS NULL 
ORDER BY usage_count DESC 
LIMIT 5;

-- بررسی آخرین درخواست‌ها
SELECT pr.title, u.full_name as requester, pr.status, pr.created_at
FROM payment_requests pr
JOIN users u ON pr.requester_id = u.id
ORDER BY pr.created_at DESC
LIMIT 5;
```

### **نتایج انتظاری:**
- **کاربران:** 4 رکورد
- **گروه‌ها:** 4 رکورد  
- **درخواست‌ها:** 8 رکورد
- **تگ‌ها:** 15 رکورد (جدید در ورژن 3.0)

---

## 🔑 **اطلاعات ورود**

### **حساب‌های کاربری:**
```
Admin Account:
Username: admin
Password: admin123

Manager Account:
Username: manager1  
Password: admin123

User Accounts:
Username: user1 / user2
Password: admin123
```

### **تنظیمات دیتابیس:**
```php
// app/config/database.php
$config = [
    'host' => 'localhost',
    'database' => 'samanat_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci'
];
```

---

## 🚀 **ویژگی‌های جدید ورژن 3.0**

### **✨ سیستم تگ‌ها:**
- **15 تگ نمونه** با رنگ‌بندی متنوع
- **محاسبه luminance** برای رنگ متن بهینه
- **تگ‌های دایره‌ای** برای کاراکترهای تکی
- **جستجوی چند کلمه‌ای** در تگ‌ها
- **آمار استفاده** و محبوبیت تگ‌ها

### **🔍 جستجوی پیشرفته:**
- **Real-time search** با debouncing
- **Multi-word search** با منطق AND
- **Results highlighting** با پس‌زمینه زرد
- **ESC key support** برای پاک کردن جستجو

---

## 📝 **یادداشت‌های مهم**

### **⚠️ قبل از Import:**
1. **Backup** از دیتابیس قبلی بگیرید
2. **Character Set** را utf8mb4 تنظیم کنید
3. **Permissions** دیتابیس را بررسی کنید

### **✅ بعد از Import:**
1. **تست ورود** با حساب admin
2. **بررسی تگ‌ها** در صفحه مدیریت تگ‌ها
3. **تست جستجو** در لیست درخواست‌ها و تگ‌ها
4. **Theme toggle** را امتحان کنید

### **🔧 عیب‌یابی:**
- اگر تگ‌ها نمایش داده نمی‌شوند، جدول `tags` را بررسی کنید
- اگر جستجو کار نمی‌کند، JavaScript errors را چک کنید
- اگر Foreign Keys خطا دادند، ترتیب import جداول را بررسی کنید

---

## 📈 **آمار نهایی ورژن 3.0**

### **Database Schema:**
- **جداول اصلی:** 4 جدول
- **Indexes:** 15+ index
- **Foreign Keys:** 4 constraint
- **داده‌های نمونه:** 31 رکورد

### **New Features:**
- **Tag Management System** کامل
- **Advanced Search** چند کلمه‌ای
- **Color Science** برای تگ‌ها
- **Real-time UI** با animations

---

**📅 تاریخ:** 1404/10/15  
**🎯 ورژن:** 3.0.0 تکمیل شده  
**📊 وضعیت:** آماده Production و فاز 4  
**🔄 بعدی:** سیستم مدیریت اسناد و تصاویر 