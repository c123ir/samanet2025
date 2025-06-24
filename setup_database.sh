#!/bin/bash

# 🚀 اسکریپت راه‌اندازی دیتابیس سامانت
# نسخه: 2.0.0
# تاریخ: 1404/03/31

# رنگ‌ها برای خروجی زیبا
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# متغیرهای پیش‌فرض
DEFAULT_DB_NAME="samanat_db"
DEFAULT_DB_USER="root"
BACKUP_FILE="samanat_database_backup_20250622_230707.sql"

echo -e "${PURPLE}
╔══════════════════════════════════════════════════════════════╗
║                    🚀 سامانت - راه‌اندازی دیتابیس                     ║
║              سامانه مدیریت حواله و بایگانی اسناد               ║
║                         نسخه: 2.0.0                        ║
╚══════════════════════════════════════════════════════════════╝
${NC}"

# بررسی وجود فایل بک‌آپ
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ خطا: فایل بک‌آپ یافت نشد!${NC}"
    echo -e "${YELLOW}لطفاً فایل $BACKUP_FILE را در همین مسیر قرار دهید.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ فایل بک‌آپ پیدا شد: $BACKUP_FILE${NC}"
echo -e "${CYAN}📊 اندازه فایل: $(du -h $BACKUP_FILE | cut -f1)${NC}"
echo

# بررسی نصب MySQL
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ MySQL یافت نشد!${NC}"
    echo -e "${YELLOW}لطفاً ابتدا MySQL یا MariaDB نصب کنید.${NC}"
    echo
    echo -e "${CYAN}برای macOS:${NC}"
    echo "brew install mysql"
    echo
    echo -e "${CYAN}برای Ubuntu/Debian:${NC}"
    echo "sudo apt-get install mysql-server"
    echo
    echo -e "${CYAN}برای CentOS/RHEL:${NC}"
    echo "sudo yum install mysql-server"
    exit 1
fi

echo -e "${GREEN}✅ MySQL پیدا شد${NC}"

# دریافت اطلاعات دیتابیس
echo -e "${BLUE}📝 لطفاً اطلاعات دیتابیس را وارد کنید:${NC}"
echo

read -p "نام دیتابیس [$DEFAULT_DB_NAME]: " DB_NAME
DB_NAME=${DB_NAME:-$DEFAULT_DB_NAME}

read -p "نام کاربری MySQL [$DEFAULT_DB_USER]: " DB_USER
DB_USER=${DB_USER:-$DEFAULT_DB_USER}

echo -n "رمز عبور MySQL: "
read -s DB_PASS
echo

# تست اتصال به MySQL
echo -e "${YELLOW}🔗 در حال تست اتصال به MySQL...${NC}"
if ! mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" >/dev/null 2>&1; then
    echo -e "${RED}❌ خطا در اتصال به MySQL!${NC}"
    echo -e "${YELLOW}لطفاً نام کاربری و رمز عبور را بررسی کنید.${NC}"
    exit 1
fi

echo -e "${GREEN}✅ اتصال به MySQL برقرار شد${NC}"

# بررسی وجود دیتابیس
echo -e "${YELLOW}🔍 بررسی وجود دیتابیس $DB_NAME...${NC}"
if mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME;" >/dev/null 2>&1; then
    echo -e "${YELLOW}⚠️  دیتابیس $DB_NAME از قبل وجود دارد!${NC}"
    read -p "آیا می‌خواهید آن را حذف و دوباره ایجاد کنید؟ (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}🗑️  در حال حذف دیتابیس قدیمی...${NC}"
        mysql -u "$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS $DB_NAME;" 2>/dev/null
        echo -e "${GREEN}✅ دیتابیس قدیمی حذف شد${NC}"
    else
        echo -e "${RED}❌ عملیات لغو شد${NC}"
        exit 1
    fi
fi

# ایجاد دیتابیس جدید
echo -e "${YELLOW}🔨 در حال ایجاد دیتابیس $DB_NAME...${NC}"
mysql -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ دیتابیس $DB_NAME ایجاد شد${NC}"
else
    echo -e "${RED}❌ خطا در ایجاد دیتابیس!${NC}"
    exit 1
