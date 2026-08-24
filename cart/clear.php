<?php
require_once '../includes/db_connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM cart_items WHERE user_id = $user_id");
} else {
    unset($_SESSION['cart']);
}
unset($_SESSION['coupon_code']);
unset($_SESSION['coupon_discount']);
echo json_encode(['success' => true]);
