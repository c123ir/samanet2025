# 🎨 راه‌حل کامل UI/UX و سیستم CSS استاندارد
**پروژه سامانت - بهینه‌سازی رابط کاربری**

## 🔍 تحلیل وضعیت فعلی

### ✅ موارد مثبت موجود:
- یکپارچه‌سازی Header و Sidebar انجام شده
- سیستم Theme (روز/شب) فعال
- طراحی Mobile-first پیاده‌سازی شده
- ساختار Component-based ایجاد شده

### ❌ چالش‌های باقی‌مانده:
- هنوز 9 فایل CSS مختلف وجود دارد
- تکرار کدها در فایل‌های مختلف
- عدم استاندارد واحد برای component ها
- پیچیدگی در نگهداری و توسعه

## 🎯 راه‌حل پیشنهادی: **سیستم CSS مدرن و یکپارچه**

### 📁 ساختار CSS نهایی پیشنهادی:

```
assets/css/
├── 🎨 design-system.css      # Core: Colors, Typography, Spacing
├── 📱 layout.css             # Header, Sidebar, Navigation
├── 🧩 components.css         # تمام کامپوننت‌ها در یک فایل
├── 📱 responsive.css         # Media queries مرکزی
├── 🌙 themes.css             # Dark/Light themes
├── 🎭 animations.css         # تمام انیمیشن‌ها
└── 📄 pages/                 # فایل‌های اختصاصی صفحات
    ├── dashboard.css
    ├── requests.css
    ├── users.css
    └── tags.css
```

## 🚀 **مرحله 1: ایجاد Design System یکپارچه**

### A) فایل `design-system.css` (جایگزین variables.css):

