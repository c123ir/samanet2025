<?php
/**
 * نام فایل: Group.php
 * مسیر فایل: /app/models/Group.php
 * توضیح: مدل گروه‌های کاری سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once APP_PATH . 'models/Database.php';

class Group 
{
    protected $table = 'user_groups';
    protected $fillable = [
        'name', 'description', 'permissions', 'parent_id', 'created_by',
        'is_active', 'color', 'icon', 'max_members', 'auto_approve'
    ];
    protected $hidden = [];
    
    private $db;
    
    public function __construct() 
    {
        $this->db = getDB();
    }

    /**
     * مجوزهای پیش‌فرض گروه‌ها
     */
    const DEFAULT_PERMISSIONS = [
        'view_requests' => 'مشاهده درخواست‌ها',
        'create_requests' => 'ایجاد درخواست جدید',
        'edit_requests' => 'ویرایش درخواست‌ها',
        'delete_requests' => 'حذف درخواست‌ها',
        'approve_requests' => 'تایید درخواست‌ها',
        'reject_requests' => 'رد درخواست‌ها',
        'view_documents' => 'مشاهده اسناد',
        'upload_documents' => 'آپلود اسناد',
        'download_documents' => 'دانلود اسناد',
        'delete_documents' => 'حذف اسناد',
        'view_reports' => 'مشاهده گزارش‌ها',
        'export_reports' => 'خروجی گزارش‌ها',
        'manage_users' => 'مدیریت کاربران',
        'manage_groups' => 'مدیریت گروه‌ها',
        'system_settings' => 'تنظیمات سیستم'
    ];

    /**
     * رنگ‌های پیش‌فرض گروه‌ها
     */
    const DEFAULT_COLORS = [
        '#667eea', '#06D6A0', '#f093fb', '#ff6b6b', '#4ecdc4',
        '#45b7d1', '#f9ca24', '#f0932b', '#eb4d4b', '#6c5ce7'
    ];

    /**
     * آیکون‌های پیش‌فرض گروه‌ها
     */
    const DEFAULT_ICONS = [
        'fas fa-users', 'fas fa-briefcase', 'fas fa-building',
        'fas fa-cogs', 'fas fa-chart-bar', 'fas fa-file-alt',
        'fas fa-shield-alt', 'fas fa-graduation-cap', 'fas fa-handshake'
    ];

    /**
     * ایجاد گروه جدید
     */
    public function createGroup($data) 
    {
        try {
            // اعتبارسنجی داده‌های ورودی
            $validation = $this->validateGroupData($data);
            if ($validation !== true) {
                return ['success' => false, 'errors' => $validation];
            }

            // بررسی تکراری نبودن نام گروه
            if ($this->groupNameExists($data['name'])) {
                return ['success' => false, 'errors' => ['این نام گروه قبلاً استفاده شده است']];
            }

            // تنظیم مقادیر پیش‌فرض
            $data['is_active'] = $data['is_active'] ?? 1;
            $data['color'] = $data['color'] ?? $this->getRandomColor();
            $data['icon'] = $data['icon'] ?? $this->getRandomIcon();
            $data['max_members'] = $data['max_members'] ?? 50;
            $data['auto_approve'] = $data['auto_approve'] ?? 0;
            $data['created_at'] = date('Y-m-d H:i:s');

            // تبدیل permissions به JSON
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $data['permissions'] = json_encode($data['permissions']);
            } else {
                $data['permissions'] = json_encode([]);
            }

            // ایجاد گروه
            $groupId = $this->create($data);
            
            if ($groupId) {
                writeLog("گروه جدید ایجاد شد: {$data['name']} (ID: {$groupId}) توسط کاربر: {$data['created_by']}", 'INFO');
                return ['success' => true, 'group_id' => $groupId];
            }

            return ['success' => false, 'errors' => ['خطا در ایجاد گروه']];

        } catch (Exception $e) {
            writeLog("خطا در ایجاد گروه: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * به‌روزرسانی گروه
     */
    public function updateGroup($groupId, $data) 
    {
        try {
            // بررسی وجود گروه
            $group = $this->find($groupId);
            if (!$group) {
                return ['success' => false, 'errors' => ['گروه یافت نشد']];
            }

            // اعتبارسنجی داده‌های ورودی
            $validation = $this->validateGroupData($data, $groupId);
            if ($validation !== true) {
                return ['success' => false, 'errors' => $validation];
            }

            // بررسی تکراری نبودن نام گروه
            if (isset($data['name']) && $this->groupNameExists($data['name'], $groupId)) {
                return ['success' => false, 'errors' => ['این نام گروه قبلاً استفاده شده است']];
            }

            // تبدیل permissions به JSON
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $data['permissions'] = json_encode($data['permissions']);
            }

            $data['updated_at'] = date('Y-m-d H:i:s');

            // به‌روزرسانی گروه
            $result = $this->update($groupId, $data);
            
            if ($result) {
                writeLog("گروه به‌روزرسانی شد: {$group['name']} (ID: {$groupId})", 'INFO');
                return ['success' => true];
            }

            return ['success' => false, 'errors' => ['خطا در به‌روزرسانی گروه']];

        } catch (Exception $e) {
            writeLog("خطا در به‌روزرسانی گروه: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * حذف گروه
     */
    public function deleteGroup($groupId) 
    {
        try {
            // بررسی وجود گروه
            $group = $this->find($groupId);
            if (!$group) {
                return ['success' => false, 'errors' => ['گروه یافت نشد']];
            }

            // بررسی اینکه آیا کاربری در گروه وجود دارد
            if ($this->hasMembers($groupId)) {
                return ['success' => false, 'errors' => ['ابتدا تمام اعضای گروه را حذف کنید']];
            }

            // بررسی اینکه آیا درخواستی مرتبط با گروه وجود دارد
            if ($this->hasRequests($groupId)) {
                return ['success' => false, 'errors' => ['این گروه دارای درخواست‌های مرتبط است و قابل حذف نیست']];
            }

            // حذف گروه
            $result = $this->delete($groupId);
            
            if ($result) {
                writeLog("گروه حذف شد: {$group['name']} (ID: {$groupId})", 'WARNING');
                return ['success' => true];
            }

            return ['success' => false, 'errors' => ['خطا در حذف گروه']];

        } catch (Exception $e) {
            writeLog("خطا در حذف گروه: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * دریافت تمام گروه‌ها
     */
    public function getAllGroups($includeInactive = false) 
    {
        try {
            $where = null;
            if (!$includeInactive) {
                $where = [
                    'condition' => 'is_active = ?',
                    'params' => [1]
                ];
            }

            $groups = $this->where('1', '1', null, '*');
            if (!$includeInactive) {
                $groups = array_filter($groups, function($group) {
                    return $group['is_active'] == 1;
                });
            }

            // تبدیل permissions از JSON
            foreach ($groups as &$group) {
                $group['permissions'] = json_decode($group['permissions'], true) ?: [];
                $group['member_count'] = $this->getMemberCount($group['id']);
            }

            return $groups;

        } catch (Exception $e) {
            writeLog("خطا در دریافت گروه‌ها: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت گروه با جزئیات
     */
    public function getGroupDetails($groupId) 
    {
        try {
            $group = $this->find($groupId);
            if (!$group) {
                return null;
            }

            // تبدیل permissions از JSON
            $group['permissions'] = json_decode($group['permissions'], true) ?: [];
            
            // دریافت تعداد اعضا
            $group['member_count'] = $this->getMemberCount($groupId);
            
            // دریافت لیست اعضا
            $group['members'] = $this->getGroupMembers($groupId);
            
            // دریافت آمار درخواست‌ها
            $group['request_stats'] = $this->getGroupRequestStats($groupId);

            return $group;

        } catch (Exception $e) {
            writeLog("خطا در دریافت جزئیات گروه: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }

    /**
     * دریافت اعضای گروه
     */
    public function getGroupMembers($groupId, $activeOnly = true) 
    {
        try {
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            
            $condition = 'group_id = ?';
            $params = [$groupId];
            
            if ($activeOnly) {
                $condition .= ' AND status = ?';
                $params[] = 'active';
            }
            
            $members = $userModel->where('group_id', $groupId, null, 'id, username, full_name, role, status, last_login');
            
            if ($activeOnly) {
                $members = array_filter($members, function($member) {
                    return $member['status'] === 'active';
                });
            }

            return array_values($members);

        } catch (Exception $e) {
            writeLog("خطا در دریافت اعضای گروه: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * اضافه کردن عضو به گروه
     */
    public function addMemberToGroup($groupId, $userId) 
    {
        try {
            // بررسی وجود گروه
            $group = $this->find($groupId);
            if (!$group || !$group['is_active']) {
                return ['success' => false, 'errors' => ['گروه یافت نشد یا غیرفعال است']];
            }

            // بررسی ظرفیت گروه
            if ($group['max_members'] && $this->getMemberCount($groupId) >= $group['max_members']) {
                return ['success' => false, 'errors' => ['ظرفیت گروه تکمیل است']];
            }

            // بررسی وجود کاربر
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user) {
                return ['success' => false, 'errors' => ['کاربر یافت نشد']];
            }

            // بررسی اینکه کاربر قبلاً عضو گروه نباشد
            if ($user['group_id'] == $groupId) {
                return ['success' => false, 'errors' => ['کاربر قبلاً عضو این گروه است']];
            }

            // اضافه کردن کاربر به گروه
            $result = $userModel->update($userId, ['group_id' => $groupId]);
            
            if ($result) {
                writeLog("کاربر {$user['username']} به گروه {$group['name']} اضافه شد", 'INFO');
                return ['success' => true];
            }

            return ['success' => false, 'errors' => ['خطا در اضافه کردن عضو']];

        } catch (Exception $e) {
            writeLog("خطا در اضافه کردن عضو به گروه: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * حذف عضو از گروه
     */
    public function removeMemberFromGroup($groupId, $userId) 
    {
        try {
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user || $user['group_id'] != $groupId) {
                return ['success' => false, 'errors' => ['کاربر عضو این گروه نیست']];
            }

            // حذف کاربر از گروه
            $result = $userModel->update($userId, ['group_id' => null]);
            
            if ($result) {
                $group = $this->find($groupId);
                writeLog("کاربر {$user['username']} از گروه {$group['name']} حذف شد", 'INFO');
                return ['success' => true];
            }

            return ['success' => false, 'errors' => ['خطا در حذف عضو']];

        } catch (Exception $e) {
            writeLog("خطا در حذف عضو از گروه: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * بررسی دسترسی کاربر به گروه
     */
    public function checkUserAccess($userId, $groupId, $permission = null) 
    {
        try {
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user || $user['status'] !== 'active') {
                return false;
            }

            // مدیران سیستم دسترسی کامل دارند
            if ($user['role'] === 'admin') {
                return true;
            }

            // بررسی عضویت در گروه
            if ($user['group_id'] != $groupId) {
                return false;
            }

            // اگر مجوز خاصی مورد نیاز نیست، عضویت کافی است
            if (!$permission) {
                return true;
            }

            // بررسی مجوز خاص
            $group = $this->find($groupId);
            if (!$group) {
                return false;
            }

            $permissions = json_decode($group['permissions'], true) ?: [];
            return isset($permissions[$permission]) && $permissions[$permission];

        } catch (Exception $e) {
            writeLog("خطا در بررسی دسترسی: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * جستجو در گروه‌ها
     */
    public function searchGroups($term, $limit = 20) 
    {
        try {
            $term = Security::sanitizeSearchTerm($term);
            $groups = $this->search(['name', 'description'], $term, $limit);
            
            // تبدیل permissions و اضافه کردن تعداد اعضا
            foreach ($groups as &$group) {
                $group['permissions'] = json_decode($group['permissions'], true) ?: [];
                $group['member_count'] = $this->getMemberCount($group['id']);
            }

            return $groups;

        } catch (Exception $e) {
            writeLog("خطا در جستجوی گروه‌ها: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت آمار گروه‌ها
     */
    public function getGroupStats() 
    {
        try {
            $stmt = $this->db->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                AVG(member_count) as avg_members
                FROM user_groups");
            return $stmt->fetch();

        } catch (Exception $e) {
            writeLog("خطا در دریافت آمار گروه‌ها: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * اعتبارسنجی داده‌های گروه
     */
    private function validateGroupData($data, $excludeId = null) 
    {
        $errors = [];

        // بررسی نام گروه
        if (empty($data['name'])) {
            $errors[] = 'نام گروه الزامی است';
        } elseif (mb_strlen($data['name'], 'UTF-8') < 2) {
            $errors[] = 'نام گروه باید حداقل ۲ کاراکتر باشد';
        } elseif (mb_strlen($data['name'], 'UTF-8') > 100) {
            $errors[] = 'نام گروه نباید بیش از ۱۰۰ کاراکتر باشد';
        }

        // بررسی توضیحات
        if (!empty($data['description']) && mb_strlen($data['description'], 'UTF-8') > 500) {
            $errors[] = 'توضیحات نباید بیش از ۵۰۰ کاراکتر باشد';
        }

        // بررسی حداکثر اعضا
        if (isset($data['max_members'])) {
            if (!is_numeric($data['max_members']) || $data['max_members'] < 1 || $data['max_members'] > 1000) {
                $errors[] = 'حداکثر تعداد اعضا باید بین ۱ تا ۱۰۰۰ باشد';
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * بررسی وجود نام گروه
     */
    private function groupNameExists($name, $excludeId = null) 
    {
        $group = $this->first('name', $name);
        
        if (!$group) {
            return false;
        }
        
        if ($excludeId && $group['id'] == $excludeId) {
            return false;
        }
        
        return true;
    }

    /**
     * شمارش اعضای گروه
     */
    private function getMemberCount($groupId) 
    {
        try {
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            
            return $userModel->count('*', [
                'condition' => 'group_id = ? AND status = ?',
                'params' => [$groupId, 'active']
            ]);

        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * بررسی وجود اعضا در گروه
     */
    private function hasMembers($groupId) 
    {
        return $this->getMemberCount($groupId) > 0;
    }

    /**
     * بررسی وجود درخواست‌های مرتبط
     */
    private function hasRequests($groupId) 
    {
        try {
            $db = getDB();
            $sql = "SELECT COUNT(*) as count FROM payment_requests WHERE group_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * دریافت آمار درخواست‌های گروه
     */
    private function getGroupRequestStats($groupId) 
    {
        try {
            $db = getDB();
            $stats = [
                'total' => 0,
                'pending' => 0,
                'completed' => 0,
                'cancelled' => 0
            ];

            // تعداد کل درخواست‌ها
            $sql = "SELECT COUNT(*) as count FROM payment_requests WHERE group_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetch();
            $stats['total'] = $result['count'];

            // آمار بر اساس وضعیت
            $sql = "SELECT status, COUNT(*) as count FROM payment_requests WHERE group_id = ? GROUP BY status";
            $stmt = $db->prepare($sql);
            $stmt->execute([$groupId]);
            
            while ($row = $stmt->fetch()) {
                if (isset($stats[$row['status']])) {
                    $stats[$row['status']] = $row['count'];
                }
            }

            return $stats;

        } catch (Exception $e) {
            return ['total' => 0, 'pending' => 0, 'completed' => 0, 'cancelled' => 0];
        }
    }

    /**
     * انتخاب رنگ تصادفی
     */
    private function getRandomColor() 
    {
        return self::DEFAULT_COLORS[array_rand(self::DEFAULT_COLORS)];
    }

    /**
     * انتخاب آیکون تصادفی
     */
    private function getRandomIcon() 
    {
        return self::DEFAULT_ICONS[array_rand(self::DEFAULT_ICONS)];
    }
}
?>