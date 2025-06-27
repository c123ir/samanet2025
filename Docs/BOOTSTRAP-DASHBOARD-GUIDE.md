# 🎯 Bootstrap 5 Professional Dashboard System
## راهنمای استفاده از سیستم داشبورد حرفه‌ای سامانت

**وضعیت:** ✅ فعال - Enterprise Grade  
**تکنولوژی:** Bootstrap 5.3.0 RTL  
**سطح:** حرفه‌ای و استاندارد  

---

## 🎨 **چرا Bootstrap 5؟**

بعد از تست سیستم‌های مختلف، Bootstrap 5 بهترین انتخاب برای سامانت است:

- ✅ **استاندارد جهانی**: میلیون‌ها پروژه از آن استفاده می‌کنند
- ✅ **RTL کامل**: پشتیبانی کامل از فارسی
- ✅ **Responsive طبیعی**: Mobile-First بدون پیچیدگی
- ✅ **Theme System**: Dark/Light Mode آماده
- ✅ **Performance بالا**: بهینه و سبک
- ✅ **نگهداری آسان**: آپدیت و توسعه راحت

---

## 🏗️ **ساختار کلی**

### **HTML Template:**
```html
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عنوان صفحه - سامانت</title>
    
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/theme-system.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <!-- Navbar محتوا -->
        </header>
        
        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- محتوای صفحه -->
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/theme-system.js"></script>
</body>
</html>
```

---

## 📊 **کامپوننت‌های آماده**

### **1. Header استاندارد**
```html
<header class="dashboard-header">
    <nav class="navbar navbar-expand-lg h-100">
        <div class="container-fluid">
            <a class="navbar-brand text-gradient" href="#">
                <i class="fas fa-chart-line me-2"></i>
                نام صفحه
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <button class="theme-toggle btn d-flex align-items-center justify-content-center" 
                        onclick="toggleTheme()" title="تغییر تم">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>
                
                <div class="user-avatar">
                    ک
                </div>
            </div>
        </div>
    </nav>
</header>
```

### **2. کارت‌های آمار (Stats Cards)**
```html
<div class="row stats-row g-3 animate-fade-in">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="stat-card animate-delay-1">
            <div class="stat-label">عنوان</div>
            <div class="stat-value">123</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+12%</span>
            </div>
        </div>
    </div>
    <!-- 3 کارت دیگر -->
</div>
```

### **3. کارت محتوا**
```html
<div class="dashboard-card animate-fade-in">
    <div class="dashboard-card-header">
        <h5 class="dashboard-card-title">
            <i class="fas fa-table"></i>
            عنوان محتوا
        </h5>
    </div>
    <div class="dashboard-card-body">
        <!-- محتوای کارت -->
    </div>
</div>
```

### **4. جدول حرفه‌ای**
```html
<div class="table-responsive">
    <table class="table dashboard-table table-hover mb-0">
        <thead>
            <tr>
                <th>ستون 1</th>
                <th>ستون 2</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>داده 1</td>
                <td>داده 2</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### **5. فرم جستجو**
```html
<form method="GET" class="row g-3">
    <div class="col-12 col-md-4">
        <label class="form-label">جستجو</label>
        <input type="text" name="search" class="form-control form-control-dashboard" placeholder="جستجو...">
    </div>
    
    <div class="col-6 col-md-2">
        <label class="form-label">فیلتر</label>
        <select name="filter" class="form-select form-control-dashboard">
            <option value="">همه</option>
            <option value="1">گزینه 1</option>
        </select>
    </div>
    
    <div class="col-12 col-md-auto d-flex align-items-end">
        <button type="submit" class="btn btn-dashboard btn-dashboard-primary">
            <i class="fas fa-search me-1"></i>
            جستجو
        </button>
    </div>
