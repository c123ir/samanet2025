<?php
/**
 * نام فایل: PaymentRequest.php
 * مسیر فایل: /app/models/PaymentRequest.php
 * توضیح: مدل درخواست‌های حواله سامانت
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once APP_PATH . 'models/Database.php';

class PaymentRequest extends Database
{
    private $db;
    protected $table = 'payment_requests';
    protected $fillable = [
        'requester_id', 'group_id', 'title', 'description', 'amount',
        'account_number', 'account_holder', 'bank_name', 'iban', 'card_number',
        'priority', 'status', 'assigned_to', 'due_date', 'reference_number',
        'category', 'tags', 'is_urgent', 'approval_notes', 'rejection_reason'
    ];
    protected $hidden = [];
    
    public function __construct() 
    {
        parent::__construct(); // فراخوانی constructor پدر
        $this->db = $this->connection; // استفاده از connection از کلاس پدر
    }

    /**
     * وضعیت‌های درخواست
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED = 'approved';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * اولویت‌های درخواست
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * دسته‌بندی‌های درخواست
     */
    const CATEGORIES = [
        'salary' => 'حقوق و دستمزد',
        'supplier' => 'پرداخت به تامین‌کنندگان',
        'expense' => 'هزینه‌های عملیاتی',
        'investment' => 'سرمایه‌گذاری',
        'loan' => 'وام و تسهیلات',
        'tax' => 'مالیات و عوارض',
        'insurance' => 'بیمه',
        'utility' => 'آب، برق، گاز',
        'rent' => 'اجاره',
        'other' => 'سایر موارد'
    ];

    /**
     * دریافت لیست وضعیت‌های درخواست
     */
    public static function getStatuses() 
    {
        return [
            self::STATUS_PENDING => 'در انتظار',
            self::STATUS_PROCESSING => 'در حال پردازش',
            self::STATUS_APPROVED => 'تایید شده',
            self::STATUS_COMPLETED => 'تکمیل شده',
            self::STATUS_REJECTED => 'رد شده',
            self::STATUS_CANCELLED => 'لغو شده'
        ];
    }

    /**
     * دریافت لیست اولویت‌های درخواست
     */
    public static function getPriorities() 
    {
        return [
            self::PRIORITY_LOW => 'کم',
            self::PRIORITY_NORMAL => 'معمولی',
            self::PRIORITY_HIGH => 'بالا',
            self::PRIORITY_URGENT => 'فوری'
        ];
    }

    /**
     * ثابت برای سازگاری با کدهای قدیمی
     */
    const REQUEST_PRIORITIES = [
        'low' => 'کم',
        'normal' => 'معمولی',
        'high' => 'بالا',
        'urgent' => 'فوری'
    ];

    /**
     * بانک‌های ایرانی
     */
    const IRANIAN_BANKS = [
        'mellat' => 'بانک ملت',
        'melli' => 'بانک ملی ایران',
        'tejarat' => 'بانک تجارت',
        'saderat' => 'بانک صادرات ایران',
        'parsian' => 'بانک پارسیان',
        'pasargad' => 'بانک پاسارگاد',
        'dey' => 'بانک دی',
        'eghtesad_novin' => 'بانک اقتصاد نوین',
        'sepah' => 'بانک سپه',
        'refah' => 'بانک رفاه کارگران',
        'saman' => 'بانک سامان',
        'sina' => 'بانک سینا',
        'shahr' => 'بانک شهر',
        'karafarin' => 'بانک کارآفرین',
        'middle_east' => 'بانک خاورمیانه',
        'ayandeh' => 'بانک آینده'
    ];

    /**
     * ایجاد درخواست حواله جدید
     */
    public function createRequest($data) 
    {
        writeLog("🚀 PaymentRequest::createRequest() called", 'INFO');
        writeLog("📥 Input data: " . json_encode($data), 'INFO');
        
        try {
            // اعتبارسنجی داده‌های ورودی
            writeLog("🔍 Starting validation...", 'INFO');
            $validation = $this->validateRequestData($data);
            if ($validation !== true) {
                writeLog("❌ Validation failed: " . json_encode($validation), 'ERROR');
                return ['success' => false, 'errors' => $validation];
            }
            writeLog("✅ Validation passed", 'INFO');

            // تولید شماره مرجع یکتا
            writeLog("🔢 Generating reference number...", 'INFO');
            $data['reference_number'] = $this->generateReferenceNumber();
            writeLog("📋 Reference number generated: " . $data['reference_number'], 'INFO');

            // تنظیم مقادیر پیش‌فرض
            $data['status'] = $data['status'] ?? self::STATUS_PENDING;
            $data['priority'] = $data['priority'] ?? self::PRIORITY_NORMAL;
            $data['is_urgent'] = $data['is_urgent'] ?? 0;
            $data['created_at'] = date('Y-m-d H:i:s');

            // پردازش تگ‌ها
            if (isset($data['tags'])) {
                if (is_array($data['tags'])) {
                    $data['tags'] = json_encode($data['tags']);
                } elseif (is_string($data['tags'])) {
                    $tags = explode(',', $data['tags']);
                    $data['tags'] = json_encode(array_map('trim', $tags));
                }
            }

            writeLog("📋 Final data before DB insert: " . json_encode($data), 'INFO');

            // شروع تراکنش
            writeLog("🔄 Starting transaction...", 'INFO');
            $this->beginTransaction();

            try {
                // ایجاد درخواست
                writeLog("💾 Calling create() method...", 'INFO');
                $requestId = $this->create($data);
                writeLog("📤 create() returned: " . ($requestId ?: 'false'), 'INFO');
                
                if (!$requestId) {
                    throw new Exception('خطا در ایجاد درخواست - create() returned false');
                }

                // ثبت در تاریخچه گردش کار
                writeLog("📊 Adding workflow history...", 'INFO');
                $historyResult = $this->addWorkflowHistory($requestId, $data['requester_id'], 'created', null, self::STATUS_PENDING, 'درخواست ایجاد شد');
                writeLog("📊 Workflow history result: " . ($historyResult ? 'success' : 'failed'), 'INFO');

                // تایید تراکنش
                writeLog("✅ Committing transaction...", 'INFO');
                $this->commit();
                writeLog("✅ Transaction committed successfully", 'INFO');

                writeLog("درخواست حواله جدید ایجاد شد: {$data['reference_number']} توسط کاربر: {$data['requester_id']}", 'INFO');
                
                $result = [
                    'success' => true, 
                    'request_id' => $requestId,
                    'reference_number' => $data['reference_number']
                ];
                
                writeLog("🎉 createRequest() finished successfully: " . json_encode($result), 'INFO');
                return $result;

            } catch (Exception $e) {
                writeLog("💥 Exception in transaction: " . $e->getMessage(), 'ERROR');
                $this->rollback();
                writeLog("🔙 Transaction rolled back", 'INFO');
                throw $e;
            }

        } catch (Exception $e) {
            writeLog("💥 Exception in createRequest(): " . $e->getMessage(), 'ERROR');
            writeLog("📍 Exception location: " . $e->getFile() . ':' . $e->getLine(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است: ' . $e->getMessage()]];
        }
    }

    /**
     * به‌روزرسانی درخواست
     */
    public function updateRequest($requestId, $data, $userId) 
    {
        try {
            // بررسی وجود درخواست
            $request = $this->find($requestId);
            if (!$request) {
                return ['success' => false, 'errors' => ['درخواست یافت نشد']];
            }

            // بررسی مجوز ویرایش
            if (!$this->canEditRequest($request, $userId)) {
                return ['success' => false, 'errors' => ['شما مجوز ویرایش این درخواست را ندارید']];
            }

            // اعتبارسنجی داده‌ها
            $validation = $this->validateRequestData($data, $requestId);
            if ($validation !== true) {
                return ['success' => false, 'errors' => $validation];
            }

            $oldStatus = $request['status'];
            $newStatus = $data['status'] ?? $oldStatus;

            // پردازش تگ‌ها
            if (isset($data['tags'])) {
                if (is_array($data['tags'])) {
                    $data['tags'] = json_encode($data['tags']);
                } elseif (is_string($data['tags'])) {
                    $tags = explode(',', $data['tags']);
                    $data['tags'] = json_encode(array_map('trim', $tags));
                }
            }

            $data['updated_at'] = date('Y-m-d H:i:s');

            // شروع تراکنش
            $this->beginTransaction();

            try {
                // به‌روزرسانی درخواست
                $result = $this->update($requestId, $data);
                
                if (!$result) {
                    throw new Exception('خطا در به‌روزرسانی درخواست');
                }

                // ثبت تغییر وضعیت در تاریخچه
                if ($oldStatus !== $newStatus) {
                    $this->addWorkflowHistory($requestId, $userId, 'status_changed', $oldStatus, $newStatus, $data['approval_notes'] ?? null);
                } else {
                    $this->addWorkflowHistory($requestId, $userId, 'updated', null, null, 'درخواست به‌روزرسانی شد');
                }

                $this->commit();

                writeLog("درخواست حواله به‌روزرسانی شد: {$request['reference_number']} توسط کاربر: {$userId}", 'INFO');
                return ['success' => true];

            } catch (Exception $e) {
                $this->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            writeLog("خطا در به‌روزرسانی درخواست: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * تایید درخواست
     */
    public function approveRequest($requestId, $userId, $notes = null) 
    {
        return $this->changeRequestStatus($requestId, $userId, self::STATUS_APPROVED, $notes);
    }

    /**
     * رد درخواست
     */
    public function rejectRequest($requestId, $userId, $reason) 
    {
        return $this->changeRequestStatus($requestId, $userId, self::STATUS_REJECTED, null, $reason);
    }

    /**
     * تکمیل درخواست
     */
    public function completeRequest($requestId, $userId, $notes = null) 
    {
        return $this->changeRequestStatus($requestId, $userId, self::STATUS_COMPLETED, $notes);
    }

    /**
     * لغو درخواست
     */
    public function cancelRequest($requestId, $userId, $reason = null) 
    {
        return $this->changeRequestStatus($requestId, $userId, self::STATUS_CANCELLED, null, $reason);
    }

    /**
     * تغییر وضعیت درخواست
     */
    private function changeRequestStatus($requestId, $userId, $newStatus, $notes = null, $reason = null) 
    {
        try {
            $request = $this->find($requestId);
            if (!$request) {
                return ['success' => false, 'errors' => ['درخواست یافت نشد']];
            }

            // بررسی مجوز تغییر وضعیت
            if (!$this->canChangeStatus($request, $userId, $newStatus)) {
                return ['success' => false, 'errors' => ['شما مجوز تغییر وضعیت را ندارید']];
            }

            $oldStatus = $request['status'];

            // داده‌های به‌روزرسانی
            $updateData = [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($notes) {
                $updateData['approval_notes'] = $notes;
            }

            if ($reason) {
                $updateData['rejection_reason'] = $reason;
            }

            if ($newStatus === self::STATUS_COMPLETED) {
                $updateData['completed_at'] = date('Y-m-d H:i:s');
            }

            // شروع تراکنش
            $this->beginTransaction();

            try {
                // به‌روزرسانی درخواست
                $result = $this->update($requestId, $updateData);
                
                if (!$result) {
                    throw new Exception('خطا در تغییر وضعیت');
                }

                // ثبت در تاریخچه
                $comment = $notes ?: $reason ?: "وضعیت به {$this->getStatusLabel($newStatus)} تغییر یافت";
                $this->addWorkflowHistory($requestId, $userId, 'status_changed', $oldStatus, $newStatus, $comment);

                $this->commit();

                writeLog("وضعیت درخواست {$request['reference_number']} از {$oldStatus} به {$newStatus} تغییر یافت توسط کاربر: {$userId}", 'INFO');
                return ['success' => true];

            } catch (Exception $e) {
                $this->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            writeLog("خطا در تغییر وضعیت درخواست: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }

    /**
     * دریافت درخواست‌های گروه
     */
    public function getGroupRequests($groupId, $filters = []) 
    {
        try {
            $conditions = ['group_id = ?'];
            $params = [$groupId];

            // فیلتر وضعیت
            if (!empty($filters['status'])) {
                $conditions[] = 'status = ?';
                $params[] = $filters['status'];
            }

            // فیلتر اولویت
            if (!empty($filters['priority'])) {
                $conditions[] = 'priority = ?';
                $params[] = $filters['priority'];
            }

            // فیلتر تاریخ
            if (!empty($filters['date_from'])) {
                $conditions[] = 'created_at >= ?';
                $params[] = $filters['date_from'] . ' 00:00:00';
            }

            if (!empty($filters['date_to'])) {
                $conditions[] = 'created_at <= ?';
                $params[] = $filters['date_to'] . ' 23:59:59';
            }

            // فیلتر مبلغ
            if (!empty($filters['amount_min'])) {
                $conditions[] = 'amount >= ?';
                $params[] = $filters['amount_min'];
            }

            if (!empty($filters['amount_max'])) {
                $conditions[] = 'amount <= ?';
                $params[] = $filters['amount_max'];
            }

            // فیلتر جستجو
            if (!empty($filters['search'])) {
                $searchTerm = '%' . Security::sanitizeSearchTerm($filters['search']) . '%';
                $conditions[] = '(title LIKE ? OR description LIKE ? OR account_holder LIKE ? OR reference_number LIKE ?)';
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            // ترتیب‌بندی
            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDir = $filters['order_dir'] ?? 'DESC';

            // صفحه‌بندی
            $page = $filters['page'] ?? 1;
            $perPage = $filters['per_page'] ?? 20;

            // ساخت کوئری
            $whereClause = implode(' AND ', $conditions);
            $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY {$orderBy} {$orderDir}";

            // اجرای کوئری با صفحه‌بندی
            $requests = $this->paginate($page, $perPage, [
                'condition' => $whereClause,
                'params' => $params
            ]);

            // اضافه کردن اطلاعات کاربران
            if ($requests && isset($requests['data'])) {
                foreach ($requests['data'] as &$request) {
                    $request = $this->enrichRequestData($request);
                }
            }

            return $requests;

        } catch (Exception $e) {
            writeLog("خطا در دریافت درخواست‌های گروه: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت جزئیات درخواست
     */
    public function getRequestDetails($requestId) 
    {
        try {
            $request = $this->find($requestId);
            if (!$request) {
                return null;
            }

            // اضافه کردن اطلاعات تکمیلی
            $request = $this->enrichRequestData($request);

            // دریافت اسناد مرتبط
            $request['documents'] = $this->getRequestDocuments($requestId);

            // دریافت تاریخچه گردش کار
            $request['workflow_history'] = $this->getWorkflowHistory($requestId);

            return $request;

        } catch (Exception $e) {
            writeLog("خطا در دریافت جزئیات درخواست: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }

    /**
     * دریافت آمار درخواست‌ها
     */
    public function getRequestStats($groupFilter = null, $startDate = null, $endDate = null) 
    {
        $sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as rejected,
            SUM(amount) as total_amount,
            SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount
            FROM payment_requests WHERE 1=1";
        
        $params = [];
        
        if ($groupFilter) {
            $sql .= " AND group_id = ?";
            $params[] = $groupFilter;
        }
        
        if ($startDate) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * تولید شماره مرجع یکتا
     */
    private function generateReferenceNumber() 
    {
        try {
            $prefix = 'REQ';
            $date = date('ymd'); // استفاده از date به جای jdate
            $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            do {
                $referenceNumber = $prefix . $date . $random;
                $exists = $this->first('reference_number', $referenceNumber);
                if ($exists) {
                    $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                }
            } while ($exists);

            return $referenceNumber;
        } catch (Exception $e) {
            writeLog("خطا در تولید شماره مرجع: " . $e->getMessage(), 'ERROR');
            // fallback به شماره ساده
            return 'REQ' . time() . mt_rand(100, 999);
        }
    }

    /**
     * اعتبارسنجی داده‌های درخواست
     */
    private function validateRequestData($data, $requestId = null) 
    {
        $errors = [];

        // ✅ بررسی عنوان - فقط این فیلد اجباری است
        if (empty($data['title'])) {
            $errors[] = 'عنوان درخواست الزامی است';
        } elseif (mb_strlen($data['title'], 'UTF-8') < 3) {
            $errors[] = 'عنوان درخواست باید حداقل ۳ کاراکتر باشد';
        } elseif (mb_strlen($data['title'], 'UTF-8') > 255) {
            $errors[] = 'عنوان درخواست نباید بیش از ۲۵۵ کاراکتر باشد';
        }

        // ⚠️ بررسی مبلغ - فقط در صورت وارد کردن
        if (!empty($data['amount'])) {
            if (!is_numeric($data['amount'])) {
                $errors[] = 'مبلغ باید عددی باشد';
            } elseif ($data['amount'] <= 0) {
                $errors[] = 'مبلغ باید مثبت باشد';
            } elseif ($data['amount'] > 999999999999) {
                $errors[] = 'مبلغ بیش از حد مجاز است';
            }
        }

        // ⚠️ بررسی شماره حساب - فقط در صورت وارد کردن
        if (!empty($data['account_number']) && !$this->validateAccountNumber($data['account_number'])) {
            $errors[] = 'فرمت شماره حساب صحیح نیست';
        }

        // ⚠️ بررسی نام صاحب حساب - فقط در صورت وارد کردن
        if (!empty($data['account_holder']) && mb_strlen($data['account_holder'], 'UTF-8') < 2) {
            $errors[] = 'نام صاحب حساب باید حداقل ۲ کاراکتر باشد';
        }

        // ⚠️ بررسی IBAN - فقط در صورت وارد کردن
        if (!empty($data['iban']) && !$this->validateIBAN($data['iban'])) {
            $errors[] = 'فرمت IBAN صحیح نیست';
        }

        // ⚠️ بررسی تاریخ سررسید - فقط در صورت وارد کردن
        if (!empty($data['due_date']) && !$this->validateDate($data['due_date'])) {
            $errors[] = 'فرمت تاریخ سررسید صحیح نیست';
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * اعتبارسنجی شماره حساب
     */
    private function validateAccountNumber($accountNumber) 
    {
        // حذف فاصله‌ها و خطوط تیره
        $accountNumber = preg_replace('/[\s\-]/', '', $accountNumber);
        
        // بررسی طول و عددی بودن
        return preg_match('/^\d{10,26}$/', $accountNumber);
    }

    /**
     * اعتبارسنجی IBAN
     */
    private function validateIBAN($iban) 
    {
        // حذف فاصله‌ها
        $iban = str_replace(' ', '', strtoupper($iban));
        
        // بررسی فرمت IBAN ایران
        return preg_match('/^IR\d{24}$/', $iban);
    }

    /**
     * اعتبارسنجی تاریخ
     */
    private function validateDate($date) 
    {
        return strtotime($date) !== false;
    }

    /**
     * تشخیص لیبل وضعیت
     */
    private function getStatusLabel($status) 
    {
        $labels = [
            self::STATUS_PENDING => 'در انتظار',
            self::STATUS_PROCESSING => 'در حال پردازش',
            self::STATUS_APPROVED => 'تایید شده',
            self::STATUS_COMPLETED => 'تکمیل شده',
            self::STATUS_REJECTED => 'رد شده',
            self::STATUS_CANCELLED => 'لغو شده'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * بررسی مجوز ویرایش درخواست
     */
    private function canEditRequest($request, $userId) 
    {
        // سازنده درخواست یا مدیران می‌توانند ویرایش کنند
        return $request['requester_id'] == $userId || 
               Security::checkPermission('manager') ||
               Security::checkPermission('admin');
    }

    /**
     * بررسی مجوز تغییر وضعیت
     */
    private function canChangeStatus($request, $userId, $newStatus) 
    {
        // مدیران و حسابداران می‌توانند وضعیت را تغییر دهند
        return Security::checkPermission('accountant') ||
               Security::checkPermission('manager') ||
               Security::checkPermission('admin');
    }

    /**
     * غنی‌سازی داده‌های درخواست
     */
    private function enrichRequestData($request) 
    {
        try {
            // تبدیل تگ‌ها از JSON - بررسی null بودن
            $request['tags'] = (!empty($request['tags']) && $request['tags'] !== null) 
                ? json_decode($request['tags'], true) ?: [] 
                : [];

            // اضافه کردن لیبل‌ها
            $request['status_label'] = $this->getStatusLabel($request['status']);
            $request['priority_label'] = REQUEST_PRIORITIES[$request['priority']] ?? $request['priority'];
            $request['category_label'] = self::CATEGORIES[$request['category']] ?? $request['category'];

            // فرمت کردن مبلغ
            $request['amount_formatted'] = number_format($request['amount']);

            // فرمت کردن تاریخ‌ها
            $request['created_at_jalali'] = date('Y/m/d H:i', strtotime($request['created_at']));
            if ($request['due_date']) {
                $request['due_date_jalali'] = date('Y/m/d', strtotime($request['due_date']));
            }

            // اضافه کردن اطلاعات درخواست‌کننده
            require_once APP_PATH . 'models/User.php';
            $userModel = new User();
            $requester = $userModel->find($request['requester_id']);
            $request['requester_name'] = $requester ? $requester['full_name'] : 'نامشخص';

            return $request;

        } catch (Exception $e) {
            return $request;
        }
    }

    /**
     * دریافت اسناد درخواست
     */
    private function getRequestDocuments($requestId) 
    {
        try {
            require_once APP_PATH . 'models/Document.php';
            $documentModel = new Document();
            return $documentModel->getRequestDocuments($requestId);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * دریافت تاریخچه گردش کار
     */
    private function getWorkflowHistory($requestId) 
    {
        try {
            $sql = "SELECT wh.*, u.full_name as user_name 
                    FROM workflow_history wh 
                    LEFT JOIN users u ON wh.user_id = u.id 
                    WHERE wh.request_id = ? 
                    ORDER BY wh.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$requestId]);
            $history = $stmt->fetchAll();

            // فرمت کردن تاریخ‌ها
            foreach ($history as &$item) {
                $item['created_at_jalali'] = date('Y/m/d H:i', strtotime($item['created_at']));
            }

            return $history;

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * اضافه کردن رکورد به تاریخچه گردش کار
     */
    private function addWorkflowHistory($requestId, $userId, $action, $oldStatus = null, $newStatus = null, $comment = null) 
    {
        try {
            $historyData = [
                'request_id' => $requestId,
                'user_id' => $userId,
                'action' => $action,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => $comment,
                'ip_address' => SecurityConfig::getClientIP(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $sql = "INSERT INTO workflow_history (request_id, user_id, action, old_status, new_status, comment, ip_address, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $requestId, $userId, $action, $oldStatus, 
                $newStatus, $comment, SecurityConfig::getClientIP(), 
                date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            writeLog("خطا در ثبت تاریخچه گردش کار: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت درخواست‌های کاربر
     */
    public function getUserRequests($userId, $filters = []) 
    {
        try {
            $conditions = ['requester_id = ?'];
            $params = [$userId];

            // اضافه کردن فیلترها
            if (!empty($filters['status'])) {
                $conditions[] = 'status = ?';
                $params[] = $filters['status'];
            }

            if (!empty($filters['date_from'])) {
                $conditions[] = 'created_at >= ?';
                $params[] = $filters['date_from'] . ' 00:00:00';
            }

            if (!empty($filters['date_to'])) {
                $conditions[] = 'created_at <= ?';
                $params[] = $filters['date_to'] . ' 23:59:59';
            }

            $whereClause = implode(' AND ', $conditions);
            $page = $filters['page'] ?? 1;
            $perPage = $filters['per_page'] ?? 20;

            $requests = $this->paginate($page, $perPage, [
                'condition' => $whereClause,
                'params' => $params
            ]);

            // غنی‌سازی داده‌ها
            if ($requests && isset($requests['data'])) {
                foreach ($requests['data'] as &$request) {
                    $request = $this->enrichRequestData($request);
                }
            }

            return $requests;

        } catch (Exception $e) {
            writeLog("خطا در دریافت درخواست‌های کاربر: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * جستجوی پیشرفته در درخواست‌ها
     */
    public function advancedSearch($searchParams, $groupId = null) 
    {
        try {
            $conditions = [];
            $params = [];

            // محدود کردن به گروه خاص
            if ($groupId) {
                $conditions[] = 'group_id = ?';
                $params[] = $groupId;
            }

            // جستجوی متنی
            if (!empty($searchParams['search'])) {
                $searchTerm = '%' . Security::sanitizeSearchTerm($searchParams['search']) . '%';
                $conditions[] = '(title LIKE ? OR description LIKE ? OR account_holder LIKE ? OR reference_number LIKE ?)';
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            // فیلتر وضعیت (چندگانه)
            if (!empty($searchParams['statuses']) && is_array($searchParams['statuses'])) {
                $placeholders = str_repeat('?,', count($searchParams['statuses']) - 1) . '?';
                $conditions[] = "status IN ({$placeholders})";
                $params = array_merge($params, $searchParams['statuses']);
            }

            // فیلتر اولویت (چندگانه)
            if (!empty($searchParams['priorities']) && is_array($searchParams['priorities'])) {
                $placeholders = str_repeat('?,', count($searchParams['priorities']) - 1) . '?';
                $conditions[] = "priority IN ({$placeholders})";
                $params = array_merge($params, $searchParams['priorities']);
            }

            // فیلتر دسته‌بندی
            if (!empty($searchParams['categories']) && is_array($searchParams['categories'])) {
                $placeholders = str_repeat('?,', count($searchParams['categories']) - 1) . '?';
                $conditions[] = "category IN ({$placeholders})";
                $params = array_merge($params, $searchParams['categories']);
            }

            // فیلتر محدوده مبلغ
            if (!empty($searchParams['amount_min'])) {
                $conditions[] = 'amount >= ?';
                $params[] = $searchParams['amount_min'];
            }

            if (!empty($searchParams['amount_max'])) {
                $conditions[] = 'amount <= ?';
                $params[] = $searchParams['amount_max'];
            }

            // فیلتر محدوده تاریخ ایجاد
            if (!empty($searchParams['created_from'])) {
                $conditions[] = 'created_at >= ?';
                $params[] = $searchParams['created_from'] . ' 00:00:00';
            }

            if (!empty($searchParams['created_to'])) {
                $conditions[] = 'created_at <= ?';
                $params[] = $searchParams['created_to'] . ' 23:59:59';
            }

            // فیلتر تاریخ سررسید
            if (!empty($searchParams['due_from'])) {
                $conditions[] = 'due_date >= ?';
                $params[] = $searchParams['due_from'];
            }

            if (!empty($searchParams['due_to'])) {
                $conditions[] = 'due_date <= ?';
                $params[] = $searchParams['due_to'];
            }

            // فیلتر بانک
            if (!empty($searchParams['banks']) && is_array($searchParams['banks'])) {
                $placeholders = str_repeat('?,', count($searchParams['banks']) - 1) . '?';
                $conditions[] = "bank_name IN ({$placeholders})";
                $params = array_merge($params, $searchParams['banks']);
            }

            // فیلتر درخواست‌های فوری
            if (isset($searchParams['is_urgent']) && $searchParams['is_urgent'] !== '') {
                $conditions[] = 'is_urgent = ?';
                $params[] = $searchParams['is_urgent'];
            }

            // فیلتر تگ‌ها
            if (!empty($searchParams['tags']) && is_array($searchParams['tags'])) {
                foreach ($searchParams['tags'] as $tag) {
                    $conditions[] = 'JSON_CONTAINS(tags, ?)';
                    $params[] = json_encode($tag);
                }
            }

            // ساخت کوئری نهایی
            $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);
            
            // ترتیب‌بندی
            $orderBy = $searchParams['order_by'] ?? 'created_at';
            $orderDir = strtoupper($searchParams['order_dir'] ?? 'DESC');
            
            // تنظیم صفحه‌بندی
            $page = $searchParams['page'] ?? 1;
            $perPage = $searchParams['per_page'] ?? 20;

            // اجرای جستجو
            $results = $this->paginate($page, $perPage, [
                'condition' => $whereClause,
                'params' => $params
            ]);

            // غنی‌سازی نتایج
            if ($results && isset($results['data'])) {
                foreach ($results['data'] as &$request) {
                    $request = $this->enrichRequestData($request);
                }
            }

            return $results;

        } catch (Exception $e) {
            writeLog("خطا در جستجوی پیشرفته: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت درخواست‌های منقضی
     */
    public function getExpiredRequests($groupId = null) 
    {
        try {
            $conditions = ['due_date < CURDATE()', 'status NOT IN (?, ?, ?)'];
            $params = [self::STATUS_COMPLETED, self::STATUS_REJECTED, self::STATUS_CANCELLED];

            if ($groupId) {
                $conditions[] = 'group_id = ?';
                $params[] = $groupId;
            }

            $whereClause = implode(' AND ', $conditions);
            
            $requests = $this->where('1', '1', null, '*');
            $filteredRequests = [];

            foreach ($requests as $request) {
                // بررسی شرایط فیلتر
                $isExpired = $request['due_date'] && strtotime($request['due_date']) < time();
                $isNotFinished = !in_array($request['status'], [self::STATUS_COMPLETED, self::STATUS_REJECTED, self::STATUS_CANCELLED]);
                $isInGroup = !$groupId || $request['group_id'] == $groupId;

                if ($isExpired && $isNotFinished && $isInGroup) {
                    $filteredRequests[] = $this->enrichRequestData($request);
                }
            }

            return $filteredRequests;

        } catch (Exception $e) {
            writeLog("خطا در دریافت درخواست‌های منقضی: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت درخواست‌های فوری
     */
    public function getUrgentRequests($groupId = null) 
    {
        try {
            $conditions = ['(is_urgent = 1 OR priority = ?)'];
            $params = [self::PRIORITY_URGENT];

            if ($groupId) {
                $conditions[] = 'group_id = ?';
                $params[] = $groupId;
            }

            $whereClause = implode(' AND ', $conditions);
            
            $requests = $this->where('1', '1', null, '*');
            $filteredRequests = [];

            foreach ($requests as $request) {
                $isUrgent = $request['is_urgent'] == 1 || $request['priority'] === self::PRIORITY_URGENT;
                $isInGroup = !$groupId || $request['group_id'] == $groupId;

                if ($isUrgent && $isInGroup) {
                    $filteredRequests[] = $this->enrichRequestData($request);
                }
            }

            // ترتیب‌بندی بر اساس تاریخ ایجاد (جدیدترین اول)
            usort($filteredRequests, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return $filteredRequests;

        } catch (Exception $e) {
            writeLog("خطا در دریافت درخواست‌های فوری: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * دریافت گزارش ماهانه
     */
    public function getMonthlyReport($year, $month, $groupFilter = null) 
    {
        try {
            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $conditions = ['created_at >= ?', 'created_at <= ?'];
            $params = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

            if ($groupFilter) {
                $conditions[] = 'group_id = ?';
                $params[] = $groupFilter;
            }

            $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);

            // آمار کلی
            $stats = $this->getRequestStats($groupFilter, $startDate, $endDate);

            // آمار روزانه
            $sql = "SELECT 
                        DATE(created_at) as date,
                        COUNT(*) as count,
                        SUM(amount) as total_amount
                    FROM {$this->table} 
                    WHERE {$whereClause}
                    GROUP BY DATE(created_at)
                    ORDER BY date";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $dailyStats = $stmt->fetchAll();

            // آمار بر اساس دسته‌بندی
            $sql = "SELECT 
                        category,
                        COUNT(*) as count,
                        SUM(amount) as total_amount
                    FROM {$this->table} 
                    WHERE {$whereClause} AND category IS NOT NULL
                    GROUP BY category
                    ORDER BY total_amount DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $categoryStats = $stmt->fetchAll();

            return [
                'period' => [
                    'year' => $year,
                    'month' => $month,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summary' => $stats,
                'daily' => $dailyStats,
                'categories' => $categoryStats
            ];

        } catch (Exception $e) {
            writeLog("خطا در تولید گزارش ماهانه: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }

    /**
     * صدور خروجی Excel
     */
    public function exportToExcel($requests, $filename = null) 
    {
        try {
            if (!$filename) {
                $filename = 'payment_requests_' . date('Y_m_d_H_i_s') . '.csv';
            }

            $headers = [
                'شماره مرجع',
                'عنوان',
                'مبلغ',
                'صاحب حساب',
                'شماره حساب',
                'بانک',
                'وضعیت',
                'اولویت',
                'تاریخ ایجاد',
                'تاریخ سررسید'
            ];

            $csvData = [];
            $csvData[] = $headers;

            foreach ($requests as $request) {
                $csvData[] = [
                    $request['reference_number'],
                    $request['title'],
                    number_format($request['amount']),
                    $request['account_holder'],
                    $request['account_number'],
                    $request['bank_name'],
                    $this->getStatusLabel($request['status']),
                    REQUEST_PRIORITIES[$request['priority']] ?? $request['priority'],
                    jdate('Y/m/d H:i', strtotime($request['created_at'])),
                    $request['due_date'] ? jdate('Y/m/d', strtotime($request['due_date'])) : ''
                ];
            }

            // ایجاد فایل CSV
            $output = fopen('php://temp', 'w');
            
            // اضافه کردن BOM برای نمایش صحیح فارسی در Excel
            fwrite($output, "\xEF\xBB\xBF");
            
            foreach ($csvData as $row) {
                fputcsv($output, $row);
            }
            
            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);

            return [
                'content' => $csvContent,
                'filename' => $filename,
                'mime_type' => 'text/csv; charset=utf-8'
            ];

        } catch (Exception $e) {
            writeLog("خطا در صدور Excel: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت آخرین درخواست‌ها
     */
    public function getRecentRequests($limit = 10, $groupId = null) 
    {
        try {
            $conditions = [];
            $params = [];

            if ($groupId) {
                $conditions[] = 'group_id = ?';
                $params[] = $groupId;
            }

            $whereClause = empty($conditions) ? '1=1' : implode(' AND ', $conditions);
            
            $sql = "SELECT * FROM {$this->table} 
                    WHERE {$whereClause} 
                    ORDER BY created_at DESC 
                    LIMIT {$limit}";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $requests = $stmt->fetchAll();

            // غنی‌سازی داده‌ها
            foreach ($requests as &$request) {
                $request = $this->enrichRequestData($request);
            }

            return $requests;

        } catch (Exception $e) {
            writeLog("خطا در دریافت آخرین درخواست‌ها: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * کپی کردن درخواست
     */
    public function duplicateRequest($requestId, $userId) 
    {
        try {
            $originalRequest = $this->find($requestId);
            if (!$originalRequest) {
                return ['success' => false, 'errors' => ['درخواست اصلی یافت نشد']];
            }

            // حذف فیلدهای غیرقابل کپی
            unset($originalRequest['id']);
            unset($originalRequest['reference_number']);
            unset($originalRequest['status']);
            unset($originalRequest['created_at']);
            unset($originalRequest['updated_at']);
            unset($originalRequest['completed_at']);
            unset($originalRequest['approval_notes']);
            unset($originalRequest['rejection_reason']);

            // تنظیم کاربر جدید
            $originalRequest['requester_id'] = $userId;
            $originalRequest['title'] = 'کپی: ' . $originalRequest['title'];

            // ایجاد درخواست جدید
            $result = $this->createRequest($originalRequest);

            if ($result['success']) {
                writeLog("درخواست {$originalRequest['reference_number']} کپی شد توسط کاربر: {$userId}", 'INFO');
            }

            return $result;

        } catch (Exception $e) {
            writeLog("خطا در کپی کردن درخواست: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'errors' => ['خطای سیستمی رخ داده است']];
        }
    }
}
?>