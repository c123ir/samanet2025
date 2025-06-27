# 🔍 راهنمای استفاده از سیستم جستجوی پیشرفته

## مقدمه

این سیستم امکان اضافه کردن جستجوی چندکلمه‌ای، real-time و با highlighting را به هر صفحه‌ای از پروژه می‌دهد.

## 🏗️ معماری سیستم

سیستم شامل سه بخش اصلی است:

1. **Backend Helper**: `app/helpers/AdvancedSearch.php`
2. **Frontend Component**: `assets/js/advanced-search.js`  
3. **CSS Styles**: `assets/css/advanced-search.css`

---

## 🚀 راه‌اندازی در یک صفحه جدید

### مرحله 1: Backend Setup

#### در Model:
```php
<?php
// app/models/YourModel.php

class YourModel extends Database 
{
    protected $table = 'your_table';
    
    /**
     * جستجوی پیشرفته با استفاده از AdvancedSearch Helper
     */
    public function searchWithFilters(string $search = '', array $filters = []): array 
    {
        $searchFields = ['t.title', 't.id', 't.description'];
        $joins = [
            [
                'type' => 'LEFT',
                'table' => 'users u',
                'condition' => 't.created_by = u.id',
                'select' => 'u.full_name as creator_name'
            ]
        ];
        
        return \App\Helpers\AdvancedSearch::performSearch(
            $this, $this->table . ' t', $search, $searchFields, $filters, $joins
        );
    }
}
```

#### در Controller:
```php
<?php
// app/controllers/YourController.php

class YourController extends BaseController 
{
    /**
     * API جستجو
     */
    public function api(): void
    {
        header('Content-Type: application/json');
        
        try {
            $search = trim($_GET['search'] ?? '');
            $filters = ['status' => $_GET['status'] ?? 'all'];
            
            // اعتبارسنجی
            $validation = \App\Helpers\AdvancedSearch::validateSearchParams($search, ['status'], $filters);
            
            if (!$validation['valid']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => implode(', ', $validation['errors'])
                ]);
                return;
            }
            
            // جستجو
            $results = $this->model->searchWithFilters($validation['sanitized_search'], $validation['sanitized_filters']);
            
            // highlighting
            $processedResults = \App\Helpers\AdvancedSearch::processSearchResults($results, $search, ['title', 'description']);
            
            // پاسخ استاندارد
            $response = \App\Helpers\AdvancedSearch::generateApiResponse($processedResults, $search);
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            error_log("Search API Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'خطا در جستجو']);
        }
    }
}
```

### مرحله 2: Frontend Setup

#### 2.1. HTML View:
```php
<?php
// app/views/your-page/list.php

// Load CSS
$this->addCSS('advanced-search');

// Load JS
$this->addJS('advanced-search');
?>

<div class="container-fluid">
    <!-- Search Container -->
    <div class="card mb-3">
        <div class="card-body">
            <div id="searchContainer">
                <!-- AdvancedSearch کامپوننت اینجا ایجاد می‌شود -->
            </div>
        </div>
    </div>
    
    <!-- Results Container -->
    <div class="card">
        <div class="card-header">
            <h5>نتایج</h5>
        </div>
        <div class="card-body p-0">
            <div id="searchResults">
                <!-- نتایج اینجا نمایش داده می‌شوند -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // راه‌اندازی AdvancedSearch
    const searchSystem = new AdvancedSearch({
        apiUrl: '<?= url('your-route?action=api') ?>',
        containerSelector: '#searchContainer',
        resultsSelector: '#searchResults',
        placeholder: 'جستجو در آیتم‌ها... (مثال: عنوان تاریخ، نام سازنده)',
        helpText: 'با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید',
        highlightFields: ['title', 'description'],
        
        // Renderer سفارشی برای نتایج
        customResultRenderer: function(results) {
            return results.map(item => `
                <div class="result-item p-3 border-bottom" data-id="${item.id}">
                    <div class="d-flex justify-content-between">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.title_highlighted || item.title}</h6>
                            <p class="mb-1 text-muted">${item.description_highlighted || item.description || ''}</p>
                            <small class="text-muted">
                                #${item.id} | ${item.created_at} | ${item.creator_name || 'نامشخص'}
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="btn-group btn-group-sm">
                                <a href="edit.php?id=${item.id}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteItem(${item.id})" class="btn btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        },
        
        // Callbacks
        onSearchStart: function(query) {
            console.log('جستجو شروع شد:', query);
        },
        
        onSearchComplete: function(data, query) {
            console.log('جستجو تکمیل شد:', data.total, 'نتیجه');
        },
        
        onError: function(error, query) {
            console.error('خطا در جستجو:', error);
        }
    });
    
    // دسترسی global برای debugging
    window.searchSystem = searchSystem;
});
</script>
```

#### 2.2. CSS اضافی (در صورت نیاز):
```css
/* app/assets/css/your-page.css */