```css
/* ===== DESIGN TOKENS ===== */
:root {
  /* Colors - Material Design 3.0 inspired */
  --primary: #6366f1;
  --primary-50: #eef2ff;
  --primary-100: #e0e7ff;
  --primary-500: #6366f1;
  --primary-600: #4f46e5;
  --primary-700: #4338ca;
  
  /* Neutrals */
  --white: #ffffff;
  --black: #000000;
  --gray-50: #f8fafc;
  --gray-100: #f1f5f9;
  --gray-200: #e2e8f0;
  --gray-300: #cbd5e1;
  --gray-400: #94a3b8;
  --gray-500: #64748b;
  --gray-600: #475569;
  --gray-700: #334155;
  --gray-800: #1e293b;
  --gray-900: #0f172a;
  
  /* Success, Warning, Danger */
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  
  /* Spacing Scale - 8px base */
  --space-0: 0;
  --space-1: 0.25rem;    /* 4px */
  --space-2: 0.5rem;     /* 8px */
  --space-3: 0.75rem;    /* 12px */
  --space-4: 1rem;       /* 16px */
  --space-5: 1.25rem;    /* 20px */
  --space-6: 1.5rem;     /* 24px */
  --space-8: 2rem;       /* 32px */
  --space-10: 2.5rem;    /* 40px */
  --space-12: 3rem;      /* 48px */
  --space-16: 4rem;      /* 64px */
  
  /* Typography Scale */
  --text-xs: 0.75rem;    /* 12px */
  --text-sm: 0.875rem;   /* 14px */
  --text-base: 1rem;     /* 16px */
  --text-lg: 1.125rem;   /* 18px */
  --text-xl: 1.25rem;    /* 20px */
  --text-2xl: 1.5rem;    /* 24px */
  --text-3xl: 1.875rem;  /* 30px */
  --text-4xl: 2.25rem;   /* 36px */
  
  /* Font Weights */
  --font-light: 300;
  --font-normal: 400;
  --font-medium: 500;
  --font-semibold: 600;
  --font-bold: 700;
  
  /* Border Radius */
  --radius-sm: 0.25rem;   /* 4px */
  --radius-md: 0.375rem;  /* 6px */
  --radius-lg: 0.5rem;    /* 8px */
  --radius-xl: 0.75rem;   /* 12px */
  --radius-2xl: 1rem;     /* 16px */
  --radius-full: 9999px;
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
  --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
  
  /* Transitions */
  --transition-fast: all 0.15s ease;
  --transition-normal: all 0.3s ease;
  --transition-slow: all 0.5s ease;
  
  /* Z-Index Scale */
  --z-dropdown: 1000;
  --z-sticky: 1020;
  --z-fixed: 1030;
  --z-modal-backdrop: 1040;
  --z-modal: 1050;
  --z-popover: 1060;
  --z-tooltip: 1070;
  
  /* Layout */
  --container-max-width: 1200px;
  --sidebar-width: 280px;
  --header-height: 70px;
  --mobile-nav-height: 60px;
  
  /* Glass Morphism */
  --glass-bg: rgba(255, 255, 255, 0.7);
  --glass-border: rgba(255, 255, 255, 0.18);
  --backdrop-blur: blur(10px);
}

/* Dark Theme Variables */
[data-theme="dark"] {
  --white: #0f172a;
  --black: #ffffff;
  --gray-50: #1e293b;
  --gray-100: #334155;
  --gray-200: #475569;
  --gray-300: #64748b;
  --gray-400: #94a3b8;
  --gray-500: #cbd5e1;
  --gray-600: #e2e8f0;
  --gray-700: #f1f5f9;
  --gray-800: #f8fafc;
  --gray-900: #ffffff;
  
  --glass-bg: rgba(30, 41, 59, 0.7);
  --glass-border: rgba(148, 163, 184, 0.18);
}

/* Typography System */
.text-xs { font-size: var(--text-xs); }
.text-sm { font-size: var(--text-sm); }
.text-base { font-size: var(--text-base); }
.text-lg { font-size: var(--text-lg); }
.text-xl { font-size: var(--text-xl); }
.text-2xl { font-size: var(--text-2xl); }
.text-3xl { font-size: var(--text-3xl); }
.text-4xl { font-size: var(--text-4xl); }

.font-light { font-weight: var(--font-light); }
.font-normal { font-weight: var(--font-normal); }
.font-medium { font-weight: var(--font-medium); }
.font-semibold { font-weight: var(--font-semibold); }
.font-bold { font-weight: var(--font-bold); }

/* Color Utilities */
.text-primary { color: var(--primary); }
.text-success { color: var(--success); }
.text-warning { color: var(--warning); }
.text-danger { color: var(--danger); }
.text-muted { color: var(--gray-500); }

.bg-primary { background-color: var(--primary); }
.bg-success { background-color: var(--success); }
.bg-warning { background-color: var(--warning); }
.bg-danger { background-color: var(--danger); }
.bg-gray-50 { background-color: var(--gray-50); }
.bg-white { background-color: var(--white); }

/* Spacing Utilities */
.p-0 { padding: var(--space-0); }
.p-1 { padding: var(--space-1); }
.p-2 { padding: var(--space-2); }
.p-3 { padding: var(--space-3); }
.p-4 { padding: var(--space-4); }
.p-5 { padding: var(--space-5); }
.p-6 { padding: var(--space-6); }
.p-8 { padding: var(--space-8); }

.m-0 { margin: var(--space-0); }
.m-1 { margin: var(--space-1); }
.m-2 { margin: var(--space-2); }
.m-3 { margin: var(--space-3); }
.m-4 { margin: var(--space-4); }
.m-5 { margin: var(--space-5); }
.m-6 { margin: var(--space-6); }
.m-8 { margin: var(--space-8); }

/* Flexbox Utilities */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-wrap { flex-wrap: wrap; }
.items-center { align-items: center; }
.items-start { align-items: flex-start; }
.items-end { align-items: flex-end; }
.justify-center { justify-content: center; }
.justify-between { justify-content: space-between; }
.justify-start { justify-content: flex-start; }
.justify-end { justify-content: flex-end; }

/* Grid Utilities */
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.gap-1 { gap: var(--space-1); }
.gap-2 { gap: var(--space-2); }
.gap-3 { gap: var(--space-3); }
.gap-4 { gap: var(--space-4); }
.gap-6 { gap: var(--space-6); }

/* Border Radius */
.rounded-sm { border-radius: var(--radius-sm); }
.rounded-md { border-radius: var(--radius-md); }
.rounded-lg { border-radius: var(--radius-lg); }
.rounded-xl { border-radius: var(--radius-xl); }
.rounded-2xl { border-radius: var(--radius-2xl); }
.rounded-full { border-radius: var(--radius-full); }

/* Shadows */
.shadow-sm { box-shadow: var(--shadow-sm); }
.shadow-md { box-shadow: var(--shadow-md); }
.shadow-lg { box-shadow: var(--shadow-lg); }
.shadow-xl { box-shadow: var(--shadow-xl); }
.shadow-2xl { box-shadow: var(--shadow-2xl); }

/* Display */
.block { display: block; }
.inline-block { display: inline-block; }
.inline { display: inline; }
.hidden { display: none; }

/* Position */
.relative { position: relative; }
.absolute { position: absolute; }
.fixed { position: fixed; }
.sticky { position: sticky; }

/* Common Animations */
.transition { transition: var(--transition-normal); }
.transition-fast { transition: var(--transition-fast); }
.transition-slow { transition: var(--transition-slow); }

.hover-lift:hover { transform: translateY(-2px); }
.hover-scale:hover { transform: scale(1.05); }
.hover-shadow:hover { box-shadow: var(--shadow-lg); }
```

