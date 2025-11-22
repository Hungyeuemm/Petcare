<?php
require_once __DIR__ . '/db_connection.php';

$conn = getDbConnection();

// --------------------------
// HÀM ĐĂNG KÝ NGƯỜI DÙNG
// --------------------------
function registerUser($username, $password, $confirm_password, $name_customer = null, $email_customer = null, $phone_customer = null, $address = null) {
    global $conn;

    // Basic validation
    if (empty($username) || empty($password)) {
        return "Username và mật khẩu không được rỗng";
    }
    if ($password !== $confirm_password) {
        return "Mật khẩu xác nhận không khớp";
    }

    // Kiểm tra trùng username trên bảng 'users'
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
        error_log('registerUser prepare failed: ' . $conn->error);
        return "Lỗi hệ thống (prepare): " . $conn->error;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return "Tên đăng nhập đã tồn tại";
    }
    $stmt->close();

    // Hash mật khẩu và insert vào bảng users
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $ins = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    if (!$ins) {
        return "Lỗi hệ thống khi chuẩn bị lưu dữ liệu";
    }
    $ins->bind_param("ss", $username, $hashed_password);
    if (!$ins->execute()) {
        $err = $ins->error;
        $ins->close();
        error_log('Insert failed (registerUser): ' . $err);
        return "Đăng ký thất bại: $err";
    }

    $new_id = $conn->insert_id;
    $ins->close();

    // Lưu thông tin khách hàng vào bảng customers nếu có đủ dữ liệu
    if ($name_customer !== null && $email_customer !== null && $phone_customer !== null && $address !== null) {
        $c = $conn->prepare("INSERT INTO customers (user_id, name_customer, phone_customer, email_customer, address) VALUES (?, ?, ?, ?, ?)");
        if ($c) {
            $c->bind_param('issss', $new_id, $name_customer, $phone_customer, $email_customer, $address);
            $c->execute();
            $c->close();
        }
    }

    // Start session and set user info
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['user'] = [
        'id' => $new_id,
        'username' => $username,
        'role' => 'user'
    ];

    return true;
}
/**
 * Hàm xử lý đăng nhập
 * @param string $username
 * @param string $password
 * @return array Mảng chứa kết quả đăng nhập
 */
function processLogin($username, $password)
{
    global $conn;

    // Kiểm tra input
    if (empty($username) || empty($password)) {
        return [
            'success' => false,
            'message' => 'Vui lòng nhập đầy đủ thông tin đăng nhập!'
        ];
    }

    // Xác thực người dùng
    $sql = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log('Prepare failed (select): ' . $conn->error);
        return [
            'success' => false,
            'message' => 'Lỗi hệ thống, vui lòng thử lại!'
        ];
    }

    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        error_log('Execute failed (select): ' . $stmt->error);
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Lỗi hệ thống, vui lòng thử lại!'
        ];
    }

    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Nếu password trong DB là hash => dùng password_verify
        if (password_verify($password, $user['password'])) {
            // Lưu session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            $stmt->close();
            return [
                'success' => true,
                'user' => $user,
                'message' => 'Đăng nhập thành công!'
            ];
        }

        // Nếu stored password là plain text (tài khoản cũ), so sánh trực tiếp
        if ($password === $user['password']) {
            // Cập nhật thành hash để an toàn hơn
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update) {
                $update->bind_param('si', $newHash, $user['id']);
                $update->execute();
                $update->close();
            }

            // Lưu session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            $stmt->close();
            return [
                'success' => true,
                'user' => $user,
                'message' => 'Đăng nhập thành công! (mật khẩu đã được cập nhật)'
            ];
        }
    }

    $stmt->close();
    return [
        'success' => false,
        'message' => 'Sai tên đăng nhập hoặc mật khẩu!'
    ];
}

/**
 * Hàm xử lý đăng xuất
 */
function handleLogout()
{
    // Xóa toàn bộ session
    session_unset();
    session_destroy();

    return [
        'success' => true,
        'message' => 'Đăng xuất thành công!'
    ];
}

/**
 * Hàm kiểm tra đăng nhậpp
 */
function isLoggedIn()
{
    return isset($_SESSION['user']);
}

/**
 * Hàm kiểm tra quyền admin
 */
function isAdmin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