/* استایل‌های سفارشی اضافی */
.result-item .btn-group {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.result-item:hover .btn-group {
    opacity: 1;
}

/* Custom highlighting برای این صفحه */
.your-page mark {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb) !important;
    color: #1565c0 !important;
}
```

---

## 💡 مزایای استفاده

- ✅ **صرفه‌جویی زمان**: کدنویسی یکبار، استفاده همه‌جا
- ✅ **یکسان‌سازی**: UI/UX یکسان در تمام صفحات  
- ✅ **نگهداری آسان**: تغییرات در یک مکان
- ✅ **قابلیت‌های پیشرفته**: highlighting، keyboard shortcuts، validation
- ✅ **بهینه‌سازی**: debouncing، caching، error handling
- ✅ **Mobile Ready**: responsive و touch-friendly

## 🎯 نتیجه‌گیری

با این سیستم می‌توانید در کمتر از 10 خط کد، جستجوی حرفه‌ای به هر صفحه‌ای اضافه کنید!

---

## 🔧 تنظیمات پیشرفته

### تنظیم searchFields پیچیده:
```php
$searchFields = [
    't.title',
    't.code',
    'CONCAT(t.first_name, " ", t.last_name)', // ترکیب فیلدها
    'CASE WHEN t.status = "active" THEN "فعال" ELSE "غیرفعال" END', // شرطی
    'DATE_FORMAT(t.created_at, "%Y/%m/%d")', // فرمت تاریخ
];
```

### تنظیم Filters پیشرفته:
```php
$filters = [
    't.status' => $_GET['status'] ?? 'all',
    't.category_id' => $_GET['category'] ?? 'all',
    'DATE(t.created_at)' => $_GET['date'] ?? 'all'
];
```

### Joins پیچیده:
```php
$joins = [
    [
        'type' => 'LEFT',
        'table' => 'categories c',
        'condition' => 't.category_id = c.id',
        'select' => 'c.name as category_name'
    ],
    [
        'type' => 'INNER',
        'table' => 'users u',
        'condition' => 't.user_id = u.id AND u.active = 1',
        'select' => 'u.full_name as user_name, u.email as user_email'
    ]
];
```

---

## 📱 تنظیمات Responsive

### Mobile-First Configuration:
```javascript
const searchConfig = {
    // تنظیمات معمولی...
    
    // تنظیمات موبایل
    mobileBreakpoint: 768,
    mobileOptions: {
        debounceDelay: 300, // تاخیر بیشتر برای موبایل
        placeholder: 'جستجو...', // متن کوتاه‌تر
        enableStats: false, // مخفی کردن آمار در موبایل
    }
};

// اعمال تنظیمات موبایل
if (window.innerWidth <= searchConfig.mobileBreakpoint) {
    Object.assign(searchConfig, searchConfig.mobileOptions);
}