## 🧩 **مرحله 2: کامپوننت‌های استاندارد**

### فایل `components.css`:

```css
/* ===== BASE RESET ===== */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Vazirmatn', 'IRANSans', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: var(--text-base);
  line-height: 1.6;
  color: var(--gray-800);
  background-color: var(--gray-50);
  direction: rtl;
  text-align: right;
}

/* ===== BUTTONS ===== */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-4);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: var(--transition-fast);
  text-decoration: none;
  white-space: nowrap;
  user-select: none;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background-color: var(--primary);
  color: white;
  border-color: var(--primary);
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--primary-600);
  border-color: var(--primary-600);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-secondary {
  background-color: white;
  color: var(--gray-700);
  border-color: var(--gray-300);
}

.btn-secondary:hover:not(:disabled) {
  background-color: var(--gray-50);
  border-color: var(--gray-400);
}

.btn-success {
  background-color: var(--success);
  color: white;
  border-color: var(--success);
}

.btn-danger {
  background-color: var(--danger);
  color: white;
  border-color: var(--danger);
}

.btn-sm {
  padding: var(--space-2) var(--space-3);
  font-size: var(--text-xs);
}

.btn-lg {
  padding: var(--space-4) var(--space-6);
  font-size: var(--text-lg);
}

.btn-icon {
  width: 40px;
  height: 40px;
  padding: 0;
  border-radius: var(--radius-full);
}

/* ===== CARDS ===== */
.card {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
  overflow: hidden;
  transition: var(--transition-normal);
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.card-header {
  padding: var(--space-4) var(--space-6);
  border-bottom: 1px solid var(--gray-200);
  background-color: var(--gray-50);
}

.card-body {
  padding: var(--space-6);
}

.card-footer {
  padding: var(--space-4) var(--space-6);
  border-top: 1px solid var(--gray-200);
  background-color: var(--gray-50);
}

.glass-card {
  background: var(--glass-bg);
  backdrop-filter: var(--backdrop-blur);
  -webkit-backdrop-filter: var(--backdrop-blur);
  border: 1px solid var(--glass-border);
}

/* ===== FORMS ===== */
.form-group {
  margin-bottom: var(--space-4);
}

.form-label {
  display: block;
  margin-bottom: var(--space-2);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--gray-700);
}

.form-control {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  font-size: var(--text-base);
  color: var(--gray-800);
  background-color: white;
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-md);
  transition: var(--transition-fast);
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-control::placeholder {
  color: var(--gray-400);
}

.form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: left 0.75rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-left: 2.5rem;
}

.form-control-sm {
  padding: var(--space-2) var(--space-3);
  font-size: var(--text-sm);
}

.form-control-lg {
  padding: var(--space-4) var(--space-5);
  font-size: var(--text-lg);
}

/* ===== TABLES ===== */
.table-container {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: var(--space-4);
  text-align: right;
  border-bottom: 1px solid var(--gray-200);
}

.table th {
  background-color: var(--gray-50);
  font-weight: var(--font-semibold);
  color: var(--gray-700);
  font-size: var(--text-sm);
}

.table tbody tr:hover {
  background-color: var(--gray-50);
}

.table tbody tr:last-child td {
  border-bottom: none;
}

/* ===== BADGES ===== */
.badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-1);
  padding: var(--space-1) var(--space-2);
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  border-radius: var(--radius-full);
}

.badge-primary {
  background-color: var(--primary-100);
  color: var(--primary-700);
}

.badge-success {
  background-color: #dcfce7;
  color: #166534;
}

.badge-warning {
  background-color: #fef3c7;
  color: #92400e;
}

.badge-danger {
  background-color: #fee2e2;
  color: #991b1b;
}

/* ===== ALERTS ===== */
.alert {
  padding: var(--space-4);
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  margin-bottom: var(--space-4);
}

.alert-success {
  background-color: #f0fdf4;
  border-color: #bbf7d0;
  color: #166534;
}

.alert-warning {
  background-color: #fffbeb;
  border-color: #fed7aa;
  color: #92400e;
}

.alert-danger {
  background-color: #fef2f2;
  border-color: #fecaca;
  color: #991b1b;
}

.alert-info {
  background-color: #eff6ff;
  border-color: #bfdbfe;
  color: #1e40af;
}

/* ===== MODALS ===== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: var(--z-modal);
  opacity: 0;
  visibility: hidden;
  transition: var(--transition-normal);
}

.modal-overlay.show {
  opacity: 1;
  visibility: visible;
}

.modal {
  background: white;
  border-radius: var(--radius-xl);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  transform: scale(0.9);
  transition: var(--transition-normal);
}

.modal-overlay.show .modal {
  transform: scale(1);
}

.modal-header {
  padding: var(--space-6);
  border-bottom: 1px solid var(--gray-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--gray-800);
}

.modal-body {
  padding: var(--space-6);
  max-height: 60vh;
  overflow-y: auto;
}

.modal-footer {
  padding: var(--space-6);
  border-top: 1px solid var(--gray-200);
  display: flex;
  gap: var(--space-3);
  justify-content: flex-end;
}

/* ===== DROPDOWNS ===== */
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 200px;
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  z-index: var(--z-dropdown);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: var(--transition-fast);
}

.dropdown.show .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: var(--space-3) var(--space-4);
  color: var(--gray-700);
  text-decoration: none;
  border: none;
  background: transparent;
  text-align: right;
  cursor: pointer;
  transition: var(--transition-fast);
}

.dropdown-item:hover {
  background-color: var(--gray-50);
  color: var(--gray-900);
}

.dropdown-divider {
  height: 1px;
  background-color: var(--gray-200);
  margin: var(--space-2) 0;
}

/* ===== PROGRESS ===== */
.progress {
  height: 8px;
  background-color: var(--gray-200);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: var(--primary);
  border-radius: var(--radius-full);
  transition: width 0.3s ease;
}

.progress-bar-success {
  background-color: var(--success);
}

.progress-bar-warning {
  background-color: var(--warning);
}

.progress-bar-danger {
  background-color: var(--danger);
}

/* ===== SPINNERS ===== */
.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid var(--gray-200);
  border-top-color: var(--primary);
  border-radius: var(--radius-full);
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.spinner-sm {
  width: 16px;
  height: 16px;
  border-width: 1px;
}

.spinner-lg {
  width: 32px;
  height: 32px;
  border-width: 3px;
}

/* ===== STATS CARDS ===== */
.stat-card {
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  transition: var(--transition-normal);
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.stat-number {
  font-size: var(--text-3xl);
  font-weight: var(--font-bold);
  color: var(--primary);
  margin-bottom: var(--space-2);
}

.stat-label {
  font-size: var(--text-sm);
  color: var(--gray-600);
  font-weight: var(--font-medium);
}

.stat-icon {
  width: 48px;
  height: 48px;
  background: var(--primary-100);
  color: var(--primary);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-xl);
  margin-bottom: var(--space-4);
}

/* ===== DARK MODE ADAPTATIONS ===== */
[data-theme="dark"] .card,
[data-theme="dark"] .form-control,
[data-theme="dark"] .table-container,
[data-theme="dark"] .modal,
[data-theme="dark"] .dropdown-menu,
[data-theme="dark"] .stat-card {
  background-color: var(--gray-100);
  border-color: var(--gray-200);
  color: var(--gray-800);
}

[data-theme="dark"] .card-header,
[data-theme="dark"] .card-footer,
[data-theme="dark"] .table th {
  background-color: var(--gray-50);
}

[data-theme="dark"] .form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

[data-theme="dark"] .table tbody tr:hover,
[data-theme="dark"] .dropdown-item:hover {
  background-color: var(--gray-50);
}
```

