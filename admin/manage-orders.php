<?php
require_once '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

$success_msg = "";

// Handle Status Update
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if(mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $order_id")) {
        $success_msg = "Order #$order_id status updated to $status.";
    }
}

// Fetch all orders
$orders = mysqli_query($conn, "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - VibrantShop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar { height: 100vh; background: var(--dark-color); color: white; padding: 2rem; position: fixed; width: 280px; }
        .admin-main { margin-left: 280px; padding: 3rem; background: #f0f2f5; min-height: 100vh; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 1rem; border-radius: 12px; margin-bottom: 0.5rem; display: flex; align-items: center; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: var(--primary-gradient); }
    </style>
</head>
<body>

<div class="admin-sidebar">
    <h3 class="fw-bold mb-5"><i class="bi bi-lightning-charge-fill text-primary"></i> Vibrant<span class="text-primary">Admin</span></h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
        <a class="nav-link" href="manage-products.php"><i class="bi bi-box-seam me-3"></i> Manage Products</a>
        <a class="nav-link active" href="manage-orders.php"><i class="bi bi-cart-check me-3"></i> View Orders</a>
        <a class="nav-link" href="manage-users.php"><i class="bi bi-people me-3"></i> Manage Users</a>
        <hr class="my-4 text-secondary">
        <a class="nav-link" href="../index.php"><i class="bi bi-house me-3"></i> Back to Site</a>
        <a class="nav-link text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
    </nav>
</div>

<main class="admin-main">
    <div class="mb-5">
        <h1 class="fw-bold mb-0">Manage Orders</h1>
        <p class="text-muted">Track and update customer orders</p>
    </div>

    <?php if(!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td class="ps-4 fw-bold">#<?php echo $order['id']; ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $order['username']; ?></div>
                            <small class="text-muted"><?php echo $order['email']; ?></small>
                        </td>
                        <td class="text-muted small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td class="fw-bold text-primary">$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td>
                            <form action="manage-orders.php" method="POST" class="d-flex gap-2">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-light btn-sm rounded-pill fw-bold" onclick="alert('Viewing order details for #<?php echo $order['id']; ?>')">Details</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
