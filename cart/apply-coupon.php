<?php
require_once '../includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$code = isset($_POST['coupon_code']) ? trim(mysqli_real_escape_string($conn, $_POST['coupon_code'])) : '';

if (empty($code)) {
    unset($_SESSION['coupon_code']);
    unset($_SESSION['coupon_discount']);
    $_SESSION['coupon_success'] = false;
    $_SESSION['coupon_msg'] = "Coupon removed.";
    header('Location: index.php');
    exit;
}

// Calculate current subtotal to validate coupon requirements
$subtotal = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT ci.quantity, COALESCE(v.price, p.discount_price, p.price) as final_price FROM cart_items ci JOIN products p ON ci.product_id = p.id LEFT JOIN product_variants v ON ci.variant_id = v.id WHERE ci.user_id = $user_id";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)) {
        $subtotal += $row['final_price'] * $row['quantity'];
    }
} else {
    // Calculate for guest
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach($_SESSION['cart'] as $item) {
            $p_id = (int)$item['product_id'];
            $v_id = (int)$item['variant_id'];
            $q = "SELECT discount_price, price FROM products WHERE id=$p_id";
            if($r = mysqli_query($conn, $q)) {
                $p = mysqli_fetch_assoc($r);
                $final = $p['discount_price'] > 0 ? $p['discount_price'] : $p['price'];
                if ($v_id > 0) {
                    $vq = "SELECT price FROM product_variants WHERE id=$v_id";
                    if($vr = mysqli_query($conn, $vq)) {
                        $v = mysqli_fetch_assoc($vr);
                        $final = $v['price'];
                    }
                }
                $subtotal += $final * $item['qty'];
            }
        }
    }
}

// Validate coupon
$q = "SELECT * FROM coupons WHERE code = '$code' AND status = 'active' AND start_date <= NOW() AND expiry_date >= NOW()";
$res = mysqli_query($conn, $q);

if (mysqli_num_rows($res) === 0) {
    $_SESSION['coupon_success'] = false;
    $_SESSION['coupon_msg'] = "Invalid or expired coupon code.";
} else {
    $coupon = mysqli_fetch_assoc($res);
    
    if ($subtotal < $coupon['minimum_order']) {
        $_SESSION['coupon_success'] = false;
        $_SESSION['coupon_msg'] = "Minimum order amount of $" . $coupon['minimum_order'] . " required.";
    } else {
        // Calculate discount
        $discount = 0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = $subtotal * ($coupon['discount_value'] / 100);
            if ($coupon['maximum_discount'] > 0 && $discount > $coupon['maximum_discount']) {
                $discount = $coupon['maximum_discount'];
            }
        } else {
            $discount = $coupon['discount_value'];
        }
        
        $_SESSION['coupon_code'] = $code;
        $_SESSION['coupon_discount'] = $discount;
        $_SESSION['coupon_id'] = $coupon['id'];
        $_SESSION['coupon_success'] = true;
        $_SESSION['coupon_msg'] = "Coupon applied successfully!";
    }
}

header('Location: index.php');
exit;
