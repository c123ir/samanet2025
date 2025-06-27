# 🔍 MANDATORY: Advanced Real-Time Search & Filter System - UPDATED
## Professional Component-Based Search Implementation for All Pages

**Version:** 2.0 Component-Based Professional  
**Status:** ✅ ACTIVE - Auto-Apply to ALL Search/Filter Features  
**Purpose:** Standardized advanced search using reusable AdvancedSearch component

---

## 🎯 **CRITICAL IMPLEMENTATION RULES**

### **MANDATORY: Use AdvancedSearch Component for EVERY search/filter functionality**

When implementing ANY search or filter feature, you MUST use this component-based system:

1. ✅ **AdvancedSearch PHP Helper Class** for backend
2. ✅ **AdvancedSearch JavaScript Component** for frontend  
3. ✅ **Advanced-search.css** for styling
4. ✅ **Real-time search** with 200ms debouncing
5. ✅ **Multi-word search** with space separator AND logic
6. ✅ **Results highlighting** with `<mark>` tags
7. ✅ **Live statistics** and search terms display
8. ✅ **Keyboard shortcuts** (ESC clear, Enter navigate)
9. ✅ **Mobile-responsive** design with touch support
10. ✅ **Error handling** and loading states

---

## 🔧 **MANDATORY Backend Implementation**

### **1. Include AdvancedSearch Helper (REQUIRED):**
```php
<?php
// در ابتدای Controller
require_once __DIR__ . '/../helpers/AdvancedSearch.php';

class YourController extends BaseController
{
    // Controller code...
}
```

### **2. Model Implementation (REQUIRED):**
```php
/**
 * MANDATORY: Use AdvancedSearch Helper in Model
 */
class YourModel extends Database 
{
    protected $table = 'your_table';
    
    /**
     * جستجوی پیشرفته با استفاده از AdvancedSearch Helper
     */
    public function searchWithFilters(string $search = '', array $filters = []): array 
    {
        // تعریف فیلدهای قابل جستجو - CUSTOMIZE برای هر مدل
        $searchFields = [
            't.title',           // عنوان
            't.id',             // شناسه  
            't.description',    // توضیحات
            't.code',           // کد (در صورت وجود)
            't.created_at'      // تاریخ
        ];
        
        // تعریف joins در صورت نیاز
        $joins = [
            [
                'type' => 'LEFT',
                'table' => 'users u',
                'condition' => 't.created_by = u.id',
                'select' => 'u.full_name as creator_name'
            ]
        ];
        
        // استفاده از AdvancedSearch Helper
        require_once __DIR__ . '/../helpers/AdvancedSearch.php';
        return AdvancedSearch::performSearch(
            $this,
            $this->table . ' t',
            $search,
            $searchFields,
            $filters,
            $joins,
            'created_at',
            'DESC'
        );
    }
}
```

### **3. Controller API Endpoint (REQUIRED):**
```php
/**
 * MANDATORY: API endpoint using AdvancedSearch Helper
 */
public function api(): void
{
    header('Content-Type: application/json');
    
    try {
        $search = trim($_GET['search'] ?? '');
        $filters = [
            'status' => $_GET['status'] ?? 'all',
            'category' => $_GET['category'] ?? 'all'
            // Add more filters as needed
        ];
        
        // اعتبارسنجی پارامترها
        $validation = AdvancedSearch::validateSearchParams(
            $search,
            ['status', 'category'], // فیلترهای مجاز
            $filters
        );
        
        if (!$validation['valid']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => implode(', ', $validation['errors'])
            ]);
            return;
        }
        
        // جستجو
        $results = $this->model->searchWithFilters(
            $validation['sanitized_search'],
            $validation['sanitized_filters']
        );
        
        // پردازش نتایج برای highlighting
        $processedResults = AdvancedSearch::processSearchResults(
            $results,
            $search,
            ['title', 'description', 'creator_name'] // فیلدهای قابل highlight
        );
        
        // تولید پاسخ استاندارد
        $response = AdvancedSearch::generateApiResponse(
            $processedResults,
            $search,
            [
                'page_type' => 'your_page_type',
                'filters_applied' => array_filter($filters, function($v) { return $v !== 'all'; })
            ]
        );
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        error_log("Search API Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'خطا در جستجو'
        ]);
    }
}
```

---

## 🎨 **MANDATORY Frontend Implementation**

### **1. Include Required Files (REQUIRED):**
```php
<?php
// در View یا Layout
$this->addCSS('advanced-search'); // فایل CSS کامپوننت
$this->addJS('advanced-search');  // فایل JavaScript کامپوننت
?>

<!-- یا مستقیماً -->
<link rel="stylesheet" href="<?= url('assets/css/advanced-search.css') ?>">
<script src="<?= url('assets/js/advanced-search.js') ?>"></script>
```

