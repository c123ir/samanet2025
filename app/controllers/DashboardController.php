<?php
/**
 * نام فایل: DashboardController.php
 * مسیر فایل: /app/controllers/DashboardController.php
 * توضیح: کنترلر داشبورد اصلی سامانت - نسخه بازسازی شده
 * تاریخ بازطراحی: 1404/10/31
 * نسخه: 4.0 کامل
 */

require_once APP_PATH . 'controllers/BaseController.php';
require_once APP_PATH . 'models/PaymentRequest.php';
require_once APP_PATH . 'models/User.php';
require_once APP_PATH . 'models/Group.php';
require_once APP_PATH . 'models/Tag.php';

class DashboardController extends BaseController 
{
    protected $requiredRole = 'user';
    private $paymentRequestModel;
    private $userModel;
    private $groupModel;
    private $tagModel;
    private $db;

    public function __construct() 
    {
        parent::__construct();
        $this->db = getDB();
        $this->paymentRequestModel = new PaymentRequest();
        $this->userModel = new User();
        $this->groupModel = new Group();
        $this->tagModel = new Tag();
    }

    /**
     * نمایش داشبورد اصلی با داده‌های واقعی
     */
    public function index() 
    {
        $this->requireAuth();
        $user = $this->getCurrentUser();
        $groupId = $user['group_id'];

        try {
            // دریافت آمار واقعی از دیتابیس
            $stats = $this->getActualDashboardStats($groupId, $user['role']);
            $recentRequests = $this->getActualRecentRequests($groupId, $user['role']);
            $urgentRequests = $this->getActualUrgentRequests($groupId, $user['role']);
            $popularTags = $this->getPopularTags();
            
            writeLog("Dashboard data loaded for user: {$user['id']}, group: {$groupId}", 'INFO');

        } catch (Exception $e) {
            writeLog("Dashboard Error: " . $e->getMessage(), "ERROR");
            
            // Fallback data in case of error
            $stats = [
                'totalUsers' => 0,
                'totalRequests' => 0,
                'pendingRequests' => 0,
                'completedRequests' => 0,
                'totalAmount' => 0,
                'todayRequests' => 0
            ];
            $recentRequests = [];
            $urgentRequests = [];
            $popularTags = [];
        }
        
        $this->render('dashboard/index', [
            'title' => 'داشبورد سامانت',
            'page_title' => 'داشبورد سامانت',
            'page_subtitle' => 'خلاصه‌ای از وضعیت سیستم',
            'current_user' => $user,
            'user' => $user,
            'stats' => $stats,
            'recent_requests' => $recentRequests,
            'urgent_requests' => $urgentRequests,
            'popular_tags' => $popularTags,
            'additional_css' => ['/assets/css/dashboard.css']
        ]);
    }

    /**
     * دریافت آمار واقعی داشبورد
     */
    private function getActualDashboardStats($groupId, $userRole) 
    {
        try {
            // آمار کل کاربران (برای مدیران)
            if ($userRole === 'admin' || $userRole === 'manager') {
                $totalUsers = $this->userModel->count();
            } else {
                $totalUsers = $this->userModel->getGroupMemberCount($groupId);
            }

            // آمار درخواست‌ها
            $requestStats = $this->paymentRequestModel->getRequestStats($groupId);
            
            // آمار امروز
            $todayStats = $this->getTodayStats($groupId);
            
            return [
                'totalUsers' => $totalUsers,
                'totalRequests' => $requestStats['total'] ?? 0,
                'pendingRequests' => $requestStats['pending'] ?? 0,
                'completedRequests' => $requestStats['completed'] ?? 0,
                'processingRequests' => $requestStats['processing'] ?? 0,
                'rejectedRequests' => $requestStats['rejected'] ?? 0,
                'totalAmount' => $requestStats['total_amount'] ?? 0,
                'completedAmount' => $requestStats['completed_amount'] ?? 0,
                'todayRequests' => $todayStats['total'] ?? 0,
                'todayCompleted' => $todayStats['completed'] ?? 0,
                'todayAmount' => $todayStats['total_amount'] ?? 0
            ];

        } catch (Exception $e) {
            writeLog("Error getting dashboard stats: " . $e->getMessage(), 'ERROR');
            throw $e;
        }
    }