## 📱 **مرحله 3: Layout و Navigation مدرن**

### فایل `layout.css`:

```css
/* ===== MAIN LAYOUT ===== */
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.content-wrapper {
  flex: 1;
  display: flex;
  margin-right: var(--sidebar-width);
  transition: var(--transition-normal);
}

.main-content {
  flex: 1;
  padding: var(--space-6);
  background-color: var(--gray-50);
  min-height: calc(100vh - var(--header-height));
}

/* ===== HEADER ===== */
.dashboard-header {
  position: fixed;
  top: 0;
  right: var(--sidebar-width);
  left: 0;
  height: var(--header-height);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--gray-200);
  z-index: var(--z-sticky);
  padding: 0 var(--space-6);
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: var(--transition-normal);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.header-title {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--gray-800);
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.search-box {
  position: relative;
  width: 300px;
}

.search-input {
  width: 100%;
  padding: var(--space-2) var(--space-4);
  padding-left: var(--space-10);
  border: 1px solid var(--gray-300);
  border-radius: var(--radius-full);
  background: var(--glass-bg);
  backdrop-filter: var(--backdrop-blur);
  font-size: var(--text-sm);
  transition: var(--transition-fast);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  background: white;
}

.search-icon {
  position: absolute;
  left: var(--space-3);
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  font-size: var(--text-sm);
}

.theme-toggle,
.notification-btn,
.user-profile {
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition-fast);
  color: var(--gray-600);
  position: relative;
}

.theme-toggle:hover,
.notification-btn:hover,
.user-profile:hover {
  background: var(--gray-100);
  color: var(--gray-800);
}

.notification-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  background: var(--danger);
  border-radius: var(--radius-full);
  border: 2px solid white;
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: var(--radius-full);
  object-fit: cover;
}

.mobile-menu-toggle {
  display: none;
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  border-radius: var(--radius-md);
  cursor: pointer;
  color: var(--gray-600);
}

/* ===== SIDEBAR ===== */
.sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: var(--sidebar-width);
  height: 100vh;
  background: white;
  border-left: 1px solid var(--gray-200);
  z-index: var(--z-fixed);
  overflow-y: auto;
  transition: var(--transition-normal);
}

.sidebar-header {
  padding: var(--space-6);
  border-bottom: 1px solid var(--gray-200);
  text-align: center;
}

.sidebar-logo {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--primary);
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
}

.sidebar-nav {
  padding: var(--space-4) 0;
}

.nav-section {
  margin-bottom: var(--space-6);
}

.nav-section-title {
  padding: 0 var(--space-6) var(--space-2);
  font-size: var(--text-xs);
  font-weight: var(--font-semibold);
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.nav-item {
  display: block;
  padding: var(--space-3) var(--space-6);
  color: var(--gray-700);
  text-decoration: none;
  font-weight: var(--font-medium);
  position: relative;
  transition: var(--transition-fast);
}

.nav-item:hover {
  background: var(--gray-50);
  color: var(--gray-900);
}

.nav-item.active {
  background: var(--primary-50);
  color: var(--primary-700);
  border-left: 3px solid var(--primary);
}

.nav-item i {
  width: 20px;
  margin-left: var(--space-3);
  text-align: center;
  font-size: var(--text-base);
}

.nav-badge {
  position: absolute;
  left: var(--space-4);
  top: 50%;
  transform: translateY(-50%);
  background: var(--primary);
  color: white;
  font-size: var(--text-xs);
  padding: 2px 6px;
  border-radius: var(--radius-full);
  min-width: 18px;
  text-align: center;
}

/* ===== MOBILE BOTTOM NAVIGATION ===== */
.mobile-bottom-nav {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: var(--mobile-nav-height);
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-top: 1px solid var(--gray-200);
  z-index: var(--z-fixed);
  padding: 0;
}

.mobile-nav-container {
  display: flex;
  align-items: center;
  justify-content: space-around;
  height: 100%;
  max-width: var(--container-max-width);
  margin: 0 auto;
  padding: 0 var(--space-4);
}

.mobile-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-1);
  color: var(--gray-500);
  text-decoration: none;
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
  transition: var(--transition-fast);
  padding: var(--space-2);
  border-radius: var(--radius-md);
  min-width: 60px;
  position: relative;
}

.mobile-nav-item i {
  font-size: var(--text-lg);
}

.mobile-nav-item.active {
  color: var(--primary);
  background: var(--primary-50);
}

.mobile-nav-item.main-item {
  transform: translateY(-8px);
  background: var(--primary);
  color: white;
  border-radius: var(--radius-full);
  width: 50px;
  height: 50px;
  box-shadow: var(--shadow-lg);
}

.mobile-nav-item.main-item i {
  font-size: var(--text-xl);
}

.mobile-nav-item.main-item span {
  display: none;
}

/* ===== BREADCRUMB ===== */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  margin-bottom: var(--space-6);
  font-size: var(--text-sm);
}

.breadcrumb-item {
  color: var(--gray-500);
  text-decoration: none;
  transition: var(--transition-fast);
}

.breadcrumb-item:hover {
  color: var(--gray-700);
}

.breadcrumb-item.active {
  color: var(--gray-800);
  font-weight: var(--font-medium);
}

.breadcrumb-separator {
  color: var(--gray-400);
  font-size: var(--text-xs);
}

/* ===== PAGE HEADER ===== */
.page-header {
  margin-bottom: var(--space-6);
  padding-bottom: var(--space-4);
  border-bottom: 1px solid var(--gray-200);
}

.page-title {
  font-size: var(--text-3xl);
  font-weight: var(--font-bold);
  color: var(--gray-900);
  margin-bottom: var(--space-2);
}

.page-description {
  color: var(--gray-600);
  font-size: var(--text-base);
  line-height: 1.6;
}

.page-actions {
  display: flex;
  gap: var(--space-3);
  margin-top: var(--space-4);
}

/* ===== SIDEBAR OVERLAY FOR MOBILE ===== */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: var(--z-modal-backdrop);
  opacity: 0;
  visibility: hidden;
  transition: var(--transition-normal);
}

.sidebar-overlay.show {
  opacity: 1;
  visibility: visible;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Large screens (1200px+) */
@media (min-width: 1200px) {
  .main-content {
    padding: var(--space-8);
  }
  
  .search-box {
    width: 400px;
  }
}

/* Tablet (768px - 1199px) */
@media (max-width: 1199px) {
  .search-box {
    width: 250px;
  }
}

/* Mobile (< 992px) */
@media (max-width: 991px) {
  .content-wrapper {
    margin-right: 0;
  }
  
  .dashboard-header {
    right: 0;
    padding: 0 var(--space-4);
  }
  
  .sidebar {
    right: -100%;
    box-shadow: var(--shadow-xl);
  }
  
  .sidebar.show {
    right: 0;
  }
  
  .mobile-menu-toggle {
    display: flex;
  }
  
  .search-box {
    display: none;
  }
  
  .header-title {
    font-size: var(--text-xl);
  }
}

/* Small mobile (< 768px) */
@media (max-width: 767px) {
  .mobile-bottom-nav {
    display: block;
  }
  
  body {
    padding-bottom: var(--mobile-nav-height);
  }
  
  .main-content {
    padding: var(--space-4);
  }
  
  .dashboard-header {
    padding: 0 var(--space-3);
  }
  
  .header-actions {
    gap: var(--space-2);
  }
  
  .theme-toggle,
  .notification-btn,
  .user-profile {
    width: 36px;
    height: 36px;
  }
}

/* Very small mobile (< 576px) */
@media (max-width: 575px) {
  .main-content {
    padding: var(--space-3);
  }
  
  .page-title {
    font-size: var(--text-2xl);
  }
  
  .header-title {
    font-size: var(--text-lg);
  }
}

/* ===== DARK MODE LAYOUT ===== */
[data-theme="dark"] .dashboard-header {
  background: rgba(30, 41, 59, 0.8);
  border-bottom-color: var(--gray-200);
}

[data-theme="dark"] .sidebar {
  background: var(--gray-100);
  border-left-color: var(--gray-200);
}

[data-theme="dark"] .mobile-bottom-nav {
  background: rgba(30, 41, 59, 0.9);
  border-top-color: var(--gray-200);
}

[data-theme="dark"] .nav-item:hover,
[data-theme="dark"] .mobile-nav-item:hover {
  background: var(--gray-50);
}

[data-theme="dark"] .nav-item.active {
  background: var(--primary-100);
  color: var(--primary-500);
}

[data-theme="dark"] .search-input {
  background: var(--gray-50);
  border-color: var(--gray-200);
  color: var(--gray-800);
}

[data-theme="dark"] .search-input:focus {
  background: var(--gray-100);
}
```

