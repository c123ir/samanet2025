<?php
/**
 * نام فایل: Database.php
 * مسیر فایل: /app/models/Database.php
 * توضیح: کلاس پایه مدل‌های دیتابیس
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

class Database 
{
    protected $connection;
    protected $table;
    protected $fillable = [];
    protected $hidden = [];
    
    public function __construct() 
    {
        $this->connection = getDB();
    }
    
    public function getConnection() 
    {
        return $this->connection;
    }
    
    public function query($sql, $params = []) 
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            writeLog("خطا در اجرای query: " . $e->getMessage() . " SQL: " . $sql, 'ERROR');
            throw $e;
        }
    }

    /**
     * دریافت تمام رکوردها
     */
    public function all($columns = '*') 
    {
        try {
            $sql = "SELECT {$columns} FROM {$this->table} ORDER BY id DESC";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            writeLog("خطا در دریافت تمام رکوردها: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت رکورد بر اساس ID
     */
    public function find($id, $columns = '*') 
    {
        try {
            $sql = "SELECT {$columns} FROM {$this->table} WHERE id = ? LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            writeLog("خطا در دریافت رکورد با ID {$id}: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت رکورد بر اساس شرط
     */
    public function where($column, $operator, $value = null, $columns = '*') 
    {
        try {
            // اگر دو پارامتر داده شده، operator برابر است
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }

            $sql = "SELECT {$columns} FROM {$this->table} WHERE {$column} {$operator} ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$value]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            writeLog("خطا در دریافت رکوردها با شرط: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت اولین رکورد بر اساس شرط
     */
    public function first($column, $operator, $value = null, $columns = '*') 
    {
        try {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }

            $sql = "SELECT {$columns} FROM {$this->table} WHERE {$column} {$operator} ? LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$value]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            writeLog("خطا در دریافت اولین رکورد: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * ایجاد رکورد جدید
     */
    public function create($data) 
    {
        try {
            writeLog("🔧 Database::create() called for table: {$this->table}", 'INFO');
            writeLog("📥 Raw input data: " . json_encode($data), 'INFO');
            
            // فیلتر کردن داده‌ها بر اساس fillable
            if (!empty($this->fillable)) {
                $filteredData = array_intersect_key($data, array_flip($this->fillable));
                writeLog("📋 Fillable fields: " . json_encode($this->fillable), 'INFO');
                writeLog("📋 Filtered data: " . json_encode($filteredData), 'INFO');
                $data = $filteredData;
            }

            // حذف فیلدهای مخفی
            if (!empty($this->hidden)) {
                $data = array_diff_key($data, array_flip($this->hidden));
                writeLog("🙈 Hidden fields removed", 'INFO');
            }

            // اضافه کردن تاریخ ایجاد
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
                writeLog("📅 Added created_at: " . $data['created_at'], 'INFO');
            }

            $columns = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            writeLog("🔍 SQL Query: " . $sql, 'INFO');
            writeLog("🔍 SQL Params: " . json_encode($data), 'INFO');
            
            $stmt = $this->connection->prepare($sql);
            
            if ($stmt->execute($data)) {
                $insertId = $this->connection->lastInsertId();
                writeLog("✅ Record created successfully with ID: " . $insertId, 'INFO');
                return $insertId;
            } else {
                $errorInfo = $stmt->errorInfo();
                writeLog("❌ Execute failed. Error info: " . json_encode($errorInfo), 'ERROR');
                return false;
            }
            
        } catch (PDOException $e) {
            writeLog("💥 PDO Exception in create(): " . $e->getMessage(), 'ERROR');
            writeLog("📍 SQL State: " . $e->getCode(), 'ERROR');
            return false;
        }
    }

    /**
     * به‌روزرسانی رکورد
     */
    public function update($id, $data) 
    {
        try {
            // فیلتر کردن داده‌ها
            if (!empty($this->fillable)) {
                $data = array_intersect_key($data, array_flip($this->fillable));
            }

            if (!empty($this->hidden)) {
                $data = array_diff_key($data, array_flip($this->hidden));
            }

            // اضافه کردن تاریخ به‌روزرسانی
            $data['updated_at'] = date('Y-m-d H:i:s');

            $setParts = [];
            foreach (array_keys($data) as $key) {
                $setParts[] = "{$key} = :{$key}";
            }
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
            $data['id'] = $id;
            
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            writeLog("خطا در به‌روزرسانی رکورد با ID {$id}: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * حذف رکورد
     */
    public function delete($id) 
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            writeLog("خطا در حذف رکورد با ID {$id}: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * شمارش رکوردها
     */
    public function count($column = '*', $where = null) 
    {
        try {
            $sql = "SELECT COUNT({$column}) as count FROM {$this->table}";
            $params = [];
            
            if ($where) {
                $sql .= " WHERE " . $where['condition'];
                $params = $where['params'] ?? [];
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            writeLog("خطا در شمارش رکوردها: " . $e->getMessage(), 'ERROR');
            return 0;
        }
    }

    /**
     * جستجو در جدول
     */
    public function search($columns, $term, $limit = 20, $offset = 0) 
    {
        try {
            $searchColumns = implode(" LIKE ? OR ", $columns) . " LIKE ?";
            $sql = "SELECT * FROM {$this->table} WHERE {$searchColumns} LIMIT {$limit} OFFSET {$offset}";
            
            $params = array_fill(0, count($columns), "%{$term}%");
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            writeLog("خطا در جستجو: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * صفحه‌بندی
     */
    public function paginate($page = 1, $perPage = 20, $where = null) 
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            // شمارش کل رکوردها
            $totalRecords = $this->count('*', $where);
            $totalPages = ceil($totalRecords / $perPage);
            
            // دریافت رکوردها
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
            
            if ($where) {
                $sql .= " WHERE " . $where['condition'];
                $params = $where['params'] ?? [];
            }
            
            $sql .= " ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            
            return [
                'data' => $data,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalRecords,
                'last_page' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalRecords)
            ];
        } catch (PDOException $e) {
            writeLog("خطا در صفحه‌بندی: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * شروع تراکنش
     */
    public function beginTransaction() 
    {
        return $this->connection->beginTransaction();
    }

    /**
     * تایید تراکنش
     */
    public function commit() 
    {
        return $this->connection->commit();
    }

    /**
     * لغو تراکنش
     */
    public function rollback() 
    {
        return $this->connection->rollback();
    }

    /**
     * بررسی وجود رکورد
     */
    public function exists($column, $value) 
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$column} = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$value]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            writeLog("خطا در بررسی وجود رکورد: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * دریافت آخرین رکوردها
     */
    public function latest($limit = 10) 
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT {$limit}";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            writeLog("خطا در دریافت آخرین رکوردها: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}
?>