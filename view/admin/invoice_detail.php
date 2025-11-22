<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/db_connection.php';
require_once __DIR__ . '/../../functions/invoice_functions.php';

if (!isAdmin()) die("Không có quyền!");
if (!isset($_GET['id'])) die("Thiếu ID hóa đơn");

$invoice_id = intval($_GET['id']);
$invoice = getInvoiceById($invoice_id);
if (!$invoice) die("Không tìm thấy hóa đơn");
$items = getInvoiceItems($invoice_id);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Chi tiết hóa đơn #<?= $invoice_id ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <h4>Hóa đơn #<?= $invoice_id ?></h4>
    <div>
      <a class="btn btn-secondary" href="../invoices_list.php">Quay lại</a>
      <a class="btn btn-success" href="./invoice_pay.php?id=<?= $invoice_id ?>">Thanh toán</a>
    </div>
  </div>

  <div class="card p-3 mb-3">
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($invoice['name_customer'] ?? '-') ?></p>
    <p><strong>Điện thoại:</strong> <?= htmlspecialchars($invoice['phone_customer'] ?? '-') ?></p>
    <p><strong>Tổng tiền:</strong> <?= number_format($invoice['total_amount'] ?? 0,0,',','.') ?> đ</p>
  </div>

  <h5>Dịch vụ</h5>
  <table class="table table-bordered">
    <thead><tr><th>Dịch vụ</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
    <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['services_name'] ?? '-') ?></td>
          <td><?= intval($it['quantity']) ?></td>
          <td><?= number_format($it['price'],0,',','.') ?> đ</td>
          <td><?= number_format($it['price'] * $it['quantity'],0,',','.') ?> đ</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