## 🎭 **مرحله 4: انیمیشن‌ها و Transitions**

### فایل `animations.css`:

```css
/* ===== KEYFRAME ANIMATIONS ===== */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInUp {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

@keyframes slideInDown {
  from {
    transform: translateY(-100%);
  }
  to {
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0,0,0);
  }
  40%, 43% {
    transform: translate3d(0, -30px, 0);
  }
  70% {
    transform: translate3d(0, -15px, 0);
  }
  90% {
    transform: translate3d(0, -4px, 0);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  10%, 30%, 50%, 70%, 90% {
    transform: translateX(-10px);
  }
  20%, 40%, 60%, 80% {
    transform: translateX(10px);
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes ping {
  75%, 100% {
    transform: scale(2);
    opacity: 0;
  }
}

/* ===== ANIMATION CLASSES ===== */
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

.animate-fade-in-down {
  animation: fadeInDown 0.6s ease-out;
}

.animate-fade-in-left {
  animation: fadeInLeft 0.6s ease-out;
}

.animate-fade-in-right {
  animation: fadeInRight 0.6s ease-out;
}

.animate-slide-in-up {
  animation: slideInUp 0.4s ease-out;
}

.animate-slide-in-down {
  animation: slideInDown 0.4s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.3s ease-out;
}

.animate-bounce {
  animation: bounce 1s;
}

.animate-pulse {
  animation: pulse 2s infinite;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

.animate-ping {
  animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}

/* ===== HOVER ANIMATIONS ===== */
.hover-lift {
  transition: transform 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-4px);
}

.hover-scale {
  transition: transform 0.2s ease;
}

.hover-scale:hover {
  transform: scale(1.05);
}

.hover-rotate {
  transition: transform 0.3s ease;
}

.hover-rotate:hover {
  transform: rotate(5deg);
}

.hover-shadow {
  transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
  box-shadow: var(--shadow-xl);
}

.hover-glow {
  transition: box-shadow 0.3s ease;
}

.hover-glow:hover {
  box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
}

/* ===== LOADING STATES ===== */
.loading-skeleton {
  background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.loading-dots::after {
  content: '';
  animation: loading-dots 1.5s infinite;
}

@keyframes loading-dots {
  0%, 20% {
    content: '.';
  }
  40% {
    content: '..';
  }
  60%, 100% {
    content: '...';
  }
}

/* ===== PAGE TRANSITION ANIMATIONS ===== */
.page-enter {
  opacity: 0;
  transform: translateY(20px);
}

.page-enter-active {
  opacity: 1;
  transform: translateY(0);
  transition: opacity 0.3s, transform 0.3s;
}

.page-exit {
  opacity: 1;
  transform: translateY(0);
}

.page-exit-active {
  opacity: 0;
  transform: translateY(-20px);
  transition: opacity 0.3s, transform 0.3s;
}

/* ===== STAGGERED ANIMATIONS ===== */
.stagger-item {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s ease-out forwards;
}

.stagger-item:nth-child(1) { animation-delay: 0.1s; }
.stagger-item:nth-child(2) { animation-delay: 0.2s; }
.stagger-item:nth-child(3) { animation-delay: 0.3s; }
.stagger-item:nth-child(4) { animation-delay: 0.4s; }
.stagger-item:nth-child(5) { animation-delay: 0.5s; }
.stagger-item:nth-child(6) { animation-delay: 0.6s; }

/* ===== MICRO-INTERACTIONS ===== */
.btn-press {
  transition: transform 0.1s ease;
}

.btn-press:active {
  transform: scale(0.95);
}

.input-focus {
  transition: all 0.2s ease;
}

.input-focus:focus {
  transform: scale(1.02);
}

.card-tilt {
  transition: transform 0.3s ease;
  transform-style: preserve-3d;
}

.card-tilt:hover {
  transform: rotateX(5deg) rotateY(5deg);
}

/* ===== NOTIFICATION ANIMATIONS ===== */
.notification-enter {
  opacity: 0;
  transform: translateX(100%);
}

.notification-enter-active {
  opacity: 1;
  transform: translateX(0);
  transition: all 0.3s ease;
}

.notification-exit {
  opacity: 1;
  transform: translateX(0);
}

.notification-exit-active {
  opacity: 0;
  transform: translateX(100%);
  transition: all 0.3s ease;
}

/* ===== DARK MODE ANIMATIONS ===== */
[data-theme="dark"] .loading-skeleton {
  background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-300) 50%, var(--gray-200) 75%);
}

/* ===== REDUCED MOTION ===== */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* ===== PERFORMANCE OPTIMIZATIONS ===== */
.will-change-transform {
  will-change: transform;
}

.will-change-opacity {
  will-change: opacity;
}

.gpu-accelerated {
  transform: translateZ(0);
  backface-visibility: hidden;
  perspective: 1000;
}
```

