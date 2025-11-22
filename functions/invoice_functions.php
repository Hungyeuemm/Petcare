<?php
// /functions/invoice_functions.php
require_once __DIR__ . '/db_connection.php';

/**
 * Tạo invoice từ appointment (khi duyệt)
 * Trả về invoice_id (int) nếu thành công, false nếu lỗi.
 */
function createInvoiceFromAppointment(int $appointment_id)
{
    $conn = getDbConnection();

    // lấy appointment
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE idappointments = ? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $ap = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$ap) return false;

    // lấy giá dịch vụ
    $stmt = $conn->prepare("SELECT idservices, price_services FROM services WHERE idservices = ? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param("i", $ap['service_id']);
    $stmt->execute();
    $svc = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$svc) return false;

    $amount = (float)$svc['price_services'];
    $customer_id = (int)$ap['customer_id'];

    // nếu đã có invoice cho appointment thì trả id cũ
    $stmt = $conn->prepare("SELECT id FROM invoices WHERE appointment_id = ? LIMIT 1");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($exists) return (int)$exists['id'];

    // bắt đầu transaction
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO invoices (appointment_id, customer_id, total_amount, payment_status, created_at) VALUES (?, ?, ?, 'Chưa thanh toán', NOW())");
        $stmt->bind_param("iid", $appointment_id, $customer_id, $amount);
        $stmt->execute();
        $invoice_id = $conn->insert_id;
        $stmt->close();

        // insert invoice_items (1 dòng)
        $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, service_id, quantity, price) VALUES (?, ?, 1, ?)");
        $stmt->bind_param("iid", $invoice_id, $ap['service_id'], $amount);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        return (int)$invoice_id;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Lấy tất cả invoice (mặc định show chưa thanh toán)
 */
function getAllInvoices($only_unpaid = false)
{
    $conn = getDbConnection();
    if ($only_unpaid) {
        $sql = "SELECT i.*, c.name_customer FROM invoices i LEFT JOIN customers c ON i.customer_id = c.idcustomers WHERE i.payment_status <> 'Đã thanh toán' OR i.payment_status IS NULL ORDER BY i.created_at DESC";
    } else {
        $sql = "SELECT i.*, c.name_customer FROM invoices i LEFT JOIN customers c ON i.customer_id = c.idcustomers ORDER BY i.created_at DESC";
    }
    $res = $conn->query($sql);
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $res->free();
    }
    return $rows;
}

