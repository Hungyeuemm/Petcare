<?php
// /handle/duyet_lich_process.php
session_start();
require_once __DIR__ . '/../functions/datlich_admin_functions.php';
require_once __DIR__ . '/../functions/auth.php';

if (!isAdmin()) {
    $_SESSION['error'] = "Không có quyền truy cập!";
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/admin/status_admin.php?error=Phương thức không hợp lệ");
    exit();
}

$id = intval($_POST['idappointments'] ?? 0);
$status = $_POST['status'] ?? 'pending';
if (!$id) {
    header("Location: ../view/admin/status_admin.php?error=Thiếu ID");
    exit();
}

// Nếu duyệt thì kiểm tra trùng lịch bác sĩ
if ($status === 'approved') {
    $row = getDatLichById($id);
    if ($row && checkConflict($row['staff_id'], $row['appointment_date'], $row['appointment_time'], $id)) {
        header("Location: ../view/admin/status_admin.php?error=Lịch trùng bác sĩ khác");
        exit();
    }
}

// $ok = updateDatLichStatus($id, $status);
// if ($ok)
//     header("Location: ../view/admin/status_admin.php?success=Cập nhật thành công");
// else
//     header("Location: ../view/admin/status_admin.php?error=Cập nhật thất bại");
// exit();
$ok = updateDatLichStatus($id, $status);

if ($ok) {

    // Nếu duyệt lịch -> tạo hoá đơn
    if ($status === 'approved') {
        require_once __DIR__ . '/../functions/invoice_functions.php';
        createInvoiceForAppointment($id);
    }

    header("Location: ../view/admin/status_admin.php?success=Cập nhật thành công");
} 
else {
    header("Location: ../view/admin/status_admin.php?error=Cập nhật thất bại");
}
exit();

