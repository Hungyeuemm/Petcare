<!-- <?php
require_once __DIR__ . '/db_connection.php';
// lay danh sach bác sĩ
// function getAllstaffs(){
//     $conn = getDbConnection();
//     $sql = "SELECT idstaffs, staff_name, phone_staff, position FROM staffs";
//     $result = mysqli_query($conn, $sql);
//     $staffs = [];
//     if($result && mysqli_num_rows($result) > 0){
//         while($row = mysqli_fetch_assoc($result)){
//             $staffs[] = $row;
//         }
//     }
//     mysqli_close($conn);
//     return $staffs;
// }
// lay thong tin bác sĩ theo ID
// function getStaffById($id){
//     $conn = getDbConnection();
//     $sql = " SELECT idstaffs, staff_name, phone_staff, position FROM staffs WHERE idstaffs = ? LIMIT 1";
//     $stmt = mysqli_prepare($conn, $sql);
//     if($stmt){
//         mysqli_stmt_bind_param($stmt, "i", $id);
//         mysqli_stmt_execute($stmt);
//         $result = mysqli_stmt_get_result($stmt);
//         if(mysqli_num_rows($result) > 0){
//             $staffs = mysqli_fetch_assoc($result);
//             mysqli_stmt_close($stmt);
//             mysqli_close($conn);
//             return $staffs;
//         }
//         mysqli_stmt_close($stmt);
//     }
//     mysqli_close($conn);
//     return null;
// }
// cap nhan thong tin bác sĩ
// function updateStaff($id, $staff_name, $phone_staff, $position) {
//     $conn = getDbConnection();
//     $sql = "UPDATE staffs SET staff_name = ?, phone_staff = ?, position = ? WHERE idstaffs = ?";
//     $stmt = mysqli_prepare($conn, $sql);
//     if ($stmt) {
//         mysqli_stmt_bind_param($stmt, "sssi", $staff_name, $phone_staff, $position, $id);
//         $success = mysqli_stmt_execute($stmt);
//         mysqli_stmt_close($stmt);
//         mysqli_close($conn);
//         return $success;
//     }
//     mysqli_close($conn);
//     return false;
// }
// them bác sĩ mới
// function createStaff($staff_name, $phone_staff, $position) {
//     $conn = getDbConnection();
//     $sql = "INSERT INTO staffs (staff_name, phone_staff, position) VALUES (?, ?, ?)";
//     $stmt = mysqli_prepare($conn, $sql);
//     if ($stmt) {
//         mysqli_stmt_bind_param($stmt, "sss", $staff_name, $phone_staff, $position);
//         $success = mysqli_stmt_execute($stmt);
//         mysqli_stmt_close($stmt);
//         mysqli_close($conn);
//         return $success;
//     }
//     mysqli_close($conn);
//     return false;
// }
// xoa bác sĩ
// function deleteStaff($id) {
//     $conn = getDbConnection();
//     $sql = "DELETE FROM staffs WHERE idstaffs = ?";
//     $stmt = mysqli_prepare($conn, $sql);
//     if ($stmt) {
//         mysqli_stmt_bind_param($stmt, "i", $id);
//         $success = mysqli_stmt_execute($stmt);
//         mysqli_stmt_close($stmt);
//         mysqli_close($conn);
//         return $success;
//     }
//     mysqli_close($conn);
//     return false;
// }
// function deleteMultipleStaffs($ids) {
//     if (empty($ids) || !is_array($ids)) {
//         return 0;
//     }
    
//     $conn = getDbConnection();
//     $success_count = 0;
    
//     foreach ($ids as $id) {
//         $id = (int)$id; // Bảo mật: chuyển sang integer
//         $sql = "DELETE FROM staffs WHERE idstaffs = ?";
//         $stmt = mysqli_prepare($conn, $sql);
//         if ($stmt) {
//             mysqli_stmt_bind_param($stmt, "i", $id);
//             if (mysqli_stmt_execute($stmt)) {
//                 $success_count++;
//             }
//             mysqli_stmt_close($stmt);
//         }
//     }
    
