<?php
/**
 * نام فایل: view.php
 * مسیر فایل: /app/views/requests/view.php
 * توضیح: صفحه نمایش جزئیات درخواست حواله
 * تاریخ ایجاد: 1404/03/31
 * نویسنده: توسعه‌دهنده سامانت
 */

// تنظیم متغیرهای صفحه
$page_title = $title ?? 'جزئیات درخواست';
$page_description = 'مشاهده جزئیات و وضعیت درخواست حواله';
$active_menu = 'requests';

// دریافت پیام‌های flash
$flash = $_SESSION['flash'] ?? null;
if ($flash) {
    unset($_SESSION['flash']);
}

// اطلاعات درخواست
$request = $request ?? [];
$current_user = $user ?? [];
$can_edit = $can_edit ?? false;
$can_approve = $can_approve ?? false;
?>

<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    جزئیات درخواست
                </h1>
                <p class="page-subtitle text-muted">
                    کد مرجع: <?= htmlspecialchars($request['reference_number'] ?? '') ?>
                </p>
            </div>
            <div>
                <a href="?route=requests" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    بازگشت به لیست
                </a>
                <?php if ($can_edit): ?>
                <a href="?route=requests&action=edit&id=<?= $request['id'] ?>" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>
                    ویرایش
                </a>
                <?php endif; ?>
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

    <div class="row">
        <!-- اطلاعات اصلی درخواست -->
        <div class="col-lg-8">
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        اطلاعات درخواست
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">عنوان درخواست</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($request['title'] ?? '') ?></p>
                        </div>

                        <?php if (!empty($request['description'])): ?>
                        <div class="col-12">
                            <label class="form-label fw-bold">توضیحات</label>
                            <p class="form-control-plaintext"><?= nl2br(htmlspecialchars($request['description'])) ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">مبلغ</label>
                            <p class="form-control-plaintext">
                                <?= $request['amount'] ? number_format($request['amount']) . ' ریال' : 'مشخص نشده' ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">اولویت</label>
                            <p class="form-control-plaintext">
                                <?php
                                $priority_classes = [
                                    'low' => 'success',
                                    'normal' => 'secondary', 
                                    'high' => 'warning',
                                    'urgent' => 'danger'
                                ];
                                $priority_class = $priority_classes[$request['priority']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $priority_class ?>">
                                    <?= $request['priority_label'] ?? $request['priority'] ?>
                                </span>
                            </p>
                        </div>

                        <?php if (!empty($request['category'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">دسته‌بندی</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($request['category_label'] ?? $request['category']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($request['due_date'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاریخ سررسید</label>
                            <p class="form-control-plaintext"><?= $request['due_date_jalali'] ?? $request['due_date'] ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ($request['is_urgent']): ?>
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                این درخواست فوری است
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- اطلاعات حساب -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-university me-2"></i>
                        اطلاعات حساب مقصد
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if (!empty($request['account_holder'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">صاحب حساب</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($request['account_holder']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($request['bank_name'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">بانک</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($request['bank_name']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($request['account_number'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">شماره حساب</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($request['account_number']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($request['card_number'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">شماره کارت</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($request['card_number']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($request['iban'])): ?>
                        <div class="col-12">
                            <label class="form-label fw-bold">شماره شبا</label>
                            <p class="form-control-plaintext">IR<?= htmlspecialchars($request['iban']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- sidebar اطلاعات جانبی -->
        <div class="col-lg-4">
            <!-- وضعیت درخواست -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        وضعیت درخواست
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $status_classes = [
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'primary',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'secondary'
                    ];
                    $status_class = $status_classes[$request['status']] ?? 'secondary';
                    ?>
                    <div class="status-badge mb-3">
                        <span class="badge bg-<?= $status_class ?> fs-6 p-3">
                            <?= $request['status_label'] ?? $request['status'] ?>
                        </span>
                    </div>

                    <!-- دکمه‌های عملیات -->
                    <?php if ($can_approve && $request['status'] === 'pending'): ?>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="approveRequest(<?= $request['id'] ?>)">
                            <i class="fas fa-check me-2"></i>
                            تایید درخواست
                        </button>
                        <button class="btn btn-danger" onclick="rejectRequest(<?= $request['id'] ?>)">
                            <i class="fas fa-times me-2"></i>
                            رد درخواست
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if ($request['status'] === 'approved'): ?>
                    <div class="d-grid">
                        <button class="btn btn-primary" onclick="completeRequest(<?= $request['id'] ?>)">
                            <i class="fas fa-check-double me-2"></i>
                            تکمیل درخواست
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- اطلاعات درخواست‌کننده -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        درخواست‌کننده
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <i class="fas fa-user-circle fa-2x text-muted"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-bold"><?= htmlspecialchars($request['requester_name'] ?? 'نامشخص') ?></p>
                            <small class="text-muted">درخواست‌کننده</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تواریخ مهم -->
            <div class="flat-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        تواریخ مهم
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="fas fa-plus timeline-icon text-primary"></i>
                            <div class="timeline-content">
                                <h6>ایجاد درخواست</h6>
                                <small class="text-muted"><?= $request['created_at_jalali'] ?? $request['created_at'] ?></small>
                            </div>
                        </div>

                        <?php if (!empty($request['updated_at']) && $request['updated_at'] !== $request['created_at']): ?>
                        <div class="timeline-item">
                            <i class="fas fa-edit timeline-icon text-info"></i>
                            <div class="timeline-content">
                                <h6>آخرین بروزرسانی</h6>
                                <small class="text-muted"><?= $request['updated_at_jalali'] ?? $request['updated_at'] ?></small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($request['due_date'])): ?>
                        <div class="timeline-item">
                            <i class="fas fa-clock timeline-icon text-warning"></i>
                            <div class="timeline-content">
                                <h6>تاریخ سررسید</h6>
                                <small class="text-muted"><?= $request['due_date_jalali'] ?? $request['due_date'] ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تایید درخواست -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تایید درخواست</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST" action="?route=requests&action=approve">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">توضیحات (اختیاری)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3"
                                  placeholder="توضیحات تایید..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-success">تایید درخواست</button>
                </div>
            </form>
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
            <form id="rejectForm" method="POST" action="?route=requests&action=reject">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label required">دلیل رد</label>
                        <textarea class="form-control" id="reject_reason" name="reason" rows="3"
                                  placeholder="لطفاً دلیل رد درخواست را توضیح دهید..." required></textarea>
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

<!-- Modal تکمیل درخواست -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تکمیل درخواست</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeForm" method="POST" action="?route=requests&action=complete">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                    <div class="mb-3">
                        <label for="complete_notes" class="form-label">توضیحات (اختیاری)</label>
                        <textarea class="form-control" id="complete_notes" name="notes" rows="3"
                                  placeholder="توضیحات تکمیل..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">تکمیل درخواست</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveRequest(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectRequest(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function completeRequest(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('completeModal'));
    modal.show();
}
</script>

<style>
/* استایل‌های مخصوص صفحه جزئیات */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline-item {
    position: relative;
    padding-right: 3rem;
    margin-bottom: 1.5rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-icon {
    position: absolute;
    right: 0;
    top: 0;
    width: 2rem;
    height: 2rem;
    background: var(--glass-bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.status-badge .badge {
    border-radius: 15px;
    font-weight: 500;
}

.avatar i {
    color: var(--text-muted);
}

.form-control-plaintext {
    padding: 0.375rem 0;
    margin-bottom: 0;
    line-height: 1.5;
    color: var(--text-primary);
    background-color: transparent;
    border: none;
    border-bottom: 1px solid var(--glass-border);
}

.card-header {
    background: var(--glass-bg);
    border-bottom: 1px solid var(--glass-border);
}
</style>
