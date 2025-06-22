<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خطا - <?php echo $error_code ?? 500; ?></title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        
        .error-code {
            font-size: 4rem;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        .error-message {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .back-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        
        .back-button:hover {
            background: #5a6fd8;
        }
        
        .timestamp {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?php echo $error_code ?? 500; ?></div>
        <div class="error-message">
            <?php echo $error_message ?? 'خطایی رخ داده است'; ?>
        </div>
        <a href="<?php echo url('dashboard'); ?>" class="back-button">
            بازگشت به داشبورد
        </a>
        <div class="timestamp">
            <?php echo date('Y/m/d H:i:s'); ?>
        </div>
    </div>
</body>
</html> 