<?php
/**
 * کلاس جستجوی پیشرفته قابل استفاده مجدد
 * 
 * این کلاس امکان جستجوی چندکلمه‌ای با highlighting و فیلترهای مختلف را
 * برای تمام مدل‌های پروژه فراهم می‌کند.
 * 
 * @author Cursor AI + Developer Team
 * @version 1.0
 * @since 2025-06-27
 */

class AdvancedSearch 
{
    /**
     * انجام جستجوی پیشرفته چندکلمه‌ای
     * 
     * @param object $model نمونه از مدل برای اجرای query
     * @param string $table نام جدول اصلی
     * @param string $search عبارت جستجو
     * @param array $searchFields فیلدهای قابل جستجو
     * @param array $filters فیلترهای اضافی
     * @param array $joins جدول‌های join شونده
     * @param string $orderBy فیلد مرتب‌سازی
     * @param string $orderDir جهت مرتب‌سازی
     * @return array نتایج جستجو
     */
    public static function performSearch(
        object $model,
        string $table,
        string $search = '',
        array $searchFields = [],
        array $filters = [],
        array $joins = [],
        string $orderBy = 'created_at',
        string $orderDir = 'DESC'
    ): array {
        
        // اگر deleted_at وجود دارد از آن استفاده کن
        $conditions = [];
        // فعلاً deleted_at را غیرفعال می‌کنیم برای debugging
        // $conditions = ["{$table}.deleted_at IS NULL"];
        $params = [];
        
        // Multi-word search implementation
        if (!empty($search)) {
            $searchTerms = array_filter(array_map('trim', explode(' ', $search)));
            $searchConditions = [];
            
            foreach ($searchTerms as $index => $term) {
                $termConditions = [];
                
                // جستجو در فیلدهای مختلف
                foreach ($searchFields as $field) {
                    $termConditions[] = "{$field} LIKE ?";
                    $params[] = "%{$term}%";
                }
                
                if (!empty($termConditions)) {
                    $searchConditions[] = '(' . implode(' OR ', $termConditions) . ')';
                }
            }
            
            if (!empty($searchConditions)) {
                $conditions[] = '(' . implode(' AND ', $searchConditions) . ')';
            }
        }
        
        // اعمال فیلترهای اضافی
        foreach ($filters as $field => $value) {
            if (!empty($value) && $value !== 'all') {
                if (is_array($value)) {
                    $placeholders = str_repeat('?,', count($value) - 1) . '?';
                    $conditions[] = "{$field} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $conditions[] = "{$field} = ?";
                    $params[] = $value;
                }
            }
        }
        
        // ساخت query نهایی
        // استخراج alias از table name
        $tableAlias = '';
        if (strpos($table, ' ') !== false) {
            $parts = explode(' ', $table);
            $tableAlias = end($parts);
            $selectFields = "{$tableAlias}.*";
        } else {
            $selectFields = "{$table}.*";
        }
        
        $joinClause = '';
        
        // اضافه کردن join ها
        foreach ($joins as $join) {
            $joinClause .= " " . $join['type'] . " JOIN " . $join['table'] . " ON " . $join['condition'];
            if (isset($join['select'])) {
                $selectFields .= ", " . $join['select'];
            }
        }
        
        // ساخت WHERE clause
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(' AND ', $conditions);
        }
        
        // اصلاح ORDER BY clause
        $orderByClause = '';
        if ($tableAlias) {
            $orderByClause = "ORDER BY {$tableAlias}.{$orderBy} {$orderDir}";
        } else {
            $orderByClause = "ORDER BY {$table}.{$orderBy} {$orderDir}";
        }
        
        $query = "
            SELECT {$selectFields}
            FROM {$table}
            {$joinClause}
            {$whereClause}
            {$orderByClause}
        ";
        
