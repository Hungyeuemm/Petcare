<?php
require_once __DIR__ . '/db_connection.php';

// Lấy danh sách tất cả lịch hẹn
function getAllAppointments() {
    $conn = getDbConnection();
    $sql = "SELECT a.*, 
            c.name as customer_name, 
            p.name_pet as pet_name,
            s.staff_name,
            sv.name_service
            FROM appointments a
            LEFT JOIN customers c ON a.customer_id = c.idcustomers
            LEFT JOIN pets p ON a.pet_id = p.idpets
            LEFT JOIN staffs s ON a.staff_id = s.idstaffs
            LEFT JOIN services sv ON a.service_id = sv.idservices
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $result = mysqli_query($conn, $sql);
    $appointments = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $appointments;
}

// Lấy lịch hẹn với phân trang
function getAppointmentsWithPagination($limit, $offset, $status_filter = null) {
    $conn = getDbConnection();
    
    $sql = "SELECT a.*, 
            c.name as customer_name,
            c.phone_number as customer_phone,
            p.name_pet as pet_name,
            s.staff_name,
            sv.name_service
            FROM appointments a
            LEFT JOIN customers c ON a.customer_id = c.idcustomers
            LEFT JOIN pets p ON a.pet_id = p.idpets
            LEFT JOIN staffs s ON a.staff_id = s.idstaffs
            LEFT JOIN services sv ON a.service_id = sv.idservices";
    
    if ($status_filter && $status_filter !== 'all') {
        $sql .= " WHERE a.status = ?";
    }
    
    $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    $appointments = [];
    
    if ($stmt) {
        if ($status_filter && $status_filter !== 'all') {
            mysqli_stmt_bind_param($stmt, "sii", $status_filter, $limit, $offset);
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $appointments[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $appointments;
}

// Đếm tổng số lịch hẹn
function countTotalAppointments($status_filter = null) {
    $conn = getDbConnection();
    
    if ($status_filter && $status_filter !== 'all') {
        $sql = "SELECT COUNT(*) as total FROM appointments WHERE status = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $status_filter);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];
        mysqli_stmt_close($stmt);
    } else {
        $sql = "SELECT COUNT(*) as total FROM appointments";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];
    }
    
    mysqli_close($conn);
    return $total;
}

// Đếm số lượng theo trạng thái
function countAppointmentsByStatus() {
    $conn = getDbConnection();
    $sql = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
    $result = mysqli_query($conn, $sql);
    
    $counts = [
        'pending' => 0,
        'confirmed' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $counts[$row['status']] = $row['count'];
        }
    }
    
    mysqli_close($conn);
    return $counts;
}

// Lấy thông tin lịch hẹn theo ID
function getAppointmentById($id) {
    $conn = getDbConnection();
    $sql = "SELECT a.*, 
            c.name as customer_name,
            p.name_pet as pet_name,
            s.staff_name,
            sv.name_service
            FROM appointments a
            LEFT JOIN customers c ON a.customer_id = c.idcustomers
            LEFT JOIN pets p ON a.pet_id = p.idpets
            LEFT JOIN staffs s ON a.staff_id = s.idstaffs
            LEFT JOIN services sv ON a.service_id = sv.idservices
            WHERE a.idappointments = ? LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $appointment = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $appointment;
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

// Kiểm tra bác sĩ có trống lịch không
function isStaffAvailable($staff_id, $appointment_date, $appointment_time, $exclude_appointment_id = null) {
    $conn = getDbConnection();
    
    $sql = "SELECT COUNT(*) as count FROM appointments 
            WHERE staff_id = ? 
            AND appointment_date = ? 
            AND appointment_time = ? 
            AND status IN ('pending', 'confirmed')";
    
    if ($exclude_appointment_id) {
        $sql .= " AND idappointments != ?";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        if ($exclude_appointment_id) {
            mysqli_stmt_bind_param($stmt, "issi", $staff_id, $appointment_date, $appointment_time, $exclude_appointment_id);
        } else {
            mysqli_stmt_bind_param($stmt, "iss", $staff_id, $appointment_date, $appointment_time);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        return $row['count'] == 0;
    }
    
    mysqli_close($conn);
    return false;
}

// Tạo lịch hẹn mới
function createAppointment($customer_id, $pet_id, $staff_id, $service_id, $appointment_date, $appointment_time, $notes = '') {
    // Kiểm tra bác sĩ có trống lịch không
    if (!isStaffAvailable($staff_id, $appointment_date, $appointment_time)) {
        return ['success' => false, 'message' => 'Bác sĩ đã có lịch hẹn trong thời gian này'];
    }
    
    $conn = getDbConnection();
    $status = 'pending'; // Mặc định là chờ duyệt
    
    $sql = "INSERT INTO appointments (customer_id, pet_id, staff_id, service_id, appointment_date, appointment_time, notes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiiissss", $customer_id, $pet_id, $staff_id, $service_id, $appointment_date, $appointment_time, $notes, $status);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        if ($success) {
            return ['success' => true, 'message' => 'Đặt lịch hẹn thành công, đang chờ duyệt'];
        }
    }
    
    mysqli_close($conn);
    return ['success' => false, 'message' => 'Đặt lịch hẹn thất bại'];
}

// Cập nhật lịch hẹn
function updateAppointment($id, $customer_id, $pet_id, $staff_id, $service_id, $appointment_date, $appointment_time, $notes, $status) {
    // Kiểm tra bác sĩ có trống lịch không (trừ lịch hẹn hiện tại)
    if (!isStaffAvailable($staff_id, $appointment_date, $appointment_time, $id)) {
        return ['success' => false, 'message' => 'Bác sĩ đã có lịch hẹn trong thời gian này'];
    }
    
    $conn = getDbConnection();
    $sql = "UPDATE appointments 
            SET customer_id = ?, pet_id = ?, staff_id = ?, service_id = ?, 
                appointment_date = ?, appointment_time = ?, notes = ?, status = ?
            WHERE idappointments = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiiissssi", $customer_id, $pet_id, $staff_id, $service_id, 
                              $appointment_date, $appointment_time, $notes, $status, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        if ($success) {
            return ['success' => true, 'message' => 'Cập nhật lịch hẹn thành công'];
        }
    }
    
    mysqli_close($conn);
    return ['success' => false, 'message' => 'Cập nhật lịch hẹn thất bại'];
}