</form>
```

### **6. سایدبار عملیات سریع**
```html
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h6 class="dashboard-card-title">
            <i class="fas fa-bolt"></i>
            عملیات سریع
        </h6>
    </div>
    <div class="dashboard-card-body">
        <a href="/create" class="quick-action-item">
            <div class="quick-action-icon bg-success">
                <i class="fas fa-plus"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">ایجاد جدید</div>
                <div class="quick-action-desc">ایجاد رکورد جدید</div>
            </div>
        </a>
        
        <a href="/export" class="quick-action-item">
            <div class="quick-action-icon bg-primary">
                <i class="fas fa-download"></i>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">خروجی Excel</div>
                <div class="quick-action-desc">دانلود فایل</div>
            </div>
        </a>
    </div>
</div>
```

---

## 📱 **Responsive Breakpoints**

Bootstrap 5 از breakpoint های استاندارد استفاده می‌کند:

```css
/* Extra small devices (phones, less than 576px) */
/* Default styles */

/* Small devices (tablets, 576px and up) */
@media (min-width: 576px) { ... }

/* Medium devices (small laptops, 768px and up) */
@media (min-width: 768px) { ... }

/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) { ... }

/* Extra large devices (large desktops, 1200px and up) */
@media (min-width: 1200px) { ... }

/* Extra extra large devices (1400px and up) */
@media (min-width: 1400px) { ... }
```

### **Grid Layout:**
```html
<!-- Desktop: 4 ستون، Tablet: 2 ستون، Mobile: 1 ستون -->
<div class="row g-3">
    <div class="col-12 col-md-6 col-lg-3">کارت 1</div>
    <div class="col-12 col-md-6 col-lg-3">کارت 2</div>
    <div class="col-12 col-md-6 col-lg-3">کارت 3</div>
    <div class="col-12 col-md-6 col-lg-3">کارت 4</div>
</div>

<!-- محتوا + سایدبار -->
<div class="row g-3">
    <div class="col-12 col-xl-9">محتوای اصلی (75%)</div>
    <div class="col-12 col-xl-3">سایدبار (25%)</div>
</div>
```

---

## 🌙 **Theme System**

### **استفاده از متغیرهای تم:**
```css
/* Light Mode (Default) */
:root {
    --bs-body-bg: #ffffff;
    --bs-body-color: #212529;
    --bs-card-bg: #ffffff;
}

/* Dark Mode */
[data-theme="dark"] {
    --bs-body-bg: #1a1d29;
    --bs-body-color: #e9ecef;
    --bs-card-bg: #232937;
}

/* استفاده در CSS */
.my-component {
    background: var(--bs-card-bg);
    color: var(--bs-body-color);
}
```

### **تغییر تم با JavaScript:**
```javascript
// فعال‌سازی خودکار
document.addEventListener('DOMContentLoaded', function() {
    // theme-system.js خودش همه چیز را مدیریت می‌کند
});
```

---

## 🚀 **نحوه استفاده در پروژه**

### **1. کنترلر:**
```php
class MyController extends BaseController 
{
    public function index() 
    {
        $this->render('my-page/index', [
            'title' => 'عنوان صفحه',
            'data' => $data,
            'additional_css' => ['css/bootstrap-dashboard.css'] // فقط همین!
        ]);
    }
}
```

### **2. View File:**
```php
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- Headers -->
    <link href="/assets/css/bootstrap-dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- استفاده از کامپوننت‌های آماده -->
    </div>
</body>
</html>
```

---

## 🎨 **کلاس‌های کاربردی**

### **رنگ‌ها:**
```html
<!-- دکمه‌ها -->
<button class="btn btn-dashboard btn-dashboard-primary">اصلی</button>
<button class="btn btn-dashboard btn-dashboard-success">موفقیت</button>

<!-- Badge ها -->
<span class="badge bg-success">فعال</span>
<span class="badge bg-danger">غیرفعال</span>

<!-- آیکون‌ها با رنگ -->
<div class="quick-action-icon bg-primary">
    <i class="fas fa-plus"></i>
</div>
```

### **انیمیشن‌ها:**
```html
<!-- انیمیشن ورود -->
<div class="animate-fade-in">محتوا</div>

