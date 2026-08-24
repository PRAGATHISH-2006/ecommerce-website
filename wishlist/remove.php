<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

if ($item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item']);
    exit;
}

$q = "DELETE wi FROM wishlist_items wi JOIN wishlist w ON wi.wishlist_id = w.id WHERE wi.id = $item_id AND w.user_id = $user_id";
mysqli_query($conn, $q);

echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