    /**
     * دریافت آخرین درخواست‌های واقعی
     */
    private function getActualRecentRequests($groupId, $userRole) 
    {
        try {
            $limit = 5;
            
            if ($userRole === 'admin' || $userRole === 'manager') {
                // مدیران تمام درخواست‌ها را می‌بینند
                return $this->paymentRequestModel->getRecentRequests($limit);
            } else {
                // کاربران عادی فقط درخواست‌های گروه خود را می‌بینند
                return $this->paymentRequestModel->getRecentRequests($limit, $groupId);
            }

        } catch (Exception $e) {
            writeLog("Error getting recent requests: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت درخواست‌های فوری واقعی
     */
    private function getActualUrgentRequests($groupId, $userRole) 
    {
        try {
            if ($userRole === 'admin' || $userRole === 'manager') {
                return $this->paymentRequestModel->getUrgentRequests();
            } else {
                return $this->paymentRequestModel->getUrgentRequests($groupId);
            }

        } catch (Exception $e) {
            writeLog("Error getting urgent requests: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت تگ‌های محبوب
     */
    private function getPopularTags() 
    {
        try {
            return $this->tagModel->getPopular(6);
        } catch (Exception $e) {
            writeLog("Error getting popular tags: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت آمار امروز
     */
    private function getTodayStats($groupId) 
    {
        try {
            $today = date('Y-m-d');
            return $this->paymentRequestModel->getRequestStats($groupId, $today, $today);

        } catch (Exception $e) {
            writeLog("Error getting today stats: " . $e->getMessage(), 'ERROR');
            return ['total' => 0, 'completed' => 0, 'total_amount' => 0];
        }
    }

    /**
     * API برای دریافت آمار زنده
     */
    public function getLiveStats() 
    {
        try {
            $user = $this->getCurrentUser();
            $groupId = $user['group_id'];
            
            $stats = $this->getActualDashboardStats($groupId, $user['role']);
            
            $this->json([
                'success' => true,
                'data' => $stats,
                'last_update' => jdate('Y/m/d H:i:s')
            ]);

        } catch (Exception $e) {
            $this->sendError('خطا در دریافت آمار زنده', 500);
        }
    }

    /**
     * API برای بروزرسانی درخواست‌های اخیر
     */
    public function getRecentRequestsData() 
    {
        try {
            $user = $this->getCurrentUser();
            $groupId = $user['group_id'];
            
            $recentRequests = $this->getActualRecentRequests($groupId, $user['role']);
            
            $this->json([
                'success' => true,
                'data' => $recentRequests,
                'count' => count($recentRequests)
            ]);

        } catch (Exception $e) {
            $this->sendError('خطا در دریافت درخواست‌های اخیر', 500);
        }
    }

    /**
     * دریافت آمار کلی داشبورد
     */
    private function getDashboardStats($userId, $userRole, $userGroupId) 
    {
        try {
            $stats = [];

            // تعیین محدوده دسترسی بر اساس نقش
            $groupFilter = null;
            if ($userRole !== 'admin' && $userRole !== 'manager') {
                $groupFilter = $userGroupId;
            }

            // آمار درخواست‌ها
            $requestStats = $this->paymentRequestModel->getRequestStats($groupFilter);
            
            $stats['requests'] = [
                'total' => $requestStats['total'] ?? 0,
                'pending' => $requestStats['pending'] ?? 0,
                'processing' => $requestStats['processing'] ?? 0,
                'completed' => $requestStats['completed'] ?? 0,
                'rejected' => $requestStats['rejected'] ?? 0,
                'total_amount' => $requestStats['total_amount'] ?? 0,
                'completed_amount' => $requestStats['completed_amount'] ?? 0
            ];

            // محاسبه درصدها
            if ($stats['requests']['total'] > 0) {
                $stats['requests']['pending_percentage'] = round(($stats['requests']['pending'] / $stats['requests']['total']) * 100, 1);
                $stats['requests']['completed_percentage'] = round(($stats['requests']['completed'] / $stats['requests']['total']) * 100, 1);
            } else {
                $stats['requests']['pending_percentage'] = 0;
                $stats['requests']['completed_percentage'] = 0;
            }

            // آمار کاربران (فقط برای مدیران)
            if ($userRole === 'admin' || $userRole === 'manager') {
                $userStats = $this->userModel->getUserStats();
                $stats['users'] = [
                    'total' => $userStats['total'] ?? 0,
                    'active' => $userStats['active'] ?? 0,
                    'inactive' => $userStats['inactive'] ?? 0,
                    'online' => $this->getOnlineUsersCount()
                ];

                // آمار گروه‌ها
                $groupStats = $this->groupModel->getGroupStats();
                $stats['groups'] = [
                    'total' => $groupStats['total'] ?? 0,
                    'active' => $groupStats['active'] ?? 0,
                    'avg_members' => $groupStats['avg_members'] ?? 0
                ];
            }

            // آمار امروز
            $todayStats = $this->getTodayStats($groupFilter);
            $stats['today'] = $todayStats;

            // آمار این ماه
            $thisMonthStats = $this->getThisMonthStats($groupFilter);
            $stats['this_month'] = $thisMonthStats;

            return $stats;

        } catch (Exception $e) {
            writeLog("خطا در دریافت آمار داشبورد: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت آمار این ماه
     */
    private function getThisMonthStats($groupFilter = null) 
    {
        try {
            $firstDayOfMonth = date('Y-m-01');
            $today = date('Y-m-d');
            return $this->paymentRequestModel->getRequestStats($groupFilter, $firstDayOfMonth, $today);

        } catch (Exception $e) {
            return ['total' => 0, 'completed' => 0, 'pending' => 0];
        }
    }

    /**
     * دریافت آمار روزانه
     */
    private function getDayStats($date, $userRole, $userGroupId) 
    {
        try {
            $groupFilter = null;
            if ($userRole !== 'admin' && $userRole !== 'manager') {
                $groupFilter = $userGroupId;
            }

            $stats = $this->paymentRequestModel->getRequestStats($groupFilter, $date, $date);
            
            return [
                'count' => $stats['total'] ?? 0,
                'amount' => $stats['total_amount'] ?? 0
            ];

        } catch (Exception $e) {
            return ['count' => 0, 'amount' => 0];
        }
    }

    /**
     * شمارش کاربران آنلاین
     */
    private function getOnlineUsersCount() 
    {
        try {
            // کاربرانی که در 15 دقیقه گذشته فعالیت داشته‌اند
            $sql = "SELECT COUNT(*) as count FROM users 
                    WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 15 MINUTE) 
                    AND status = 'active'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;

        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * شمارش کاربران جدید امروز
     */
    private function getNewUsersToday() 
    {
        try {
            $today = date('Y-m-d');
            $sql = "SELECT COUNT(*) as count FROM users 
                    WHERE DATE(created_at) = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$today]);
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;

        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * به‌روزرسانی آخرین فعالیت کاربر
     */
    public function updateLastActivity() 
    {
        try {
            if (Security::isLoggedIn()) {
                $userId = $_SESSION['user_id'];
                $this->userModel->updateLastActivity($userId);
            }

            $this->json(['success' => true]);

        } catch (Exception $e) {
            $this->sendError('خطا در به‌روزرسانی فعالیت', 500);
        }
    }

    /**
     * تغییر حالت داشبورد (compact/full)
     */
    public function toggleDashboardMode() 
    {
        try {
            $mode = $this->input('mode', 'full');
            
            if (!in_array($mode, ['compact', 'full'])) {
                $this->sendError('حالت نامعتبر است', 400);
                return;
            }

            $_SESSION['dashboard_mode'] = $mode;
            
            $this->sendSuccess('حالت داشبورد تغییر یافت', ['mode' => $mode]);

        } catch (Exception $e) {
            $this->sendError('خطا در تغییر حالت داشبورد', 500);
        }
    }

    /**
     * راهنمای سیستم
     */
    public function help() 
    {
        $this->render('dashboard/help', [
            'title' => 'راهنمای سیستم',
            'page_title' => 'راهنمای استفاده از سامانت',
            'additional_css' => ['/assets/css/dashboard.css']
        ]);
    }

    /**
     * درباره سیستم
     */
    public function about() 
    {
        $this->render('dashboard/about', [
            'title' => 'درباره سامانت',
            'page_title' => 'اطلاعات سیستم',
            'additional_css' => ['/assets/css/dashboard.css']
        ]);
    }
}
?>