<!-- انیمیشن با تاخیر -->
<div class="animate-fade-in animate-delay-1">کارت 1</div>
<div class="animate-fade-in animate-delay-2">کارت 2</div>
```

### **Spacing:**
```html
<!-- فاصله‌ها -->
<div class="p-3">padding</div>
<div class="m-4">margin</div>
<div class="gap-3">gap در flexbox</div>
<div class="g-3">gutter در grid</div>
```

---

## 📊 **مثال کامل: صفحه جدید**

```html
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت محصولات - سامانت</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/theme-system.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <nav class="navbar navbar-expand-lg h-100">
                <div class="container-fluid">
                    <a class="navbar-brand text-gradient" href="#">
                        <i class="fas fa-box me-2"></i>
                        مدیریت محصولات
                    </a>
                    
                    <div class="d-flex align-items-center gap-3">
                        <button class="theme-toggle btn d-flex align-items-center justify-content-center" 
                                onclick="toggleTheme()" title="تغییر تم">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                        <div class="user-avatar">ک</div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Stats Cards -->
            <div class="row stats-row g-3 animate-fade-in">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stat-card animate-delay-1">
                        <div class="stat-label">کل محصولات</div>
                        <div class="stat-value">245</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+15%</span>
                        </div>
                    </div>
                </div>
                <!-- 3 کارت دیگر -->
            </div>

            <!-- Search & Filters -->
            <div class="dashboard-card animate-fade-in animate-delay-4">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-search"></i>
                        جستجو و فیلتر
                    </h5>
                </div>
                <div class="dashboard-card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">جستجو</label>
                            <input type="text" name="search" class="form-control form-control-dashboard" placeholder="نام محصول...">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">دسته‌بندی</label>
                            <select name="category" class="form-select form-control-dashboard">
                                <option value="">همه</option>
                                <option value="1">دسته 1</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-dashboard btn-dashboard-primary w-100">
                                <i class="fas fa-search me-1"></i>
                                جستجو
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="row g-3">
                <!-- Products List -->
                <div class="col-12 col-xl-9">
                    <div class="dashboard-card animate-fade-in animate-delay-4">
                        <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                            <h5 class="dashboard-card-title">
                                <i class="fas fa-box"></i>
                                لیست محصولات
                                <span class="card-badge">245</span>
                            </h5>
                            <a href="/products/create" class="btn btn-dashboard btn-dashboard-success btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                جدید
                            </a>
                        </div>
                        <div class="dashboard-card-body p-0">
                            <div class="table-responsive">
                                <table class="table dashboard-table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>محصول</th>
                                            <th>قیمت</th>
                                            <th>موجودی</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>محصول نمونه</td>
                                            <td>150,000 تومان</td>
                                            <td><span class="badge bg-success">موجود</span></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-12 col-xl-3">
                    <div class="dashboard-card animate-fade-in animate-delay-4">
                        <div class="dashboard-card-header">
                            <h6 class="dashboard-card-title">
                                <i class="fas fa-bolt"></i>
                                عملیات سریع
                            </h6>
                        </div>
                        <div class="dashboard-card-body">
                            <a href="/products/create" class="quick-action-item">
                                <div class="quick-action-icon bg-success">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="quick-action-content">
                                    <div class="quick-action-title">محصول جدید</div>
                                    <div class="quick-action-desc">ایجاد محصول</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/theme-system.js"></script>
</body>
</html>
```

---

## ✅ **مزایای این سیستم**

1. **استاندارد جهانی**: Bootstrap 5 استاندارد جهانی است
2. **RTL کامل**: پشتیبانی کامل از زبان فارسی
3. **Responsive طبیعی**: بدون پیچیدگی، Mobile-First
4. **Performance بالا**: بهینه و سریع
5. **نگهداری آسان**: کد تمیز و قابل نگهداری
6. **Dark Mode**: کاملاً کار می‌کند
7. **مستندات کامل**: منابع یادگیری فراوان

---

**🎉 نتیجه:** یک سیستم داشبورد کاملاً حرفه‌ای، استاندارد، و قابل اعتماد که در همه پروژه‌ها استفاده می‌شود! 