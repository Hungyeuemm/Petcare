<?php
require_once __DIR__ . '/db_connection.php';
// lấy tất cả tài khoản người dùng
function getAllAccounts(){
    $conn = getDbConnection();
    $sql = "SELECT id, username, password FROM users";
    $result = mysqli_query($conn, $sql);
    $accounts = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $accounts[] = $row;
        }
    }
    mysqli_close($conn);
    return $accounts;
}
// function isUsernameExists($username) {
//     global $conn;
//     $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
//     $stmt->bind_param("s", $username);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->num_rows > 0;
// }
// lay thong tin khach hang theo ID
function getAccountId($id){
    $conn = getDbConnection();
    $sql = " SELECT id, username, password FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if($stmt){
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) > 0){
            $accounts = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $accounts;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}
// cap nhan thong tin khach hang
function updateAccount($id, $username, $password) {
    $conn = getDbConnection();
    $sql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $username, $password, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}
// them tai khoan
function addAccount($username, $password) {
    $conn = getDbConnection();
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
    
}
// xóa tài khoản
function deleteAccount($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM users WHERE id = ?";
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

