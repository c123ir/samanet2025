<?php
/**
 * نام فایل: User.php
 * مسیر فایل: /app/models/User.php
 * توضیح: مدل کاربران سیستم سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once APP_PATH . 'models/Database.php';

class User extends Database
{
    protected $table = 'users';
    protected $fillable = [
        'username', 'password', 'full_name', 'email', 'phone', 
        'role', 'group_id', 'status', 'last_login', 'last_login_ip',
        'remember_token', 'reset_token', 'reset_token_expiry'
    ];
    protected $hidden = ['password', 'remember_token', 'reset_token'];
    
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function getUserStats() 
    {
        try {
            $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
                FROM {$this->table}";
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            writeLog("خطا در دریافت آمار کاربران: " . $e->getMessage(), 'ERROR');
            return ['total' => 0, 'active' => 0, 'inactive' => 0];
        }
    }

    /**
     * ایجاد کاربر جدید
     */
    public function createUser($data) 
    {
        try {
            // تبدیل اعداد فارسی به انگلیسی برای فیلدهای عددی
            if (!empty($data['phone'])) {
                $data['phone'] = Security::convertPersianToEnglishNumbers($data['phone']);
            }
            
            // اعتبارسنجی داده‌های ورودی
            $validation = $this->validateUserData($data);
            if ($validation !== true) {
                return ['success' => false, 'errors' => $validation];
            }

            // بررسی تکراری نبودن نام کاربری
            if ($this->usernameExists($data['username'])) {
                return ['success' => false, 'errors' => ['این نام کاربری قبلاً استفاده شده است']];
            }

            // بررسی تکراری نبودن ایمیل
            if (!empty($data['email']) && $this->emailExists($data['email'])) {
                return ['success' => false, 'errors' => ['این ایمیل قبلاً ثبت شده است']];
            }

            // رمزگذاری پسورد
            $data['password'] = Security::hashPassword($data['password']);
            
            // تنظیم مقادیر پیش‌فرض
            $data['status'] = $data['status'] ?? 'active';
            $data['role'] = $data['role'] ?? 'user';
            $data['created_at'] = date('Y-m-d H:i:s');

            // ایجاد کاربر - تداخل موقت password از hidden
            $originalHidden = $this->hidden;
            $this->hidden = array_diff($this->hidden, ['password']);
            
            $userId = $this->create($data);
            
            // بازگرداندن تنظیمات hidden
            $this->hidden = $originalHidden;
            
            if ($userId) {
                writeLog("کاربر جدید ایجاد شد: {$data['username']} (ID: {$userId})", 'INFO');
                return ['success' => true, 'user_id' => $userId];
            }

            return ['success' => false, 'errors' => ['خطا در ایجاد کاربر']];
        } catch (Exception $e) {
            writeLog("خطا در ایجاد کاربر: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * احراز هویت کاربر
     */
    public function authenticate($username, $password, $rememberMe = false) 
    {
        try {
            // بررسی محدودیت تلاش ورود
            if (SecurityConfig::isLoginLocked()) {
                return ['success' => false, 'message' => 'حساب شما به دلیل تلاش‌های ناموفق قفل شده است'];
            }

            // جستجوی کاربر
            $user = $this->first('username', $username);
            
            if (!$user) {
                SecurityConfig::logFailedAttempt($username);
                return ['success' => false, 'message' => 'نام کاربری یا رمز عبور اشتباه است'];
            }

            // بررسی وضعیت کاربر
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'حساب کاربری شما غیرفعال است'];
            }

            // بررسی رمز عبور
            if (!Security::verifyPassword($password, $user['password'])) {
                SecurityConfig::logFailedAttempt($username);
                return ['success' => false, 'message' => 'نام کاربری یا رمز عبور اشتباه است'];
            }

            // پاک کردن تلاش‌های ناموفق
            SecurityConfig::clearFailedAttempts();

            // به‌روزرسانی اطلاعات آخرین ورود
            $this->updateLastLogin($user['id']);

            // ایجاد session
            $this->createUserSession($user, $rememberMe);

            writeLog("ورود موفق کاربر: {$username}", 'INFO');
            return ['success' => true, 'user' => $this->hidePassword($user)];

        } catch (Exception $e) {
            writeLog("خطا در احراز هویت: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'message' => 'خطای سیستمی رخ داده است'];
        }
    }

    /**
     * ایجاد session کاربر
     */
    private function createUserSession($user, $rememberMe = false) 
    {
        // تنظیم session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['group_id'] = $user['group_id'];
        $_SESSION['login_time'] = time();

        // تنظیم Remember Me
        if ($rememberMe) {
            $token = Security::generateToken(64);
            $expiry = time() + (30 * 24 * 60 * 60); // 30 روز
            
            // ذخیره token در دیتابیس
            $this->update($user['id'], ['remember_token' => hash('sha256', $token)]);
            
            // تنظیم کوکی
            setcookie('remember_token', $token, $expiry, '/', '', false, true);
        }
    }

    /**
     * به‌روزرسانی آخرین ورود
     */
    public function updateLastLogin($userId) 
    {
        $updateData = [
            'last_login' => date('Y-m-d H:i:s'),
            'last_login_ip' => SecurityConfig::getClientIP()
        ];
        
        $this->update($userId, $updateData);
    }

    /**
     * اعتبارسنجی داده‌های کاربر
     */
    private function validateUserData($data) 
    {
        $errors = [];

        // بررسی نام کاربری
        if (empty($data['username'])) {
            $errors[] = 'نام کاربری الزامی است';
        } elseif (!Security::validateUsername($data['username'])) {
            $errors[] = 'نام کاربری باید شامل حروف انگلیسی، اعداد و خط زیر باشد (3-20 کاراکتر)';
        }

        // بررسی رمز عبور
        if (empty($data['password'])) {
            $errors[] = 'رمز عبور الزامی است';
        } else {
            $passwordValidation = Security::validatePassword($data['password']);
            if ($passwordValidation !== true) {
                $errors = array_merge($errors, $passwordValidation);
            }
        }

        // بررسی نام کامل
        if (empty($data['full_name'])) {
            $errors[] = 'نام کامل الزامی است';
        }

        // بررسی ایمیل
        if (!empty($data['email']) && !Security::validateEmail($data['email'])) {
            $errors[] = 'فرمت ایمیل صحیح نیست';
        }

                    // بررسی شماره موبایل
        if (!empty($data['phone'])) {
            // تبدیل اعداد فارسی به انگلیسی قبل از validation
            $data['phone'] = Security::convertPersianToEnglishNumbers($data['phone']);
            if (!Security::validateMobile($data['phone'])) {
                $errors[] = 'فرمت شماره موبایل صحیح نیست. مثال: 09123456789';
            }
        }

        // بررسی نقش
        if (!empty($data['role']) && !array_key_exists($data['role'], USER_ROLES)) {
            $errors[] = 'نقش کاربری نامعتبر است';
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * بررسی وجود نام کاربری
     */
    public function usernameExists($username, $excludeId = null) 
    {
        $user = $this->first('username', $username);
        
        if (!$user) {
            return false;
        }
        
        // اگر ID استثنا داده شده، بررسی کن که متعلق به همان کاربر نباشد
        if ($excludeId && $user['id'] == $excludeId) {
            return false;
        }
        
        return true;
    }

    /**
     * بررسی وجود ایمیل
     */
    public function emailExists($email, $excludeId = null) 
    {
        $user = $this->first('email', $email);
        
        if (!$user) {
            return false;
        }
        
        if ($excludeId && $user['id'] == $excludeId) {
            return false;
        }
        
        return true;
    }

    /**
     * دریافت کاربران یک گروه
     */
    public function getUsersByGroup($groupId) 
    {
        $users = $this->where('group_id', $groupId, null, 'id, username, full_name, role, status');
        return array_map([$this, 'hidePassword'], $users);
    }

    /**
     * دریافت کاربران بر اساس نقش
     */
    public function getUsersByRole($role) 
    {
        $users = $this->where('role', $role);
        return array_map([$this, 'hidePassword'], $users);
    }

    /**
     * تغییر رمز عبور
     */
    public function changePassword($userId, $currentPassword, $newPassword) 
    {
        try {
            $user = $this->find($userId);
            
            if (!$user) {
                return ['success' => false, 'message' => 'کاربر یافت نشد'];
            }

            // بررسی رمز عبور فعلی
            if (!Security::verifyPassword($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'رمز عبور فعلی اشتباه است'];
            }

            // اعتبارسنجی رمز عبور جدید
            $validation = Security::validatePassword($newPassword);
            if ($validation !== true) {
                return ['success' => false, 'errors' => $validation];
            }

            // تغییر رمز عبور
            $hashedPassword = Security::hashPassword($newPassword);
            $result = $this->update($userId, ['password' => $hashedPassword]);

            if ($result) {
                writeLog("تغییر رمز عبور کاربر: {$user['username']}", 'INFO');
                return ['success' => true, 'message' => 'رمز عبور با موفقیت تغییر یافت'];
            }

            return ['success' => false, 'message' => 'خطا در تغییر رمز عبور'];

        } catch (Exception $e) {
            writeLog("خطا در تغییر رمز عبور: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'message' => 'خطای سیستمی رخ داده است'];
        }
    }

    /**
     * بازیابی رمز عبور
     */
    public function resetPassword($email) 
    {
        try {
            $user = $this->first('email', $email);
            
            if (!$user) {
                // به دلایل امنیتی، همیشه پیام موفقیت نمایش داده می‌شود
                return ['success' => true, 'message' => 'لینک بازیابی به ایمیل شما ارسال شد'];
            }

            // تولید token بازیابی
            $resetToken = Security::generateToken(32);
            $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 ساعت

            // ذخیره token در دیتابیس
            $this->update($user['id'], [
                'reset_token' => hash('sha256', $resetToken),
                'reset_token_expiry' => $expiry
            ]);

            // TODO: ارسال ایمیل بازیابی
            writeLog("درخواست بازیابی رمز عبور: {$email}", 'INFO');

            return ['success' => true, 'message' => 'لینک بازیابی به ایمیل شما ارسال شد'];

        } catch (Exception $e) {
            writeLog("خطا در بازیابی رمز عبور: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'message' => 'خطای سیستمی رخ داده است'];
        }
    }

    /**
     * جستجو در کاربران
     */
    public function searchUsers($term, $limit = 20) 
    {
        $term = Security::sanitizeSearchTerm($term);
        $users = $this->search(['username', 'full_name', 'email'], $term, $limit);
        return array_map([$this, 'hidePassword'], $users);
    }

    /**
     * مخفی کردن رمز عبور
     */
    private function hidePassword($user) 
    {
        if (is_array($user)) {
            unset($user['password'], $user['remember_token'], $user['reset_token']);
        }
        return $user;
    }

    /**
     * به‌روزرسانی آخرین فعالیت
     */
    public function updateLastActivity($userId) 
    {
        $this->update($userId, ['last_activity' => date('Y-m-d H:i:s')]);
    }

    /**
     * دریافت تعداد اعضای گروه
     */
    public function getGroupMemberCount($groupId) 
    {
        try {
            $query = "SELECT COUNT(*) as count FROM users 
                     WHERE group_id = ? AND status = 'active'";
            
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute([$groupId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] ?? 0;

        } catch (Exception $e) {
            writeLog("Error getting group member count: " . $e->getMessage(), 'ERROR');
            return 0;
        }
    }

    /**
     * جستجوی پیشرفته کاربران با فیلتر - سازگار با AdvancedSearch Component
     */
    public function searchWithFilters($searchTerm = '', $filters = [])
    {
        try {
            require_once APP_PATH . 'helpers/AdvancedSearch.php';
            
            // فیلدهای قابل جستجو
            $searchFields = [
                'username',
                'full_name', 
                'email',
                'phone'
            ];
            
            // آماده کردن فیلترهای اضافی
            $additionalFilters = [];
            
            if (!empty($filters['role'])) {
                $additionalFilters['role'] = $filters['role'];
            }
            
            if (!empty($filters['status'])) {
                $additionalFilters['status'] = $filters['status'];
            }
            
            if (!empty($filters['group_id'])) {
                $additionalFilters['group_id'] = $filters['group_id'];
            }
            
            // اجرای جستجو
            $results = AdvancedSearch::performSearch(
                $this,                                           // Model object
                $this->table,                                   // Table name
                $searchTerm,                                    // Search term
                $searchFields,                                  // Search fields
                $additionalFilters,                             // Additional filters
                [],                                             // Joins (empty for now)
                'created_at',                                   // Order by
                'DESC'                                          // Order direction
            );
            
            return $results;
            
        } catch (Exception $e) {
            writeLog("خطا در جستجوی پیشرفته کاربران: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
}
?>