<?php
session_start();
require_once __DIR__ . '/../functions/appointments_admin_functions.php';
require_once __DIR__ . '/../functions/auth.php';


// Kiểm tra đăng nhập và quyền admin
if (!isAdmin()) {
    $_SESSION['error'] = "Bạn không có quyền truy cập!";
    header("Location: ../index.php");
    exit();
}

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateAppointment();
        break;
    case 'edit':
        handleEditAppointment();
        break;
    case 'delete':
        handleDeleteAppointment();
        break;
    case 'delete_multiple':
        handleDeleteMultipleAppointments();
        break;
    case 'approve':
        handleApproveAppointment();
        break;
    case 'reject':
        handleRejectAppointment();
        break;
    case 'complete':
        handleCompleteAppointment();
        break;
    case 'check_availability':
        handleCheckAvailability();
        break;
    case 'get_pets':
        handleGetPetsByCustomer();
        break;
    default:
        header("Location: ../view/admin/appointments_admin.php");
        exit();
}

// Hàm wrapper để gọi từ view
function handleGetAllAppointments() {
    return getAllAppointments();
}

function handleGetAppointmentsWithPagination($limit, $offset, $status_filter = null) {
    return getAppointmentsWithPagination($limit, $offset, $status_filter);
}

function handleCountTotalAppointments($status_filter = null) {
    return countTotalAppointments($status_filter);
}

function handleCountAppointmentsByStatus() {
    return countAppointmentsByStatus();
}

// Tạo lịch hẹn mới
function handleCreateAppointment() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/appointments_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Validate dữ liệu
    $required_fields = ['customer_id', 'pet_id', 'staff_id', 'service_id', 'appointment_date', 'appointment_time'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            header("Location: ../view/admin/appointments_admin/create_appointments_admin.php?error=Vui lòng điền đầy đủ thông tin");
            exit();
        }
    }
    
    $customer_id = (int)$_POST['customer_id'];
    $pet_id = (int)$_POST['pet_id'];
    $staff_id = (int)$_POST['staff_id'];
    $service_id = (int)$_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    // Validate ngày
    $today = date('Y-m-d');
    if ($appointment_date < $today) {
        header("Location: ../view/admin/appointments_admin/create_appointments_admin.php?error=Không thể đặt lịch trong quá khứ");
        exit();
    }
    
    $result = createAppointment($customer_id, $pet_id, $staff_id, $service_id, $appointment_date, $appointment_time, $notes);
    
    if ($result['success']) {
        header("Location: ../view/admin/appointments_admin.php?success=" . urlencode($result['message']));
    } else {
        header("Location: ../view/admin/appointments_admin/create_appointments_admin.php?error=" . urlencode($result['message']));
    }
    exit();
}

// Chỉnh sửa lịch hẹn
function handleEditAppointment() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/appointments_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    // Validate dữ liệu
    $required_fields = ['idappointments', 'customer_id', 'pet_id', 'staff_id', 'service_id', 'appointment_date', 'appointment_time', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $id = isset($_POST['idappointments']) ? $_POST['idappointments'] : '';
            header("Location: ../view/admin/appointments_admin/edit_appointments_admin.php?id={$id}&error=Vui lòng điền đầy đủ thông tin");
            exit();
        }
    }
    
    $id = (int)$_POST['idappointments'];
    $customer_id = (int)$_POST['customer_id'];
    $pet_id = (int)$_POST['pet_id'];
    $staff_id = (int)$_POST['staff_id'];
    $service_id = (int)$_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $status = $_POST['status'];
    
    $result = updateAppointment($id, $customer_id, $pet_id, $staff_id, $service_id, $appointment_date, $appointment_time, $notes, $status);
    
    if ($result['success']) {
        header("Location: ../view/admin/appointments_admin.php?success=" . urlencode($result['message']));
    } else {
        header("Location: ../view/admin/appointments_admin/edit_appointments_admin.php?id={$id}&error=" . urlencode($result['message']));
    }
    exit();
}

