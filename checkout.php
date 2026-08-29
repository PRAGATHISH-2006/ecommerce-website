<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if(!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if(empty($cart_items)) {
    header("location: cart.php");
    exit;
}

$total_price = 0;
$ids = implode(',', array_keys($cart_items));
$query = "SELECT * FROM products WHERE id IN ($ids)";
$result = mysqli_query($conn, $query);
$products = [];
while($row = mysqli_fetch_assoc($result)) {
    $row['qty'] = $cart_items[$row['id']];
    $row['subtotal'] = $row['price'] * $row['qty'];
    $products[] = $row;
    $total_price += $row['subtotal'];
}

$error_msg = "";
$success_msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $user_id = $_SESSION['user_id'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert into orders table
        $order_sql = "INSERT INTO orders (user_id, total_price, address, payment_method) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $order_sql);
        mysqli_stmt_bind_param($stmt, "idss", $user_id, $total_price, $address, $payment_method);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        
        // Insert into order_items table
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = mysqli_prepare($conn, $item_sql);
        foreach($products as $prod) {
            mysqli_stmt_bind_param($stmt_item, "iiid", $order_id, $prod['id'], $prod['qty'], $prod['price']);
            mysqli_stmt_execute($stmt_item);
        }
        mysqli_stmt_close($stmt_item);
        
        mysqli_commit($conn);
        
        // Clear cart
        unset($_SESSION['cart']);
        $success_msg = "Order placed successfully! Order ID: #" . $order_id;
        // Redirect to order confirmation page
        header("refresh:2;url=order-history.php");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_msg = "Failed to place order. Please try again.";
    }
}
?>

<div class="container py-5">
    <?php if(!empty($success_msg)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill display-1 text-success"></i>
            </div>
            <h2 class="fw-bold"><?php echo $success_msg; ?></h2>
            <p class="text-muted">Redirecting you to your order history...</p>
        </div>
    <?php else: ?>
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="fw-bold">Checkout</h1>
                <p class="text-muted">Complete your purchase</p>
            </div>
        </div>

        <div class="row g-5">
            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <h5 class="fw-bold mb-4">Billing Details</h5>
                    <form action="checkout.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">First Name</label>
                                <input type="text" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Name</label>
                                <input type="text" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Shipping Address</label>
                                <textarea name="address" class="form-control bg-light border-0" rows="3" placeholder="Street, City, State, ZIP" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control bg-light border-0" value="<?php echo $_SESSION['username']; ?>@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" class="form-control bg-light border-0" required>
                            </div>
                        </div>

                        <hr class="my-5">

                        <h5 class="fw-bold mb-4">Payment Method (Demo)</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check p-3 border rounded-3 mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" value="Credit Card" id="pay1" checked>
                                    <label class="form-check-label d-flex align-items-center" for="pay1">
                                        <i class="bi bi-credit-card me-3 fs-4 text-primary"></i>
                                        <div>
                                            <span class="fw-bold d-block">Credit/Debit Card</span>
                                            <small class="text-muted">Visa, Mastercard, AMEX</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check p-3 border rounded-3 mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" value="PayPal" id="pay2">
                                    <label class="form-check-label d-flex align-items-center" for="pay2">
                                        <i class="bi bi-paypal me-3 fs-4 text-primary"></i>
                                        <div>
                                            <span class="fw-bold d-block">PayPal</span>
                                            <small class="text-muted">Fast and secure payment</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" name="place_order" class="btn btn-custom btn-lg py-3 shadow">
                                Place Order ($<?php echo number_format($total_price, 2); ?>)
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Review -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-light">
                    <h5 class="fw-bold mb-4">Your Order</h5>
                    <div class="mb-4">
                        <?php foreach($products as $prod): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <img src="https://via.placeholder.com/60x60" class="rounded-3 me-3">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                            <?php echo $prod['qty']; ?>
                                        </span>
                                    </div>
                                    <h6 class="mb-0 small fw-bold"><?php echo $prod['name']; ?></h6>
                                </div>
                                <span class="small fw-bold">$<?php echo number_format($prod['subtotal'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Subtotal</span>
                        <span class="small fw-bold">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Shipping</span>
                        <span class="text-success small fw-bold">Free</span>
                    </div>
                    <div class="d-flex justify-content-between mt-3 mb-2">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
