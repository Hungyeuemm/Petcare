<?php
require_once __DIR__ . '/db_connection.php';
// laay 
function getAllCustomers() {
    $conn = getDbConnection();
    $sql = "SELECT idcustomers, user_id, name_customer, phone_customer, email_customer, address FROM customers";
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
//
//
// Laays thong tin khach hang theo ID
function getCustomerById($id) {
    $conn = getDbConnection();
    $sql = "SELECT idcustomers, user_id, name_customer, phone_customer, email_customer, address FROM customers WHERE idcustomers = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if($stmt){
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
       
        if(mysqli_num_rows($result) > 0){
            $customer = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $customer;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}
// cap nhan thong tin khach hang
function updateCustomer($id, $user_id,$name_customer, $phone_customer, $email_customer, $address) {
    $conn = getDbConnection();
    $sql = "UPDATE customers SET user_id = ?, name_customer = ?, phone_customer = ?, email_customer = ?, address = ? WHERE idcustomers = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $user_id, $name_customer, $phone_customer, $email_customer, $address, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}
// xoa khach hang
function deleteCustomer($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM customers WHERE idcustomers = ?";
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
// them khach hang
// thêm khách hàng mới
function createCustomer($user_id, $name_customer, $phone_customer, $email_customer, $address) {
    $conn = getDbConnection();

    // Kiểm tra user_id đã có khách hàng chưa (mỗi user chỉ 1 khách hàng)
    $check_sql = "SELECT idcustomers FROM customers WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($conn);
        return "Tài khoản này đã có thông tin khách hàng.";
    }
    mysqli_stmt_close($check_stmt);

    // Thêm mới
    $sql = "INSERT INTO customers (user_id, name_customer, phone_customer, email_customer, address)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $name_customer, $phone_customer, $email_customer, $address);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success ? true : "Thêm khách hàng thất bại.";
    }

    mysqli_close($conn);
    return "Lỗi không xác định khi thêm khách hàng.";
}
