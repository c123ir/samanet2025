<?php
/**
 * نام فایل: UserController.php
 * مسیر فایل: /app/controllers/UserController.php
 * توضیح: کنترلر مدیریت کاربران سامانت - نسخه بازسازی شده
 * تاریخ بازطراحی: 1404/10/31
 * نسخه: 4.0 کامل
 */

require_once 'BaseController.php';
require_once APP_PATH . 'models/User.php';
require_once APP_PATH . 'models/Tag.php';

class UserController extends BaseController
{
    private $userModel;
    private $tagModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->tagModel = new Tag();
        
        // بررسی دسترسی مدیریت
        if (!Security::checkPermission('manager')) {
            Security::redirect('dashboard', 'شما به این بخش دسترسی ندارید', 'error');
        }
    }

    /**
     * نمایش لیست کاربران با پشتیبانی از جستجو و فیلتر
     */
    public function index()
    {
        try {
            // دریافت فیلترها
            $filters = [
                'search' => trim($_GET['search'] ?? ''),
                'role' => $_GET['role'] ?? '',
                'status' => $_GET['status'] ?? '',
                'group_id' => $_GET['group_id'] ?? ''
            ];
            
            // دریافت کاربران
            if (!empty($filters['search'])) {
                $users = $this->userModel->searchWithFilters($filters['search'], $filters);
            } else {
                $users = $this->getAllUsersWithFilters($filters);
            }
            
            // آمار کاربران
            $stats = $this->userModel->getUserStats();
            
            // دریافت همه تگ‌ها برای تخصیص
            $tags = $this->tagModel->getAllTags();
            
            $this->render('users/index', [
                'title' => 'مدیریت کاربران',
                'page_title' => 'مدیریت کاربران',
                'page_subtitle' => 'مدیریت کاربران و نقش‌های سیستم',
                'users' => $users,
                'stats' => $stats,
                'roles' => USER_ROLES,
                'tags' => $tags,
                'filters' => $filters,
                'additional_css' => ['css/users.css']
            ]);
            
        } catch (Exception $e) {
            writeLog("خطا در نمایش لیست کاربران: " . $e->getMessage(), 'ERROR');
            
            $this->render('users/index', [
                'title' => 'مدیریت کاربران',
                'users' => [],
                'stats' => ['total' => 0, 'active' => 0, 'inactive' => 0],
                'roles' => USER_ROLES,
                'tags' => [],
                'filters' => [],
                'additional_css' => ['css/users.css']
            ]);
        }
    }

    /**
     * دریافت کاربران با فیلترها
     */
    private function getAllUsersWithFilters($filters)
    {
        $query = "SELECT u.*, ug.name as group_name 
                  FROM users u 
                  LEFT JOIN user_groups ug ON u.group_id = ug.id 
                  WHERE u.status IN ('active', 'inactive')";
        $params = [];

        // فیلتر نقش
        if (!empty($filters['role'])) {
            $query .= " AND u.role = ?";
            $params[] = $filters['role'];
        }

        // فیلتر وضعیت
        if (!empty($filters['status'])) {
            $query .= " AND u.status = ?";
            $params[] = $filters['status'];
        }

        // فیلتر گروه
        if (!empty($filters['group_id'])) {
            $query .= " AND u.group_id = ?";
            $params[] = $filters['group_id'];
        }

        $query .= " ORDER BY u.created_at DESC";

        return $this->userModel->query($query, $params)->fetchAll();
    }

    /**
     * جستجوی پیشرفته کاربران - سازگار با AdvancedSearch
     */
    public function searchWithFilters($searchTerm = '', $filters = [])
    {
        try {
            require_once APP_PATH . 'helpers/AdvancedSearch.php';
            
            // فیلدهای قابل جستجو
            $searchFields = [
                'u.username',
                'u.full_name', 
                'u.email',
                'u.phone',
                'ug.name'  // گروه کاربر
            ];
            
            // آماده کردن فیلترهای اضافی
            $additionalFilters = [];
            
            if (!empty($filters['role'])) {
                $additionalFilters['u.role'] = $filters['role'];
            }
            
            if (!empty($filters['status'])) {
                $additionalFilters['u.status'] = $filters['status'];
            }
            
            if (!empty($filters['group_id'])) {
                $additionalFilters['u.group_id'] = $filters['group_id'];
            }
            
            // تعریف join
            $joins = [
                'LEFT JOIN user_groups ug ON u.group_id = ug.id'
            ];
            
            // اجرای جستجو
            $results = AdvancedSearch::performSearch(
                $this->userModel,
                'users u',
                $searchTerm,
                $searchFields,
                $additionalFilters,
                $joins,
                'u.created_at',
                'DESC'
            );
            
            return $results;
            
        } catch (Exception $e) {
            writeLog("خطا در جستجوی پیشرفته کاربران: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * API endpoint برای جستجوی زنده کاربران
     */
    public function api()
    {
        header('Content-Type: application/json');
        
        try {
            $search = trim($_GET['search'] ?? '');
            $filters = [
                'role' => $_GET['role'] ?? '',
                'status' => $_GET['status'] ?? '',
                'group_id' => $_GET['group_id'] ?? ''
            ];
            
            if (!empty($search)) {
                $results = $this->searchWithFilters($search, $filters);
            } else {
                $results = $this->getAllUsersWithFilters($filters);
            }
            
            // پردازش نتایج برای highlighting
            require_once APP_PATH . 'helpers/AdvancedSearch.php';
            $processedResults = AdvancedSearch::processSearchResults(
                $results,
                $search,
                ['username', 'full_name', 'email', 'group_name']
            );
            
            // اضافه کردن اطلاعات تکمیلی به هر کاربر
            $enrichedResults = [];
            foreach ($processedResults as $user) {
                $enrichedResults[] = $this->enrichUserData($user);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $enrichedResults,
                'total' => count($enrichedResults),
                'search_terms' => !empty($search) ? array_filter(array_map('trim', explode(' ', $search))) : [],
                'has_search' => !empty($search)
            ]);
            
        } catch (Exception $e) {
            writeLog("خطا در API کاربران: " . $e->getMessage(), 'ERROR');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'خطا در جستجو'
            ]);
        }
    }

    /**
     * غنی‌سازی داده‌های کاربر
     */
    private function enrichUserData($user)
    {
        // حذف اطلاعات حساس
        unset($user['password'], $user['remember_token'], $user['reset_token']);
        
        // اضافه کردن برچسب نقش
        $user['role_label'] = USER_ROLES[$user['role']] ?? $user['role'];
        
        // فرمت‌بندی تاریخ
        $user['created_at_formatted'] = isset($user['created_at']) ? jdate('Y/m/d H:i', strtotime($user['created_at'])) : '';
        $user['last_login_formatted'] = isset($user['last_login']) ? jdate('Y/m/d H:i', strtotime($user['last_login'])) : 'هرگز';
        
        return $user;
    }

    /**
     * نمایش فرم ایجاد کاربر جدید
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateUser();
            return;
        }

        // دریافت تگ‌ها برای انتخاب
        $tags = $this->tagModel->getAllTags();

        $this->render('users/create', [
            'title' => 'ایجاد کاربر جدید',
            'page_title' => 'ایجاد کاربر جدید',
            'page_subtitle' => 'افزودن کاربر جدید به سیستم',
            'roles' => USER_ROLES,
            'tags' => $tags,
            'additional_css' => ['css/users.css']
        ]);
    }

    /**
     * نمایش جزئیات کاربر
     */
    public function show($id)
    {
        try {
            $user = $this->userModel->find($id);
            
            if (!$user) {
                $this->setFlash('error', 'کاربر یافت نشد');
                Security::redirect('users');
                return;
            }

            // حذف اطلاعات حساس
            unset($user['password'], $user['remember_token'], $user['reset_token']);

            $this->render('users/view', [
                'title' => 'جزئیات کاربر',
                'page_title' => 'جزئیات کاربر',
                'page_subtitle' => 'مشاهده اطلاعات ' . $user['full_name'],
                'user' => $user,
                'roles' => USER_ROLES,
                'additional_css' => ['css/users.css']
            ]);
            
        } catch (Exception $e) {
            writeLog("خطا در نمایش جزئیات کاربر: " . $e->getMessage(), 'ERROR');
            $this->setFlash('error', 'خطا در بارگذاری جزئیات کاربر');
            Security::redirect('users');
        }
    }

    /**
     * تغییر وضعیت کاربر
     */
    public function toggleStatus($id)
    {
        try {
            // جلوگیری از تغییر وضعیت خود
            if ($id == $_SESSION['user_id']) {
                $this->json(['success' => false, 'message' => 'نمی‌توانید وضعیت حساب خود را تغییر دهید']);
                return;
            }

            $user = $this->userModel->find($id);
            
            if (!$user) {
                $this->json(['success' => false, 'message' => 'کاربر یافت نشد']);
                return;
            }

            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
            $result = $this->userModel->update($id, ['status' => $newStatus]);
            
            if ($result) {
                $statusLabel = ($newStatus === 'active') ? 'فعال' : 'غیرفعال';
                writeLog("وضعیت کاربر {$user['username']} به {$statusLabel} تغییر یافت", 'INFO');
                
                $this->json([
                    'success' => true, 
                    'message' => "وضعیت کاربر به {$statusLabel} تغییر یافت",
                    'newStatus' => $newStatus
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'خطا در تغییر وضعیت']);
            }
            
        } catch (Exception $e) {
            writeLog("خطا در تغییر وضعیت کاربر: " . $e->getMessage(), 'ERROR');
            $this->json(['success' => false, 'message' => 'خطای سیستمی رخ داده است']);
        }
    }

    /**
     * تخصیص تگ به کاربر
     */
    public function assignTags()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'متد درخواست نامعتبر است']);
            return;
        }

        try {
            $userId = (int)$this->input('user_id');
            $tagIds = $this->input('tag_ids', []);

            if (!$userId) {
                $this->json(['success' => false, 'message' => 'شناسه کاربر نامعتبر است']);
                return;
            }

            // بررسی وجود کاربر
            $user = $this->userModel->find($userId);
            if (!$user) {
                $this->json(['success' => false, 'message' => 'کاربر یافت نشد']);
                return;
            }

            // تبدیل تگ‌ها به JSON
            $tagsJson = json_encode(array_map('intval', $tagIds));
            
            // بروزرسانی کاربر
            $result = $this->userModel->update($userId, ['tags' => $tagsJson]);

            if ($result) {
                $this->json([
                    'success' => true,
                    'message' => 'تگ‌ها با موفقیت تخصیص یافت'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'خطا در تخصیص تگ‌ها']);
            }

        } catch (Exception $e) {
            writeLog("خطا در تخصیص تگ به کاربر: " . $e->getMessage(), 'ERROR');
            $this->json(['success' => false, 'message' => 'خطای سیستمی رخ داده است']);
        }
    }

    /**
     * حذف کاربر
     */
    public function delete($id)
    {
        try {
            // جلوگیری از حذف خود
            if ($id == $_SESSION['user_id']) {
                $this->json(['success' => false, 'message' => 'نمی‌توانید حساب خود را حذف کنید']);
                return;
            }

            $user = $this->userModel->find($id);
            
            if (!$user) {
                $this->json(['success' => false, 'message' => 'کاربر یافت نشد']);
                return;
            }

            // تغییر وضعیت به deleted
            $result = $this->userModel->update($id, ['status' => 'deleted']);
            
            if ($result) {
                writeLog("کاربر {$user['username']} حذف شد", 'INFO');
                $this->json([
                    'success' => true,
                    'message' => 'کاربر با موفقیت حذف شد'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'خطا در حذف کاربر']);
            }
            
        } catch (Exception $e) {
            writeLog("خطا در حذف کاربر: " . $e->getMessage(), 'ERROR');
            $this->json(['success' => false, 'message' => 'خطای سیستمی رخ داده است']);
        }
    }

    /**
     * پردازش ایجاد کاربر جدید
     */
    private function handleCreateUser()
    {
        try {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'role' => $_POST['role'] ?? 'user',
                'status' => $_POST['status'] ?? 'active',
                'group_id' => $_POST['group_id'] ?? null,
                'tags' => json_encode($_POST['tag_ids'] ?? [])
            ];

            // تبدیل اعداد فارسی به انگلیسی برای فیلد موبایل
            if (!empty($data['phone'])) {
                $data['phone'] = Security::convertPersianToEnglishNumbers($data['phone']);
                $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
            }

            $result = $this->userModel->createUser($data);
            
            if ($result['success']) {
                $this->setFlash('success', 'کاربر جدید با موفقیت ایجاد شد');
                Security::redirect("users/show/{$result['user_id']}");
            } else {
                $this->setFlash('error', implode('<br>', $result['errors']));
                
                $tags = $this->tagModel->getAllTags();
                $this->render('users/create', [
                    'title' => 'ایجاد کاربر جدید',
                    'roles' => USER_ROLES,
                    'tags' => $tags,
                    'old_data' => $data,
                    'additional_css' => ['css/users.css']
                ]);
            }
            
        } catch (Exception $e) {
            writeLog("خطا در ایجاد کاربر: " . $e->getMessage(), 'ERROR');
            $this->setFlash('error', 'خطای سیستمی رخ داده است');
            
            $tags = $this->tagModel->getAllTags();
            $this->render('users/create', [
                'title' => 'ایجاد کاربر جدید',
                'roles' => USER_ROLES,
                'tags' => $tags,
                'additional_css' => ['css/users.css']
            ]);
        }
    }
}
?>