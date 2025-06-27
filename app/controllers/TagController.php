<?php
/**
 * نام فایل: TagController.php
 * مسیر: /app/controllers/TagController.php
 * توضیح: کنترلر مدیریت تگ‌ها - Enterprise Grade
 * تاریخ ایجاد: 1404/10/15
 * نسخه: 1.0 حرفه‌ای
 */

declare(strict_types=1);

// Load AdvancedSearch Helper
require_once __DIR__ . '/../helpers/AdvancedSearch.php';

/**
 * کنترلر مدیریت تگ‌ها
 */
class TagController extends BaseController
{
    private Tag $tagModel;
    
    protected $requireAuth = false; // موقتاً غیرفعال برای تست
    
    public function __construct()
    {
        parent::__construct();
        $this->tagModel = new Tag();
    }
    
    /**
     * صفحه اصلی مدیریت تگ‌ها
     */
    public function index(): void
    {
        try {
            // دریافت فیلترها
            $filters = [
                'search' => $this->input('search', ''),
                'order_by' => $this->input('order_by', 'created_at'),
                'order_dir' => $this->input('order_dir', 'DESC')
            ];
            
            // دریافت تگ‌ها
            $tags = $this->tagModel->getAllTags($filters);
            
            // دریافت آمار
            $stats = $this->tagModel->getTagStatistics();
            
            // دریافت محبوب‌ترین تگ‌ها
            $popularTags = $this->tagModel->getPopularTags(5);
            
            // داده‌های view
            $data = [
                'title' => 'مدیریت تگ‌ها',
                'page_title' => 'مدیریت تگ‌ها',
                'tags' => $tags,
                'stats' => $stats,
                'popular_tags' => $popularTags,
                'filters' => $filters,
                'total_tags' => $stats['total_tags'] ?? 0,
                'used_tags' => $stats['used_tags'] ?? 0,
                'unused_tags' => $stats['unused_tags'] ?? 0,
                'max_usage' => $stats['max_usage'] ?? 0,
                'additional_css' => ['css/bootstrap-dashboard.css']
            ];
            
            $this->render('tags/list', $data);
            
        } catch (Exception $e) {
            writeLog("خطا در صفحه مدیریت تگ‌ها: " . $e->getMessage(), 'ERROR');
            $this->sendError('خطا در بارگذاری صفحه');
        }
    }
    
    /**
     * نمایش فرم ایجاد تگ جدید
     */
    public function create()
    {
        try {
            // تولید گرادینت تصادفی برای شروع
            $randomGradient = $this->tagModel->generateRandomGradient();
            
            $this->render('tags/create', [
                'title' => 'ایجاد تگ جدید',
                'random_gradient' => $randomGradient,
                'additional_css' => ['css/bootstrap-dashboard.css']
            ]);
            
        } catch (Exception $e) {
            error_log('Error in create form: ' . $e->getMessage());
            header('Location: ' . url('tags') . '?error=' . urlencode('خطا در نمایش فرم ایجاد'));
            exit;
        }
    }
    
