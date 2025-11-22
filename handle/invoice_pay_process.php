<?php
session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/invoice_functions.php';

if (!isAdmin()) {
    $_SESSION['error'] = "Không có quyền";
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/admin/invoices_list.php?error=Phương thức không hợp lệ");
    exit();
}

$invoice_id = intval($_POST['invoice_id'] ?? 0);
$method     = trim($_POST['method'] ?? 'Tiền mặt');
$note       = trim($_POST['note'] ?? '');

if (!$invoice_id) {
    header("Location: ../view/admin/invoices_list.php?error=Thiếu ID");
    exit();
}

// --- Lấy đúng staff_id từ appointment ---
$staff_id = getStaffIdFromInvoice($invoice_id);

if (!$staff_id) {
    header("Location: ../view/admin/invoices_list.php?error=Không tìm thấy bác sĩ khám");
    exit();
}

$ok = markInvoicePaidAndArchive($invoice_id, $staff_id, $method, $note);

if ($ok) {
    header("Location: ../view/admin/invoice_admin/payment_history.php?success=paid");
} else {
    header("Location: ../view/admin/invoices_list.php?error=Thanh toán thất bại");
}
exit();
