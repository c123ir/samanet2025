#!/bin/bash

# اسکریپت راه‌اندازی سرور PHP برای پروژه سامانت
# نویسنده: سامانت تیم
# تاریخ: 1404/03/31

echo "🚀 راه‌اندازی سرور PHP برای پروژه سامانت..."
echo "📁 پوشه کاری: $(pwd)"

# بررسی وجود PHP
if ! command -v php &> /dev/null; then
    echo "❌ خطا: PHP نصب نشده است"
    echo "لطفا ابتدا PHP را نصب کنید:"
    echo "  macOS: brew install php"
    echo "  Ubuntu: sudo apt install php"
    exit 1
fi

# نمایش ورژن PHP
PHP_VERSION=$(php -v | head -n 1)
echo "✅ $PHP_VERSION"

# بررسی پورت
PORT=3110
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  پورت $PORT در حال استفاده است"
    echo "آیا می‌خواهید پروسه قبلی را متوقف کنید؟ (y/n)"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        echo "🔄 متوقف کردن پروسه قبلی..."
        pkill -f "php.*localhost:$PORT" 2>/dev/null || true
        sleep 2
    else
        echo "❌ عملیات لغو شد"
        exit 1
    fi
fi

# راه‌اندازی سرور
echo "🌐 راه‌اندازی سرور روی http://localhost:$PORT"
echo "⏹️  برای متوقف کردن: Ctrl+C"
echo "📖 برای مشاهده لاگ‌ها، این terminal را باز نگه دارید"
echo ""
echo "🔗 لینک‌های مفید:"
echo "   • صفحه اصلی: http://localhost:$PORT/"
echo "   • صفحه ورود: http://localhost:$PORT/index.php?route=login"
echo "   • داشبورد: http://localhost:$PORT/index.php?route=dashboard"
echo ""
echo "🔑 اطلاعات ورود:"
echo "   • نام کاربری: admin"
echo "   • رمز عبور: 123456"
echo ""
echo "=================================================================================="

# اجرای سرور
php -S localhost:$PORT router.php 2>&1 | while IFS= read -r line; do
    echo "[$(date '+%H:%M:%S')] $line"
done 