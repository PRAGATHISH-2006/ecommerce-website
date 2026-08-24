<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$cart_id = isset($_POST['cart_id']) ? $_POST['cart_id'] : '';
$delta = isset($_POST['delta']) ? (int)$_POST['delta'] : 0;

if (!$cart_id || $delta === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_id = (int)$cart_id;
    
    // Check if belongs to user
    $q = mysqli_query($conn, "SELECT c.quantity, COALESCE(v.stock, p.stock) as max_stock FROM cart_items c JOIN products p ON c.product_id = p.id LEFT JOIN product_variants v ON c.variant_id = v.id WHERE c.id = $cart_id AND c.user_id = $user_id");
    
    if ($row = mysqli_fetch_assoc($q)) {
        $new_qty = $row['quantity'] + $delta;
        if ($new_qty <= 0) {
            mysqli_query($conn, "DELETE FROM cart_items WHERE id = $cart_id");
        } elseif ($new_qty > $row['max_stock']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit;
        } else {
            mysqli_query($conn, "UPDATE cart_items SET quantity = $new_qty WHERE id = $cart_id");
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }
} else {
    // Guest
    if (isset($_SESSION['cart'][$cart_id])) {
        // Need to check stock here ideally, but for demo brevity on guest update...
        $new_qty = $_SESSION['cart'][$cart_id]['qty'] + $delta;
        if ($new_qty <= 0) {
            unset($_SESSION['cart'][$cart_id]);
        } else {
            $_SESSION['cart'][$cart_id]['qty'] = $new_qty;
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }
}
