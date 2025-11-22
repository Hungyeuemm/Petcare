<?php
require_once __DIR__ . '/db_connection.php';

// Lưu lịch sử thanh toán
function savePaymentHistory($invoice_id, $customer_id, $staff_id, $amount, $method, $note) {
    $conn = getDbConnection();

    $stmt = $conn->prepare("
        INSERT INTO payment_history (invoice_id, customer_id, staff_id, amount, method, note)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiidss", $invoice_id, $customer_id, $staff_id, $amount, $method, $note);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

// Lấy toàn bộ lịch sử
function getPaymentHistoryAll() {
    $conn = getDbConnection();
    $sql = "
        SELECT p.*, c.name_customer 
        FROM payment_history p
        LEFT JOIN customers c ON p.customer_id = c.idcustomers
        ORDER BY p.id DESC
    ";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}
