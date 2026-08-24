<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$variant_id = isset($_POST['variant_id']) && !empty($_POST['variant_id']) ? (int)$_POST['variant_id'] : 'NULL';
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($product_id <= 0 || $qty <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

// Check stock
$stock = 0;
if ($variant_id !== 'NULL') {
    $v_res = mysqli_query($conn, "SELECT stock FROM product_variants WHERE id=$variant_id");
    if ($v = mysqli_fetch_assoc($v_res)) {
        $stock = $v['stock'];
    }
} else {
    $p_res = mysqli_query($conn, "SELECT stock FROM products WHERE id=$product_id");
    if ($p = mysqli_fetch_assoc($p_res)) {
        $stock = $p['stock'];
    }
}

if ($stock < $qty) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    exit;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Check if item already exists in cart
    $check_query = "SELECT id, quantity FROM cart_items WHERE user_id=$user_id AND product_id=$product_id AND variant_id=" . ($variant_id === 'NULL' ? "IS NULL" : "=$variant_id");
    $check_res = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);
        $new_qty = $row['quantity'] + $qty;
        if ($new_qty > $stock) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit;
        }
        $update_id = $row['id'];
        mysqli_query($conn, "UPDATE cart_items SET quantity=$new_qty WHERE id=$update_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart_items (user_id, product_id, variant_id, quantity) VALUES ($user_id, $product_id, $variant_id, $qty)");
    }
} else {
    // Session fallback for guests
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $key = $product_id . '_' . ($variant_id === 'NULL' ? '0' : $variant_id);
    
    if (isset($_SESSION['cart'][$key])) {
        $new_qty = $_SESSION['cart'][$key]['qty'] + $qty;
        if ($new_qty > $stock) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
            exit;
        }
        $_SESSION['cart'][$key]['qty'] = $new_qty;
    } else {
        $_SESSION['cart'][$key] = [
            'product_id' => $product_id,
            'variant_id' => $variant_id === 'NULL' ? 0 : $variant_id,
            'qty' => $qty
        ];
    }
}

echo json_encode(['success' => true, 'message' => 'Added to cart']);
