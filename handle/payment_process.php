<?php
// /handle/payment_process.php
session_start();
require_once __DIR__ . '/../functions/db_connection.php';
require_once __DIR__ . '/../functions/auth.php';

header('Content-Type: application/json; charset=utf-8');
if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Không có quyền']);
    exit;
}

$invoice_id = intval($_POST['invoice_id'] ?? 0);
if (!$invoice_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID hóa đơn']);
    exit;
}

$conn = getDbConnection();

// Lấy appointment_id từ invoice
$stmt = mysqli_prepare($conn, "SELECT appointment_id FROM invoices WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $invoice_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$row) {
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy hóa đơn']);
    exit;
}

$appointment_id = intval($row['appointment_id']);

// Transaction: cập nhật hóa đơn -> xóa appointment
mysqli_begin_transaction($conn);
try {
    // cập nhật invoice
    $stmt = mysqli_prepare($conn, "UPDATE invoices SET is_paid = 1, payment_status = 'Đã thanh toán', paid_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $invoice_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // xóa appointment (theo yêu cầu: khi thanh toán thì xóa appointment)
    $stmt = mysqli_prepare($conn, "DELETE FROM appointments WHERE idappointments = ?");
    mysqli_stmt_bind_param($stmt, "i", $appointment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);
    mysqli_close($conn);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
}
