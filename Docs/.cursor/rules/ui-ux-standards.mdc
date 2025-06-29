# 🎨 سیستم UI/UX استاندارد سامانت - راهنمای کامل Cursor AI

## 🎯 هدف اصلی
تبدیل پروژه سامانت به یک سیستم UI/UX مدرن، یکپارچه و Mobile-First با بهترین تجربه کاربری.

## 📁 ساختار CSS استاندارد (MANDATORY)

### فایل‌های CSS اصلی (6 فایل):
```
assets/css/
├── design-system.css    # متغیرها، رنگ‌ها، utility classes
├── components.css       # دکمه‌ها، کارت‌ها، فرم‌ها، جدول‌ها
├── layout.css          # Header، Sidebar، Navigation
├── themes.css          # سیستم Light/Dark/Auto Theme
├── responsive.css      # Mobile-First Responsive Design
├── animations.css      # انیمیشن‌ها و Transitions
└── pages/             # فایل‌های اختصاصی صفحات
    ├── dashboard.css
    ├── requests.css
    ├── users.css
    └── tags.css
```

### فایل‌های قدیمی برای حذف:
```
❌ @header.css
❌ @sidebar.css
❌ flat-design.css
❌ style.css
❌ advanced-search.css
❌ mobile-nav.css
❌ main.css (محتوا منتقل شده)
❌ theme-system.css (جایگزین شده)
❌ variables.css (جایگزین شده)
```

## 🎨 Design System Principles

### 1. Colors (رنگ‌ها):
```css
--primary: #6366f1;
--success: #10b981;
--warning: #f59e0b;
--danger: #ef4444;
--gray-50: #f8fafc;
--gray-900: #0f172a;
```

### 2. Typography (فونت‌ها):
- **اصلی:** Vazirmatn
- **Sizes:** xs(12px), sm(14px), base(16px), lg(18px), xl(20px), 2xl(24px), 3xl(30px), 4xl(36px)
- **Weights:** light(300), normal(400), medium(500), semibold(600), bold(700)

### 3. Spacing (فاصله‌ها):
- **Scale:** 8px base system
- **Values:** 0, 4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 48px, 64px

### 4. Components (کامپوننت‌ها):
- **Buttons:** .btn, .btn-primary, .btn-secondary, .btn-icon
- **Cards:** .card, .stat-card, .glass-card
- **Forms:** .form-control, .form-label, .form-group
- **Tables:** .table-container, .table
- **Badges:** .badge, .badge-primary, .badge-success
- **Alerts:** .alert, .alert-success, .alert-danger

## 📱 Mobile-First Rules

### Breakpoints:
- **Mobile:** < 768px (اولویت اصلی)
- **Tablet:** 768px - 1024px
- **Desktop:** > 1024px

### Mobile Navigation:
- **Sidebar:** Hidden در موبایل، Overlay در تبلت
- **Bottom Nav:** Navigation اصلی در پایین صفحه
- **Header:** ارتفاع 70px در دسکتاپ، 60px در موبایل

## 🌙 Theme System

### Data Attributes:
```html
<html data-theme="light|dark|auto">
```

### CSS Variables for Themes:
```css
[data-theme="light"] {
  --body-bg: #ffffff;
  --text-primary: #1e293b;
}

[data-theme="dark"] {
  --body-bg: #0f172a;
  --text-primary: #f8fafc;
}
```

## 🏗️ HTML Structure Standard

### Layout کلی:
```html
<div class="app-container">
  <aside class="sidebar">...</aside>
  <div class="content-wrapper">
    <header class="dashboard-header">...</header>
    <main class="main-content">...</main>
  </div>
  <nav class="mobile-bottom-nav">...</nav>
  <div class="sidebar-overlay"></div>
</div>
```

### Page Structure:
```html
<div class="page-content">
  <nav class="breadcrumb">...</nav>
  <div class="page-header">
    <h1 class="page-title">...</h1>
    <p class="page-description">...</p>
    <div class="page-actions">...</div>
  </div>
  <!-- محتوای اصلی -->
</div>
```

### Stat Cards:
```html
<div class="stat-card hover-lift stagger-item">
  <div class="stat-icon">
    <i class="fas fa-users"></i>
  </div>
  <div class="stat-number">1,234</div>
  <div class="stat-label">کاربران فعال</div>
</div>
```

