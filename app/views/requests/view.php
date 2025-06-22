<?php
/**
 * نام فایل: view.php
 * مسیر فایل: /app/views/requests/view.php
 * توضیح: صفحه نمایش جزئیات درخواست حواله
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تنظیم متغیرهای صفحه
$page_title = $title ?? 'جزئیات درخواست حواله';
$page_description = 'نمایش کامل اطلاعات و تاریخچه درخواست حواله';
$active_menu = 'requests';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}

// چک کردن وجود درخواست
if (!isset($request) || !$request) {
    header('Location: ?route=requests');
    exit;
}

// تنظیم متغیرها
$current_user = $user ?? [];
$can_edit = ($current_user['id'] == $request['requester_id']) || in_array($current_user['role'], ['admin', 'manager']);
$can_approve = in_array($current_user['role'], ['admin', 'manager']) && $request['status'] === 'pending';
?>

<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-file-invoice text-primary me-2"></i>
                    جزئیات درخواست حواله
                </h1>
                <p class="page-subtitle text-muted">
                    شماره مرجع: <span class="fw-bold text-primary"><?= $request['reference_number'] ?></span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <!-- دکمه بازگشت -->
                <a href="?route=requests" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    بازگشت به لیست
                </a>
                
                <!-- دکمه ویرایش -->
                <?php if ($can_edit && $request['status'] === 'pending'): ?>
                <a href="?route=requests&action=edit&id=<?= $request['id'] ?>" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>
                    ویرایش
                </a>
                <?php endif; ?>
                
                <!-- دکمه چاپ -->
                <button onclick="printRequest()" class="btn btn-outline-info">
                    <i class="fas fa-print me-2"></i>
                    چاپ
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="row">
        <!-- اطلاعات اصلی درخواست -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <!-- کارت اطلاعات اصلی -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        اطلاعات اصلی درخواست
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- عنوان درخواست -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold">عنوان درخواست:</label>
                            <div class="info-value">
                                <?= htmlspecialchars($request['title']) ?>
                            </div>
                        </div>

                        <!-- مبلغ -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">مبلغ:</label>
                            <div class="info-value amount-display">
                                <?php if ($request['amount']): ?>
                                    <span class="amount-number"><?= $request['amount_formatted'] ?></span>
                                    <span class="text-muted"> ریال</span>
                                <?php else: ?>
                                    <span class="text-muted">مشخص نشده</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- اولویت -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اولویت:</label>
                            <div class="info-value">
                                <span class="badge priority-<?= $request['priority'] ?>">
                                    <?= $request['priority_label'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- دسته‌بندی -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">دسته‌بندی:</label>
                            <div class="info-value">
                                <?= $request['category_label'] ?? 'تعیین نشده' ?>
                            </div>
                        </div>

                        <!-- تاریخ سررسید -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاریخ سررسید:</label>
                            <div class="info-value">
                                <?= $request['due_date'] ? date('Y/m/d', strtotime($request['due_date'])) : 'تعیین نشده' ?>
                            </div>
                        </div>

                        <!-- توضیحات -->
                        <?php if ($request['description']): ?>
                        <div class="col-12">
                            <label class="form-label fw-bold">توضیحات:</label>
                            <div class="info-value">
                                <?= nl2br(htmlspecialchars($request['description'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- تگ‌ها -->
                        <?php if ($request['tags'] && $request['tags'] !== '[""]'): ?>
                        <div class="col-12">
                            <label class="form-label fw-bold">تگ‌ها:</label>
                            <div class="info-value">
                                <div class="tags-display">
                                    <?php 
                                    $tags = json_decode($request['tags'], true);
                                    if (is_array($tags)):
                                        foreach ($tags as $tag):
                                            if (!empty(trim($tag))):
                                    ?>
                                        <span class="badge bg-light text-dark me-1 mb-1"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php 
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- کارت اطلاعات حساب -->
            <?php if ($request['account_holder'] || $request['bank_name'] || $request['account_number'] || $request['iban']): ?>
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-university text-success me-2"></i>
                        اطلاعات حساب مقصد
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if ($request['account_holder']): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">نام صاحب حساب:</label>
                            <div class="info-value"><?= htmlspecialchars($request['account_holder']) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($request['bank_name']): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">بانک:</label>
                            <div class="info-value"><?= htmlspecialchars($request['bank_name']) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($request['account_number']): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">شماره حساب:</label>
                            <div class="info-value ltr-text"><?= htmlspecialchars($request['account_number']) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($request['card_number']): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">شماره کارت:</label>
                            <div class="info-value ltr-text"><?= htmlspecialchars($request['card_number']) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($request['iban']): ?>
                        <div class="col-12">
                            <label class="form-label fw-bold">شماره شبا:</label>
                            <div class="info-value ltr-text">IR<?= htmlspecialchars($request['iban']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- دکمه‌های عملیات برای مدیران -->
            <?php if ($can_approve): ?>
            <div class="flat-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs text-warning me-2"></i>
                        عملیات مدیریتی
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <button onclick="approveRequest()" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            تایید درخواست
                        </button>
                        <button onclick="showRejectModal()" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>
                            رد درخواست
                        </button>
                        <button onclick="showCommentModal()" class="btn btn-warning">
                            <i class="fas fa-comment me-2"></i>
                            افزودن یادداشت
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar - اطلاعات کلی و تاریخچه -->
        <div class="col-xl-4 col-lg-5">
            <!-- کارت وضعیت درخواست -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-info me-2"></i>
                        وضعیت درخواست
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="status-display mb-3">
                        <span class="badge status-<?= $request['status'] ?> fs-6 px-3 py-2">
                            <?= $request['status_label'] ?>
                        </span>
                    </div>
                    
                    <div class="status-info">
                        <small class="text-muted d-block mb-2">
                            ایجاد شده در: <?= $request['created_at_jalali'] ?>
                        </small>
                        <?php if ($request['updated_at'] && $request['updated_at'] !== $request['created_at']): ?>
                        <small class="text-muted d-block">
                            آخرین بروزرسانی: <?= date('Y/m/d H:i', strtotime($request['updated_at'])) ?>
                        </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- کارت اطلاعات درخواست‌دهنده -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user text-secondary me-2"></i>
                        درخواست‌دهنده
                    </h5>
                </div>
                <div class="card-body">
                    <div class="requester-info">
                        <div class="mb-2">
                            <strong><?= htmlspecialchars($request['requester_name']) ?></strong>
                        </div>
                        <small class="text-muted">
                            گروه: <?= htmlspecialchars($request['group_name']) ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- QR Code شماره مرجع -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-qrcode text-dark me-2"></i>
                        کد QR
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="mb-3"></div>
                    <small class="text-muted">
                        برای اشتراک‌گذاری سریع درخواست
                    </small>
                </div>
            </div>

            <!-- تاریخچه تغییرات -->
            <?php if (isset($workflow_history) && !empty($workflow_history)): ?>
            <div class="flat-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        تاریخچه تغییرات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($workflow_history as $index => $history): ?>
                        <div class="timeline-item <?= $index === 0 ? 'timeline-item-current' : '' ?>">
                            <div class="timeline-marker">
                                <i class="fas fa-<?= getHistoryIcon($history['action']) ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong><?= getHistoryActionLabel($history['action']) ?></strong>
                                    <small class="text-muted ms-2">
                                        <?= date('Y/m/d H:i', strtotime($history['created_at'])) ?>
                                    </small>
                                </div>
                                <div class="timeline-body">
                                    <small class="text-muted">
                                        توسط: <?= htmlspecialchars($history['user_name']) ?>
                                    </small>
                                    <?php if ($history['comment']): ?>
                                    <div class="mt-1">
                                        <small><?= htmlspecialchars($history['comment']) ?></small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal رد درخواست -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رد درخواست</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">دلیل رد درخواست <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" 
                                  placeholder="لطفاً دلیل رد درخواست را بنویسید..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-danger">رد درخواست</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal افزودن یادداشت -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن یادداشت</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="commentForm">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="comment" class="form-label">یادداشت</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" 
                                  placeholder="یادداشت یا نظر خود را بنویسید..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ثبت یادداشت</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript مخصوص این صفحه -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ایجاد QR Code
    generateQRCode();
    
    // تنظیم event listener های modal ها
    setupModals();
});

// ایجاد QR Code
function generateQRCode() {
    const qrCodeDiv = document.getElementById('qrcode');
    const referenceNumber = '<?= $request['reference_number'] ?>';
    const url = `${window.location.origin}${window.location.pathname}?route=requests&action=show&id=<?= $request['id'] ?>`;
    
    QRCode.toCanvas(qrCodeDiv, url, {
        width: 120,
        height: 120,
        colorDark: '#667eea',
        colorLight: '#ffffff',
        margin: 1
    }, function(error) {
        if (error) {
            console.error('خطا در ایجاد QR Code:', error);
            qrCodeDiv.innerHTML = '<small class="text-muted">خطا در ایجاد QR Code</small>';
        }
    });
}

// تنظیم Modal ها
function setupModals() {
    // Modal رد درخواست
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAction('reject', new FormData(this));
        });
    }
    
    // Modal یادداشت
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAction('comment', new FormData(this));
        });
    }
}

// تایید درخواست
function approveRequest() {
    if (confirm('آیا از تایید این درخواست اطمینان دارید؟')) {
        const formData = new FormData();
        formData.append('_token', '<?= $csrf_token ?>');
        formData.append('id', '<?= $request['id'] ?>');
        
        submitAction('approve', formData);
    }
}

// نمایش Modal رد درخواست
function showRejectModal() {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

// نمایش Modal یادداشت
function showCommentModal() {
    const modal = new bootstrap.Modal(document.getElementById('commentModal'));
    modal.show();
}

// ارسال عملیات
async function submitAction(action, formData) {
    try {
        const response = await fetch(`?route=requests&action=${action}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // نمایش پیام موفقیت
            showAlert('success', result.message || 'عملیات با موفقیت انجام شد');
            
            // ریلود صفحه بعد از 1.5 ثانیه
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', result.message || 'خطا در انجام عملیات');
        }
        
        // بستن modal ها
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            bootstrap.Modal.getInstance(modal).hide();
        });
        
    } catch (error) {
        console.error('خطا در ارسال:', error);
        showAlert('error', 'خطا در ارتباط با سرور');
    }
}

// نمایش Alert
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const contentWrapper = document.querySelector('.content-wrapper');
    contentWrapper.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Scroll به بالا
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// چاپ درخواست
function printRequest() {
    window.print();
}
</script>

<style>
/* استایل‌های مخصوص صفحه نمایش */
.flat-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    box-shadow: var(--glass-shadow);
    backdrop-filter: var(--card-blur);
    transition: all 0.3s ease;
}

