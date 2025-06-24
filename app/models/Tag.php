<?php
/**
 * نام فایل: Tag.php
 * مسیر: /app/models/Tag.php
 * توضیح: مدل مدیریت تگ‌ها - Enterprise Grade
 * تاریخ ایجاد: 1404/10/15
 * نسخه: 1.0 حرفه‌ای
 */

declare(strict_types=1);

/**
 * کلاس مدیریت تگ‌ها
 */
class Tag extends Database
{
    protected $table = 'tags';
    protected $fillable = ['tag_code', 'title', 'color_start', 'color_end', 'text_color', 'created_by'];
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * دریافت تمامی تگ‌ها
     * @param array $filters فیلترهای جستجو
     * @return array
     */
    public function getAllTags(array $filters = []): array
    {
        $query = "
            SELECT t.*, u.full_name as creator_name 
            FROM {$this->table} t
            LEFT JOIN users u ON t.created_by = u.id 
            WHERE t.deleted_at IS NULL
        ";
        
        $params = [];
        
        // فیلترها
        if (!empty($filters['search'])) {
            $query .= " AND (t.title LIKE ? OR t.id LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (isset($filters['usage_count'])) {
            if ($filters['usage_count'] === 'used') {
                $query .= " AND t.usage_count > 0";
            } elseif ($filters['usage_count'] === 'unused') {
                $query .= " AND t.usage_count = 0";
            }
        }
        
        // مرتب‌سازی
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        
        $allowedOrderBy = ['id', 'title', 'usage_count', 'created_at'];
        if (!in_array($orderBy, $allowedOrderBy)) {
            $orderBy = 'created_at';
        }
        
        if (!in_array($orderDir, ['ASC', 'DESC'])) {
            $orderDir = 'DESC';
        }
        
        $query .= " ORDER BY t.{$orderBy} {$orderDir}";
        
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * دریافت تگ بر اساس شناسه
     * @param int $tagId
     * @return array|null
     */
    public function getTagById(int $tagId): ?array
    {
        $query = "
            SELECT t.*, u.full_name as creator_name 
            FROM {$this->table} t
            LEFT JOIN users u ON t.created_by = u.id 
            WHERE t.id = ? AND t.deleted_at IS NULL
        ";
        
        $stmt = $this->query($query, [$tagId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * ایجاد تگ جدید
     * @param array $data
     * @return array نتیجه عملیات
     */
    public function createTag(array $data): array
    {
        // اعتبارسنجی
        $validation = $this->validateTagData($data);
        if (!$validation['valid']) {
            return $validation;
        }
        
        // محاسبه رنگ متن بهینه
        $textColor = $this->getOptimalTextColor($data['color_start'], $data['color_end']);
        
        $createData = [
            'title' => $data['title'],
            'color_start' => $data['color_start'],
            'color_end' => $data['color_end'],
            'text_color' => $textColor,
            'created_by' => $data['created_by'],
            'usage_count' => 0
        ];
        
        $tagId = $this->create($createData);
        
        if ($tagId) {
            return [
                'success' => true,
                'message' => 'تگ با موفقیت ایجاد شد',
                'tag_id' => $tagId
            ];
        }
        
        return [
            'success' => false,
            'message' => 'خطا در ایجاد تگ'
        ];
    }
    
    /**
     * بروزرسانی تگ
     * @param int $tagId
     * @param array $data
     * @return array
     */
    public function updateTag(int $tagId, array $data): array
    {
        // بررسی وجود تگ
        $existingTag = $this->getTagById($tagId);
        if (!$existingTag) {
            return [
                'success' => false,
                'message' => 'تگ یافت نشد'
            ];
        }
        
        // اعتبارسنجی
        $validation = $this->validateTagData($data, $tagId);
        if (!$validation['valid']) {
            return $validation;
        }
        
        // محاسبه رنگ متن بهینه
        $textColor = $this->getOptimalTextColor($data['color_start'], $data['color_end']);
        
        $updateData = [
            'title' => $data['title'],
            'color_start' => $data['color_start'],
            'color_end' => $data['color_end'],
            'text_color' => $textColor
        ];
        
        $result = $this->update($tagId, $updateData);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'تگ با موفقیت بروزرسانی شد'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'خطا در بروزرسانی تگ'
        ];
    }
    
    /**
     * حذف نرم تگ
     * @param int $tagId
     * @return array
     */
    public function deleteTag(int $tagId): array
    {
        // بررسی وجود تگ
        $tag = $this->getTagById($tagId);
        if (!$tag) {
            return [
                'success' => false,
                'message' => 'تگ یافت نشد'
            ];
        }
        
        // بررسی استفاده شدن تگ
        if ($tag['usage_count'] > 0) {
            return [
                'success' => false,
                'message' => 'امکان حذف تگ در حال استفاده وجود ندارد'
            ];
        }
        
        $query = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->query($query, [$tagId]);
        
        if ($stmt) {
            return [
                'success' => true,
                'message' => 'تگ با موفقیت حذف شد'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'خطا در حذف تگ'
        ];
    }
    
    /**
     * افزایش شمارنده استفاده
     * @param int $tagId
     * @return bool
     */
    public function incrementUsage(int $tagId): bool
    {
        $query = "UPDATE {$this->table} SET usage_count = usage_count + 1 WHERE id = ?";
        $stmt = $this->query($query, [$tagId]);
        return $stmt !== false;
    }
    
    /**
     * کاهش شمارنده استفاده
     * @param int $tagId
     * @return bool
     */
    public function decrementUsage(int $tagId): bool
    {
        $query = "UPDATE {$this->table} SET usage_count = GREATEST(0, usage_count - 1) WHERE id = ?";
        $stmt = $this->query($query, [$tagId]);
        return $stmt !== false;
    }
    
    /**
     * دریافت آمار تگ‌ها
     * @return array
     */
    public function getTagStatistics(): array
    {
        $stats = [
            'total_tags' => 0,
            'used_tags' => 0,
            'unused_tags' => 0,
            'max_usage' => 0
        ];
        
        $query = "
            SELECT 
                COUNT(*) as total_tags,
                SUM(CASE WHEN usage_count > 0 THEN 1 ELSE 0 END) as used_tags,
                SUM(CASE WHEN usage_count = 0 THEN 1 ELSE 0 END) as unused_tags,
                COALESCE(MAX(usage_count), 0) as max_usage
            FROM {$this->table} 
            WHERE deleted_at IS NULL
        ";
        
        $stmt = $this->query($query);
        $result = $stmt->fetch();
        if ($result) {
            $stats = $result;
        }
        
        return $stats;
    }
    
    /**
     * دریافت محبوب‌ترین تگ‌ها
     * @param int $limit
     * @return array
     */
    public function getPopularTags(int $limit = 10): array
    {
        $query = "
            SELECT * FROM {$this->table} 
            WHERE deleted_at IS NULL AND usage_count > 0 
            ORDER BY usage_count DESC, created_at DESC 
            LIMIT ?
        ";
        
        $stmt = $this->query($query, [$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * تولید گرادینت تصادفی
     * @return array
     */
    public function generateRandomGradient(): array
    {
        $gradients = [
            ['#667eea', '#764ba2'], // Purple Blue
            ['#f093fb', '#f5576c'], // Pink Red
            ['#4facfe', '#00f2fe'], // Blue Cyan
            ['#43e97b', '#38f9d7'], // Green Teal
            ['#fa709a', '#fee140'], // Pink Yellow
            ['#a8edea', '#fed6e3'], // Mint Pink
            ['#ff9a9e', '#fecfef'], // Coral Pink
            ['#ffecd2', '#fcb69f'], // Peach
            ['#a8edea', '#fed6e3'], // Aqua
            ['#ffefd5', '#ffefba'], // Light Yellow
            ['#667eea', '#764ba2'], // Violet
            ['#f12711', '#f5af19'], // Orange Red
            ['#4facfe', '#00f2fe'], // Sky Blue
            ['#43e97b', '#38f9d7'], // Forest Green
            ['#fa709a', '#fee140']  // Sunset
        ];
        
        $selectedGradient = $gradients[array_rand($gradients)];
        
        return [
            'color_start' => $selectedGradient[0],
            'color_end' => $selectedGradient[1],
            'text_color' => $this->getOptimalTextColor($selectedGradient[0], $selectedGradient[1])
        ];
    }
    
    /**
     * محاسبه رنگ متن بهینه
     * @param string $colorStart
     * @param string $colorEnd
     * @return string
     */
    public function getOptimalTextColor(string $colorStart, string $colorEnd): string
    {
        // محاسبه میانگین روشنایی دو رنگ
        $luminanceStart = $this->getLuminance($colorStart);
        $luminanceEnd = $this->getLuminance($colorEnd);
        $averageLuminance = ($luminanceStart + $luminanceEnd) / 2;
        
        // اگر روشنایی کم باشد، متن سفید - اگر زیاد باشد، متن تیره
        return $averageLuminance > 0.5 ? '#1f2937' : '#ffffff';
    }
    
    /**
     * محاسبه روشنایی رنگ
     * @param string $hex
     * @return float
     */
    private function getLuminance(string $hex): float
    {
        // حذف # از ابتدای رنگ
        $hex = ltrim($hex, '#');
        
        // تبدیل hex به RGB
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        
        // اعمال فرمول luminance
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
    
    /**
     * اعتبارسنجی داده‌های تگ
     * @param array $data
     * @param int|null $excludeId
     * @return array
     */
    private function validateTagData(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        
        // بررسی عنوان
        if (empty($data['title']) || !trim($data['title'])) {
            $errors[] = 'عنوان تگ الزامی است';
        } elseif (strlen(trim($data['title'])) > 100) {
            $errors[] = 'عنوان تگ نباید بیش از 100 کاراکتر باشد';
        }
        
        // بررسی یکتا بودن عنوان
        if (!empty($data['title'])) {
            $query = "SELECT id FROM {$this->table} WHERE title = ? AND deleted_at IS NULL";
            $params = [$data['title']];
            
            if ($excludeId) {
                $query .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->query($query, $params);
            if ($stmt->fetch()) {
                $errors[] = 'عنوان تگ قبلاً استفاده شده است';
            }
        }
        
        // بررسی رنگ شروع
        if (empty($data['color_start']) || !$this->isValidHexColor($data['color_start'])) {
            $errors[] = 'رنگ شروع گرادینت نامعتبر است';
        }
        
        // بررسی رنگ پایان
        if (empty($data['color_end']) || !$this->isValidHexColor($data['color_end'])) {
            $errors[] = 'رنگ پایان گرادینت نامعتبر است';
        }
        
        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => implode('<br>', $errors)
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * بررسی معتبر بودن کد رنگ hex
     * @param string $color
     * @return bool
     */
    private function isValidHexColor(string $color): bool
    {
        return preg_match('/^#[a-fA-F0-9]{6}$/', $color) === 1;
    }
} 