<?php
require_once '../includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || !isset($_SESSION['checkout_totals']) || !isset($_SESSION['checkout_address_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$address_id = (int)$_SESSION['checkout_address_id'];
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$totals = $_SESSION['checkout_totals'];
$coupon_id = isset($_SESSION['coupon_id']) ? (int)$_SESSION['coupon_id'] : 'NULL';

// Validate cart is not empty
$c_res = mysqli_query($conn, "
    SELECT ci.quantity, p.id as product_id, ci.variant_id, COALESCE(v.price, p.discount_price, p.price) as price, COALESCE(v.stock, p.stock) as stock 
    FROM cart_items ci 
    JOIN products p ON ci.product_id = p.id 
    LEFT JOIN product_variants v ON ci.variant_id = v.id 
    WHERE ci.user_id = $user_id
");
if (mysqli_num_rows($c_res) == 0) {
    header("Location: ../cart/index.php");
    exit;
}

$items = [];
while($row = mysqli_fetch_assoc($c_res)) {
    if ($row['quantity'] > $row['stock']) {
        // Handle out of stock during checkout gracefully (future enhancement: redirect to cart with error)
        die("Item out of stock!");
    }
    $items[] = $row;
}

mysqli_begin_transaction($conn);
try {
    // 1. Create Order
    $subtotal = $totals['subtotal'];
    $discount = $totals['discount'];
    $shipping = $totals['shipping'];
    $tax = $totals['tax'];
    $final_amount = $totals['total'];
    
    // In Phase 5 (Payments), payment_status will be PENDING, but for COD it can be PENDING.
    $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, address_id, subtotal, discount, shipping, tax, total_price, final_amount, coupon_id, payment_method, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, $coupon_id, ?, 'pending', 'PENDING')");
    mysqli_stmt_bind_param($stmt, "iidddddds", $user_id, $address_id, $subtotal, $discount, $shipping, $tax, $final_amount, $final_amount, $payment_method);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);
    
    // 2. Insert Order Items (Inventory is reduced in Phase 5 upon success, but for COD we reduce now)
    $stmt_items = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $v_id = $item['variant_id'] ?: null; // null if 0 or empty
        mysqli_stmt_bind_param($stmt_items, "iiiid", $order_id, $item['product_id'], $v_id, $item['quantity'], $item['price']);
        mysqli_stmt_execute($stmt_items);
        
        // Reduce stock (For COD, we assume success immediately)
        if ($payment_method === 'Cash on Delivery') {
            if ($v_id) {
                mysqli_query($conn, "UPDATE product_variants SET stock = stock - {$item['quantity']} WHERE id = $v_id");
            } else {
                mysqli_query($conn, "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}");
            }
        }
    }
    
    // 3. Log Coupon Usage
    if ($coupon_id !== 'NULL') {
        mysqli_query($conn, "INSERT INTO coupon_usage (coupon_id, user_id, order_id) VALUES ($coupon_id, $user_id, $order_id)");
        mysqli_query($conn, "UPDATE coupons SET usage_limit = usage_limit - 1 WHERE id = $coupon_id AND usage_limit IS NOT NULL AND usage_limit > 0");
    }
    
    // 4. Clear Cart
    mysqli_query($conn, "DELETE FROM cart_items WHERE user_id = $user_id");
    
    // 5. Clean up Sessions
    unset($_SESSION['coupon_code']);
    unset($_SESSION['coupon_id']);
    unset($_SESSION['coupon_discount']);
    unset($_SESSION['coupon_success']);
    unset($_SESSION['coupon_msg']);
    unset($_SESSION['checkout_totals']);
    unset($_SESSION['checkout_address_id']);
    
    mysqli_commit($conn);
    
    // Redirect to Order Confirmation (For Phase 4, we use order history or a quick success page)
    $_SESSION['checkout_success_order_id'] = $order_id;
    header("Location: success.php");
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    die("Checkout failed: " . $e->getMessage());
}