## 🎭 Animation Guidelines

### کلاس‌های انیمیشن:
- `.animate-fade-in-up` - ورود از پایین
- `.stagger-item` - انیمیشن پلکانی
- `.hover-lift` - بلند شدن در hover
- `.transition` - انتقال نرم

### Performance:
- استفاده از `transform` به جای `top/left`
- `will-change` برای انیمیشن‌های پیچیده
- `@media (prefers-reduced-motion: reduce)` برای accessibility

## 📋 Component Standards

### Buttons:
```html
<!-- Primary -->
<button class="btn btn-primary">
  <i class="fas fa-save"></i>
  ذخیره
</button>

<!-- Icon Only -->
<button class="btn-icon btn-primary">
  <i class="fas fa-edit"></i>
</button>
```

### Forms:
```html
<div class="form-group">
  <label class="form-label required">عنوان</label>
  <input type="text" class="form-control" placeholder="...">
  <div class="form-error">پیام خطا</div>
</div>
```

### Tables:
```html
<div class="table-container">
  <table class="table">
    <thead>
      <tr><th>ستون</th></tr>
    </thead>
    <tbody>
      <tr><td>داده</td></tr>
    </tbody>
  </table>
</div>
```

### Modals:
```html
<div class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <h2 class="modal-title">عنوان</h2>
      <button class="modal-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">محتوا</div>
    <div class="modal-footer">
      <button class="btn btn-primary">تایید</button>
    </div>
  </div>
</div>
```

## 📂 File Import Order در main.php

```html
<!-- Core CSS - ترتیب مهم است -->
<link href="/assets/css/design-system.css" rel="stylesheet">
<link href="/assets/css/components.css" rel="stylesheet">
<link href="/assets/css/layout.css" rel="stylesheet">
<link href="/assets/css/themes.css" rel="stylesheet">
<link href="/assets/css/responsive.css" rel="stylesheet">
<link href="/assets/css/animations.css" rel="stylesheet">

<!-- Page-specific CSS -->
<link href="/assets/css/pages/[page-name].css" rel="stylesheet">
```

## 🔧 JavaScript Requirements

### Core JS Files:
```html
<script src="/assets/js/theme.js"></script>
<script src="/assets/js/navigation.js"></script>
<script src="/assets/js/animations.js"></script>
```

### Global Object:
```javascript
window.SAMANAT = {
  baseUrl: '/path',
  currentTheme: 'light',
  isRTL: true,
  language: 'fa'
};
```

## 🎨 کلاس‌های Utility مهم

### Spacing:
- `p-0` تا `p-12` (padding)
- `m-0` تا `m-12` (margin)
- `gap-1` تا `gap-8` (grid gap)

### Display:
- `flex`, `grid`, `block`, `hidden`
- `items-center`, `justify-between`
- `flex-col`, `flex-wrap`

### Typography:
- `text-xs` تا `text-4xl`
- `font-light` تا `font-bold`
- `text-center`, `text-right`

### Colors:
- `text-primary`, `text-success`, `text-danger`
- `bg-primary`, `bg-white`, `bg-gray-50`

### Responsive:
- `block-md`, `hidden-lg`, `grid-cols-md-2`
- `text-md-lg`, `p-lg-6`

## 🎯 Cursor AI Actions Required

### 1. File Management:
```bash
# حذف فایل‌های قدیمی
DELETE: @header.css, @sidebar.css, flat-design.css, style.css, advanced-search.css, mobile-nav.css, theme-system.css

# ایجاد ساختار جدید
CREATE: design-system.css, components.css, layout.css, themes.css, responsive.css, animations.css
```

### 2. Update All PHP Files:
- **main.php**: بروزرسانی CSS imports
- **header.php**: حذف CSS های inline
- **sidebar.php**: استفاده از کلاس‌های جدید
- **footer.php**: بروزرسانی mobile navigation

### 3. Update All View Files:
- **dashboard**: استفاده از `.stat-card`, `.stagger-item`
- **requests**: استفاده از `.table-container`, `.btn` classes
- **users**: استفاده از components استاندارد
- **tags**: یکپارچه‌سازی با design system

