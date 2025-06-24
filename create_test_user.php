<?php
// ایجاد کاربر تست
require_once 'app/config/database.php';

try {
    $db = getDB();
    
    echo "<h2>🔧 ایجاد کاربر تست</h2>\n";
    
    // بررسی وجود کاربر admin
    $stmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminUser = $stmt->fetch();
    
    if ($adminUser) {
        echo "<p>✅ کاربر admin موجود است:</p>\n";
        echo "<ul>\n";
        echo "<li><strong>نام کاربری:</strong> " . $adminUser['username'] . "</li>\n";
        echo "<li><strong>نام کامل:</strong> " . $adminUser['full_name'] . "</li>\n";
        echo "<li><strong>نقش:</strong> " . $adminUser['role'] . "</li>\n";
        echo "<li><strong>گروه:</strong> " . $adminUser['group_id'] . "</li>\n";
        echo "</ul>\n";
        
        // بروزرسانی رمز عبور admin
        $newPassword = 'admin123';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
        $result = $stmt->execute([$hashedPassword]);
        
        if ($result) {
            echo "<p>🔐 <strong>رمز عبور admin بروزرسانی شد:</strong></p>\n";
            echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #0066cc; border-radius: 5px;'>\n";
            echo "<p><strong>نام کاربری:</strong> admin</p>\n";
            echo "<p><strong>رمز عبور:</strong> admin123</p>\n";
            echo "</div>\n";
        } else {
            echo "<p style='color: red;'>❌ خطا در بروزرسانی رمز عبور</p>\n";
        }
        
    } else {
        echo "<p>⚠️ کاربر admin وجود ندارد. ایجاد کاربر جدید...</p>\n";
        
        $userData = [
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name' => 'مدیر سیستم',
            'email' => 'admin@samanet.com',
            'phone' => '09123456789',
            'role' => 'admin',
            'group_id' => 1,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $stmt = $db->prepare("INSERT INTO users (username, password_hash, full_name, email, phone, role, group_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $userData['username'],
            $userData['password_hash'], 
            $userData['full_name'],
            $userData['email'],
            $userData['phone'],
            $userData['role'],
            $userData['group_id'],
            $userData['status'],
            $userData['created_at']
        ]);
        
        if ($result) {
            echo "<p>✅ کاربر admin ایجاد شد!</p>\n";
            echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #0066cc; border-radius: 5px;'>\n";
            echo "<p><strong>نام کاربری:</strong> admin</p>\n";
            echo "<p><strong>رمز عبور:</strong> admin123</p>\n";
            echo "</div>\n";
        } else {
            echo "<p style='color: red;'>❌ خطا در ایجاد کاربر</p>\n";
        }
    }
    
    echo "<br>\n";
    echo "<h3>📋 مراحل ورود:</h3>\n";
    echo "<ol>\n";
    echo "<li>به آدرس <a href='http://localhost:3110'>http://localhost:3110</a> بروید</li>\n";
    echo "<li>نام کاربری: <strong>admin</strong></li>\n";
    echo "<li>رمز عبور: <strong>admin123</strong></li>\n";
    echo "<li>بعد از ورود، به قسمت 'درخواست‌های حواله' بروید</li>\n";
    echo "</ol>\n";
    
    // حذف فایل پس از اجرا
    if (file_exists(__FILE__)) {
        unlink(__FILE__);
        echo "<p><em>🗑️ این فایل برای امنیت حذف شد.</em></p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>خطا:</strong> " . $e->getMessage() . "</p>\n";
}
?> 