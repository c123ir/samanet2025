<?php
/**
 * نام فایل: UserController.php
 * مسیر فایل: /app/controllers/UserController.php
 * توضیح: کنترلر مدیریت کاربران سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once 'BaseController.php';
require_once APP_PATH . 'models/User.php';

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        
        // بررسی دسترسی مدیریت
        if (!Security::checkPermission('manager')) {
            Security::redirect('dashboard', 'شما به این بخش دسترسی ندارید', 'error');
        }
    }

    /**
     * نمایش لیست کاربران
     */
    public function index()
    {
        try {
            // دریافت کاربران
            $users = $this->userModel->all('id, username, full_name, email, role, status, created_at, last_login');
            
            // آمار کاربران
            $stats = $this->userModel->getUserStats();
            
            $this->render('users/index', [
                'title' => 'مدیریت کاربران',
                'users' => $users,
                'stats' => $stats,
                'roles' => USER_ROLES,
                'additional_css' => ['css/users.css'] // CSS جدید حرفه‌ای
            ]);
            
        } catch (Exception $e) {
            error_log("خطا در نمایش لیست کاربران: " . $e->getMessage());
            $this->setFlash('error', 'خطا در بارگذاری لیست کاربران');
            $this->render('users/index', [
                'title' => 'مدیریت کاربران',
                'additional_css' => ['css/users.css']
            ]);
        }
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

        $this->renderView('users/create', [
            'roles' => USER_ROLES
        ], 'ایجاد کاربر جدید');
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
            }

            // حذف اطلاعات حساس
            unset($user['password'], $user['remember_token'], $user['reset_token']);

            $this->renderView('users/view', [
                'user' => $user,
                'roles' => USER_ROLES
            ], 'جزئیات کاربر');
            
        } catch (Exception $e) {
            error_log("خطا در نمایش جزئیات کاربر: " . $e->getMessage());
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
                $this->jsonResponse(['success' => false, 'message' => 'نمی‌توانید وضعیت حساب خود را تغییر دهید']);
                return;
            }

            $user = $this->userModel->find($id);
            
            if (!$user) {
                $this->jsonResponse(['success' => false, 'message' => 'کاربر یافت نشد']);
                return;
            }

            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
            $result = $this->userModel->update($id, ['status' => $newStatus]);
            
            if ($result) {
                $statusLabel = ($newStatus === 'active') ? 'فعال' : 'غیرفعال';
                writeLog("وضعیت کاربر {$user['username']} به {$statusLabel} تغییر یافت", 'INFO');
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => "وضعیت کاربر به {$statusLabel} تغییر یافت",
                    'newStatus' => $newStatus
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'خطا در تغییر وضعیت']);
            }
            
        } catch (Exception $e) {
            error_log("خطا در تغییر وضعیت کاربر: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'خطای سیستمی رخ داده است']);
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
                'status' => $_POST['status'] ?? 'active'
            ];

            // تبدیل اعداد فارسی به انگلیسی برای فیلد موبایل
            if (!empty($data['phone'])) {
                $data['phone'] = Security::convertPersianToEnglishNumbers($data['phone']);
                // حذف کاراکترهای غیرعددی
                $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
            }

            $result = $this->userModel->createUser($data);
            
            if ($result['success']) {
                $this->setFlash('success', 'کاربر جدید با موفقیت ایجاد شد');
                Security::redirect("users/show/{$result['user_id']}");
            } else {
                $this->setFlash('error', implode('<br>', $result['errors']));
                $this->renderView('users/create', [
                    'roles' => USER_ROLES,
                    'old_data' => $data
                ], 'ایجاد کاربر جدید');
            }
            
        } catch (Exception $e) {
            error_log("خطا در ایجاد کاربر: " . $e->getMessage());
            $this->setFlash('error', 'خطای سیستمی رخ داده است');
            $this->renderView('users/create', [
                'roles' => USER_ROLES
            ], 'ایجاد کاربر جدید');
        }
    }
}
?>