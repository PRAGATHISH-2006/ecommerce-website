<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!isset($_SESSION['checkout_success_order_id'])) {
    header("Location: ../index.php");
    exit;
}
$order_id = $_SESSION['checkout_success_order_id'];
unset($_SESSION['checkout_success_order_id']);
?>

<div class="container py-5 text-center" style="min-height: 60vh;">
    <div class="mb-4 mt-5">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    </div>
    <h1 class="fw-bold mb-3">Order Confirmed!</h1>
    <p class="text-muted fs-5 mb-4">Thank you for your purchase. Your order <span class="fw-bold text-dark">#<?php echo $order_id; ?></span> has been placed successfully.</p>
    
    <div class="d-flex justify-content-center gap-3">
        <a href="../order-history.php" class="btn btn-outline-primary rounded-pill px-4">View Orders</a>
        <a href="../products/index.php" class="btn btn-primary rounded-pill px-4 shadow-sm">Continue Shopping</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