## 🌙 **مرحله 5: سیستم Theme مدرن**

### فایل `themes.css`:

```css
/* ===== THEME SYSTEM ===== */
[data-theme="light"] {
  /* Override any specific light theme variables if needed */
  color-scheme: light;
}

[data-theme="dark"] {
  color-scheme: dark;
  
  /* Background Colors */
  --body-bg: #0f172a;
  --content-bg: #1e293b;
  --surface-bg: #334155;
  
  /* Text Colors */
  --text-primary: #f8fafc;
  --text-secondary: #e2e8f0;
  --text-muted: #94a3b8;
  
  /* Border Colors */
  --border-primary: #334155;
  --border-secondary: #475569;
  
  /* Input Colors */
  --input-bg: #1e293b;
  --input-border: #475569;
  --input-focus-border: #6366f1;
  
  /* Card Colors */
  --card-bg: #1e293b;
  --card-border: #334155;
  --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
  
  /* Navigation Colors */
  --nav-bg: #1e293b;
  --nav-item-hover: #334155;
  --nav-item-active: #3730a3;
  
  /* Glass Morphism Dark */
  --glass-bg-dark: rgba(30, 41, 59, 0.8);
  --glass-border-dark: rgba(148, 163, 184, 0.2);
}

/* ===== THEME TOGGLE BUTTON ===== */
.theme-toggle {
  position: relative;
  overflow: hidden;
}

.theme-toggle .icon-sun,
.theme-toggle .icon-moon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  transition: var(--transition-normal);
}

[data-theme="light"] .theme-toggle .icon-sun {
  opacity: 1;
  transform: translate(-50%, -50%) rotate(0deg);
}

[data-theme="light"] .theme-toggle .icon-moon {
  opacity: 0;
  transform: translate(-50%, -50%) rotate(180deg);
}

[data-theme="dark"] .theme-toggle .icon-sun {
  opacity: 0;
  transform: translate(-50%, -50%) rotate(-180deg);
}

[data-theme="dark"] .theme-toggle .icon-moon {
  opacity: 1;
  transform: translate(-50%, -50%) rotate(0deg);
}

/* ===== SMOOTH THEME TRANSITION ===== */
* {
  transition: background-color 0.3s ease, 
              border-color 0.3s ease, 
              color 0.3s ease,
              box-shadow 0.3s ease;
}

/* ===== AUTO THEME DETECTION ===== */
@media (prefers-color-scheme: dark) {
  :root:not([data-theme]) {
    color-scheme: dark;
    /* Apply dark theme variables */
  }
}

@media (prefers-color-scheme: light) {
  :root:not([data-theme]) {
    color-scheme: light;
    /* Apply light theme variables */
  }
}

/* ===== SYSTEM THEME SPECIFIC STYLES ===== */
[data-theme="system"] {
  /* Use system preference */
}

@media (prefers-color-scheme: dark) {
  [data-theme="system"] {
    /* Apply dark theme */
    color-scheme: dark;
  }
}

@media (prefers-color-scheme: light) {
  [data-theme="system"] {
    /* Apply light theme */
    color-scheme: light;
  }
}
```

