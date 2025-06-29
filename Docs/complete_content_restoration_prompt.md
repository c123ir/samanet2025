# 🚀 مأموریت کامل بازسازی محتوای پروژه سامانت
## راهنمای جامع بازیابی و بهینه‌سازی صفحات اصلی

**هدف:** بازسازی کامل محتوای واقعی صفحات با حفظ UI/UX جدید Enterprise Grade

---

## 🎯 **تحلیل مشکلات فعلی**

### ❌ **مشکلات شناسایی شده:**
1. **محتوای تکراری** در همه صفحات
2. **عدم اتصال به دیتابیس** واقعی
3. **جداول فیک** بدون داده واقعی
4. **عدم وجود جستجوی پیشرفته** در صفحات
5. **مسیربندی نادرست** بین صفحات
6. **نبود دکمه‌های عملیاتی** واقعی

---

## 📋 **دیتابیس Schema موجود**

### **جداول اصلی پروژه:**
```sql
-- کاربران
users: id, username, password, full_name, email, phone, role, group_id, status, avatar, last_login, created_at

-- گروه‌های کاری  
user_groups: id, name, description, permissions, parent_id, created_by, created_at

-- درخواست‌های حواله
payment_requests: id, reference_number, requester_id, group_id, title, description, amount, account_number, account_holder, bank_name, priority, status, tags, created_at

-- تگ‌های سیستم
tags: id, title, color_start, color_end, text_color, usage_count, created_by, created_at
```

---

## 🎨 **UI/UX Standards (حفظ شود)**

### **Enterprise Grade Components:**
- ✅ **Layout System:** 60px header + 280px sidebar + mobile bottom nav
- ✅ **Color System:** Professional gray palette با primary #5E3AEE
- ✅ **Typography:** Vazirmatn font با proper hierarchy
- ✅ **Dark/Light Theme:** کاملاً functional با localStorage
- ✅ **Responsive Design:** Perfect mobile-first از 320px تا 4K
- ✅ **Animation System:** Smooth transitions و micro-interactions

### **CSS Classes الزامی (استفاده ادامه یابد):**
```css
.dashboard-pro, .stat-card-pro, .data-table, .search-container, 
.page-header, .quick-actions, .theme-toggle, .mobile-nav
```

---

## 🔧 **مأموریت 1: بازسازی صفحه Dashboard**

### **محتوای مطلوب:**
```php
// app/views/dashboard/index.php
// داده‌های واقعی از دیتابیس
$totalUsers = User::count();
$totalRequests = PaymentRequest::count(); 
$pendingRequests = PaymentRequest::countByStatus('pending');
$totalAmount = PaymentRequest::getTotalAmount();
$recentRequests = PaymentRequest::getRecent(5);
$popularTags = Tag::getPopular(6);
```

### **Stats Cards واقعی:**
1. **کل کاربران** - با آیکون fas fa-users
2. **کل درخواست‌ها** - با آیکون fas fa-file-alt  
3. **درخواست‌های در انتظار** - با آیکون fas fa-clock
4. **مجموع مبالغ** - با آیکون fas fa-money-bill-wave

### **Widgets مطلوب:**
1. **آخرین درخواست‌ها** - جدول 5 درخواست اخیر
2. **تگ‌های محبوب** - نمایش 6 تگ پربازدید
3. **Quick Actions** - دکمه‌های میانبر برای عملیات سریع
4. **آمار ماهانه** - نمودار یا آمار عملکرد

---

## 🏷️ **مأموریت 2: صفحه مدیریت تگ‌ها**

### **فایل‌های موجود قبلی:**
```
app/views/tags/
├── list.php     # لیست تگ‌ها با جستجو
├── create.php   # ایجاد تگ جدید
├── edit.php     # ویرایش تگ
└── api.php      # API endpoint
```

### **قابلیت‌های مطلوب:**
1. **جستجوی پیشرفته چندسیلابی** - با کلاس AdvancedSearch موجود
2. **جدول تگ‌ها** با ستون‌های:
   - شناسه تگ
   - عنوان تگ  
   - پیش‌نمایش (با gradient background)
   - تعداد استفاده
   - ایجادکننده
   - تاریخ ایجاد
   - عملیات (ویرایش/حذف)

