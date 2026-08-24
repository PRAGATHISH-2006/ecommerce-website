<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['checkout_address_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$address_id = (int)$_SESSION['checkout_address_id'];

// Get address
$a_res = mysqli_query($conn, "SELECT * FROM addresses WHERE id=$address_id AND user_id=$user_id");
if (mysqli_num_rows($a_res) == 0) {
    header("Location: index.php");
    exit;
}
$addr = mysqli_fetch_assoc($a_res);

// Calculate totals securely
$cart_items = [];
$subtotal = 0;
$query = "
    SELECT ci.quantity as qty, p.id as product_id, p.name, p.image, 
           COALESCE(v.price, p.discount_price, p.price) as final_price,
           v.size, v.color, v.storage
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    LEFT JOIN product_variants v ON ci.variant_id = v.id
    WHERE ci.user_id = $user_id
";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['final_price'] * $row['qty'];
    $cart_items[] = $row;
    $subtotal += $row['subtotal'];
}

$tax_rate = 0.05; // 5%
$shipping = $subtotal > 100 ? 0 : 15;
$tax = $subtotal * $tax_rate;
$discount = 0;

if (isset($_SESSION['coupon_id'])) {
    $c_id = (int)$_SESSION['coupon_id'];
    $c_res = mysqli_query($conn, "SELECT * FROM coupons WHERE id=$c_id AND status='active'");
    if ($coupon = mysqli_fetch_assoc($c_res)) {
        if ($subtotal >= $coupon['minimum_order']) {
            if ($coupon['discount_type'] === 'percentage') {
                $discount = $subtotal * ($coupon['discount_value'] / 100);
                if ($coupon['maximum_discount'] > 0 && $discount > $coupon['maximum_discount']) {
                    $discount = $coupon['maximum_discount'];
                }
            } else {
                $discount = $coupon['discount_value'];
            }
        }
    }
}

$total_price = $subtotal + $tax + $shipping - $discount;
if ($total_price < 0) $total_price = 0;

// Save totals to session to process order securely
$_SESSION['checkout_totals'] = [
    'subtotal' => $subtotal,
    'discount' => $discount,
    'shipping' => $shipping,
    'tax' => $tax,
    'total' => $total_price
];
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold">Checkout: Summary & Payment</h2>
            <div class="progress mt-3 mx-auto" style="width: 50%; height: 5px;">
                <div class="progress-bar bg-primary" style="width: 100%;"></div>
            </div>
            <div class="d-flex justify-content-between mt-2 mx-auto text-muted small fw-bold" style="width: 50%;">
                <span>Address</span>
                <span class="text-primary">Summary & Payment</span>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-7">
            <!-- Review Shipping -->
            <div class="card border-0 shadow-sm p-4 rounded-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Shipping Address</h5>
                    <a href="index.php" class="btn btn-sm btn-light rounded-pill">Change</a>
                </div>
                <div class="text-muted small">
                    <span class="fw-bold text-dark d-block mb-1"><?php echo htmlspecialchars($addr['full_name']); ?></span>
                    <?php echo htmlspecialchars($addr['street_address']); ?><br>
                    <?php if($addr['apartment']) echo htmlspecialchars($addr['apartment']) . '<br>'; ?>
                    <?php echo htmlspecialchars($addr['city']) . ', ' . htmlspecialchars($addr['state']) . ' ' . htmlspecialchars($addr['postal_code']); ?><br>
                    <?php echo htmlspecialchars($addr['country']); ?>
                </div>
            </div>

            <!-- Payment Method (Demo) -->
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h5 class="fw-bold mb-4">Payment Method</h5>
                <form action="process.php" method="POST">
                    <div class="form-check p-3 border rounded-3 mb-3 border-primary bg-primary bg-opacity-10">
                        <input class="form-check-input" type="radio" name="payment_method" value="Cash on Delivery" id="pay1" checked>
                        <label class="form-check-label d-flex align-items-center" for="pay1">
                            <i class="bi bi-cash-stack me-3 fs-4 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block">Cash on Delivery</span>
                                <small class="text-muted">Pay when your order arrives</small>
                            </div>
                        </label>
                    </div>
                    <!-- Future: Razorpay in Phase 5 -->
                    <div class="form-check p-3 border rounded-3 text-muted">
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label d-flex align-items-center">
                            <i class="bi bi-credit-card me-3 fs-4"></i>
                            <div>
                                <span class="fw-bold d-block">Online Payment (Razorpay)</span>
                                <small>Available in Phase 5</small>
                            </div>
                        </label>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" name="place_order" class="btn btn-primary btn-lg py-3 rounded-pill fw-bold shadow-sm">
                            Confirm Order - $<?php echo number_format($total_price, 2); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-light">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                <div class="mb-4">
                    <?php foreach($cart_items as $prod): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="position-relative">
                                    <img src="../<?php echo empty($prod['image']) ? 'assets/images/default.jpg' : $prod['image']; ?>" class="rounded-3 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary shadow-sm">
                                        <?php echo $prod['qty']; ?>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($prod['name']); ?></h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <?php 
                                            $opts = [];
                                            if($prod['size']) $opts[] = $prod['size'];
                                            if($prod['color']) $opts[] = $prod['color'];
                                            if($prod['storage']) $opts[] = $prod['storage'];
                                            echo implode(' | ', $opts);
                                        ?>
                                    </small>
                                </div>
                            </div>
                            <span class="small fw-bold">$<?php echo number_format($prod['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Subtotal</span>
                    <span class="small fw-bold">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <?php if($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span class="small">Discount</span>
                        <span class="small fw-bold">-$<?php echo number_format($discount, 2); ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Tax (5%)</span>
                    <span class="small fw-bold">$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Shipping</span>
                    <span class="<?php echo $shipping == 0 ? 'text-success' : ''; ?> small fw-bold">
                        <?php echo $shipping == 0 ? 'Free' : '$' . number_format($shipping, 2); ?>
                    </span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold fs-5">Total</span>
                    <span class="fw-bold fs-5 text-primary">$<?php echo number_format($total_price, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