.flat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-header {
    border-bottom: 1px solid var(--glass-border);
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px 20px 0 0;
    padding: 1.25rem 1.5rem;
}

.card-body {
    padding: 1.5rem;
}

.info-value {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-top: 0.25rem;
    font-weight: 500;
}

.amount-display {
    background: linear-gradient(135deg, var(--success-color), var(--primary-color));
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
}

.amount-number {
    font-family: 'Courier New', monospace;
    font-size: 1.2rem;
}

.ltr-text {
    direction: ltr;
    text-align: left;
    font-family: 'Courier New', monospace;
}

/* Badge های وضعیت */
.badge.status-pending {
    background: var(--warning-color);
    color: #856404;
}

.badge.status-approved {
    background: var(--success-color);
    color: white;
}

.badge.status-rejected {
    background: var(--danger-color);
    color: white;
}

.badge.status-completed {
    background: var(--info-color);
    color: white;
}

/* Badge های اولویت */
.badge.priority-low {
    background: #17a2b8;
    color: white;
}

.badge.priority-normal {
    background: #6c757d;
    color: white;
}

.badge.priority-high {
    background: #fd7e14;
    color: white;
}

.badge.priority-urgent {
    background: #dc3545;
    color: white;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--glass-border);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -2.25rem;
    top: 0.25rem;
    width: 1.5rem;
    height: 1.5rem;
    background: var(--glass-bg);
    border: 2px solid var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    color: var(--primary-color);
}