// Xóa lịch hẹn
function handleDeleteAppointment() {
    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/appointments_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = (int)$_GET['id'];
    $result = deleteAppointment($id);
    
    if ($result) {
        header("Location: ../view/admin/appointments_admin.php?success=Xóa lịch hẹn thành công");
    } else {
        header("Location: ../view/admin/appointments_admin.php?error=Xóa lịch hẹn thất bại");
    }
    exit();
}

// Xóa nhiều lịch hẹn
function handleDeleteMultipleAppointments() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/appointments_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['selected_ids']) || empty($_POST['selected_ids'])) {
        header("Location: ../view/admin/appointments_admin.php?error=Chưa chọn lịch hẹn nào để xóa");
        exit();
    }
    
    $selected_ids = $_POST['selected_ids'];
    $success_count = deleteMultipleAppointments($selected_ids);
    
    if ($success_count > 0) {
        header("Location: ../view/admin/appointments_admin.php?success=Đã xóa {$success_count} lịch hẹn thành công");
    } else {
        header("Location: ../view/admin/appointments_admin.php?error=Xóa lịch hẹn thất bại");
    }
    exit();
}

// Duyệt lịch hẹn
function handleApproveAppointment() {
    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/appointments_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = (int)$_GET['id'];
    $result = updateAppointmentStatus($id, 'confirmed');
    
    if ($result) {
        header("Location: ../view/admin/appointments_admin.php?success=Đã duyệt lịch hẹn thành công");
    } else {
        header("Location: ../view/admin/appointments_admin.php?error=Duyệt lịch hẹn thất bại");
    }
    exit();
}

// Từ chối lịch hẹn
function handleRejectAppointment() {
    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/appointments_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = (int)$_GET['id'];
    $result = updateAppointmentStatus($id, 'cancelled');
    
    if ($result) {
        header("Location: ../view/admin/appointments_admin.php?success=Đã từ chối lịch hẹn");
    } else {
        header("Location: ../view/admin/appointments_admin.php?error=Từ chối lịch hẹn thất bại");
    }
    exit();
}

// Hoàn thành lịch hẹn
function handleCompleteAppointment() {
    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/appointments_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = (int)$_GET['id'];
    $result = updateAppointmentStatus($id, 'completed');
    
    if ($result) {
        header("Location: ../view/admin/appointments_admin.php?success=Đã đánh dấu hoàn thành");
    } else {
        header("Location: ../view/admin/appointments_admin.php?error=Cập nhật thất bại");
    }
    exit();
}

// Kiểm tra lịch trống (AJAX)
function handleCheckAvailability() {
    header('Content-Type: application/json');
    
    if (!isset($_POST['staff_id']) || !isset($_POST['appointment_date']) || !isset($_POST['appointment_time'])) {
        echo json_encode(['available' => false, 'message' => 'Thiếu thông tin']);
        exit();
    }
    
    $staff_id = (int)$_POST['staff_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $exclude_id = isset($_POST['exclude_id']) ? (int)$_POST['exclude_id'] : null;
    
    $available = isStaffAvailable($staff_id, $appointment_date, $appointment_time, $exclude_id);
    
    if ($available) {
        echo json_encode(['available' => true, 'message' => 'Bác sĩ còn trống lịch']);
    } else {
        echo json_encode(['available' => false, 'message' => 'Bác sĩ đã có lịch hẹn trong thời gian này']);
    }
    exit();
}

// Lấy danh sách thú cưng theo khách hàng (AJAX)
function handleGetPetsByCustomer() {
    header('Content-Type: application/json');
    
    if (!isset($_POST['customer_id'])) {
        echo json_encode(['success' => false, 'pets' => []]);
        exit();
    }
    
    $customer_id = (int)$_POST['customer_id'];
    $pets = getPetsByCustomer($customer_id);
    
    echo json_encode(['success' => true, 'pets' => $pets]);
    exit();
}
?>