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
            $requests = $this->paymentRequestModel->getGroupRequests($groupId, $filters);
            
            // دریافت آمار
            $stats = $this->paymentRequestModel->getRequestStats($groupId);

            $this->render('requests/list', [
                'title' => 'مدیریت درخواست‌ها',
                'requests' => $requests,
                'stats' => $stats,
                'filters' => $filters,
                'statuses' => PaymentRequest::getStatuses(),
                'priorities' => PaymentRequest::getPriorities()
            ]);

        } catch (Exception $e) {
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
                'user' => $user
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
        try {
            $this->requireAuth();
            $this->validateCsrfToken();

            $user = $this->getCurrentUser();
            
            // دریافت داده‌های POST
            $data = [
                'requester_id' => $user['id'],
                'group_id' => $user['group_id'],
                'title' => $this->input('title'),
                'description' => $this->input('description'),
                'amount' => $this->convertPersianNumbers($this->input('amount')),
                'account_number' => $this->convertPersianNumbers($this->input('account_number')),
                'account_holder' => $this->input('account_holder'),
                'bank_name' => $this->input('bank_name'),
                'iban' => $this->input('iban'),
                'card_number' => $this->convertPersianNumbers($this->input('card_number')),
                'priority' => $this->input('priority', PaymentRequest::PRIORITY_NORMAL),
                'category' => $this->input('category'),
                'due_date' => $this->input('due_date'),
                'tags' => $this->input('tags'),
                'is_urgent' => $this->input('is_urgent') ? 1 : 0
            ];

            // ایجاد درخواست
            $result = $this->paymentRequestModel->createRequest($data);

            if ($result['success']) {
                $this->setFlashMessage('success', 'درخواست حواله با موفقیت ایجاد شد');
                $this->redirect('?route=requests&action=show&id=' . $result['request_id']);
            } else {
                $this->setFlashMessage('error', implode('<br>', $result['errors']));
                $this->redirect('?route=requests&action=create');
            }

        } catch (Exception $e) {
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

            $this->redirect('?route=requests&action=show&id=' . $requestId);

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

            $this->redirect('?route=requests&action=show&id=' . $requestId);

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

            $this->redirect('?route=requests&action=show&id=' . $requestId);

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
}
?>