// Cập nhật trạng thái lịch hẹn
function updateAppointmentStatus($id, $status) {
    $conn = getDbConnection();
    $sql = "UPDATE appointments SET status = ? WHERE idappointments = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

// Xóa lịch hẹn
function deleteAppointment($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM appointments WHERE idappointments = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

// Xóa nhiều lịch hẹn
function deleteMultipleAppointments($ids) {
    if (empty($ids) || !is_array($ids)) {
        return 0;
    }
    
    $conn = getDbConnection();
    $success_count = 0;
    
    foreach ($ids as $id) {
        $id = (int)$id;
        $sql = "DELETE FROM appointments WHERE idappointments = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
    return $success_count;
}

// Lấy danh sách khách hàng
function getAllCustomers() {
    $conn = getDbConnection();
    $sql = "SELECT idcustomers, name, phone_number FROM customers ORDER BY name";
    $result = mysqli_query($conn, $sql);
    $customers = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $customers;
}

// Lấy danh sách thú cưng theo khách hàng
function getPetsByCustomer($customer_id) {
    $conn = getDbConnection();
    $sql = "SELECT idpets, name_pet FROM pets WHERE customer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $pets = [];
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pets[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $pets;
}

// Lấy danh sách dịch vụ
function getAllServices() {
    $conn = getDbConnection();
    $sql = "SELECT idservices, name_service, price FROM services ORDER BY name_service";
    $result = mysqli_query($conn, $sql);
    $services = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $services;
}

// Lấy danh sách bác sĩ
function getAllStaffsForAppointment() {
    $conn = getDbConnection();
    $sql = "SELECT idstaffs, staff_name, position FROM staffs ORDER BY staff_name";
    $result = mysqli_query($conn, $sql);
    $staffs = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $staffs[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $staffs;
}

// Lấy lịch bận của bác sĩ trong ngày
function getStaffScheduleByDate($staff_id, $date) {
    $conn = getDbConnection();
    $sql = "SELECT appointment_time FROM appointments 
            WHERE staff_id = ? 
            AND appointment_date = ? 
            AND status IN ('pending', 'confirmed')
            ORDER BY appointment_time";
    
    $stmt = mysqli_prepare($conn, $sql);
    $schedule = [];
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $staff_id, $date);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $schedule[] = $row['appointment_time'];
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $schedule;
}
?>