<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$cart_id = isset($_POST['cart_id']) ? $_POST['cart_id'] : '';

if (!$cart_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_id = (int)$cart_id;
    mysqli_query($conn, "DELETE FROM cart_items WHERE id = $cart_id AND user_id = $user_id");
    echo json_encode(['success' => true]);
} else {
    // Guest
    if (isset($_SESSION['cart'][$cart_id])) {
        unset($_SESSION['cart'][$cart_id]);
    }
    echo json_encode(['success' => true]);
}
