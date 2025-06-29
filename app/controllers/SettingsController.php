<?php
/**
 * کنترلر تنظیمات سیستم
 */
class SettingsController extends BaseController 
{
    public function index()
    {
        $this->requireAuth();
        
        $stats = [
            'total_users' => 14,
            'total_requests' => 45,
            'total_tags' => 8,
            'disk_usage' => '2.3 GB',
            'system_status' => 'active',
            'last_backup' => 'هرگز',
            'php_version' => PHP_VERSION,
            'mysql_version' => '8.0.32'
        ];
        
        $settings = [
            'site_name' => 'سامانت',
            'site_version' => '4.0.0',
            'max_upload_size' => '5MB',
            'session_timeout' => '30 دقیقه',
            'default_language' => 'فارسی',
            'timezone' => 'Asia/Tehran'
        ];
        
        $this->render('settings/index', [
            'title' => 'تنظیمات سیستم',
            'stats' => $stats,
            'settings' => $settings,
            'additional_css' => ['css/dashboard.css']
        ]);
    }
} 