        try {
            $stmt = $model->query($query, $params);
            $result = $stmt->fetchAll();
            return $result ?: [];
        } catch (\Exception $e) {
            error_log("AdvancedSearch Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * پردازش نتایج برای highlighting
     * 
     * @param array $results نتایج خام
     * @param string $search عبارت جستجو
     * @param array $highlightFields فیلدهای قابل highlight
     * @return array نتایج پردازش شده
     */
    public static function processSearchResults(
        array $results, 
        string $search, 
        array $highlightFields = []
    ): array {
        
        if (empty($search) || empty($results)) {
            return $results;
        }
        
        $searchTerms = array_filter(array_map('trim', explode(' ', $search)));
        $processedResults = [];
        
        foreach ($results as $item) {
            foreach ($highlightFields as $field) {
                if (isset($item[$field])) {
                    $item["{$field}_highlighted"] = self::highlightSearchTerms($item[$field], $searchTerms);
                }
            }
            $processedResults[] = $item;
        }
        
        return $processedResults;
    }
    
    /**
     * اعمال highlighting به متن
     * 
     * @param string $text متن اصلی
     * @param array $terms کلمات جستجو
     * @return string متن با highlighting
     */
    public static function highlightSearchTerms(string $text, array $terms): string 
    {
        foreach ($terms as $term) {
            $text = preg_replace('/(' . preg_quote($term, '/') . ')/iu', '<mark>$1</mark>', $text);
        }
        return $text;
    }
    
    /**
     * تولید پاسخ API استاندارد برای جستجو
     * 
     * @param array $results نتایج
     * @param string $search عبارت جستجو
     * @param array $meta اطلاعات اضافی
     * @return array پاسخ JSON
     */
    public static function generateApiResponse(
        array $results, 
        string $search = '', 
        array $meta = []
    ): array {
        
        $searchTerms = !empty($search) ? array_filter(array_map('trim', explode(' ', $search))) : [];
        
        return [
            'success' => true,
            'data' => $results,
            'total' => count($results),
            'search_terms' => $searchTerms,
            'has_search' => !empty($search),
            'meta' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0'
            ], $meta)
        ];
    }
    
    /**
     * تولید configuration برای frontend
     * 
     * @param string $apiUrl آدرس API
     * @param array $searchFields فیلدهای جستجو
     * @param array $options تنظیمات اضافی
     * @return array تنظیمات frontend
     */
    public static function generateFrontendConfig(
        string $apiUrl,
        array $searchFields = [],
        array $options = []
    ): array {
        
        return [
            'apiUrl' => $apiUrl,
            'searchFields' => $searchFields,
            'placeholder' => $options['placeholder'] ?? 'جستجو...',
            'helpText' => $options['helpText'] ?? 'با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید',
            'debounceDelay' => $options['debounceDelay'] ?? 200,
            'highlightFields' => $options['highlightFields'] ?? [],
            'enableStats' => $options['enableStats'] ?? true,
            'enableKeyboardShortcuts' => $options['enableKeyboardShortcuts'] ?? true,
            'emptyStateMessage' => $options['emptyStateMessage'] ?? 'نتیجه‌ای یافت نشد'
        ];
    }
    
    /**
     * اعتبارسنجی پارامترهای جستجو
     * 
     * @param string $search عبارت جستجو
     * @param array $allowedFilters فیلترهای مجاز
     * @param array $filters فیلترهای دریافتی
     * @return array نتیجه اعتبارسنجی
     */
    public static function validateSearchParams(
        string $search, 
        array $allowedFilters = [], 
        array $filters = []
    ): array {
        
        $errors = [];
        
        // بررسی طول جستجو
        if (strlen($search) > 200) {
            $errors[] = 'عبارت جستجو نباید بیش از 200 کاراکتر باشد';
        }
        
        // بررسی فیلترهای نامعتبر
        foreach ($filters as $key => $value) {
            if (!in_array($key, $allowedFilters)) {
                $errors[] = "فیلتر '{$key}' مجاز نیست";
            }
        }
        
        // بررسی کاراکترهای مشکوک
        if (preg_match('/[<>\'";(){}]/', $search)) {
            $errors[] = 'عبارت جستجو شامل کاراکترهای غیرمجاز است';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'sanitized_search' => htmlspecialchars(trim($search), ENT_QUOTES, 'UTF-8'),
            'sanitized_filters' => array_map(function($value) {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }, $filters)
        ];
    }
    