## 📱 **مرحله 6: Responsive Design مدرن**

### فایل `responsive.css`:

```css
/* ===== MOBILE FIRST APPROACH ===== */

/* Base styles for mobile (320px+) */
.container {
  width: 100%;
  padding-left: var(--space-4);
  padding-right: var(--space-4);
  margin-left: auto;
  margin-right: auto;
}

/* ===== BREAKPOINT SYSTEM ===== */

/* Small mobile (576px+) */
@media (min-width: 576px) {
  .container {
    max-width: 540px;
    padding-left: var(--space-6);
    padding-right: var(--space-6);
  }
  
  .grid-cols-sm-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  
  .text-sm-lg {
    font-size: var(--text-lg);
  }
}

/* Tablet (768px+) */
@media (min-width: 768px) {
  .container {
    max-width: 720px;
  }
  
  .grid-cols-md-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  
  .grid-cols-md-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  
  .hidden-md {
    display: none;
  }
  
  .block-md {
    display: block;
  }
  
  .flex-md {
    display: flex;
  }
}

/* Large tablet / Small desktop (992px+) */
@media (min-width: 992px) {
  .container {
    max-width: 960px;
  }
  
  .grid-cols-lg-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  
  .grid-cols-lg-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  
  .hidden-lg {
    display: none;
  }
  
  .block-lg {
    display: block;
  }
}

/* Desktop (1200px+) */
@media (min-width: 1200px) {
  .container {
    max-width: 1140px;
  }
  
  .grid-cols-xl-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  
  .grid-cols-xl-5 {
    grid-template-columns: repeat(5, minmax(0