    /**
     * ذخیره تگ جدید
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirectWithError(url('tags'), 'متد درخواست نامعتبر است');
        }
        
        // دریافت داده‌ها
        $data = [
            'title' => trim($this->input('title')),
            'color_start' => $this->input('color_start'),
            'color_end' => $this->input('color_end'),
            'created_by' => 1 // $this->getCurrentUserId() ?? 1
        ];
        
        // ایجاد تگ
        $result = $this->tagModel->createTag($data);
        
        if ($result['success']) {
            $this->redirectWithMessage(url('tags'), 'success', $result['message']);
        } else {
            $this->redirectWithError(url('tags/create'), $result['message']);
        }
    }
    
    /**
     * نمایش فرم ویرایش تگ
     */
    public function edit()
    {
        try {
            $id = (int)($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                $_SESSION['flash'] = [
                    'type' => 'error',
                    'message' => 'شناسه تگ نامعتبر است'
                ];
                header('Location: ' . url('tags'));
                exit;
            }
            
            $tag = $this->tagModel->getTagById($id);
            
            if (!$tag) {
                $_SESSION['flash'] = [
                    'type' => 'error',
                    'message' => 'تگ مورد نظر یافت نشد'
                ];
                header('Location: ' . url('tags'));
                exit;
            }
            
            $this->render('tags/edit', [
                'title' => 'ویرایش تگ',
                'tag' => $tag,
                'additional_css' => ['css/bootstrap-dashboard.css']
            ]);
            
        } catch (Exception $e) {
            error_log('Error in edit form: ' . $e->getMessage());
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'خطا در نمایش فرم ویرایش'
            ];
            header('Location: ' . url('tags'));
            exit;
        }
    }
    
    /**
     * بروزرسانی تگ
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirectWithError(url('tags'), 'متد درخواست نامعتبر است');
        }
        
        $id = (int)$this->input('id');
        
        if (!$id) {
            $this->redirectWithError(url('tags'), 'شناسه تگ نامعتبر است');
        }
        
        // دریافت داده‌ها
        $data = [
            'title' => trim($this->input('title')),
            'color_start' => $this->input('color_start'),
            'color_end' => $this->input('color_end')
        ];
        
        // بروزرسانی تگ
        $result = $this->tagModel->updateTag($id, $data);
        
        if ($result['success']) {
            $this->redirectWithMessage(url('tags'), 'success', $result['message']);
        } else {
            $this->redirectWithError(url("tags/edit?id={$id}"), $result['message']);
        }
    }
    
    /**
     * حذف تگ
     */
    public function delete(): void
    {
        if (!$this->isPost()) {
            $this->sendJSON(['success' => false, 'message' => 'متد درخواست نامعتبر است']);
        }
        
        $id = (int)$this->input('id');
        
        if (!$id) {
            $this->sendJSON(['success' => false, 'message' => 'شناسه تگ نامعتبر است']);
        }
        
        // حذف تگ
        $result = $this->tagModel->deleteTag($id);
        $this->sendJSON($result);
    }
    
    /**
     * API جستجوی پیشرفته تگ‌ها با استفاده از AdvancedSearch Helper
     */
    public function api(): void
    {
        header('Content-Type: application/json');
        
        try {
            $search = trim($_GET['search'] ?? '');
            $filters = [
                'status' => $_GET['status'] ?? 'all'
            ];
            
            // اعتبارسنجی پارامترها
            $validation = AdvancedSearch::validateSearchParams(
                $search,
                ['status'],
                $filters
            );
            
            if (!$validation['valid']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => implode(', ', $validation['errors'])
                ]);
                return;
            }
            
            // جستجو
            $results = $this->tagModel->searchWithFilters(
                $validation['sanitized_search'],
                $validation['sanitized_filters']
            );
            
            // پردازش نتایج برای highlighting
            $processedResults = AdvancedSearch::processSearchResults(
                $results,
                $search,
                ['title', 'creator_name']
            );
            
            // تولید پاسخ استاندارد
            $response = AdvancedSearch::generateApiResponse(
                $processedResults,
                $search,
                [
                    'page_type' => 'tags',
                    'filters_applied' => array_filter($filters, function($v) { return $v !== 'all'; })
                ]
            );
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            error_log("Tags API Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'خطا در جستجو'
            ]);
        }
    }
    
    /**
     * پیش‌نمایش تگ (AJAX)
     */
    public function preview(): void
    {
        if (!$this->isPost()) {
            $this->sendJSON(['success' => false, 'message' => 'متد درخواست نامعتبر است']);
        }
        
        $title = trim($this->input('title', ''));
        $colorStart = $this->input('color_start', '#667eea');
        $colorEnd = $this->input('color_end', '#764ba2');
        
        // تعیین رنگ بهینه متن
        $textColor = $this->tagModel->getOptimalTextColor($colorStart, $colorEnd);
        
        $this->sendJSON([
            'success' => true,
            'data' => [
                'title' => $title,
                'color_start' => $colorStart,
                'color_end' => $colorEnd,
                'text_color' => $textColor
            ]
        ]);
    }
    
    /**
     * تولید گرادینت تصادفی (AJAX)
     */
    public function randomGradient(): void
    {
        $gradient = $this->tagModel->generateRandomGradient();
        
        $this->sendJSON([
            'success' => true,
            'data' => $gradient
        ]);
    }
} 