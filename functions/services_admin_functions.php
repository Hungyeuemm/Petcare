<?php
require_once __DIR__ . '/db_connection.php';
// lay danh sach dich vu
function getAllServices()
{
    $conn = getDbConnection();
    $sql = "SELECT idservices, services_name, price_services FROM services";
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
// lay thong tin dich vu theo ID
function getServiceById($id)
{
    $conn = getDbConnection();
    $sql = "SELECT idservices, services_name, price_services FROM services WHERE idservices = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $service = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $service;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}
// cap nhan thong tin dich vu
function updateService($id, $services_name, $price_services)
{
    $conn = getDbConnection();
    $sql = "UPDATE services SET services_name = ?, price_services = ? WHERE idservices = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sdi", $services_name, $price_services, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}
// tạo dich vu moi
function createService($services_name, $price_services)
{
    $conn = getDbConnection();
    $sql = "INSERT INTO services (services_name, price_services) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sd", $services_name, $price_services);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}
// xoa dich vu
function deleteService($id)
{
    $conn = getDbConnection();
    $sql = "DELETE FROM services WHERE idservices = ?";
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

// Đếm tổng số dịch vụ
function countAllServices()
{
    $conn = getDbConnection();
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM services");
    $data = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $data['total'] ?? 0;
}

// Lấy dịch vụ theo trang
function getServicesPaginated($limit, $offset)
{
    $conn = getDbConnection();
    $stmt = mysqli_prepare($conn, "SELECT idservices, services_name, price_services FROM services LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $services = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $services;
}
// Lấy tất cả role (để chọn khi thêm/sửa)
function getAllRoles()
{
    $conn = getDbConnection();
    $sql = "SELECT idrole, role_name FROM roles";
    $result = mysqli_query($conn, $sql);
    $roles = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }
    }
    mysqli_close($conn);
    return $roles;
}

// Lấy role của 1 dịch vụ
function getRolesByService($idservices)
{
    $conn = getDbConnection();
    $sql = "SELECT idroles FROM service_role WHERE idservices = ?"; // ✅ đúng tên cột

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("❌ Lỗi prepare getRolesByService: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $idservices);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $roles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = $row['idroles'];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $roles;
}


// Cập nhật role cho dịch vụ
function updateServiceRoles($idservices, $roleIds)
{
    $conn = getDbConnection();
    mysqli_begin_transaction($conn);
    try {
        // Xóa cũ
        $stmtDel = mysqli_prepare($conn, "DELETE FROM service_role WHERE idservices = ?");
        if (!$stmtDel) {
            throw new Exception("Lỗi prepare DELETE: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmtDel, "i", $idservices);
        mysqli_stmt_execute($stmtDel);
        mysqli_stmt_close($stmtDel);

        // Thêm mới
        if (!empty($roleIds)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO service_role (idservices, idroles) VALUES (?, ?)"); // ✅ đúng tên cột
            if (!$stmt) {
                throw new Exception("Lỗi prepare INSERT: " . mysqli_error($conn));
            }
            foreach ($roleIds as $rid) {
                mysqli_stmt_bind_param($stmt, "ii", $idservices, $rid);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_commit($conn);
        mysqli_close($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        die("❌ Lỗi updateServiceRoles: " . $e->getMessage());
    }
}

// Khi tạo dịch vụ mới
function createServiceWithRoles($name, $price, $roles)
{
    $conn = getDbConnection();

    // Kiểm tra kết nối
    if (!$conn) {
        die("❌ Lỗi kết nối DB: " . mysqli_connect_error());
    }

    // 1️⃣ Thêm dịch vụ mới
    $sql = "INSERT INTO services (services_name, price_services) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("❌ Lỗi prepare dịch vụ: " . mysqli_error($conn));
    }

    // ép kiểu giá về float để tránh lỗi
    $price = floatval($price);

    mysqli_stmt_bind_param($stmt, "sd", $name, $price);

    if (!mysqli_stmt_execute($stmt)) {
        die("❌ Lỗi thực thi INSERT dịch vụ: " . mysqli_stmt_error($stmt));
    }

    $serviceId = mysqli_insert_id($conn);

    // 2️⃣ Gán role cho dịch vụ
    foreach ($roles as $roleId) {
        $query = "INSERT INTO service_role (idservices, idroles) VALUES (?, ?)";
        $stmt2 = mysqli_prepare($conn, $query);

        if (!$stmt2) {
            die("❌ Lỗi prepare service_role: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt2, "ii", $serviceId, $roleId);

        if (!mysqli_stmt_execute($stmt2)) {
            die("❌ Lỗi thực thi INSERT service_role: " . mysqli_stmt_error($stmt2));
        }
    }

    mysqli_close($conn);
    return true;
}