function getInvoiceById(int $id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT i.*, c.name_customer, c.phone_customer FROM invoices i LEFT JOIN customers c ON i.customer_id = c.idcustomers WHERE i.id = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

function getInvoiceItems(int $invoice_id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT ii.*, s.services_name FROM invoice_items ii LEFT JOIN services s ON ii.service_id = s.idservices WHERE ii.invoice_id = ?");
    if (!$stmt) return [];
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/**
 * Thanh toán: lưu vào payment_history, xóa appointment và xóa invoice (theo yêu cầu).
 * Trả về true/false.
 */
function markInvoicePaidAndArchive(int $invoice_id, int $staff_id, string $method = 'Tiền mặt', ?string $note = null)
{
    $conn = getDbConnection();

    // Lấy invoice
    $stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$invoice) return false;

    $customer_id    = (int)$invoice['customer_id'];
    $amount         = (float)$invoice['total_amount'];
    $appointment_id = (int)$invoice['appointment_id'];

    // Start transaction
    $conn->begin_transaction();
    try {

        // 1. Cập nhật trạng thái hóa đơn
        $stmt = $conn->prepare("
            UPDATE invoices 
            SET payment_status = 'Đã thanh toán',
                is_paid = 1,
                paid_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $stmt->close();

        // 2. Lưu lịch sử thanh toán
        $stmt = $conn->prepare("
            INSERT INTO payment_history (invoice_id, customer_id, staff_id, amount, method, note, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiidss", $invoice_id, $customer_id, $staff_id, $amount, $method, $note);
        $stmt->execute();
        $stmt->close();

        // 3. Xóa appointment nếu tồn tại
        if ($appointment_id > 0) {
            $stmt = $conn->prepare("DELETE FROM appointments WHERE idappointments = ?");
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $stmt->close();
        }

        // 4. Xóa invoice_items
        $stmt = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $stmt->close();

        // 5. Xóa invoice
        $stmt = $conn->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}



function deleteInvoiceById(int $invoice_id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $stmt->close();
    }
    $stmt = $conn->prepare("DELETE FROM invoices WHERE id = ?");
    if (!$stmt) return false;
    $stmt->bind_param("i", $invoice_id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
function createInvoiceForAppointment($appointment_id)
{
    $conn = getDbConnection();

    // Lấy appointment
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE idappointments = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$app) return false;

    $customer_id = $app['customer_id'];
    $service_id  = $app['service_id'];

    // Lấy giá dịch vụ
    $stmt = $conn->prepare("SELECT price_services FROM services WHERE idservices = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $svc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$svc) return false;

    $amount = (float)$svc['price_services'];

    // Kiểm tra có hóa đơn cũ chưa
    $stmt = $conn->prepare("SELECT id FROM invoices WHERE appointment_id = ? LIMIT 1");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists) return $exists['id'];

    // Tạo hóa đơn
    $stmt = $conn->prepare("
        INSERT INTO invoices (appointment_id, customer_id, total_amount, payment_status, is_paid, created_at)
        VALUES (?, ?, ?, 'Chưa thanh toán', 0, NOW())
    ");
    $stmt->bind_param("iid", $appointment_id, $customer_id, $amount);
    $stmt->execute();
    $invoice_id = $conn->insert_id;
    $stmt->close();

    // Chi tiết hóa đơn
    $stmt = $conn->prepare("
        INSERT INTO invoice_items (invoice_id, service_id, quantity, price)
        VALUES (?, ?, 1, ?)
    ");
    $stmt->bind_param("iid", $invoice_id, $service_id, $amount);
    $stmt->execute();
    $stmt->close();

    return $invoice_id;
}
// ---- Lưu lịch sử thanh toán ----
function savePaymentHistory($invoice_id, $customer_id, $staff_id, $amount, $method, $note = null)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        INSERT INTO payment_history (invoice_id, customer_id, staff_id, amount, method, note)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iiidss",
        $invoice_id,
        $customer_id,
        $staff_id,
        $amount,
        $method,
        $note
    );

    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}


// ---- Đánh dấu hóa đơn đã thanh toán ----
// function markInvoicePaidAndArchive($invoice_id, $staff_id, $method, $note)
// {
//     $conn = getDbConnection();

//     // Lấy thông tin hóa đơn
//     $stmt = $conn->prepare("SELECT customer_id, total_amount FROM invoices WHERE id = ?");
//     $stmt->bind_param("i", $invoice_id);
//     $stmt->execute();
//     $invoice = $stmt->get_result()->fetch_assoc();
//     $stmt->close();

//     if (!$invoice) return false;

//     $customer_id = $invoice['customer_id'];
//     $amount      = $invoice['total_amount'];

//     // Cập nhật trạng thái hóa đơn
//     $stmt = $conn->prepare("
//         UPDATE invoices
//         SET is_paid = 1,
//             payment_status = 'Đã thanh toán',
//             paid_at = NOW()
//         WHERE id = ?
//     ");
//     $stmt->bind_param("i", $invoice_id);
//     $ok = $stmt->execute();
//     $stmt->close();
//     $ok = $stmt->execute();
//     if (!$ok) {
//         echo "LỖI SQL: " . $stmt->error;
//     }

//     if (!$ok) return false;

//     // Lưu lịch sử
//     return savePaymentHistory(
//         $invoice_id,
//         $customer_id,
//         $staff_id,
//         $amount,
//         $method,
//         $note
//     );
// }
function getStaffIdFromInvoice($invoice_id)
{
    $conn = getDbConnection();

    $stmt = $conn->prepare("
        SELECT a.staff_id 
        FROM invoices i
        JOIN appointments a ON i.appointment_id = a.idappointments
        WHERE i.id = ?
    ");

    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $res['staff_id'] ?? null;
}
