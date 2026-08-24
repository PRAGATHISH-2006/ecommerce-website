<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$cart_items = [];
$total_price = 0;
$subtotal = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "
        SELECT ci.id as cart_id, ci.quantity as qty, p.id as product_id, p.name, p.image, 
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
} else {
    // Guest cart
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach($_SESSION['cart'] as $key => $item) {
            $p_id = (int)$item['product_id'];
            $v_id = (int)$item['variant_id'];
            $qty = (int)$item['qty'];
            
            $q = "SELECT id as product_id, name, image, discount_price, price FROM products WHERE id=$p_id";
            $r = mysqli_query($conn, $q);
            if ($p = mysqli_fetch_assoc($r)) {
                $p['cart_id'] = $key; // Using session key as ID
                $p['qty'] = $qty;
                $p['final_price'] = $p['discount_price'] > 0 ? $p['discount_price'] : $p['price'];
                $p['size'] = $p['color'] = $p['storage'] = null;
                
                if ($v_id > 0) {
                    $vq = "SELECT price, size, color, storage FROM product_variants WHERE id=$v_id";
                    $vr = mysqli_query($conn, $vq);
                    if ($v = mysqli_fetch_assoc($vr)) {
                        $p['final_price'] = $v['price'];
                        $p['size'] = $v['size'];
                        $p['color'] = $v['color'];
                        $p['storage'] = $v['storage'];
                    }
                }
                
                $p['subtotal'] = $p['final_price'] * $p['qty'];
                $cart_items[] = $p;
                $subtotal += $p['subtotal'];
            }
        }
    }
}

// Calculate tax and shipping
$tax_rate = 0.05; // 5% tax
$shipping = $subtotal > 100 ? 0 : 15; // Free shipping over $100
$tax = $subtotal * $tax_rate;
$discount = isset($_SESSION['coupon_discount']) ? $_SESSION['coupon_discount'] : 0;
$total_price = $subtotal + $tax + $shipping - $discount;

if ($total_price < 0) $total_price = 0;
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold display-5">Your Shopping Cart</h1>
            <p class="text-muted">Review your selected items before checkout</p>
        </div>
    </div>

    <?php if(empty($cart_items)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x display-1 text-muted"></i>
            </div>
            <h3>Your cart is empty!</h3>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="../products/index.php" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="row g-5">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <?php foreach($cart_items as $item): ?>
                        <div class="row align-items-center mb-4 pb-4 border-bottom">
                            <div class="col-md-2 col-4">
                                <img src="../<?php echo empty($item['image']) ? 'assets/images/default.jpg' : $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded-3">
                            </div>
                            <div class="col-md-4 col-8">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <?php if($item['size'] || $item['color'] || $item['storage']): ?>
                                    <p class="text-muted small mb-0">
                                        <?php 
                                            $opts = [];
                                            if($item['size']) $opts[] = $item['size'];
                                            if($item['color']) $opts[] = $item['color'];
                                            if($item['storage']) $opts[] = $item['storage'];
                                            echo implode(' | ', $opts);
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 col-6 mt-3 mt-md-0 text-center">
                                <div class="input-group input-group-sm mx-auto" style="width: 100px;">
                                    <button class="btn btn-light border" type="button" onclick="updateCart('<?php echo $item['cart_id']; ?>', -1)">-</button>
                                    <input type="text" class="form-control text-center border-0 bg-light" value="<?php echo $item['qty']; ?>" readonly>
                                    <button class="btn btn-light border" type="button" onclick="updateCart('<?php echo $item['cart_id']; ?>', 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 col-4 mt-3 mt-md-0 text-end">
                                <h6 class="fw-bold text-primary mb-0">$<?php echo number_format($item['subtotal'], 2); ?></h6>
                            </div>
                            <div class="col-md-1 col-2 mt-3 mt-md-0 text-end">
                                <button onclick="removeCartItem('<?php echo $item['cart_id']; ?>')" class="btn btn-link text-danger p-0">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="../products/index.php" class="text-decoration-none fw-bold text-primary"><i class="bi bi-arrow-left me-2"></i> Continue Shopping</a>
                        <button onclick="clearCart()" class="btn btn-outline-danger btn-sm border-0 rounded-pill px-3">Clear Cart</button>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: #f8f9ff;">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    
                    <form action="apply-coupon.php" method="POST" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control border-0 bg-white shadow-sm" placeholder="Promo Code" value="<?php echo isset($_SESSION['coupon_code']) ? htmlspecialchars($_SESSION['coupon_code']) : ''; ?>">
                            <button class="btn btn-primary shadow-sm" type="submit">Apply</button>
                        </div>
                        <?php if(isset($_SESSION['coupon_msg'])): ?>
                            <small class="text-<?php echo $_SESSION['coupon_success'] ? 'success' : 'danger'; ?> mt-2 d-block">
                                <?php echo $_SESSION['coupon_msg']; unset($_SESSION['coupon_msg']); ?>
                            </small>
                        <?php endif; ?>
                    </form>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <?php if($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <span>Discount (<?php echo htmlspecialchars($_SESSION['coupon_code']); ?>)</span>
                        <span class="fw-bold">-$<?php echo number_format($discount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Estimated Tax (5%)</span>
                        <span class="fw-bold">$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="<?php echo $shipping == 0 ? 'text-success fw-bold' : 'fw-bold'; ?>">
                            <?php echo $shipping == 0 ? 'Free' : '$' . number_format($shipping, 2); ?>
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-5 fw-bold text-primary">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <a href="../checkout/index.php" class="btn btn-primary btn-lg w-100 py-3 mb-3 rounded-pill shadow-sm">
                        Proceed to Checkout
                    </a>
                    <p class="text-center small text-muted mb-0">
                        <i class="bi bi-shield-lock me-1"></i> Secure and encrypted payment
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateCart(cart_id, delta) {
    fetch('update.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'cart_id=' + encodeURIComponent(cart_id) + '&delta=' + delta
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) location.reload();
        else alert(data.message);
    });
}

function removeCartItem(cart_id) {
    if(!confirm('Remove this item?')) return;
    fetch('remove.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'cart_id=' + encodeURIComponent(cart_id)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) location.reload();
        else alert(data.message);
    });
}

function clearCart() {
    if(!confirm('Clear entire cart?')) return;
    fetch('clear.php', { method: 'POST' })
    .then(() => location.reload());
}
</script>

<?php require_once '../includes/footer.php'; ?>