### 4. Component Standardization:
- همه دکمه‌ها: `.btn .btn-*`
- همه کارت‌ها: `.card` یا `.stat-card`
- همه فرم‌ها: `.form-control`, `.form-group`
- همه جدول‌ها: `.table-container`

## ✅ Validation Rules

### هر صفحه باید شامل:
1. ✅ **Responsive Design**: عملکرد صحیح در همه اندازه‌ها
2. ✅ **Theme Support**: پشتیبانی کامل از Light/Dark mode
3. ✅ **Mobile Navigation**: Bottom nav در موبایل
4. ✅ **Consistent Components**: استفاده از کامپوننت‌های استاندارد
5. ✅ **Proper Animations**: انیمیشن‌های نرم و مناسب
6. ✅ **Accessibility**: ARIA labels و semantic HTML
7. ✅ **Performance**: بهینه‌سازی CSS و JS

### CSS Validation:
- ❌ هیچ CSS inline نباشد
- ❌ استفاده از `!important` ممنوع
- ✅ استفاده از CSS Variables
- ✅ Mobile-First Media Queries
- ✅ Consistent Naming Convention

## 🚀 Implementation Strategy

### مرحله 1: Core Files
1. حذف فایل‌های قدیمی CSS
2. ایجاد 6 فایل CSS جدید
3. بروزرسانی main.php

### مرحله 2: Component Update
1. بازنویسی تمام view files
2. جایگزینی کلاس‌های قدیمی
3. اضافه کردن responsive classes

### مرحله 3: Testing
1. تست در همه breakpoint ها
2. تست Theme switching
3. تست Mobile navigation
4. تست Performance

## 🎨 Design Principles

### 1. **Mobile-First**: همه چیز ابتدا برای موبایل طراحی شود
### 2. **Component-Based**: استفاده از کامپوننت‌های قابل استفاده مجدد
### 3. **Performance-First**: بهینه‌سازی برای سرعت
### 4. **Accessibility-First**: در نظر گیری کاربران با نیازهای خاص
### 5. **Consistent**: یکپارچگی در تمام صفحات

## ⚡ Performance Rules

### CSS:
- استفاده از CSS Variables به جای مقادیر ثابت
- ترکیب selectors مشابه
- حداقل استفاده از nested selectors
- استفاده از `transform` برای انیمیشن‌ها

### HTML:
- Semantic markup
- Proper heading hierarchy (h1, h2, h3...)
- Alt text برای تصاویر
- ARIA labels برای accessibility

### JavaScript:
- Event delegation
- Debounced scroll/resize handlers
- Lazy loading برای تصاویر
- Service Worker برای caching

## 🔍 Quality Checklist

برای هر صفحه جدید یا بروزرسانی شده:

### ✅ Visual:
- [ ] طراحی یکپارچه با سایر صفحات
- [ ] رنگ‌ها مطابق Design System
- [ ] فاصله‌ها منطقی و یکپارچه
- [ ] Typography صحیح

### ✅ Functionality:
- [ ] تمام دکمه‌ها کار می‌کنند
- [ ] فرم‌ها validation دارند
- [ ] لینک‌ها صحیح هستند
- [ ] Modal ها درست باز/بسته می‌شوند

### ✅ Responsive:
- [ ] موبایل: عملکرد عالی در 320px-767px
- [ ] تبلت: عملکرد عالی در 768px-1024px
- [ ] دسکتاپ: عملکرد عالی در 1024px+

### ✅ Performance:
- [ ] بارگذاری سریع (< 3 ثانیه)
- [ ] انیمیشن‌های smooth (60fps)
- [ ] بدون layout shift
- [ ] حجم CSS کمینه

### ✅ Accessibility:
- [ ] کنتراست رنگ‌ها مناسب
- [ ] Navigation با keyboard
- [ ] Screen reader compatibility
- [ ] Focus indicators

---

## 🎯 FINAL MISSION FOR CURSOR AI

**تبدیل کامل پروژه سامانت به یک سیستم UI/UX مدرن، یکپارچه و Mobile-First با رعایت تمام اصول و استانداردهای ذکر شده در این document.**

هر تغییر، هر صفحه جدید، هر کامپوننت باید دقیقاً مطابق این راهنما باشد. هیچ استثنایی وجود ندارد!