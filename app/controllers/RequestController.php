<?php
/**
 * کنترلر مدیریت درخواست‌ها
 */

require_once 'BaseController.php';

class RequestController extends BaseController
{
    public function index()
    {
        $this->render('requests/index', [
            'title' => 'مدیریت درخواست‌ها',
            'requests' => []
        ]);
    }
    
    public function create()
    {
        $this->render('requests/create', [
            'title' => 'ایجاد درخواست جدید'
        ]);
    }
}
?>