3. **دکمه افزودن تگ جدید** - لینک به صفحه create
4. **Color Picker** در صفحه ایجاد/ویرایش
5. **Preview تگ** در real-time

### **کد الزامی برای جستجو:**
```javascript
// assets/js/tags-search.js
const search = new AdvancedSearch({
    apiUrl: '/tags/api',
    containerSelector: '#tagSearchContainer',
    resultsSelector: '#tagResults'
});
```

---

## 👥 **مأموریت 3: صفحه مدیریت کاربران**

### **محتوای مطلوب:**
```php
// app/views/users/index.php
$users = User::getAllWithGroups();
$groups = UserGroup::getAll();
$tags = Tag::getAll(); // برای تخصیص تگ
```

### **جدول کاربران:**
| ستون | محتوا |
|------|-------|
| شناسه | ID کاربر |
| نام کاربری | username |
| نام کامل | full_name |
| ایمیل | email |
| نقش | role (admin/manager/user) |
| گروه | group_name |
| تگ‌ها | نمایش تگ‌های کاربر + دکمه اضافه |
| وضعیت | active/inactive |
| عملیات | ویرایش/حذف |

### **قابلیت‌های جدید:**
1. **تخصیص تگ به کاربر** - modal popup برای انتخاب
2. **جستجوی پیشرفته** در کاربران
3. **فیلتر بر اساس گروه** و نقش
4. **دکمه افزودن کاربر جدید**

### **مدال تخصیص تگ:**
```html
<div class="modal" id="assignTagModal">
    <div class="modal-content">
        <h3>تخصیص تگ به کاربر</h3>
        <div class="tag-selector">
            <!-- چک‌باکس تگ‌ها -->
        </div>
        <button class="btn-primary">ذخیره</button>
    </div>
</div>
```

---

## 📄 **مأموریت 4: صفحه درخواست‌ها**

### **فایل‌های موجود قبلی:**
```
app/views/requests/
├── index.php    # لیست درخواست‌ها
├── create.php   # ایجاد درخواست
├── edit.php     # ویرایش درخواست
├── view.php     # نمایش جزئیات
└── api.php      # API endpoint
```

### **جدول درخواست‌ها:**
| ستون | محتوا |
|------|-------|
| شماره مرجع | reference_number |
| عنوان | title |
| درخواست‌کننده | requester name |
| مبلغ | amount (با جداکننده هزارگان) |
| وضعیت | status badge |
| اولویت | priority badge |
| تگ‌ها | نمایش تگ‌های درخواست |
| تاریخ | created_at |
| عملیات | نمایش/ویرایش/حذف |

### **فیلدهای فرم ایجاد/ویرایش:**
```php
// اضافه کردن فیلد تگ به فرم
<div class="form-group">
    <label>تگ‌ها</label>
    <select name="tags[]" multiple class="tag-selector">
        <?php foreach($tags as $tag): ?>
            <option value="<?= $tag['id'] ?>"><?= $tag['title'] ?></option>
        <?php endforeach; ?>
    </select>
</div>
```

### **Database Update مطلوب:**
```sql
-- اگر فیلد tags در جدول payment_requests JSON نیست:
ALTER TABLE payment_requests 
MODIFY COLUMN tags JSON NULL;

-- یا ایجاد جدول رابطه many-to-many:
CREATE TABLE request_tags (
    request_id INT,
    tag_id INT,
    PRIMARY KEY (request_id, tag_id)
);
```

---

## 🔍 **مأموریت 5: پیاده‌سازی جستجوی پیشرفته**

### **فایل‌های موجود:**
```
app/helpers/AdvancedSearch.php     # کلاس جستجو
assets/js/advanced-search.js       # Frontend
assets/css/advanced-search.css     # Styles
```

### **اضافه کردن به همه صفحات:**

