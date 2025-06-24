<?php
/**
 * نام فایل: DashboardController.php
 * مسیر فایل: /app/controllers/DashboardController.php
 * توضیح: کنترلر داشبورد اصلی سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once APP_PATH . 'controllers/BaseController.php';
require_once APP_PATH . 'models/PaymentRequest.php';
require_once APP_PATH . 'models/User.php';
require_once APP_PATH . 'models/Group.php';

class DashboardController extends BaseController 
{
    protected $requiredRole = 'user';
    private $paymentRequestModel;
    private $userModel;
    private $groupModel;
    private $db;

    public function __construct() 
    {
        parent::__construct();
        $this->db = getDB();
        $this->paymentRequestModel = new PaymentRequest();
        $this->userModel = new User();
        $this->groupModel = new Group();
    }

    /**
     * نمایش داشبورد اصلی
     */
    public function index() 
    {
        try {
            $this->requireAuth();
            
            // دریافت اطلاعات کاربر فعلی
            $userId = $_SESSION['user_id'] ?? null;
            $userRole = $_SESSION['user_role'] ?? 'user';
            $userGroupId = $_SESSION['group_id'] ?? 1;

            // اطلاعات کاربر
            $user = [
                'id' => $userId,
                'username' => $_SESSION['username'] ?? 'admin',
                'full_name' => $_SESSION['full_name'] ?? 'مدیر سیستم',
                'role' => $userRole,
                'group_id' => $userGroupId
            ];

            $this->data['user'] = $user;

            // تنظیم عنوان صفحه
            $this->data['page_title'] = 'داشبورد اصلی';

            // آمار واقعی (فعلاً نمونه‌های ثابت اما زیبا)
            $this->data['stats'] = [
                'requests' => [
                    'total' => 247,
                    'pending' => 18,
                    'processing' => 12,
                    'completed' => 195,
                    'rejected' => 22,
                    'total_amount' => 12500000000, // 12.5 میلیارد
                    'completed_amount' => 9800000000, // 9.8 میلیارد
                    'pending_percentage' => 7.3,
                    'completed_percentage' => 78.9
                ],
                'today' => [
                    'total' => 8,
                    'completed' => 5,
                    'pending' => 3
                ]
            ];

            // درخواست‌های نمونه زیبا
            $this->data['recent_requests'] = [
                [
                    'id' => 1,
                    'reference_number' => 'REQ-2025-0001',
                    'title' => 'پرداخت حقوق کارکنان - دی ماه',
                    'amount' => 450000000,
                    'status' => 'completed',
                    'status_label' => 'تکمیل شده',
                    'priority' => 'high',
                    'priority_label' => 'بالا',
                    'is_urgent' => false,
                    'requester_name' => 'احمد محمدی',
                    'created_at' => '1404/10/12 09:30',
                    'created_at_jalali' => '1404/10/12'
                ],
                [
                    'id' => 2,
                    'reference_number' => 'REQ-2025-0002',
                    'title' => 'خرید تجهیزات اداری',
                    'amount' => 25000000,
                    'status' => 'pending',
                    'status_label' => 'در انتظار',
                    'priority' => 'normal',
                    'priority_label' => 'معمولی',
                    'is_urgent' => false,
                    'requester_name' => 'فاطمه رضایی',
                    'created_at' => '1404/10/12 14:15',
                    'created_at_jalali' => '1404/10/12'
                ],
                [
                    'id' => 3,
                    'reference_number' => 'REQ-2025-0003',
                    'title' => 'پرداخت قبض برق',
                    'amount' => 8500000,
                    'status' => 'processing',
                    'status_label' => 'در حال پردازش',
                    'priority' => 'urgent',
                    'priority_label' => 'فوری',
                    'is_urgent' => true,
                    'requester_name' => 'علی احمدی',
                    'created_at' => '1404/10/13 08:45',
                    'created_at_jalali' => '1404/10/13'
                ]
            ];

            $this->data['urgent_requests'] = [
                [
                    'id' => 3,
                    'reference_number' => 'REQ-2025-0003',
                    'title' => 'پرداخت قبض برق',
                    'amount' => 8500000,
                    'status' => 'processing',
                    'due_date' => '1404/10/15',
                    'requester_name' => 'علی احمدی'
                ]
            ];

            $this->data['notifications'] = [
                [
                    'type' => 'warning',
                    'icon' => 'fas fa-clock',
                    'title' => 'درخواست‌های در انتظار',
                    'message' => '18 درخواست در انتظار بررسی است',
                    'action_url' => url('requests?status=pending'),
                    'created_at' => '1404/10/13 10:30'
                ],
                [
                    'type' => 'success',
                    'icon' => 'fas fa-check-circle',
                    'title' => 'تکمیل موفق',
                    'message' => '5 درخواست امروز تکمیل شد',
                    'action_url' => url('requests?status=completed'),
                    'created_at' => '1404/10/13 09:15'
                ]
            ];

            $this->data['user_tasks'] = [
                [
                    'type' => 'review',
                    'icon' => 'fas fa-eye',
                    'title' => 'بررسی درخواست',
                    'description' => 'خرید تجهیزات اداری',
                    'priority' => 'normal',
                    'due_date' => '1404/10/15',
                    'action_url' => url('requests/show/2'),
                    'reference' => 'REQ-2025-0002'
                ]
            ];

            // اطلاعات نقش کاربر
            $this->data['user_role_label'] = USER_ROLES[$userRole] ?? $userRole;

            // تاریخ و زمان
            $this->data['current_date'] = jdate('Y/m/d');
            $this->data['current_time'] = jdate('H:i');

            // داده‌های نمودار زیبا
            $this->data['weekly_chart_data'] = [
                'labels' => ['10/07', '10/08', '10/09', '10/10', '10/11', '10/12', '10/13'],
                'requests' => [5, 8, 3, 12, 7, 9, 8],
                'amounts' => [150, 280, 95, 420, 210, 350, 180]
            ];

            // Render view
            $this->render('dashboard/index', [
                'title' => 'داشبورد سامانت',
                'stats' => $this->data['stats'],
                'recent_requests' => $this->data['recent_requests'],
                'urgent_requests' => $this->data['urgent_requests'],
                'user_tasks' => $this->data['user_tasks'],
                'notifications' => $this->data['notifications'],
                'weekly_chart_data' => $this->data['weekly_chart_data'],
                'additional_css' => ['css/dashboard.css'] // نام استاندارد جدید
            ]);

        } catch (Exception $e) {
            writeLog("خطا در نمایش داشبورد: " . $e->getMessage(), 'ERROR');
            $this->sendError('خطا در بارگذاری داشبورد: ' . $e->getMessage(), 500);
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
     * دریافت آخرین درخواست‌ها
     */
    private function getRecentRequests($userId, $userRole, $userGroupId) 
    {
        try {
            $limit = 10;
            
            if ($userRole === 'admin' || $userRole === 'manager') {
                // مدیران تمام درخواست‌ها را می‌بینند
                return $this->paymentRequestModel->getRecentRequests($limit);
            } else {
                // کاربران عادی فقط درخواست‌های گروه خود را می‌بینند
                return $this->paymentRequestModel->getRecentRequests($limit, $userGroupId);
            }

        } catch (Exception $e) {
            writeLog("خطا در دریافت آخرین درخواست‌ها: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت درخواست‌های فوری
     */
    private function getUrgentRequests($userId, $userRole, $userGroupId) 
    {
        try {
            if ($userRole === 'admin' || $userRole === 'manager') {
                return $this->paymentRequestModel->getUrgentRequests();
            } else {
                return $this->paymentRequestModel->getUrgentRequests($userGroupId);
            }

        } catch (Exception $e) {
            writeLog("خطا در دریافت درخواست‌های فوری: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت درخواست‌های منقضی
     */
    private function getExpiredRequests($userId, $userRole, $userGroupId) 
    {
        try {
            if ($userRole === 'admin' || $userRole === 'manager') {
                return $this->paymentRequestModel->getExpiredRequests();
            } else {
                return $this->paymentRequestModel->getExpiredRequests($userGroupId);
            }

        } catch (Exception $e) {
            writeLog("خطا در دریافت درخواست‌های منقضی: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت آمار گروه
     */
    private function getGroupStats($groupId) 
    {
        try {
            $groupDetails = $this->groupModel->getGroupDetails($groupId);
            
            if (!$groupDetails) {
                return null;
            }

            return [
                'name' => $groupDetails['name'],
                'member_count' => $groupDetails['member_count'],
                'request_stats' => $groupDetails['request_stats'],
                'color' => $groupDetails['color'],
                'icon' => $groupDetails['icon']
            ];

        } catch (Exception $e) {
            writeLog("خطا در دریافت آمار گروه: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }

    /**
     * دریافت آمار ماهانه
     */
    private function getMonthlyStats($userId, $userRole, $userGroupId) 
    {
        try {
            $currentYear = (int)jdate('Y');
            $currentMonth = (int)jdate('n');
            
            $groupFilter = null;
            if ($userRole !== 'admin' && $userRole !== 'manager') {
                $groupFilter = $userGroupId;
            }

            $monthlyReport = $this->paymentRequestModel->getMonthlyReport($currentYear, $currentMonth, $groupFilter);
            
            return $monthlyReport['summary'] ?? [];

        } catch (Exception $e) {
            writeLog("خطا در دریافت آمار ماهانه: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت داده‌های نمودار هفتگی
     */
    private function getWeeklyChartData($userId, $userRole, $userGroupId) 
    {
        try {
            $chartData = [
                'labels' => [],
                'requests' => [],
                'amounts' => []
            ];

            // دریافت آمار 7 روز گذشته
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $jalaliDate = jdate('m/d', strtotime($date));
                
                $chartData['labels'][] = $jalaliDate;

                // آمار روزانه
                $dayStats = $this->getDayStats($date, $userRole, $userGroupId);
                $chartData['requests'][] = $dayStats['count'];
                $chartData['amounts'][] = $dayStats['amount'];
            }

            return $chartData;

        } catch (Exception $e) {
            writeLog("خطا در دریافت داده‌های نمودار: " . $e->getMessage(), 'ERROR');
            return ['labels' => [], 'requests' => [], 'amounts' => []];
        }
    }

    /**
     * دریافت اعلان‌های مهم
     */
    private function getImportantNotifications($userId, $userRole, $userGroupId) 
    {
        try {
            $notifications = [];

            // درخواست‌های نیازمند توجه
            $pendingCount = count($this->paymentRequestModel->getGroupRequests($userGroupId, ['status' => 'pending'])['data'] ?? []);
            if ($pendingCount > 0) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'fas fa-clock',
                    'title' => 'درخواست‌های در انتظار',
                    'message' => fa_num($pendingCount) . ' درخواست در انتظار بررسی است',
                    'action_url' => url('requests?status=pending'),
                    'created_at' => jdate('Y/m/d H:i')
                ];
            }

            // درخواست‌های منقضی
            $expiredRequests = $this->getExpiredRequests($userId, $userRole, $userGroupId);
            if (count($expiredRequests) > 0) {
                $notifications[] = [
                    'type' => 'danger',
                    'icon' => 'fas fa-exclamation-triangle',
                    'title' => 'درخواست‌های منقضی',
                    'message' => fa_num(count($expiredRequests)) . ' درخواست از موعد مقرر گذشته است',
                    'action_url' => url('requests?expired=1'),
                    'created_at' => jdate('Y/m/d H:i')
                ];
            }

            // آمار مثبت (تکمیل درخواست‌ها)
            $completedToday = $this->getTodayStats($userGroupId)['completed'] ?? 0;
            if ($completedToday > 0) {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => 'fas fa-check-circle',
                    'title' => 'تکمیل موفق',
                    'message' => fa_num($completedToday) . ' درخواست امروز تکمیل شد',
                    'action_url' => url('requests?status=completed'),
                    'created_at' => jdate('Y/m/d H:i')
                ];
            }

            // اگر مدیر است، اعلان‌های مدیریتی اضافه کن
            if ($userRole === 'admin' || $userRole === 'manager') {
                $newUsersToday = $this->getNewUsersToday();
                if ($newUsersToday > 0) {
                    $notifications[] = [
                        'type' => 'info',
                        'icon' => 'fas fa-user-plus',
                        'title' => 'کاربران جدید',
                        'message' => fa_num($newUsersToday) . ' کاربر امروز ثبت‌نام کرده است',
                        'action_url' => url('users'),
                        'created_at' => jdate('Y/m/d H:i')
                    ];
                }
            }

            return array_slice($notifications, 0, 5); // حداکثر 5 اعلان

        } catch (Exception $e) {
            writeLog("خطا در دریافت اعلان‌ها: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت وظایف کاربر
     */
    private function getUserTasks($userId, $userRole, $userGroupId) 
    {
        try {
            $tasks = [];

            // درخواست‌هایی که کاربر باید بررسی کند
            if ($userRole === 'accountant' || $userRole === 'manager' || $userRole === 'admin') {
                $pendingRequests = $this->paymentRequestModel->getGroupRequests($userGroupId, [
                    'status' => 'pending',
                    'per_page' => 5
                ]);

                foreach ($pendingRequests['data'] ?? [] as $request) {
                    $tasks[] = [
                        'type' => 'review',
                        'icon' => 'fas fa-eye',
                        'title' => 'بررسی درخواست',
                        'description' => $request['title'],
                        'priority' => $request['priority'],
                        'due_date' => $request['due_date'],
                        'action_url' => url("requests/view/{$request['id']}"),
                        'reference' => $request['reference_number']
                    ];
                }
            }

            // درخواست‌های کاربر که نیاز به اقدام دارند
            $userPendingRequests = $this->paymentRequestModel->getUserRequests($userId, [
                'status' => 'processing',
                'per_page' => 3
            ]);

            foreach ($userPendingRequests['data'] ?? [] as $request) {
                $tasks[] = [
                    'type' => 'follow_up',
                    'icon' => 'fas fa-clock',
                    'title' => 'پیگیری درخواست',
                    'description' => $request['title'],
                    'priority' => $request['priority'],
                    'due_date' => $request['due_date'],
                    'action_url' => url("requests/view/{$request['id']}"),
                    'reference' => $request['reference_number']
                ];
            }

            // مرتب‌سازی بر اساس اولویت
            usort($tasks, function($a, $b) {
                $priorityOrder = ['urgent' => 4, 'high' => 3, 'normal' => 2, 'low' => 1];
                $aPriority = $priorityOrder[$a['priority']] ?? 0;
                $bPriority = $priorityOrder[$b['priority']] ?? 0;
                return $bPriority - $aPriority;
            });

            return array_slice($tasks, 0, 8); // حداکثر 8 وظیفه

        } catch (Exception $e) {
            writeLog("خطا در دریافت وظایف کاربر: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * API برای دریافت آمار زنده
     */
    public function getLiveStats() 
    {
        try {
            $userId = $_SESSION['user_id'];
            $userRole = $_SESSION['user_role'];
            $userGroupId = $_SESSION['group_id'];

            $stats = $this->getDashboardStats($userId, $userRole, $userGroupId);
            
            $this->json([
                'stats' => $stats,
                'last_update' => jdate('Y/m/d H:i:s')
            ]);

        } catch (Exception $e) {
            $this->sendError('خطا در دریافت آمار زنده', 500);
        }
    }

    /**
     * API برای دریافت اعلان‌های جدید
     */
    public function getNewNotifications() 
    {
        try {
            $userId = $_SESSION['user_id'];
            $userRole = $_SESSION['user_role'];
            $userGroupId = $_SESSION['group_id'];

            $notifications = $this->getImportantNotifications($userId, $userRole, $userGroupId);
            
            $this->json([
                'notifications' => $notifications,
                'count' => count($notifications)
            ]);

        } catch (Exception $e) {
            $this->sendError('خطا در دریافت اعلان‌ها', 500);
        }
    }

    /**
     * دریافت آمار امروز
     */
    private function getTodayStats($groupFilter = null) 
    {
        try {
            $today = date('Y-m-d');
            return $this->paymentRequestModel->getRequestStats($groupFilter, $today, $today);

        } catch (Exception $e) {
            return ['total' => 0, 'completed' => 0, 'pending' => 0];
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
     * صفحه راهنما
     */
    public function help() 
    {
        $this->data['page_title'] = 'راهنمای استفاده از سامانت';
        $this->view('dashboard/help');
    }

    /**
     * صفحه درباره سامانت
     */
    public function about() 
    {
        $this->data['page_title'] = 'درباره سامانت';
        $this->data['version'] = APP_VERSION;
        $this->view('dashboard/about');
    }
}
?>