const search = new AdvancedSearch(searchConfig);
```

---

## 🎨 سفارشی‌سازی UI

### رنگ‌بندی سفارشی:
```css
:root {
    --search-primary: #your-brand-color;
    --search-secondary: #your-secondary-color;
}

.advanced-search-container {
    --primary: var(--search-primary);
}

.search-stats {
    background: rgba(var(--search-primary-rgb), 0.1);
    border-left-color: var(--search-primary);
}
```

### Template سفارشی برای Empty State:
```javascript
const search = new AdvancedSearch({
    // سایر تنظیمات...
    
    emptyStateTemplate: function() {
        return `
            <div class="empty-state">
                <img src="/assets/img/no-results.svg" alt="نتیجه‌ای یافت نشد" width="120">
                <h5>هیچ آیتمی یافت نشد</h5>
                <p>ممکن است بخواهید:</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> کلمات کلیدی دیگری امتحان کنید</li>
                    <li><i class="fas fa-check text-success"></i> فیلترها را تغییر دهید</li>
                    <li><i class="fas fa-check text-success"></i> یک آیتم جدید اضافه کنید</li>
                </ul>
                <button class="btn btn-primary" onclick="window.location.href='/add'">
                    <i class="fas fa-plus me-2"></i>افزودن آیتم جدید
                </button>
            </div>
        `;
    }
});
```

---

## ⚡ بهینه‌سازی Performance

### Database Indexing:
```sql
-- ایندکس‌های پیشنهادی برای بهبود عملکرد جستجو
CREATE INDEX idx_table_search ON your_table(title, status, created_at);
CREATE INDEX idx_table_fulltext ON your_table(title, description) USING FULLTEXT;
```

### Caching Results:
```php
// در Controller
public function api(): void 
{
    $cacheKey = 'search_' . md5(serialize($_GET));
    
    // بررسی cache
    if ($cached = Cache::get($cacheKey)) {
        echo $cached;
        return;
    }
    
    // انجام جستجو
    $response = $this->performSearch();
    
    // ذخیره در cache (5 دقیقه)
    Cache::set($cacheKey, $response, 300);
    
    echo $response;
}
```

---

## 🧪 Testing

### Unit Test نمونه:
```php
class AdvancedSearchTest extends TestCase 
{
    public function testPerformSearch()
    {
        $model = new YourModel();
        
        $results = $model->searchWithFilters('test query', [
            'status' => 'active'
        ]);
        
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
    }
    
    public function testHighlighting()
    {
        $results = [['title' => 'Test Title']];
        $processed = \App\Helpers\AdvancedSearch::processSearchResults(
            $results, 
            'test', 
            ['title']
        );
        
        $this->assertStringContains('<mark>test</mark>', $processed[0]['title_highlighted']);
    }
}
```

---

## 🔧 Troubleshooting

### مشکلات رایج:

1. **جستجو کار نمی‌کند**:
   - بررسی کنید API URL صحیح باشد
   - Console browser را چک کنید
   - مسیر فایل‌های CSS/JS را بررسی کنید

2. **Highlighting نمایش داده نمی‌شود**:
   - فیلدهای `highlightFields` را بررسی کنید
   - CSS برای `mark` تگ‌ها را چک کنید

3. **عملکرد کند**:
   - ایندکس‌های دیتابیس را بررسی کنید
   - `debounceDelay` را افزایش دهید
   - Caching پیاده‌سازی کنید

4. **مشکلات موبایل**:
   - Viewport meta tag را چک کنید
   - فونت‌سایز input حداقل 16px باشد
   - Touch target size کافی باشد

---

## 📚 منابع بیشتر

- [CSS Documentation](../assets/css/advanced-search.css)
- [JavaScript API Reference](../assets/js/advanced-search.js)
- [Helper Class Reference](../app/helpers/AdvancedSearch.php)

---

**نسخه:** 1.0  
**تاریخ:** 1404/03/31  
**مولف:** Cursor AI + Developer Team 