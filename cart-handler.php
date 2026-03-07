<?php
session_start();
require_once 'includes/db_connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if($action == 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if(!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        echo json_encode(['status' => 'success', 'count' => array_sum($_SESSION['cart'])]);
        exit;
    }
    
    if($action == 'remove') {
        $product_id = (int)$_POST['product_id'];
        if(isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['status' => 'success', 'count' => array_sum($_SESSION['cart'])]);
        exit;
    }
    
    if($action == 'update') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        if($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        echo json_encode(['status' => 'success', 'count' => array_sum($_SESSION['cart'])]);
        exit;
    }
}
?>