### **2. HTML Structure (REQUIRED):**
```html
<!-- MANDATORY: Search and Results containers -->
<div class="container-fluid">
    <!-- Search Container - کامپوننت اینجا ایجاد می‌شود -->
    <div class="card mb-3">
        <div class="card-body">
            <div id="searchContainer">
                <!-- AdvancedSearch Component will be created here -->
            </div>
        </div>
    </div>
    
    <!-- Results Container -->
    <div class="card">
        <div class="card-header">
            <h5>نتایج جستجو</h5>
        </div>
        <div class="card-body p-0">
            <div id="searchResults">
                <!-- Dynamic results will be displayed here -->
            </div>
        </div>
    </div>
</div>
```

### **3. JavaScript Implementation (REQUIRED):**
```javascript
/**
 * MANDATORY: Initialize AdvancedSearch Component
 */
document.addEventListener('DOMContentLoaded', function() {
    // راه‌اندازی AdvancedSearch Component
    const searchSystem = new AdvancedSearch({
        // تنظیمات اصلی
        apiUrl: '<?= url('your-route?action=api') ?>', // CUSTOMIZE: API URL
        containerSelector: '#searchContainer',
        resultsSelector: '#searchResults',
        
        // تنظیمات UI
        placeholder: 'جستجو در [موضوع]... (مثال: کلمه کلیدی نمونه)',
        helpText: 'با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید | ESC برای پاک کردن',
        enableStats: true,
        enableKeyboardShortcuts: true,
        
        // فیلدهای قابل highlighting
        highlightFields: ['title', 'description', 'creator_name'],
        
        // CUSTOMIZE: Custom result renderer برای نمایش نتایج
        customResultRenderer: function(results) {
            if (!results || results.length === 0) {
                return '<div class="text-center p-4 text-muted">نتیجه‌ای یافت نشد</div>';
            }
            
            let html = '';
            results.forEach(item => {
                html += `
                    <div class="result-item p-3 border-bottom" data-id="${item.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.title_highlighted || item.title}</h6>
                                <p class="mb-1 text-muted small">
                                    ${item.description_highlighted || item.description || ''}
                                </p>
                                <small class="text-muted">
                                    شناسه: #${item.id} | 
                                    تاریخ: ${this.formatDate(item.created_at)} |
                                    ایجاد: ${item.creator_name || 'سیستم'}
                                </small>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?id=${item.id}" class="btn btn-outline-primary" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteItem(${item.id})" class="btn btn-outline-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return html;
        },
        
        // Event Callbacks
        onSearchStart: function(query) {
            console.log('🔍 جستجو شروع شد:', query);
        },
        
        onSearchComplete: function(data, query) {
            console.log('✅ جستجو تکمیل شد:', data.total, 'نتیجه یافت شد');
            
            // Update any additional UI elements
            document.querySelector('.results-count')?.textContent = data.total;
        },
        
        onError: function(error, query) {
            console.error('❌ خطا در جستجو:', error);
            
            // Show custom error message
            this.elements.resultsContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h6>خطا در جستجو</h6>
                    <p class="mb-0">لطفاً دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.</p>
                </div>
            `;
        }
    });
    
    // دسترسی global برای debugging و extend کردن
    window.searchSystem = searchSystem;
    
    console.log('✅ AdvancedSearch Component initialized successfully');
});
```

---

## 🚀 **QUICK START GUIDE**

### **مرحله 1: Backend Setup (5 دقیقه)**
```php
// 1. در Model خود
public function searchWithFilters(string $search = '', array $filters = []): array {
    $searchFields = ['t.title', 't.id']; // فیلدهای جستجو
    $joins = []; // joins مورد نیاز
    
    require_once __DIR__ . '/../helpers/AdvancedSearch.php';
    return AdvancedSearch::performSearch($this, $this->table . ' t', $search, $searchFields, $filters, $joins);
}

// 2. در Controller خود API endpoint اضافه کنید
public function api(): void {
    $search = trim($_GET['search'] ?? '');
    $results = $this->model->searchWithFilters($search, []);
    $processed = AdvancedSearch::processSearchResults($results, $search, ['title']);
    echo json_encode(AdvancedSearch::generateApiResponse($processed, $search));
}
```

### **مرحله 2: Frontend Setup (3 دقیقه)**
```html
<!-- HTML -->
<div id="searchContainer"></div>
<div id="searchResults"></div>

<!-- CSS & JS -->
<link rel="stylesheet" href="/assets/css/advanced-search.css">
<script src="/assets/js/advanced-search.js"></script>

<!-- Initialize -->
<script>
const search = new AdvancedSearch({
    apiUrl: '/api/your-endpoint',
    containerSelector: '#searchContainer',
    resultsSelector: '#searchResults'
});
</script>
```

### **مرحله 3: Customize (2 دقیقه)**
```javascript
// تنظیم renderer برای نمایش داده‌های خود
customResultRenderer: function(results) {
    return results.map(item => `
        <div class="result-item">
            <h6>${item.title_highlighted || item.title}</h6>
            <!-- نمایش سایر فیلدهای مورد نیاز -->
        </div>
    `).join('');
}
```

---

## 📁 **REQUIRED FILES CHECKLIST**

### **✅ Files to Include:**
- [ ] `app/helpers/AdvancedSearch.php` - Backend Helper Class
- [ ] `assets/js/advanced-search.js` - Frontend Component  
- [ ] `assets/css/advanced-search.css` - Styling System
- [ ] Your Model with `searchWithFilters()` method
- [ ] Your Controller with `api()` endpoint
- [ ] Your View with proper HTML structure

### **✅ Dependencies:**
- [ ] Bootstrap 5.3.0+ (for base styling)
- [ ] FontAwesome 6.4.0+ (for icons)
- [ ] Modern browser with ES6+ support

---

## 🎯 **IMPLEMENTATION EXAMPLES**

### **Example 1: Tags Management**
```php
// TagController.php
require_once __DIR__ . '/../helpers/AdvancedSearch.php';

public function api(): void {
    $search = trim($_GET['search'] ?? '');
    $results = $this->tagModel->searchWithFilters($search, []);
    $processed = AdvancedSearch::processSearchResults($results, $search, ['title', 'creator_name']);
    echo json_encode(AdvancedSearch::generateApiResponse($processed, $search));
}
```

```javascript
// Frontend
const tagSearch = new AdvancedSearch({
    apiUrl: '/index.php?route=tags&action=api',
    containerSelector: '#searchContainer',
    resultsSelector: '#searchResults',
    placeholder: 'جستجو در تگ‌ها... (مثال: فوری قرمز، مهم 1404)',
    highlightFields: ['title', 'creator_name']
});
```

### **Example 2: Users Management**
```php
// UserController.php
public function api(): void {
    $search = trim($_GET['search'] ?? '');
    $filters = ['status' => $_GET['status'] ?? 'all'];
    $results = $this->userModel->searchWithFilters($search, $filters);
    $processed = AdvancedSearch::processSearchResults($results, $search, ['full_name', 'email', 'username']);
    echo json_encode(AdvancedSearch::generateApiResponse($processed, $search));
}
```

```javascript
// Frontend
const userSearch = new AdvancedSearch({
    apiUrl: '/index.php?route=users&action=api',
    containerSelector: '#searchContainer',
    resultsSelector: '#searchResults',
    placeholder: 'جستجو در کاربران... (مثال: احمد مدیر، تهران فعال)',
    highlightFields: ['full_name', 'email', 'username']
});
```

---

## ✅ **IMPLEMENTATION CHECKLIST**

### **✅ Backend Requirements:**
- [ ] `AdvancedSearch.php` helper included
- [ ] Model has `searchWithFilters()` method
- [ ] Controller has `api()` endpoint  
- [ ] Search fields properly defined
- [ ] Joins configured (if needed)
- [ ] Validation implemented
- [ ] Error handling added

### **✅ Frontend Requirements:**
- [ ] `advanced-search.css` included
- [ ] `advanced-search.js` included
- [ ] HTML containers present (`#searchContainer`, `#searchResults`)
- [ ] Component initialized in JavaScript
- [ ] Custom result renderer implemented
- [ ] Event callbacks configured
- [ ] Mobile responsiveness tested

### **✅ Feature Verification:**
- [ ] Real-time search works (200ms debounce)
- [ ] Multi-word search functions correctly
- [ ] Results highlighting displays
- [ ] Live statistics show
- [ ] Clear button functions
- [ ] ESC key clears search
- [ ] Enter key navigates to first result
- [ ] Mobile touch interactions work
- [ ] Loading states display
- [ ] Error handling works
- [ ] Empty state shows correctly

---

**🔍 Component Status:** ✅ PRODUCTION READY - Universal Search Solution  
**📅 Version:** 2.0 Component-Based Professional  
**🎯 Coverage:** All search and filtering features across entire project  
**💡 Key Innovation:** Reusable component-based architecture with enterprise-grade features

---

## 📚 **ADDITIONAL RESOURCES**

- **Full Documentation:** `/docs/AdvancedSearchUsage.md`
- **Example Implementation:** `/app/views/users/advanced-search-example.php`
- **Component Source:** `/assets/js/advanced-search.js`
- **Styling System:** `/assets/css/advanced-search.css`
- **Backend Helper:** `/app/helpers/AdvancedSearch.php`

**Ready to implement? Start with the Quick Start Guide above! 🚀** 