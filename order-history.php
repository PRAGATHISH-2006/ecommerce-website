<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if(!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="fw-bold">Order History</h1>
            <p class="text-muted">Manage and track your previous purchases</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Order ID</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Total Amount</th>
                                    <th class="py-3">Payment Method</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="pe-4 py-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">#<?php echo $order['id']; ?></td>
                                        <td class="text-muted small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td class="fw-bold">$<?php echo number_format($order['total_price'], 2); ?></td>
                                        <td>
                                            <span class="small text-muted"><i class="bi bi-wallet2 me-1"></i> <?php echo $order['payment_method']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $badge_class = 'bg-warning';
                                            if($order['status'] == 'delivered') $badge_class = 'bg-success';
                                            if($order['status'] == 'shipped') $badge_class = 'bg-info';
                                            if($order['status'] == 'cancelled') $badge_class = 'bg-danger';
                                            ?>
                                            <span class="badge rounded-pill <?php echo $badge_class; ?> px-3 py-2">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button class="btn btn-light btn-sm rounded-pill fw-bold px-3">View Details</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-box-seam display-1 text-muted mb-4 d-block"></i>
                    <h3>No orders yet!</h3>
                    <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your history here.</p>
                    <a href="products/index.php" class="btn btn-custom btn-lg">Browse Shop</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