//     mysqli_close($conn);
//     return $success_count;
// }

// dem tong so bác sĩ
// function countTotalStaffs() {
//     $conn = getDbConnection();
//     $sql = "SELECT COUNT(*) as total FROM staffs";
//     $result = mysqli_query($conn, $sql);
//     $total = 0;
//     if ($result) {
//         $row = mysqli_fetch_assoc($result);
//         $total = $row['total'];
//     }
//     mysqli_close($conn);
//     return $total;
// }

// // lay danh sach bác sĩ co phan trang
// function getStaffsWithPagination($limit, $offset) {
//     $conn = getDbConnection();
//     $sql = "SELECT idstaffs, staff_name, phone_staff, position FROM staffs LIMIT ? OFFSET ?";
//     $stmt = mysqli_prepare($conn, $sql);
//     $staffs = [];
    
//     if ($stmt) {
//         mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
//         mysqli_stmt_execute($stmt);
//         $result = mysqli_stmt_get_result($stmt);
        
//         if ($result && mysqli_num_rows($result) > 0) {
//             while ($row = mysqli_fetch_assoc($result)) {
//                 $staffs[] = $row;
//             }
//         }
//         mysqli_stmt_close($stmt);
//     }
    
//     mysqli_close($conn);
//     return $staffs;
// }
// ?>
<?php
require_once __DIR__ . '/db_connection.php';

// Lấy danh sách tất cả nhân viên + vai trò
function getAllstaffs() {
    $conn = getDbConnection();
    $sql = "SELECT s.idstaffs, s.staff_name, s.phone_staff, s.position, r.role_name, s.idrole
            FROM staffs s
            LEFT JOIN roles r ON s.idrole = r.idrole";
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

// Lấy thông tin nhân viên theo ID
function getStaffById($id) {
    $conn = getDbConnection();
    $sql = "SELECT s.idstaffs, s.staff_name, s.phone_staff, s.position, r.role_name, s.idrole
            FROM staffs s
            LEFT JOIN roles r ON s.idrole = r.idrole
            WHERE s.idstaffs = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $staff = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $staff;
    }
    mysqli_close($conn);
    return null;
}

// Cập nhật thông tin nhân viên
function updateStaff($id, $staff_name, $phone_staff, $position, $idrole) {
    $conn = getDbConnection();
    $sql = "UPDATE staffs SET staff_name = ?, phone_staff = ?, position = ?, idrole = ? WHERE idstaffs = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssii", $staff_name, $phone_staff, $position, $idrole, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Thêm nhân viên mới
function createStaff($staff_name, $phone_staff, $position, $idrole) {
    $conn = getDbConnection();
    $sql = "INSERT INTO staffs (staff_name, phone_staff, position, idrole) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $staff_name, $phone_staff, $position, $idrole);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Xóa nhân viên
function deleteStaff($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM staffs WHERE idstaffs = ?";
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

// Xóa nhiều nhân viên
function deleteMultipleStaffs($ids) {
    if (empty($ids) || !is_array($ids)) return 0;
    $conn = getDbConnection();
    $success_count = 0;
    foreach ($ids as $id) {
        $id = (int)$id;
        $sql = "DELETE FROM staffs WHERE idstaffs = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) $success_count++;
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
    return $success_count;
}

// Đếm tổng số nhân viên
function countTotalStaffs() {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as total FROM staffs";
    $result = mysqli_query($conn, $sql);
    $total = 0;
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];
    }
    mysqli_close($conn);
    return $total;
}

// Lấy danh sách nhân viên có phân trang
function getStaffsWithPagination($limit, $offset) {
    $conn = getDbConnection();
    $sql = "SELECT s.idstaffs, s.staff_name, s.phone_staff, s.position, r.role_name, s.idrole
            FROM staffs s
            LEFT JOIN roles r ON s.idrole = r.idrole
            LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    $staffs = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $staffs[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return $staffs;
}
?>