#### **در Models:**
```php
// app/models/Tag.php
public function searchWithFilters(string $search = '', array $filters = []): array {
    $searchFields = ['title', 'id'];
    require_once __DIR__ . '/../helpers/AdvancedSearch.php';
    return AdvancedSearch::performSearch($this, $this->table, $search, $searchFields, $filters);
}

// app/models/User.php  
public function searchWithFilters(string $search = '', array $filters = []): array {
    $searchFields = ['username', 'full_name', 'email'];
    // JOIN with groups
    $joins = ['LEFT JOIN user_groups ug ON users.group_id = ug.id'];
    require_once __DIR__ . '/../helpers/AdvancedSearch.php';
    return AdvancedSearch::performSearch($this, $this->table, $search, $searchFields, $filters, $joins);
}

// app/models/PaymentRequest.php
public function searchWithFilters(string $search = '', array $filters = []): array {
    $searchFields = ['title', 'description', 'reference_number'];
    $joins = ['LEFT JOIN users u ON payment_requests.requester_id = u.id'];
    require_once __DIR__ . '/../helpers/AdvancedSearch.php';
    return AdvancedSearch::performSearch($this, $this->table, $search, $searchFields, $filters, $joins);
}
```

#### **در Controllers:**
```php
// در همه کنترلرها اضافه کن:
public function api(): void {
    header('Content-Type: application/json');
    $search = trim($_GET['search'] ?? '');
    $results = $this->model->searchWithFilters($search, $_GET);
    $processed = AdvancedSearch::processSearchResults($results, $search, ['title', 'name', 'full_name']);
    echo json_encode(AdvancedSearch::generateApiResponse($processed, $search));
    exit;
}
```

#### **در Views:**
```html
<!-- در بالای هر صفحه لیست -->
<div id="searchContainer" class="search-container mb-4"></div>
<div id="searchResults"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new AdvancedSearch({
        apiUrl: '<?= getCurrentRoute() ?>/api',
        containerSelector: '#searchContainer',
        resultsSelector: '#searchResults',
        targetTable: 'dataTable'
    });
});
</script>
```

---

## 🛠️ **مأموریت 6: بهبود Routing System**

### **فایل‌های routing مطلوب:**
```php
// app/routes/web.php (اگر وجود ندارد ایجاد کن)
$routes = [
    'dashboard' => 'DashboardController@index',
    'tags' => 'TagController@index', 
    'tags/create' => 'TagController@create',
    'tags/edit' => 'TagController@edit',
    'tags/api' => 'TagController@api',
    'users' => 'UserController@index',
    'users/create' => 'UserController@create', 
    'users/edit' => 'UserController@edit',
    'users/api' => 'UserController@api',
    'requests' => 'RequestController@index',
    'requests/create' => 'RequestController@create',
    'requests/edit' => 'RequestController@edit', 
    'requests/view' => 'RequestController@view',
    'requests/api' => 'RequestController@api'
];
```

### **بروزرسانی index.php:**
```php
// index.php
$route = $_GET['route'] ?? 'dashboard';

// حذف trailing slashes
$route = rtrim($route, '/');

// بارگذاری routes
require_once 'app/routes/web.php';

if (isset($routes[$route])) {
    [$controller, $action] = explode('@', $routes[$route]);
    $controllerFile = "app/controllers/{$controller}.php";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $instance = new $controller();
        $instance->$action();
    } else {
        // 404 error
        http_response_code(404);
        require 'app/views/errors/404.php';
    }
} else {
    // Default redirect to dashboard
    header('Location: /?route=dashboard');
    exit;
}
```

---

## 📱 **مأموریت 7: بهبود Mobile Navigation**

### **در main.php:**
```html
<!-- Mobile Bottom Navigation - بروزرسانی لینک‌ها -->
<nav class="mobile-bottom-nav">
    <a href="/?route=dashboard" class="mobile-nav-item <?= ($current_route === 'dashboard') ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>داشبورد</span>
    </a>
    
    <a href="/?route=requests" class="mobile-nav-item <?= (strpos($current_route, 'request') === 0) ? 'active' : '' ?>">
        <i class="fas fa-file-alt"></i>
        <span>درخواست‌ها</span>
    </a>
    
    <a href="/?route=requests/create" class="mobile-nav-item main-item">
        <i class="fas fa-plus"></i>
        <span>افزودن</span>
    </a>
    
    <a href="/?route=tags" class="mobile-nav-item <?= (strpos($current_route, 'tag') === 0) ? 'active' : '' ?>">
        <i class="fas fa-tags"></i>
        <span>تگ‌ها</span>
    </a>
    
    <a href="/?route=users" class="mobile-nav-item <?= (strpos($current_route, 'user') === 0) ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>کاربران</span>
    </a>
</nav>
```

