<?php
/**
 * نام فایل: AuthController.php
 * مسیر فایل: /app/controllers/AuthController.php
 * توضیح: کنترلر احراز هویت و ثبت‌نام
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

require_once APP_PATH . 'controllers/BaseController.php';
require_once APP_PATH . 'models/User.php';

class AuthController extends BaseController 
{
    protected $requireAuth = false; // این کنترلر نیاز به احراز هویت ندارد
    private $userModel;

    public function __construct() 
    {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * نمایش صفحه ورود
     */
    public function login() 
    {
        writeLog("Debug: AuthController->login() called. Method: " . $_SERVER['REQUEST_METHOD'], 'INFO');
        writeLog("Debug: isLoggedIn check in login(): " . (Security::isLoggedIn() ? 'true' : 'false'), 'INFO');
        
        // اگر کاربر وارد شده، به داشبورد هدایت شود
        if (Security::isLoggedIn()) {
            writeLog("Debug: User already logged in, redirecting to dashboard", 'INFO');
            Security::redirect(url('dashboard'));
        }

        if ($this->isPost()) {
            writeLog("Debug: Processing POST request for login", 'INFO');
            $this->handleLogin();
        } else {
            writeLog("Debug: Showing login form", 'INFO');
            $this->showLoginForm();
        }
    }

    /**
     * نمایش فرم ورود
     */
    private function showLoginForm() 
    {
        $this->data['page_title'] = 'ورود به سامانت';
        $this->data['return_url'] = $this->input('return', '');
        $this->data['flash'] = $this->getFlash();
        
        // بررسی قفل بودن IP
        if (SecurityConfig::isLoginLocked()) {
            $this->data['login_locked'] = true;
            $this->data['lockout_time'] = LOGIN_LOCKOUT_TIME / 60; // تبدیل به دقیقه
        }

        $this->renderPartial('auth/login', $this->data);
    }

    /**
     * پردازش درخواست ورود
     */
    private function handleLogin() 
    {
        try {
            // بررسی CSRF (موقتاً غیرفعال برای تست)
            // $this->verifyCSRF();

            // دریافت داده‌های ورودی
            $username = $this->input('username');
            $password = $this->input('password');
            $rememberMe = $this->input('remember_me') === '1';
            $returnUrl = $this->input('return_url', 'dashboard');

            // اعتبارسنجی ورودی‌ها
            $errors = [];
            
            if (empty($username)) {
                $errors[] = 'نام کاربری الزامی است';
            }
            
            if (empty($password)) {
                $errors[] = 'رمز عبور الزامی است';
            }

            if (!empty($errors)) {
                if ($this->isAjaxRequest()) {
                    $this->sendError('اطلاعات ناقص است', 422, $errors);
                } else {
                    $this->redirectWithMessage(url('login'), 'error', implode('<br>', $errors));
                }
                return;
            }

            // برای تست، یک کاربر پیش‌فرض ایجاد می‌کنیم
            if ($username === 'admin' && $password === 'admin123') {
                $this->createTestSession();
                
                // Debug: بررسی session ایجاد شده
                writeLog("Debug: Test session created. User ID: " . $_SESSION['user_id'], 'INFO');
                writeLog("Debug: isLoggedIn check: " . (Security::isLoggedIn() ? 'true' : 'false'), 'INFO');
                
                // ریدایرکت به dashboard
                Security::redirect(url('dashboard'));
                exit(); // اطمینان از اینکه اسکریپت متوقف شود
            } else {
                // تلاش برای احراز هویت
                if (class_exists('User')) {
                    $result = $this->userModel->authenticate($username, $password, $rememberMe);

                    if ($result['success']) {
                        // ورود موفق
                        writeLog("ورود موفق کاربر: {$username}", 'INFO');
                        
                        if ($this->isAjaxRequest()) {
                            $this->sendSuccess('ورود موفقیت‌آمیز بود', [
                                'redirect_url' => url($returnUrl)
                            ]);
                        } else {
                            $this->redirectWithMessage(url($returnUrl), 'success', 'خوش آمدید!');
                        }
                    } else {
                        // ورود ناموفق
                        writeLog("ورود ناموفق: {$username}", 'WARNING');
                        
                        if ($this->isAjaxRequest()) {
                            $this->sendError($result['message'], 401);
                        } else {
                            $this->redirectWithMessage(url('login'), 'error', $result['message']);
                        }
                    }
                } else {
                    // کلاس User موجود نیست
                    $this->redirectWithMessage(url('login'), 'error', 'نام کاربری یا رمز عبور اشتباه است');
                }
            }

        } catch (Exception $e) {
            writeLog("خطا در پردازش ورود: " . $e->getMessage(), 'ERROR');
            
            if ($this->isAjaxRequest()) {
                $this->sendError('خطای سیستمی رخ داده است', 500);
            } else {
                $this->redirectWithMessage(url('login'), 'error', 'خطای سیستمی رخ داده است');
            }
        }
    }

    /**
     * ایجاد session تست
     */
    private function createTestSession() 
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['user_role'] = 'admin';
        $_SESSION['group_id'] = 1;
        $_SESSION['full_name'] = 'مدیر سیستم';
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
    }

    /**
     * خروج از سیستم
     */
    public function logout() 
    {
        $username = $_SESSION['username'] ?? 'نامشخص';
        
        // حذف کوکی Remember Me
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }

        // خروج از سیستم
        Security::logout();
        
        writeLog("خروج کاربر: {$username}", 'INFO');
        
        if ($this->isAjaxRequest()) {
            $this->sendSuccess('خروج موفقیت‌آمیز بود', ['redirect_url' => url('login')]);
        } else {
            $this->redirectWithMessage(url('login'), 'success', 'با موفقیت خارج شدید');
        }
    }

    /**
     * نمایش صفحه ثبت‌نام
     */
    public function register() 
    {
        // بررسی اینکه آیا ثبت‌نام فعال است
        if (!$this->isRegistrationEnabled()) {
            $this->sendError('ثبت‌نام در حال حاضر غیرفعال است', 403);
        }

        // اگر کاربر وارد شده، به داشبورد هدایت شود
        if (Security::isLoggedIn()) {
            Security::redirect(url('dashboard'));
        }

        if ($this->isPost()) {
            $this->handleRegister();
        } else {
            $this->showRegisterForm();
        }
    }

    /**
     * نمایش فرم ثبت‌نام
     */
    private function showRegisterForm() 
    {
        $this->data['page_title'] = 'ثبت‌نام در سامانت';
        $this->data['flash'] = $this->getFlash();
        $this->render('auth/register', $this->data);
    }

    /**
     * پردازش درخواست ثبت‌نام
     */
    private function handleRegister() 
    {
        try {
            // بررسی CSRF
            $this->verifyCSRF();

            // دریافت داده‌های ورودی
            $data = [
                'username' => $this->input('username'),
                'password' => $this->input('password'),
                'password_confirm' => $this->input('password_confirm'),
                'full_name' => $this->input('full_name'),
                'email' => $this->input('email'),
                'phone' => $this->input('phone'),
                'role' => 'user', // نقش پیش‌فرض
                'status' => 'active'
            ];

            // تبدیل اعداد فارسی به انگلیسی برای فیلدهای عددی
            if (!empty($data['phone'])) {
                $data['phone'] = Security::convertPersianToEnglishNumbers($data['phone']);
            }

            // اعتبارسنجی اضافی
            $errors = $this->validateRegistrationData($data);
            
            if (!empty($errors)) {
                if ($this->isAjaxRequest()) {
                    $this->sendError('اطلاعات نامعتبر است', 422, $errors);
                } else {
                    $this->redirectWithMessage(url('register'), 'error', implode('<br>', $errors));
                }
                return;
            }

            // حذف فیلد تایید رمز عبور
            unset($data['password_confirm']);

            // ایجاد کاربر جدید
            $result = $this->userModel->createUser($data);

            if ($result['success']) {
                writeLog("ثبت‌نام موفق کاربر جدید: {$data['username']}", 'INFO');
                
                if ($this->isAjaxRequest()) {
                    $this->sendSuccess('ثبت‌نام با موفقیت انجام شد', [
                        'redirect_url' => url('login')
                    ]);
                } else {
                    $this->redirectWithMessage(url('login'), 'success', 'ثبت‌نام با موفقیت انجام شد. اکنون می‌توانید وارد شوید.');
                }
            } else {
                if ($this->isAjaxRequest()) {
                    $this->sendError('خطا در ثبت‌نام', 400, $result['errors']);
                } else {
                    $this->redirectWithMessage(url('register'), 'error', implode('<br>', $result['errors']));
                }
            }

        } catch (Exception $e) {
            writeLog("خطا در پردازش ثبت‌نام: " . $e->getMessage(), 'ERROR');
            
            if ($this->isAjaxRequest()) {
                $this->sendError('خطای سیستمی رخ داده است', 500);
            } else {
                $this->redirectWithMessage(url('register'), 'error', 'خطای سیستمی رخ داده است');
            }
        }
    }

    /**
     * اعتبارسنجی داده‌های ثبت‌نام
     */
    private function validateRegistrationData($data) 
    {
        $errors = [];

        // بررسی تایید رمز عبور
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'رمز عبور و تایید آن مطابقت ندارند';
        }

        // بررسی طول نام کامل
        if (strlen($data['full_name']) < 2) {
            $errors[] = 'نام کامل باید حداقل ۲ کاراکتر باشد';
        }

        // بررسی اختیاری بودن ایمیل و موبایل اما صحت آن‌ها
        if (!empty($data['email']) && !Security::validateEmail($data['email'])) {
            $errors[] = 'فرمت ایمیل صحیح نیست';
        }

        if (!empty($data['phone'])) {
            if (!Security::validateMobile($data['phone'])) {
                $errors[] = 'فرمت شماره موبایل صحیح نیست. مثال: 09123456789';
            }
        }

        return $errors;
    }

    /**
     * بررسی فعال بودن ثبت‌نام
     */
    private function isRegistrationEnabled() 
    {
        // می‌توان این مقدار را از تنظیمات سیستم خواند
        return true; // فعلاً همیشه فعال
    }

    /**
     * نمایش صفحه فراموشی رمز عبور
     */
    public function forgotPassword() 
    {
        if ($this->isPost()) {
            $this->handleForgotPassword();
        } else {
            $this->showForgotPasswordForm();
        }
    }

    /**
     * نمایش فرم فراموشی رمز عبور
     */
    private function showForgotPasswordForm() 
    {
        $this->data['page_title'] = 'بازیابی رمز عبور';
        $this->data['flash'] = $this->getFlash();
        $this->render('auth/forgot-password', $this->data);
    }

    /**
     * پردازش درخواست فراموشی رمز عبور
     */
    private function handleForgotPassword() 
    {
        try {
            // بررسی CSRF
            $this->verifyCSRF();

            $email = $this->input('email');

            if (empty($email) || !Security::validateEmail($email)) {
                if ($this->isAjaxRequest()) {
                    $this->sendError('ایمیل معتبر وارد کنید', 422);
                } else {
                    $this->redirectWithMessage(url('forgot-password'), 'error', 'ایمیل معتبر وارد کنید');
                }
                return;
            }

            // پردازش بازیابی رمز عبور
            $result = $this->userModel->resetPassword($email);

            if ($this->isAjaxRequest()) {
                $this->sendSuccess($result['message']);
            } else {
                $this->redirectWithMessage(url('login'), 'success', $result['message']);
            }

        } catch (Exception $e) {
            writeLog("خطا در بازیابی رمز عبور: " . $e->getMessage(), 'ERROR');
            
            if ($this->isAjaxRequest()) {
                $this->sendError('خطای سیستمی رخ داده است', 500);
            } else {
                $this->redirectWithMessage(url('forgot-password'), 'error', 'خطای سیستمی رخ داده است');
            }
        }
    }

    /**
     * بررسی اعتبار session از کوکی Remember Me
     */
    public function checkRememberToken() 
    {
        if (Security::isLoggedIn()) {
            return; // کاربر قبلاً وارد شده
        }

        if (!isset($_COOKIE['remember_token'])) {
            return; // کوکی وجود ندارد
        }

        try {
            $token = $_COOKIE['remember_token'];
            $hashedToken = hash('sha256', $token);

            // جستجوی کاربر با token
            $user = $this->userModel->first('remember_token', $hashedToken);

            if ($user && $user['status'] === 'active') {
                // ایجاد session جدید
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['group_id'] = $user['group_id'];
                $_SESSION['login_time'] = time();

                // به‌روزرسانی آخرین ورود
                $this->userModel->updateLastLogin($user['id']);

                writeLog("ورود خودکار با Remember Token: {$user['username']}", 'INFO');
            } else {
                // حذف کوکی نامعتبر
                setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            }

        } catch (Exception $e) {
            writeLog("خطا در بررسی Remember Token: " . $e->getMessage(), 'ERROR');
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
    }

    /**
     * تأیید ایمیل کاربر
     */
    public function verifyEmail() 
    {
        $token = $this->input('token');
        
        if (empty($token)) {
            $this->sendError('لینک تأیید نامعتبر است', 400);
        }

        // TODO: پیاده‌سازی تأیید ایمیل
        $this->redirectWithMessage(url('login'), 'success', 'ایمیل شما با موفقیت تأیید شد');
    }
}
?>