    /**
     * تولید HTML template برای search box
     * 
     * @param array $config تنظیمات
     * @return string HTML template
     */
    public static function generateSearchBoxHtml(array $config = []): string 
    {
        $placeholder = $config['placeholder'] ?? 'جستجو...';
        $helpText = $config['helpText'] ?? 'با فاصله بین کلمات جستجوی دقیق‌تری داشته باشید | ESC برای پاک کردن';
        $showStats = $config['showStats'] ?? true;
        
        $html = '
        <div class="advanced-search-container">
            <div class="search-input-wrapper">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           id="advancedSearchInput" 
                           class="form-control border-start-0 ps-0" 
                           placeholder="' . htmlspecialchars($placeholder) . '"
                           autocomplete="off">
                    <button class="btn btn-outline-secondary border-start-0" 
                            id="advancedSearchClear" 
                            type="button" 
                            style="display: none;"
                            title="پاک کردن جستجو">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-help-text">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb me-1"></i>
                        ' . htmlspecialchars($helpText) . '
                    </small>
                </div>
            </div>';
            
        if ($showStats) {
            $html .= '
            <div class="search-stats mt-2" id="advancedSearchStats" style="display: none;">
                <small class="text-info">
                    <i class="fas fa-chart-bar me-1"></i>
                    <span id="advancedSearchCount">0</span> نتیجه یافت شد
                    <span id="advancedSearchTerms"></span>
                </small>
            </div>';
        }
        
        $html .= '
        </div>
        
        <!-- Loading indicator -->
        <div id="advancedSearchLoading" class="text-center p-4" style="display: none;">
            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
            در حال جستجو...
        </div>

        <!-- No results message -->
        <div id="advancedSearchEmpty" class="text-center p-5" style="display: none;">
            <div class="empty-state">
                <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">نتیجه‌ای یافت نشد</h5>
                <p class="text-muted">
                    برای عبارت جستجوی شما هیچ نتیجه‌ای یافت نشد.<br>
                    لطفاً کلمات کلیدی دیگری امتحان کنید.
                </p>
            </div>
        </div>';
        
        return $html;
    }
    
    /**
     * تولید CSS styles برای advanced search
     * 
     * @return string CSS styles
     */
    public static function generateSearchBoxCss(): string 
    {
        return '
        /* Advanced Search Component Styles */
        .advanced-search-container {
            margin-bottom: 1rem;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper .input-group-text {
            background: var(--gray-50, #f9fafb);
            border-color: var(--gray-200, #e5e7eb);
        }

        .search-input-wrapper .form-control {
            border-color: var(--gray-200, #e5e7eb);
            transition: all 0.15s ease;
        }

        .search-input-wrapper .form-control:focus {
            border-color: var(--primary, #5E3AEE);
            box-shadow: 0 0 0 0.2rem rgba(94, 58, 238, 0.1);
        }

        .search-help-text {
            margin-top: 0.5rem;
        }

        .search-stats {
            padding: 0.5rem 0.75rem;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 0.5rem;
            border-left: 3px solid #0d6efd;
        }

        /* Search highlighting */
        mark {
            background: #fff3cd !important;
            color: #856404 !important;
            padding: 1px 2px !important;
            border-radius: 2px !important;
            font-weight: 500 !important;
        }

        [data-theme="dark"] mark {
            background: #664d03 !important;
            color: #ffed4a !important;
        }

        /* Empty state styling */
        .empty-state {
            padding: 2rem;
        }

        .empty-state i {
            opacity: 0.5;
        }

        /* Result highlighting */
        .highlight-result {
            background: rgba(94, 58, 238, 0.1) !important;
            border: 2px solid var(--primary, #5E3AEE) !important;
            border-radius: 0.75rem !important;
            transition: all 0.3s ease !important;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .search-input-wrapper {
                margin-bottom: 0.75rem;
            }
            
            .search-help-text {
                font-size: 0.75rem;
            }
            
            .search-input-wrapper .form-control {
                font-size: 16px; /* Prevent zoom on iOS */
                padding: 12px;
            }
        }';
    }
} 