<?php 
require_once 'includes/db_connect.php';
require_once 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; background: var(--secondary-gradient); color: var(--dark-color); border-radius: 0 0 50px 50px; margin-bottom: 4rem;">
    <div class="container">
        <h1 class="display-4 fw-bold animate__animated animate__fadeInDown">Track Your Order</h1>
        <p class="lead mt-3 animate__animated animate__fadeInUp">Enter your details below to see the current status of your shipment.</p>
    </div>
</section>

<!-- Tracking Form Section -->
<section class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold mt-2">Find Your Package</h3>
                </div>
                <form action="" method="GET">
                    <div class="mb-4">
                        <label for="order_id" class="form-label fw-bold">Order ID Number</label>
                        <input type="text" class="form-control form-control-lg" id="order_id" name="order_id" required placeholder="e.g. ORD-123456789">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Billing Email Address</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required placeholder="john@example.com">
                    </div>
                    <button type="submit" class="btn btn-custom btn-lg w-100">Track Order</button>
                </form>
            </div>
            
            <div class="mt-4 text-center text-muted small">
                <p>If you don't know your order number, please check your confirmation email or contact our <a href="contact.php" class="text-primary text-decoration-none">support team</a>.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>