fi

# Import فایل بک‌آپ
echo -e "${YELLOW}📥 در حال Import فایل بک‌آپ...${NC}"
echo -e "${CYAN}این ممکن است چند دقیقه طول بکشد...${NC}"

if command -v pv &> /dev/null; then
    # استفاده از pv برای نمایش progress bar
    pv "$BACKUP_FILE" | mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
else
    # Import معمولی
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$BACKUP_FILE"
fi

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Import با موفقیت انجام شد${NC}"
else
    echo -e "${RED}❌ خطا در Import فایل!${NC}"
    exit 1
fi

# تایید Import
echo -e "${YELLOW}🔍 در حال تایید Import...${NC}"

# بررسی تعداد جداول
TABLE_COUNT=$(mysql -u "$DB_USER" -p"$DB_PASS" -N -e "USE $DB_NAME; SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>/dev/null)

# بررسی تعداد درخواست‌ها
REQUEST_COUNT=$(mysql -u "$DB_USER" -p"$DB_PASS" -N -e "USE $DB_NAME; SELECT COUNT(*) FROM payment_requests;" 2>/dev/null)

# بررسی کاربر ادمین
ADMIN_EXISTS=$(mysql -u "$DB_USER" -p"$DB_PASS" -N -e "USE $DB_NAME; SELECT COUNT(*) FROM users WHERE username = 'admin';" 2>/dev/null)

echo
echo -e "${PURPLE}📊 گزارش Import:${NC}"
echo -e "${CYAN}├─ تعداد جداول: $TABLE_COUNT${NC}"
echo -e "${CYAN}├─ تعداد درخواست‌ها: $REQUEST_COUNT${NC}"
echo -e "${CYAN}└─ کاربر ادمین: $([ "$ADMIN_EXISTS" -gt 0 ] && echo "✅ موجود" || echo "❌ ناموجود")${NC}"

# تست نهایی
if [ "$TABLE_COUNT" -ge 10 ] && [ "$REQUEST_COUNT" -gt 0 ] && [ "$ADMIN_EXISTS" -gt 0 ]; then
    echo
    echo -e "${GREEN}
╔══════════════════════════════════════════════════════════════╗
║                    🎉 راه‌اندازی موفقیت‌آمیز!                    ║
╚══════════════════════════════════════════════════════════════╝${NC}"
    
    echo -e "${PURPLE}🔑 اطلاعات ورود:${NC}"
    echo -e "${CYAN}├─ نام کاربری: admin${NC}"
    echo -e "${CYAN}├─ رمز عبور: admin123${NC}"
    echo -e "${CYAN}└─ دیتابیس: $DB_NAME${NC}"
    
    echo
    echo -e "${YELLOW}⚠️  نکات مهم:${NC}"
    echo -e "${CYAN}1. حتماً رمز عبور ادمین را تغییر دهید${NC}"
    echo -e "${CYAN}2. فایل app/config/database.php را تنظیم کنید${NC}"
    echo -e "${CYAN}3. مجوزهای فولدر upld را بررسی کنید${NC}"
    
    echo
    echo -e "${GREEN}🚀 سامانت آماده استفاده است!${NC}"
    
else
    echo -e "${RED}
╔══════════════════════════════════════════════════════════════╗
║                      ❌ خطا در Import                       ║
╚══════════════════════════════════════════════════════════════╝${NC}"
    echo -e "${YELLOW}لطفاً مراحل را دوباره بررسی کنید یا با پشتیبانی تماس بگیرید.${NC}"
fi

# پیشنهاد تنظیمات
echo
echo -e "${BLUE}📝 فایل کانفیگ پیشنهادی:${NC}"
echo -e "${CYAN}app/config/database.php:${NC}"
cat << EOF
<?php
return [
    'host' => 'localhost',
    'database' => '$DB_NAME',
    'username' => '$DB_USER',
    'password' => 'YOUR_PASSWORD',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_persian_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]
];
EOF

echo
echo -e "${PURPLE}📚 برای راهنمای کامل فایل database_import_guide.md را مطالعه کنید.${NC}" 