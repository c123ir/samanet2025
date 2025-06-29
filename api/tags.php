<?php
/**
 * API برای مدیریت برچسب‌ها
 * مسیر: /api/tags.php
 * تاریخ: 1404/10/18
 */

require_once '../config/config.php';
require_once '../app/helpers/Database.php';
require_once '../app/helpers/Security.php';
require_once '../app/helpers/Utilities.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Initialize database connection
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get action from URL path
$action = $_GET['action'] ?? 'search';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'search':
            handleSearch($pdo);
            break;
            
        case 'create':
            if ($method === 'POST') {
                handleCreate($pdo);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;
            
        case 'update':
            if ($method === 'PUT' || $method === 'POST') {
                handleUpdate($pdo);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;
            
        case 'delete':
            if ($method === 'DELETE' || $method === 'POST') {
                handleDelete($pdo);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Action not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}

/**
 * جستجوی پیشرفته در برچسب‌ها
 */
function handleSearch($pdo) {
    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? 'all';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Validate parameters
    $limit = max(1, min(100, $limit)); // Between 1 and 100
    $offset = max(0, $offset);
    
    // Build WHERE conditions
    $conditions = ['deleted_at IS NULL'];
    $params = [];
    $searchTerms = [];
    
    // Handle search query
    if (!empty($search)) {
        $searchWords = explode(' ', $search);
        $searchConditions = [];
        
        foreach ($searchWords as $word) {
            $word = trim($word);
            if (strlen($word) >= 2) {
                $searchTerms[] = $word;
                $searchConditions[] = '(
                    title LIKE ? OR 
                    description LIKE ? OR 
                    creator_name LIKE ?
                )';
                $params[] = "%{$word}%";
                $params[] = "%{$word}%";
                $params[] = "%{$word}%";
            }
        }
        
        if (!empty($searchConditions)) {
            $conditions[] = '(' . implode(' AND ', $searchConditions) . ')';
        }
    }
    
    // Handle status filter
    switch ($status) {
        case 'used':
            $conditions[] = 'usage_count > 0';
            break;
        case 'unused':
            $conditions[] = 'usage_count = 0';
            break;
        case 'popular':
            $conditions[] = 'usage_count >= 10';
            break;
        case 'recent':
            $conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
            break;
    }
    
    $whereClause = implode(' AND ', $conditions);
    
    // Count total results
    $countSql = "SELECT COUNT(*) as total FROM tags WHERE {$whereClause}";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get results with highlighting
    $sql = "
        SELECT 
            id,
            title,
            description,
            color_start,
            color_end,
            text_color,
            usage_count,
            creator_name,
            created_at,
            updated_at
        FROM tags 
        WHERE {$whereClause}
        ORDER BY 
            CASE 
                WHEN usage_count > 0 THEN 1 
                ELSE 2 
            END,
            usage_count DESC,
            created_at DESC
        LIMIT {$limit} OFFSET {$offset}
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add highlighting to results
    if (!empty($searchTerms)) {
        foreach ($tags as &$tag) {
            $tag['title_highlighted'] = highlightText($tag['title'], $searchTerms);
            $tag['description_highlighted'] = highlightText($tag['description'] ?? '', $searchTerms);
            $tag['creator_name_highlighted'] = highlightText($tag['creator_name'] ?? '', $searchTerms);
        }
    }
    
    // Response
    echo json_encode([
        'success' => true,
        'data' => $tags,
        'total' => (int)$total,
        'limit' => $limit,
        'offset' => $offset,
        'search_terms' => $searchTerms,
        'query' => $search,
        'status_filter' => $status
    ]);
}

/**
 * ایجاد برچسب جدید
 */
function handleCreate($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($input['title'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'عنوان برچسب الزامی است']);
        return;
    }
    
    // Sanitize inputs
    $title = trim($input['title']);
    $description = trim($input['description'] ?? '');
    $colorStart = $input['color_start'] ?? '#667eea';
    $colorEnd = $input['color_end'] ?? '#764ba2';
    $textColor = $input['text_color'] ?? '#ffffff';
    $creatorName = $input['creator_name'] ?? 'سیستم';
    
    // Check if tag with same title exists
    $checkSql = "SELECT id FROM tags WHERE title = ? AND deleted_at IS NULL";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$title]);
    
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'برچسب با این عنوان قبلاً ایجاد شده است']);
        return;
    }
    
    // Insert new tag
    $sql = "
        INSERT INTO tags (
            title, description, color_start, color_end, text_color, 
            creator_name, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $title, $description, $colorStart, $colorEnd, $textColor, $creatorName
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => 'برچسب با موفقیت ایجاد شد',
            'data' => ['id' => $newId]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'خطا در ایجاد برچسب']);
    }
}

/**
 * بروزرسانی برچسب
 */
function handleUpdate($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($_GET['id'] ?? $input['id'] ?? 0);
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'شناسه برچسب الزامی است']);
        return;
    }
    
    // Check if tag exists
    $checkSql = "SELECT id FROM tags WHERE id = ? AND deleted_at IS NULL";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$id]);
    
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'برچسب یافت نشد']);
        return;
    }
    
    // Build update query
    $updates = [];
    $params = [];
    
    if (isset($input['title'])) {
        $updates[] = 'title = ?';
        $params[] = trim($input['title']);
    }
    
    if (isset($input['description'])) {
        $updates[] = 'description = ?';
        $params[] = trim($input['description']);
    }
    
    if (isset($input['color_start'])) {
        $updates[] = 'color_start = ?';
        $params[] = $input['color_start'];
    }
    
    if (isset($input['color_end'])) {
        $updates[] = 'color_end = ?';
        $params[] = $input['color_end'];
    }
    
    if (isset($input['text_color'])) {
        $updates[] = 'text_color = ?';
        $params[] = $input['text_color'];
    }
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'هیچ فیلدی برای بروزرسانی ارسال نشده']);
        return;
    }
    
    $updates[] = 'updated_at = NOW()';
    $params[] = $id;
    
    $sql = "UPDATE tags SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'برچسب با موفقیت بروزرسانی شد'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'خطا در بروزرسانی برچسب']);
    }
}

/**
 * حذف برچسب (soft delete)
 */
function handleDelete($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($_GET['id'] ?? $input['id'] ?? 0);
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'شناسه برچسب الزامی است']);
        return;
    }
    
    // Check if tag exists
    $checkSql = "SELECT id, usage_count FROM tags WHERE id = ? AND deleted_at IS NULL";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$id]);
    $tag = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tag) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'برچسب یافت نشد']);
        return;
    }
    
    // Warn if tag is being used
    if ($tag['usage_count'] > 0) {
        $force = $_GET['force'] ?? $input['force'] ?? false;
        if (!$force) {
            echo json_encode([
                'success' => false,
                'error' => 'این برچسب در حال استفاده است',
                'warning' => true,
                'usage_count' => $tag['usage_count']
            ]);
            return;
        }
    }
    
    // Soft delete
    $sql = "UPDATE tags SET deleted_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'برچسب با موفقیت حذف شد'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'خطا در حذف برچسب']);
    }
}

/**
 * اعمال highlighting به متن
 */
function highlightText($text, $searchTerms) {
    if (empty($text) || empty($searchTerms)) {
        return $text;
    }
    
    $highlightedText = $text;
    
    foreach ($searchTerms as $term) {
        $highlightedText = preg_replace(
            '/(' . preg_quote($term, '/') . ')/iu',
            '<mark>$1</mark>',
            $highlightedText
        );
    }
    
    return $highlightedText;
}
?> 