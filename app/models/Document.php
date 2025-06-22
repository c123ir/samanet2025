<?php
/**
 * نام فایل: Document.php
 * مسیر فایل: /app/models/Document.php
 * توضیح: مدل اسناد و تصاویر
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once APP_PATH . 'models/Database.php';

class Document extends Database
{
    protected $table = 'documents';
    protected $fillable = [
        'request_id', 'file_name', 'file_path', 'file_type', 'file_size',
        'extracted_text', 'watermarked', 'uploaded_by', 'upload_date'
    ];
    
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * دریافت اسناد مربوط به یک درخواست
     */
    public function getRequestDocuments($requestId) 
    {
        try {
            return $this->where('request_id', $requestId);
        } catch (Exception $e) {
            writeLog("خطا در دریافت اسناد درخواست: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    /**
     * آپلود سند جدید
     */
    public function uploadDocument($requestId, $fileData, $uploadedBy) 
    {
        try {
            $data = [
                'request_id' => $requestId,
                'file_name' => $fileData['name'],
                'file_path' => $fileData['path'],
                'file_type' => $fileData['type'],
                'file_size' => $fileData['size'],
                'uploaded_by' => $uploadedBy,
                'upload_date' => date('Y-m-d H:i:s')
            ];

            return $this->create($data);
        } catch (Exception $e) {
            writeLog("خطا در آپلود سند: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}
?>
