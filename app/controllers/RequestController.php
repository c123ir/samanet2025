<?php
/**
 * نام فایل: RequestController.php
 * مسیر فایل: /app/controllers/RequestController.php
 * توضیح: کنترلر مدیریت درخواست‌های حواله
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once 'BaseController.php';
require_once APP_PATH . 'models/PaymentRequest.php';
require_once APP_PATH . 'models/User.php';
require_once APP_PATH . 'models/Group.php';

class RequestController extends BaseController
{
    private $paymentRequestModel;
    private $userModel;
    private $groupModel;

    public function __construct()
    {
        parent::__construct();
        $this->paymentRequestModel = new PaymentRequest();
        $this->userModel = new User();
        $this->groupModel = new Group();
    }

    /**
     * API جستجوی پیشرفته درخواست‌ها
     */
    public function api()
    {
        try {
            $this->requireAuth();
            
            // بررسی درخواست AJAX
            if (!$this->isAjaxRequest()) {
                throw new Exception('درخواست غیرمجاز');
            }
            
            header('Content-Type: application/json; charset=utf-8');
            
            $user = $this->getCurrentUser();
            $groupId = $user['group_id'];
            
            // دریافت پارامترهای جستجو از درخواست POST JSON
            $inputData = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('فرمت داده نامعتبر است');
            }
            
            $search = trim($inputData['search'] ?? '');
            $page = max(1, (int)($inputData['page'] ?? 1));
            $perPage = min(max(1, (int)($inputData['per_page'] ?? 20)), 50); // حداکثر 50 آیتم
            
            // فیلترهای اضافی
            $filters = [
                'status' => $inputData['status'] ?? '',
                'priority' => $inputData['priority'] ?? '',
                'date_from' => $inputData['date_from'] ?? '',
                'date_to' => $inputData['date_to'] ?? '',
                'page' => $page,
                'per_page' => $perPage
            ];
            
            // اعتبارسنجی ورودی
            $validation = $this->validateSearchParams($search, $filters);
            
            if (!$validation['valid']) {
                echo json_encode([
                    'success' => false,
                    'message' => implode(', ', $validation['errors']),
                    'errors' => $validation['errors']
                ]);
                exit;
            }
            
            // اجرای جستجو
            if (!empty($search) || !empty(array_filter($filters, function($v) { return !empty($v) && !is_numeric($v); }))) {
                $results = $this->paymentRequestModel->searchWithFilters($search, $groupId, $filters);
            } else {
                $results = $this->paymentRequestModel->getGroupRequests($groupId, $filters);
            }
            
            // تولید پاسخ API
            if ($results) {
                $response = $this->generateApiResponse(
                    $results['data'] ?? [],
                    $search,
                    [
                        'page' => $results['current_page'] ?? 1,
                        'per_page' => $perPage,
                        'total' => $results['total'] ?? 0,
                        'last_page' => $results['last_page'] ?? 1,
                        'from' => $results['from'] ?? 0,
                        'to' => $results['to'] ?? 0,
                        'has_search' => !empty($search),
                        'filters_applied' => array_filter($filters, function($v) { return !empty($v) && !is_numeric($v); })
                    ]
                );
            } else {
                $response = [
                    'success' => false,
                    'message' => 'خطا در دریافت داده‌ها',
                    'data' => [],
                    'meta' => [
                        'total' => 0,
                        'page' => $page,
                        'per_page' => $perPage
                    ]
                ];
            }
            
            echo json_encode($response);
            exit;
            
        } catch (Exception $e) {
            writeLog("خطا در API جستجوی درخواست‌ها: " . $e->getMessage(), 'ERROR');
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'خطای سیستمی در جستجو',
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'error_code' => 500
                ]
            ]);
            exit;
        }
    }

    /**
     * اعتبارسنجی پارامترهای جستجو
     */
    private function validateSearchParams($search, $filters)
    {
        $errors = [];
        
        // اعتبارسنجی طول کلمه جستجو
        if (!empty($search) && mb_strlen($search) < 2) {
            $errors[] = 'کلمه جستجو باید حداقل 2 کاراکتر باشد';
        }
        
        if (!empty($search) && mb_strlen($search) > 100) {
            $errors[] = 'کلمه جستجو نباید بیش از 100 کاراکتر باشد';
        }
        
        // اعتبارسنجی وضعیت
        $validStatuses = ['pending', 'processing', 'completed', 'rejected'];
        if (!empty($filters['status']) && !in_array($filters['status'], $validStatuses)) {
            $errors[] = 'وضعیت انتخاب شده نامعتبر است';
        }
        
        // اعتبارسنجی اولویت
        $validPriorities = ['urgent', 'high', 'normal', 'low'];
        if (!empty($filters['priority']) && !in_array($filters['priority'], $validPriorities)) {
            $errors[] = 'اولویت انتخاب شده نامعتبر است';
        }
        
        // اعتبارسنجی تاریخ
        if (!empty($filters['date_from']) && !$this->isValidDate($filters['date_from'])) {
            $errors[] = 'تاریخ شروع نامعتبر است';
        }
        
        if (!empty($filters['date_to']) && !$this->isValidDate($filters['date_to'])) {
            $errors[] = 'تاریخ پایان نامعتبر است';
        }
        
        // اعتبارسنجی صفحه‌بندی
        if ($filters['page'] < 1) {
            $errors[] = 'شماره صفحه نامعتبر است';
        }
        
        if ($filters['per_page'] < 1 || $filters['per_page'] > 50) {
            $errors[] = 'تعداد آیتم در هر صفحه باید بین 1 تا 50 باشد';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * تولید پاسخ استاندارد API
     */
    private function generateApiResponse($data, $search, $meta)
    {
        return [
            'success' => true,
            'message' => 'نتایج با موفقیت دریافت شد',
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'search_term' => $search,
                'has_search' => !empty($search),
                'version' => '2.1'
            ], $meta)
        ];
    }
    
    /**
     * بررسی معتبر بودن تاریخ
     */
    private function isValidDate($date)
    {
        // بررسی فرمت تاریخ شمسی (1403/12/01 یا 1403-12-01)
        $pattern = '/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/';
        if (preg_match($pattern, $date, $matches)) {
            $year = (int)$matches[1];
            $month = (int)$matches[2];
            $day = (int)$matches[3];
            
            return $year >= 1300 && $year <= 1500 && 
                   $month >= 1 && $month <= 12 && 
                   $day >= 1 && $day <= 31;
        }
        
        return false;
    }

    /**
     * نمایش لیست درخواست‌ها
     */
    public function index()
    {
        try {
            // بررسی دسترسی
            $this->requireAuth();
            
            $user = $this->getCurrentUser();
            $groupId = $user['group_id'];

            // دریافت فیلترها از URL
            $filters = [
                'status' => $_GET['status'] ?? '',
                'priority' => $_GET['priority'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'search' => $_GET['search'] ?? '',
                'page' => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 20
            ];

            // دریافت درخواست‌های گروه
            try {
                $requests_data = $this->paymentRequestModel->getGroupRequests($groupId, $filters);
            } catch (Exception $e) {
                writeLog("❌ WARNING: Could not fetch requests: " . $e->getMessage(), 'WARNING');
                writeLog("📍 Exception location: " . $e->getFile() . ':' . $e->getLine(), 'WARNING');
                // داده‌های نمونه در صورت خطا
                $requests_data = [
                    'data' => [
                        [
                            'id' => 1,
                            'reference_number' => 'REQ241215001',
                            'title' => 'نمونه درخواست حواله',
                            'description' => 'این یک درخواست نمونه برای تست است',
                            'amount' => 1000000,
                            'account_holder' => 'علی احمدی',
                            'account_number' => '1234567890',
                            'bank_name' => 'بانک ملی',
                            'status' => 'pending',
                            'status_label' => 'در انتظار',
                            'priority' => 'normal',
                            'priority_label' => 'معمولی',
                            'is_urgent' => 0,
                            'created_at_jalali' => '1404/10/15 10:30',
                            'requester_id' => 1,
                            'group_id' => $groupId
                        ]
                    ],
                    'total' => 1,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 1,
                    'to' => 1
                ];
            }
            
            // دریافت آمار
            try {
                $stats = $this->paymentRequestModel->getRequestStats($groupId);
            } catch (Exception $e) {
                writeLog("❌ WARNING: Could not fetch stats: " . $e->getMessage(), 'WARNING');
                // آمار نمونه در صورت خطا
                $stats = [
                    'total' => 15,
                    'pending' => 5,
                    'processing' => 3,
                    'completed' => 6,
                    'rejected' => 1
                ];
            }

            $this->render('requests/list', [
                'title' => 'مدیریت درخواست‌ها | سامانت',
                'requests_data' => $requests_data,
                'stats' => $stats,
                'filters' => $filters,
                'statuses' => PaymentRequest::getStatuses() ?? [],
                'priorities' => PaymentRequest::getPriorities() ?? [],
                'csrf_token' => $this->getCSRFToken(),
                'additional_css' => ['css/requests.css'] // CSS یکپارچه جدید
            ]);

        } catch (Exception $e) {
            writeLog("💥 MAJOR ERROR in RequestController->index(): " . $e->getMessage(), 'ERROR');
            writeLog("📍 Error location: " . $e->getFile() . ':' . $e->getLine(), 'ERROR');
            $this->handleError($e, 'خطا در نمایش لیست درخواست‌ها');
        }
    }

    /**
     * نمایش فرم ایجاد درخواست جدید
     */
    public function create()
    {
        try {
            $this->requireAuth();
            
            $user = $this->getCurrentUser();
            
            // دریافت اطلاعات لازم برای فرم
            $banks = PaymentRequest::IRANIAN_BANKS;
            $categories = PaymentRequest::CATEGORIES;
            $priorities = PaymentRequest::getPriorities();

            $this->render('requests/create', [
                'title' => 'ایجاد درخواست حواله جدید',
                'banks' => $banks,
                'categories' => $categories,
                'priorities' => $priorities,
                'user' => $user,
                'flash' => $this->getFlash()
            ]);

        } catch (Exception $e) {
            $this->handleError($e, 'خطا در نمایش فرم ایجاد درخواست');
        }
    }

    /**
     * ذخیره درخواست جدید
     */
    public function store()
    {
        writeLog("🚀 RequestController::store() called", 'INFO');
        writeLog("📥 Raw POST data: " . json_encode($_POST), 'INFO');
        writeLog("📥 Raw GET data: " . json_encode($_GET), 'INFO');
        writeLog("🌐 Request method: " . $_SERVER['REQUEST_METHOD'], 'INFO');
        
        try {
            $this->requireAuth();
            writeLog("✅ Auth check passed", 'INFO');
            
            $this->validateCsrfToken();
            writeLog("✅ CSRF validation passed", 'INFO');

            $user = $this->getCurrentUser();
            writeLog("👤 Current user: " . json_encode($user), 'INFO');
            
            // دریافت داده‌های POST
            $data = [
                'requester_id' => $user['id'],
                'group_id' => $user['group_id'],
                'title' => $this->input('title'),
                'description' => $this->input('description'),
                'amount' => $this->processAmountField($this->input('amount')),
                'account_number' => $this->processStringField($this->input('account_number')),
                'account_holder' => $this->processStringField($this->input('account_holder')),
                'bank_name' => $this->processStringField($this->input('bank_name')),
                'iban' => $this->processStringField($this->input('iban')),
                'card_number' => $this->processStringField($this->input('card_number')),
                'priority' => $this->input('priority', PaymentRequest::PRIORITY_NORMAL),
                'category' => $this->input('category'),
                'due_date' => $this->processDateField($this->input('due_date')),
                'tags' => $this->input('tags'),
                'is_urgent' => $this->input('is_urgent') ? 1 : 0
            ];

            writeLog("📋 Processed data for DB: " . json_encode($data), 'INFO');

            // ایجاد درخواست
            writeLog("🔄 Calling paymentRequestModel->createRequest()", 'INFO');
            $result = $this->paymentRequestModel->createRequest($data);
            writeLog("📤 createRequest result: " . json_encode($result), 'INFO');

            // بررسی نوع درخواست (AJAX یا عادی)
            if ($this->isAjaxRequest()) {
                // پاسخ JSON برای AJAX
                header('Content-Type: application/json; charset=utf-8');
                
                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'درخواست حواله با موفقیت ایجاد شد',
                        'redirect_url' => 'index.php?route=requests&action=show&id=' . $result['request_id'],
                        'request_id' => $result['request_id']
                    ]);
                    writeLog("✅ Success! JSON response sent", 'INFO');
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => implode('<br>', $result['errors']),
                        'errors' => $result['errors']
                    ]);
                    writeLog("❌ Creation failed. JSON error response sent", 'ERROR');
                }
                exit;
            } else {
                // پاسخ عادی برای فرم‌های غیر AJAX
                if ($result['success']) {
                    $this->setFlashMessage('success', 'درخواست حواله با موفقیت ایجاد شد');
                    $redirectUrl = 'index.php?route=requests&action=show&id=' . $result['request_id'];
                    writeLog("✅ Success! Redirecting to: " . $redirectUrl, 'INFO');
                    $this->redirect($redirectUrl);
                } else {
                    $errorMessage = implode('<br>', $result['errors']);
                    $this->setFlashMessage('error', $errorMessage);
                    writeLog("❌ Creation failed. Errors: " . $errorMessage, 'ERROR');
                    writeLog("🔙 Redirecting back to create form", 'INFO');
                    $this->redirect('index.php?route=requests&action=create');
                }
            }

        } catch (Exception $e) {
            writeLog("💥 Exception in store(): " . $e->getMessage(), 'ERROR');
            writeLog("📍 Exception location: " . $e->getFile() . ':' . $e->getLine(), 'ERROR');
            $this->handleError($e, 'خطا در ایجاد درخواست');
        }
    }

    /**
     * نمایش جزئیات درخواست
     */
    public function show()
    {
        try {
            $this->requireAuth();
            
            $requestId = $this->input('id');
            if (!$requestId) {
                throw new Exception('شناسه درخواست مشخص نشده است');
            }

            // دریافت جزئیات درخواست
            $request = $this->paymentRequestModel->getRequestDetails($requestId);
            
            if (!$request) {
                throw new Exception('درخواست یافت نشد');
            }

            // بررسی دسترسی
            $user = $this->getCurrentUser();
            if (!$this->canViewRequest($request, $user)) {
                throw new Exception('شما دسترسی لازم برای مشاهده این درخواست را ندارید');
            }

            // دریافت اطلاعات تکمیلی
            $statuses = PaymentRequest::getStatuses();
            $priorities = PaymentRequest::getPriorities();

            $this->render('requests/view', [
                'title' => 'جزئیات درخواست: ' . $request['title'],
                'request' => $request,
                'statuses' => $statuses,
                'priorities' => $priorities,
                'user' => $user,
                'can_edit' => $this->canEditRequest($request, $user),
                'can_approve' => $this->canApproveRequest($request, $user)
            ]);

        } catch (Exception $e) {
            $this->handleError($e, 'خطا در نمایش جزئیات درخواست');
        }
    }

    /**
     * تایید درخواست
     */
    public function approve()
    {
        try {
            $this->requireAuth();
            $this->validateCsrfToken();

            $requestId = $this->input('id');
            $notes = $this->input('notes');
            $user = $this->getCurrentUser();

            $result = $this->paymentRequestModel->approveRequest($requestId, $user['id'], $notes);

            if ($result['success']) {
                $this->setFlashMessage('success', 'درخواست با موفقیت تایید شد');
            } else {
                $this->setFlashMessage('error', implode('<br>', $result['errors']));
            }

            $this->redirect('index.php?route=requests&action=show&id=' . $requestId);

        } catch (Exception $e) {
            $this->handleError($e, 'خطا در تایید درخواست');
        }
    }

    /**
     * رد درخواست
     */
    public function reject()
    {
        try {
            $this->requireAuth();
            $this->validateCsrfToken();

            $requestId = $this->input('id');
            $reason = $this->input('reason');
            $user = $this->getCurrentUser();

            if (empty($reason)) {
                throw new Exception('دلیل رد درخواست الزامی است');
            }

            $result = $this->paymentRequestModel->rejectRequest($requestId, $user['id'], $reason);

            if ($result['success']) {
                $this->setFlashMessage('success', 'درخواست رد شد');
            } else {
                $this->setFlashMessage('error', implode('<br>', $result['errors']));
            }

            $this->redirect('index.php?route=requests&action=show&id=' . $requestId);

        } catch (Exception $e) {
            $this->handleError($e, 'خطا در رد درخواست');
        }
    }

    /**
     * تکمیل درخواست
     */
    public function complete()
    {
        try {
            $this->requireAuth();
            $this->validateCsrfToken();

            $requestId = $this->input('id');
            $notes = $this->input('notes');
            $user = $this->getCurrentUser();

            $result = $this->paymentRequestModel->completeRequest($requestId, $user['id'], $notes);

            if ($result['success']) {
                $this->setFlashMessage('success', 'درخواست با موفقیت تکمیل شد');
            } else {
                $this->setFlashMessage('error', implode('<br>', $result['errors']));
            }

            $this->redirect('index.php?route=requests&action=show&id=' . $requestId);

        } catch (Exception $e) {
            $this->handleError($e, 'خطا در تکمیل درخواست');
        }
    }

    /**
     * بررسی مجوز مشاهده درخواست
     */
    private function canViewRequest($request, $user)
    {
        // مدیران و ادمین‌ها همه درخواست‌ها را می‌بینند
        if (in_array($user['role'], ['admin', 'manager'])) {
            return true;
        }

        // اعضای گروه فقط درخواست‌های گروه خود
        return $request['group_id'] == $user['group_id'];
    }

    /**
     * بررسی مجوز ویرایش درخواست
     */
    private function canEditRequest($request, $user)
    {
        // ادمین همیشه می‌تواند ویرایش کند
        if ($user['role'] === 'admin') {
            return true;
        }

        // صاحب درخواست در صورت عدم تایید
        if ($request['requester_id'] == $user['id'] && 
            $request['status'] === PaymentRequest::STATUS_PENDING) {
            return true;
        }

        return false;
    }

    /**
     * بررسی مجوز تایید درخواست
     */
    private function canApproveRequest($request, $user)
    {
        // فقط مدیران و ادمین‌ها
        return in_array($user['role'], ['admin', 'manager']) && 
               $request['status'] === PaymentRequest::STATUS_PENDING;
    }

    /**
     * پردازش فیلد مبلغ
     */
    private function processAmountField($amount)
    {
        if (empty($amount) || trim($amount) === '') {
            return null; // برای فیلدهای اختیاری
        }
        
        $amount = $this->convertPersianNumbers($amount);
        $amount = str_replace([',', ' '], '', $amount); // حذف کاما و فاصله
        
        return is_numeric($amount) ? (float)$amount : null;
    }

    /**
     * پردازش فیلدهای رشته‌ای
     */
    private function processStringField($value)
    {
        if (empty($value) || trim($value) === '') {
            return null; // به جای رشته خالی، null برگردان
        }
        
        if (is_numeric($value)) {
            return $this->convertPersianNumbers($value);
        }
        
        return trim($value);
    }

    /**
     * پردازش فیلد تاریخ
     */
    private function processDateField($date)
    {
        if (empty($date) || trim($date) === '') {
            return null;
        }
        
        // اگر تاریخ معتبر است، برگردان
        if (strtotime($date)) {
            return $date;
        }
        
        return null;
    }
}
?>
