<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$total_price = 0;

if(!empty($cart_items)) {
    $ids = implode(',', array_keys($cart_items));
    $query = "SELECT * FROM products WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)) {
        $row['qty'] = $cart_items[$row['id']];
        $row['subtotal'] = $row['price'] * $row['qty'];
        $products[] = $row;
        $total_price += $row['subtotal'];
    }
}
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold display-5">Your Shopping Cart</h1>
            <p class="text-muted">Review your selected items before checkout</p>
        </div>
    </div>

    <?php if(empty($products)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x display-1 text-muted"></i>
            </div>
            <h3>Your cart is empty!</h3>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="products.php" class="btn btn-custom btn-lg">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <?php foreach($products as $prod): ?>
                        <div class="row align-items-center mb-4 pb-4 border-bottom">
                            <div class="col-md-2 col-4">
                                <img src="https://via.placeholder.com/150x150" alt="<?php echo $prod['name']; ?>" class="img-fluid rounded-3">
                            </div>
                            <div class="col-md-4 col-8">
                                <h6 class="fw-bold mb-1"><?php echo $prod['name']; ?></h6>
                                <p class="text-muted small mb-0">Product ID: #<?php echo $prod['id']; ?></p>
                            </div>
                            <div class="col-md-3 col-6 text-center">
                                <div class="input-group input-group-sm mx-auto" style="width: 100px;">
                                    <button class="btn btn-light border" type="button" onclick="updateQty(<?php echo $prod['id']; ?>, -1)">-</button>
                                    <input type="text" class="form-control text-center border-0 bg-light" value="<?php echo $prod['qty']; ?>" readonly>
                                    <button class="btn btn-light border" type="button" onclick="updateQty(<?php echo $prod['id']; ?>, 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 col-4 text-end">
                                <h6 class="fw-bold text-primary mb-0">$<?php echo number_format($prod['subtotal'], 2); ?></h6>
                            </div>
                            <div class="col-md-1 col-2 text-end">
                                <button onclick="removeFromCart(<?php echo $prod['id']; ?>)" class="btn btn-link text-danger p-0">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="products.php" class="text-decoration-none fw-bold"><i class="bi bi-arrow-left me-2"></i> Continue Shopping</a>
                        <button onclick="clearCart()" class="btn btn-outline-danger btn-sm border-0">Clear Cart</button>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: #f8f9ff;">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-5 fw-bold text-primary">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-custom btn-lg w-100 py-3 mb-3">
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
function updateQty(id, delta) {
    // This would ideally be handled via AJAX in main.js
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'cart-handler.php';
    
    // For simplicity in this demo, we'll use a direct redirect/refresh approach or AJAX
    // I'll update main.js with AJAX functions and call them here.
    const currentQty = <?php echo json_encode(array_values(array_intersect_key($cart_items, array_flip([$prod['id']])))[0] ?? 0); ?>; 
    // Actually, I'll use the AJAX updateQty function I'll write in main.js
    ajaxUpdateQty(id, delta);
}

function removeFromCart(id) {
    ajaxRemoveFromCart(id);
}
</script>

<?php require_once 'includes/footer.php'; ?>
