<?php
// /handle/delete_invoice.php
session_start();
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/invoice_functions.php';

if (!isAdmin()) {
    echo json_encode(['success'=>false,'message'=>'No permission']); exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success'=>false,'message'=>'Missing id']); exit;
}

// nếu muốn redirect (dùng link), xóa và quay lại
$ok = deleteInvoiceById($id);
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . ($ok ? "?success=deleted" : "?error=failed"));
    exit();
}
echo json_encode(['success'=> (bool)$ok ]);
