<?php
/**
 * نام فایل: Validation.php
 * مسیر فایل: /app/helpers/Validation.php
 * توضیح: کلاس اعتبارسنجی داده‌ها
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

class Validation 
{
    private $errors = [];
    private $data = [];

    /**
     * اعتبارسنجی داده‌ها بر اساس قوانین
     */
    public function validate($data, $rules) 
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $this->validateField($field, $ruleString);
        }

        return [
            'success' => empty($this->errors),
            'errors' => $this->errors,
            'data' => $this->data
        ];
    }

    /**
     * اعتبارسنجی یک فیلد
     */
    private function validateField($field, $ruleString) 
    {
        $rules = explode('|', $ruleString);
        $value = $this->data[$field] ?? null;

        foreach ($rules as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }

    /**
     * اعمال یک قانون
     */
    private function applyRule($field, $value, $rule) 
    {
        // تجزیه قانون (مثل min:3 یا max:50)
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                $this->validateRequired($field, $value);
                break;
            case 'string':
                $this->validateString($field, $value);
                break;
            case 'numeric':
                $this->validateNumeric($field, $value);
                break;
            case 'integer':
                $this->validateInteger($field, $value);
                break;
            case 'email':
                $this->validateEmail($field, $value);
                break;
            case 'mobile':
                $this->validateMobile($field, $value);
                break;
            case 'min':
                $this->validateMin($field, $value, $parameter);
                break;
            case 'max':
                $this->validateMax($field, $value, $parameter);
                break;
            case 'between':
                $this->validateBetween($field, $value, $parameter);
                break;
            case 'in':
                $this->validateIn($field, $value, $parameter);
                break;
            case 'not_in':
                $this->validateNotIn($field, $value, $parameter);
                break;
            case 'unique':
                $this->validateUnique($field, $value, $parameter);
                break;
            case 'exists':
                $this->validateExists($field, $value, $parameter);
                break;
            case 'confirmed':
                $this->validateConfirmed($field, $value);
                break;
            case 'date':
                $this->validateDate($field, $value);
                break;
            case 'persian_date':
                $this->validatePersianDate($field, $value);
                break;
            case 'file':
                $this->validateFile($field, $value);
                break;
            case 'image':
                $this->validateImage($field, $value);
                break;
            case 'mimes':
                $this->validateMimes($field, $value, $parameter);
                break;
            case 'max_size':
                $this->validateMaxSize($field, $value, $parameter);
                break;
            case 'regex':
                $this->validateRegex($field, $value, $parameter);
                break;
            case 'alpha':
                $this->validateAlpha($field, $value);
                break;
            case 'alpha_num':
                $this->validateAlphaNum($field, $value);
                break;
            case 'username':
                $this->validateUsername($field, $value);
                break;
            case 'password':
                $this->validatePassword($field, $value);
                break;
            case 'url':
                $this->validateUrl($field, $value);
                break;
            case 'ip':
                $this->validateIp($field, $value);
                break;
            case 'json':
                $this->validateJson($field, $value);
                break;
        }
    }

    /**
     * اعتبارسنجی الزامی بودن
     */
    private function validateRequired($field, $value) 
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, 'این فیلد الزامی است');
        }
    }

    /**
     * اعتبارسنجی رشته بودن
     */
    private function validateString($field, $value) 
    {
        if ($value !== null && !is_string($value)) {
            $this->addError($field, 'این فیلد باید رشته متنی باشد');
        }
    }

    /**
     * اعتبارسنجی عددی بودن
     */
    private function validateNumeric($field, $value) 
    {
        if ($value !== null && !is_numeric($value)) {
            $this->addError($field, 'این فیلد باید عدد باشد');
        }
    }

    /**
     * اعتبارسنجی عدد صحیح بودن
     */
    private function validateInteger($field, $value) 
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, 'این فیلد باید عدد صحیح باشد');
        }
    }

    /**
     * اعتبارسنجی ایمیل
     */
    private function validateEmail($field, $value) 
    {
        if ($value !== null && !Security::validateEmail($value)) {
            $this->addError($field, 'فرمت ایمیل صحیح نیست');
        }
    }

    /**
     * اعتبارسنجی شماره موبایل
     */
    private function validateMobile($field, $value) 
    {
        if ($value !== null && !Security::validateMobile($value)) {
            $this->addError($field, 'فرمت شماره موبایل صحیح نیست');
        }
    }

    /**
     * اعتبارسنجی حداقل طول
     */
    private function validateMin($field, $value, $min) 
    {
        if ($value === null) return;

        if (is_string($value) && mb_strlen($value, 'UTF-8') < $min) {
            $this->addError($field, "این فیلد باید حداقل {$min} کاراکتر باشد");
        } elseif (is_numeric($value) && $value < $min) {
            $this->addError($field, "این فیلد باید حداقل {$min} باشد");
        } elseif (is_array($value) && count($value) < $min) {
            $this->addError($field, "این فیلد باید حداقل {$min} آیتم داشته باشد");
        }
    }

    /**
     * اعتبارسنجی حداکثر طول
     */
    private function validateMax($field, $value, $max) 
    {
        if ($value === null) return;

        if (is_string($value) && mb_strlen($value, 'UTF-8') > $max) {
            $this->addError($field, "این فیلد نباید بیش از {$max} کاراکتر باشد");
        } elseif (is_numeric($value) && $value > $max) {
            $this->addError($field, "این فیلد نباید بیش از {$max} باشد");
        } elseif (is_array($value) && count($value) > $max) {
            $this->addError($field, "این فیلد نباید بیش از {$max} آیتم داشته باشد");
        }
    }

    /**
     * اعتبارسنجی بین دو مقدار
     */
    private function validateBetween($field, $value, $parameter) 
    {
        if ($value === null) return;

        list($min, $max) = explode(',', $parameter);
        
        if (is_string($value)) {
            $length = mb_strlen($value, 'UTF-8');
            if ($length < $min || $length > $max) {
                $this->addError($field, "این فیلد باید بین {$min} تا {$max} کاراکتر باشد");
            }
        } elseif (is_numeric($value)) {
            if ($value < $min || $value > $max) {
                $this->addError($field, "این فیلد باید بین {$min} تا {$max} باشد");
            }
        }
    }

    /**
     * اعتبارسنجی وجود در لیست
     */
    private function validateIn($field, $value, $parameter) 
    {
        if ($value === null) return;

        $allowedValues = explode(',', $parameter);
        if (!in_array($value, $allowedValues)) {
            $this->addError($field, 'مقدار انتخابی معتبر نیست');
        }
    }

    /**
     * اعتبارسنجی عدم وجود در لیست
     */
    private function validateNotIn($field, $value, $parameter) 
    {
        if ($value === null) return;

        $forbiddenValues = explode(',', $parameter);
        if (in_array($value, $forbiddenValues)) {
            $this->addError($field, 'مقدار وارد شده مجاز نیست');
        }
    }

    /**
     * اعتبارسنجی یکتا بودن در دیتابیس
     */
    private function validateUnique($field, $value, $parameter) 
    {
        if ($value === null) return;

        $parts = explode(',', $parameter);
        $table = $parts[0];
        $column = $parts[1] ?? $field;
        $except = $parts[2] ?? null;

        try {
            $db = getDB();
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
            $params = [$value];

            if ($except) {
                $sql .= " AND id != ?";
                $params[] = $except;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();

            if ($result['count'] > 0) {
                $this->addError($field, 'این مقدار قبلاً استفاده شده است');
            }
        } catch (Exception $e) {
            writeLog("خطا در اعتبارسنجی unique: " . $e->getMessage(), 'ERROR');
        }
    }

    /**
     * اعتبارسنجی وجود در دیتابیس
     */
    private function validateExists($field, $value, $parameter) 
    {
        if ($value === null) return;

        $parts = explode(',', $parameter);
        $table = $parts[0];
        $column = $parts[1] ?? $field;

        try {
            $db = getDB();
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$value]);
            $result = $stmt->fetch();

            if ($result['count'] == 0) {
                $this->addError($field, 'مقدار انتخابی وجود ندارد');
            }
        } catch (Exception $e) {
            writeLog("خطا در اعتبارسنجی exists: " . $e->getMessage(), 'ERROR');
        }
    }

    /**
     * اعتبارسنجی تایید فیلد
     */
    private function validateConfirmed($field, $value) 
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;

        if ($value !== $confirmValue) {
            $this->addError($field, 'فیلد تایید مطابقت ندارد');
        }
    }

    /**
     * اعتبارسنجی تاریخ
     */
    private function validateDate($field, $value) 
    {
        if ($value !== null && !strtotime($value)) {
            $this->addError($field, 'فرمت تاریخ صحیح نیست');
        }
    }

    /**
     * اعتبارسنجی تاریخ فارسی
     */
    private function validatePersianDate($field, $value) 
    {
        if ($value === null) return;

        // بررسی فرمت تاریخ فارسی (مثل 1403/05/15)
        if (!preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $value, $matches)) {
            $this->addError($field, 'فرمت تاریخ فارسی صحیح نیست (مثال: 1403/05/15)');
            return;
        }

        $year = (int)$matches[1];
        $month = (int)$matches[2];
        $day = (int)$matches[3];

        if ($month < 1 || $month > 12) {
            $this->addError($field, 'ماه وارد شده معتبر نیست');
        } elseif ($day < 1 || $day > 31) {
            $this->addError($field, 'روز وارد شده معتبر نیست');
        }
    }

    /**
     * اعتبارسنجی فایل
     */
    private function validateFile($field, $value) 
    {
        if ($value === null) return;

        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, 'فایل آپلود نشده یا خطا در آپلود');
        }
    }

    /**
     * اعتبارسنجی تصویر
     */
    private function validateImage($field, $value) 
    {
        if ($value === null) return;

        $this->validateFile($field, $value);

        if (isset($_FILES[$field])) {
            $imageInfo = getimagesize($_FILES[$field]['tmp_name']);
            if (!$imageInfo) {
                $this->addError($field, 'فایل آپلود شده تصویر معتبری نیست');
            }
        }
    }

    /**
     * اعتبارسنجی نوع فایل
     */
    private function validateMimes($field, $value, $parameter) 
    {
        if ($value === null) return;

        if (isset($_FILES[$field])) {
            $allowedMimes = explode(',', $parameter);
            $fileMime = $_FILES[$field]['type'];
            
            if (!in_array($fileMime, $allowedMimes)) {
                $this->addError($field, 'نوع فایل مجاز نیست');
            }
        }
    }

    /**
     * اعتبارسنجی حداکثر اندازه فایل
     */
    private function validateMaxSize($field, $value, $parameter) 
    {
        if ($value === null) return;

        if (isset($_FILES[$field])) {
            $maxSize = $parameter * 1024; // تبدیل KB به byte
            if ($_FILES[$field]['size'] > $maxSize) {
                $this->addError($field, "اندازه فایل نباید بیش از {$parameter} کیلوبایت باشد");
            }
        }
    }

    /**
     * اعتبارسنجی الگوی regex
     */
    private function validateRegex($field, $value, $pattern) 
    {
        if ($value !== null && !preg_match($pattern, $value)) {
            $this->addError($field, 'فرمت وارد شده صحیح نیست');
        }
    }

    /**
     * اعتبارسنجی حروف الفبا
     */
    private function validateAlpha($field, $value) 
    {
        if ($value !== null && !preg_match('/^[\p{L}\s]+$/u', $value)) {
            $this->addError($field, 'این فیلد فقط باید شامل حروف باشد');
        }
    }

    /**
     * اعتبارسنجی حروف و اعداد
     */
    private function validateAlphaNum($field, $value) 
    {
        if ($value !== null && !preg_match('/^[\p{L}\p{N}\s]+$/u', $value)) {
            $this->addError($field, 'این فیلد فقط باید شامل حروف و اعداد باشد');
        }
    }

    /**
     * اعتبارسنجی نام کاربری
     */
    private function validateUsername($field, $value) 
    {
        if ($value !== null && !Security::validateUsername($value)) {
            $this->addError($field, 'نام کاربری باید شامل حروف انگلیسی، اعداد و خط زیر باشد (3-20 کاراکتر)');
        }
    }

    /**
     * اعتبارسنجی رمز عبور
     */
    private function validatePassword($field, $value) 
    {
        if ($value !== null) {
            $result = Security::validatePassword($value);
            if ($result !== true) {
                foreach ($result as $error) {
                    $this->addError($field, $error);
                }
            }
        }
    }

    /**
     * اعتبارسنجی URL
     */
    private function validateUrl($field, $value) 
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, 'آدرس وب معتبر وارد کنید');
        }
    }

    /**
     * اعتبارسنجی IP
     */
    private function validateIp($field, $value) 
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_IP)) {
            $this->addError($field, 'آدرس IP معتبر وارد کنید');
        }
    }

    /**
     * اعتبارسنجی JSON
     */
    private function validateJson($field, $value) 
    {
        if ($value !== null) {
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError($field, 'فرمت JSON معتبر نیست');
            }
        }
    }

    /**
     * اضافه کردن خطا
     */
    private function addError($field, $message) 
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * دریافت تمام خطاها
     */
    public function getErrors() 
    {
        return $this->errors;
    }

    /**
     * بررسی وجود خطا
     */
    public function hasErrors() 
    {
        return !empty($this->errors);
    }

    /**
     * دریافت اولین خطا
     */
    public function getFirstError($field = null) 
    {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }
        
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }
        
        return null;
    }
}
?>