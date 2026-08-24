<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add to wishlist']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$variant_id = isset($_POST['variant_id']) && !empty($_POST['variant_id']) ? (int)$_POST['variant_id'] : 'NULL';

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

// Get or create wishlist
$w_res = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id = $user_id");
if (mysqli_num_rows($w_res) == 0) {
    mysqli_query($conn, "INSERT INTO wishlist (user_id) VALUES ($user_id)");
    $wishlist_id = mysqli_insert_id($conn);
} else {
    $w = mysqli_fetch_assoc($w_res);
    $wishlist_id = $w['id'];
}

// Check if already in wishlist
$check_q = "SELECT id FROM wishlist_items WHERE wishlist_id = $wishlist_id AND product_id = $product_id AND variant_id=" . ($variant_id === 'NULL' ? "IS NULL" : "=$variant_id");
$check_res = mysqli_query($conn, $check_q);

if (mysqli_num_rows($check_res) > 0) {
    echo json_encode(['success' => true, 'message' => 'Already in wishlist']);
    exit;
}

mysqli_query($conn, "INSERT INTO wishlist_items (wishlist_id, product_id, variant_id) VALUES ($wishlist_id, $product_id, $variant_id)");

echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
