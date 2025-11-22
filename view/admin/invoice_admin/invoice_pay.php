<?php
// /view/admin/invoice_admin/invoice_pay.php
session_start();
require_once __DIR__ . '/../../../functions/auth.php';
require_once __DIR__ . '/../../../functions/db_connection.php';

if (!isAdmin()) die("Không có quyền!");
if (!isset($_GET['id'])) die("Thiếu ID");
$id = intval($_GET['id']);

$conn = getDbConnection();
$stmt = $conn->prepare("SELECT i.*, c.name_customer FROM invoices i LEFT JOIN customers c ON i.customer_id = c.idcustomers WHERE i.id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$invoice) die("Không tìm thấy hóa đơn");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Thanh toán hóa đơn #<?= $id ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container col-md-6">
  <h4>Thanh toán Hoá đơn #<?= $id ?></h4>
  <div class="card p-3">
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($invoice['name_customer']) ?></p>
    <p><strong>Tổng:</strong> <?= number_format($invoice['total_amount'] ?? 0,0,',','.') ?> đ</p>

    <form method="POST" action="/Baitaplon/handle/invoice_pay_process.php">
      <input type="hidden" name="invoice_id" value="<?= $id ?>">
      <div class="mb-3">
        <label>Phương thức</label>
        <select name="method" class="form-control">
          <option>Tiền mặt</option>
          <option>Chuyển khoản</option>
          <option>POS</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Ghi chú</label>
        <input type="text" name="note" class="form-control">
      </div>
      <button class="btn btn-success w-100">Xác nhận thanh toán</button>
    </form>
  </div>
</div>
</body>
</html>
