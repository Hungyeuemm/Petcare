<?php
// /handle/datlich_admin_process.php
session_start();
require_once __DIR__ . '/../functions/datlich_admin_functions.php';
require_once __DIR__ . '/../functions/auth.php'; // nếu có isAdmin()

// Lấy action từ GET hoặc POST
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreateDatLich();
        break;
    case 'edit':
        handleEditDatLich();
        break;
    case 'delete':
        handleDeleteDatLich();
        break;
    case 'approve':
        handleApproveDatLich();
        break;
    default:
        header("Location: ../view/admin/datlich.php");
        exit();
}

/* --- Handlers --- */
function handleCreateDatLich() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_POST['customer_id'];
        $pet_id = $_POST['pet_id'];
        $staff_id = $_POST['staff_id'];
        $service_id = $_POST['service_id'];
        $date = $_POST['appointment_date'];
        $time = $_POST['appointment_time'];
        $notes = $_POST['notes'];

        // 🧩 Kiểm tra bác sĩ có làm dịch vụ này không
        if (!staffCanDoService($staff_id, $service_id)) {
            header("Location: /Baitaplon/view/admin/datlich.php?error=Bác sĩ không thực hiện được dịch vụ này");
            exit();
        }

        // 🧩 Kiểm tra trùng lịch
        if (checkConflict($staff_id, $date, $time)) {
            header("Location: /Baitaplon/view/admin/datlich.php?error=Trùng lịch với bác sĩ");
            exit();
        }

        if (createDatLich($customer_id, $pet_id, $staff_id, $service_id, $date, $time, $notes)) {
            header("Location: /Baitaplon/view/admin/datlich.php?success=Thêm thành công");
        } else {
            header("Location: /Baitaplon/view/admin/datlich.php?error=Thêm thất bại");
        }
        exit();
    }
}


function handleEditDatLich() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /Baitaplon/view/admin/datlich.php?error=Phương thức không hợp lệ");
        exit();
    }
    $id = intval($_POST['idappointments']);
    $customer_id = intval($_POST['customer_id']);
    $pet_id = intval($_POST['pet_id']);
    $staff_id = intval($_POST['staff_id']);
    $service_id = intval($_POST['service_id']);
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $notes = $_POST['notes'] ?? '';
    $status = $_POST['status'] ?? 'pending';

    // Kiểm tra trùng ca (loại trừ chính lịch đang edit)
    if (checkConflict($staff_id, $date, $time, $id)) {
        header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Trùng lịch với bác sĩ đã chọn");
        exit();
    }

    $ok = updateDatLich($id, $customer_id, $pet_id, $staff_id, $service_id, $date, $time, $notes, $status);
    if ($ok) {
        header("Location: /Baitaplon/view/admin/datlich.php?success=Cập nhật thành công");
    } else {
        header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Cập nhật thất bại");
    }
    exit();
}

function handleDeleteDatLich() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: /Baitaplon/view/admin/datlich.php?error=Phương thức không hợp lệ");
        exit();
    }
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        header("Location: /Baitaplon/view/admin/datlich.php?error=Thiếu ID");
        exit();
    }
    $ok = deleteDatLich($id);
    if ($ok) header("Location: /Baitaplon/view/admin/datlich.php?success=Xóa thành công");
    else header("Location: /Baitaplon/view/admin/datlich.php?error=Xóa thất bại");
    exit();
}

// function handleApproveDatLich() {
//     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//         header("Location: /Baitaplon/view/admin/datlich.php?error=Phương thức không hợp lệ");
//         exit();
//     }
//     $id = intval($_POST['idappointments']);
//     $status = $_POST['status'] ?? 'pending'; // 'approved' hoặc 'rejected'
//     // nếu duyệt approve: có thể kiểm tra trùng nữa nếu cần
//     if ($status === 'approved') {
//         $row = getDatLichById($id);
//         if ($row && checkConflict($row['staff_id'], $row['appointment_date'], $row['appointment_time'], $id)) {
//             header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Không thể duyệt, xung đột lịch");
//             exit();
//         }
//     }
//     $ok = updateDatLichStatus($id, $status);
//     if ($ok) header("Location: /Baitaplon/view/admin/datlich.php?success=Cập nhật trạng thái thành công");
//     else header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Cập nhật trạng thái thất bại");
//     exit();
// }
require_once __DIR__ . '/../functions/invoice_functions.php';

function handleApproveDatLich() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /Baitaplon/view/admin/datlich.php?error=Phương thức không hợp lệ");
        exit();
    }
    $id = intval($_POST['idappointments']);
    // Lấy row hiện tại
    $row = getDatLichById($id);
    if (!$row) {
        header("Location: /Baitaplon/view/admin/datlich.php?error=Không tìm thấy lịch");
        exit();
    }

    // kiểm tra trùng lịch trước khi approve
    if (checkConflict($row['staff_id'], $row['appointment_date'], $row['appointment_time'], $id)) {
        header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Không thể duyệt, xung đột lịch");
        exit();
    }

    // Cập nhật trạng thái lịch thành approved
    $ok = updateDatLichStatus($id, 'approved');
    if (!$ok) {
        header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Cập nhật trạng thái thất bại");
        exit();
    }

    // Tạo hoá đơn cho lịch này
    $invoice_id = createInvoiceForAppointment($id);
    if ($invoice_id === false) {
        // rollback: nếu cần, có thể set lại status = pending
        updateDatLichStatus($id, 'pending');
        header("Location: /Baitaplon/view/admin/datlich.php?id={$id}&error=Tạo hóa đơn thất bại");
        exit();
    }

    header("Location: /Baitaplon/view/admin/datlich.php?success=Đã duyệt &invoice={$invoice_id}");
    exit();
}


