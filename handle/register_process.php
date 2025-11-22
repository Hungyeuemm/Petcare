<?php
session_start();
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // normalize input names from the form
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $name_customer = trim($_POST['name_customer'] ?? '');
        $email_customer = trim($_POST['email_customer'] ?? '');
        $phone_customer = trim($_POST['phone_customer'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Call registerUser defined in functions/auth.php
        $result = registerUser($username, $password, $confirm_password, $name_customer, $email_customer, $phone_customer, $address);

    if ($result === true) {
        header("Location: ../index.php");
        exit();
    } else {
        // Safely encode the server error message for use in JS to avoid
        // breaking the script when the message contains quotes or newlines.
        $jsMsg = json_encode($result);
        echo "<script>var msg = $jsMsg; alert(msg); window.history.back();</script>";
    }
} else {
    header("Location: ../view/register.php");
    exit();
}