.timeline-item-current .timeline-marker {
    background: var(--primary-color);
    color: white;
    animation: pulse 2s infinite;
}

.timeline-content {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    padding: 0.75rem;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

/* Tags */
.tags-display .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
}

/* QR Code Container */
#qrcode canvas {
    border-radius: 10px;
    border: 1px solid var(--glass-border);
}

/* Print Styles */
@media print {
    .btn, .modal, .page-header .d-flex > div:last-child {
        display: none !important;
    }
    
    .flat-card {
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .page-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.75rem;
        width: 1.25rem;
        height: 1.25rem;
    }
}

/* Animation */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
    }
}
</style>

<?php
// توابع کمکی
function getHistoryIcon($action) {
    switch ($action) {
        case 'created': return 'plus-circle';
        case 'approved': return 'check-circle';
        case 'rejected': return 'times-circle';
        case 'completed': return 'flag-checkered';
        case 'updated': return 'edit';
        case 'commented': return 'comment';
        default: return 'circle';
    }
}

function getHistoryActionLabel($action) {
    switch ($action) {
        case 'created': return 'ایجاد درخواست';
        case 'approved': return 'تایید درخواست';
        case 'rejected': return 'رد درخواست';
        case 'completed': return 'تکمیل درخواست';
        case 'updated': return 'ویرایش درخواست';
        case 'commented': return 'افزودن یادداشت';
        default: return 'تغییر وضعیت';
    }
}
?>