---

## 🎯 **مأموریت 8: اتصال صحیح به دیتابیس**

### **در همه Models:**
```php
// app/models/BaseModel.php - بررسی وجود
class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // متدهای CRUD پایه
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE deleted_at IS NULL";
        return $this->db->query($sql)->fetchColumn();
    }
}
```

### **در Controllers:**
```php
// همه کنترلرها باید از دیتابیس واقعی استفاده کنند
class DashboardController extends BaseController {
    public function index() {
        // داده‌های واقعی از دیتابیس
        $userModel = new User();
        $requestModel = new PaymentRequest();
        $tagModel = new Tag();
        
        $data = [
            'totalUsers' => $userModel->count(),
            'totalRequests' => $requestModel->count(),
            'pendingRequests' => $requestModel->countByStatus('pending'),
            'recentRequests' => $requestModel->getRecent(5),
            'popularTags' => $tagModel->getPopular(6)
        ];
        
        $this->view('dashboard/index', $data);
    }
}
```

---

## ✅ **چک‌لیست تکمیل**

### **مرحله 1: بازسازی محتوا**
- [ ] Dashboard با داده‌های واقعی دیتابیس
- [ ] صفحه تگ‌ها با جستجوی پیشرفته  
- [ ] صفحه کاربران با تخصیص تگ
- [ ] صفحه درخواست‌ها با فیلد تگ

### **مرحله 2: جستجوی پیشرفته**
- [ ] اضافه کردن AdvancedSearch به همه صفحات
- [ ] API endpoints برای جستجو
- [ ] Real-time search با highlighting

### **مرحله 3: Navigation**
- [ ] Routing system صحیح
- [ ] Mobile navigation links
- [ ] Breadcrumbs در صفحات

### **مرحله 4: Database Integration** 
- [ ] اتصال Models به دیتابیس
- [ ] CRUD operations واقعی
- [ ] Error handling مناسب

### **مرحله 5: UI/UX حفظ شده**
- [ ] تمام کلاس‌های Enterprise Grade
- [ ] Dark/Light theme عملکرد
- [ ] Responsive design کامل
- [ ] Animation system فعال

---

## 📋 **گزارش نهایی مطلوب**

```markdown
# 📊 گزارش بازسازی محتوای پروژه سامانت

## ✅ تکمیل شده:
- Dashboard: [✓] کامل با داده‌های واقعی
- Tags Management: [✓] لیست + جستجو + CRUD
- Users Management: [✓] لیست + تخصیص تگ + جستجو  
- Requests Management: [✓] لیست + فیلد تگ + جستجو
- Advanced Search: [✓] در همه صفحات فعال
- Database Integration: [✓] اتصال کامل
- Routing System: [✓] مسیریابی صحیح

## 📈 آمار عملکرد:
- تعداد فایل‌های بروزرسانی شده: [X] فایل
- تعداد خط کد اضافه شده: [X] خط
- تعداد خط کد حذف شده: [X] خط  
- تعداد قابلیت‌های جدید: [X] قابلیت

## 🐛 مشکلات برطرف شده:
- محتوای تکراری صفحات
- عدم اتصال به دیتابیس
- نبود جستجوی پیشرفته
- مسیریابی نادرست
- دکمه‌های غیرفعال

## 🚀 آماده برای مرحله بعد:
- [✓] سیستم مدیریت اسناد (فاز 4)
- [✓] سیستم تایید گردش کار (فاز 5)
- [✓] گزارش‌گیری پیشرفته (فاز 6)
```

---

## 🎯 **نتیجه‌گیری**

با اجرای این مأموریت:
- ✅ **محتوای واقعی** در همه صفحات نمایش داده می‌شود
- ✅ **جستجوی پیشرفته** در همه قسمت‌ها فعال می‌شود  
- ✅ **تگ‌ها** به کاربران و درخواست‌ها اختصاص پیدا می‌کند
- ✅ **UI/UX Enterprise Grade** حفظ می‌ماند
- ✅ **Database integration** کامل می‌شود
- ✅ **Navigation** صحیح عمل می‌کند

**🎊 پروژه آماده برای فاز 4 - مدیریت اسناد!**