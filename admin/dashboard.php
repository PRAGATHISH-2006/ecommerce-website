<?php
require_once '../includes/db_connect.php';
session_start();

// Admin access only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Fetch stats safely
function get_count($conn, $query) {
    $res = mysqli_query($conn, $query);
    if($res) {
        $row = mysqli_fetch_row($res);
        return $row[0] ?? 0;
    }
    return 0;
}

$product_count = get_count($conn, "SELECT COUNT(*) FROM products");
$user_count = get_count($conn, "SELECT COUNT(*) FROM users");
$order_count = get_count($conn, "SELECT COUNT(*) FROM orders");
$total_revenue = get_count($conn, "SELECT SUM(total_price) FROM orders");

// Fetch recent orders
$recent_orders = mysqli_query($conn, "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VibrantShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar {
            height: 100vh;
            background: var(--dark-color);
            color: white;
            padding: 2rem;
            position: fixed;
            width: 280px;
        }
        .admin-main {
            margin-left: 280px;
            padding: 3rem;
            background: #f0f2f5;
            min-height: 100vh;
        }
        .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .nav-link.active {
            background: var(--primary-gradient);
        }
        .stat-card {
            border: none;
            border-radius: 24px;
            padding: 2rem;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="admin-sidebar">
    <h3 class="fw-bold mb-5"><i class="bi bi-lightning-charge-fill text-primary"></i> Vibrant<span class="text-primary">Admin</span></h3>
    <nav class="nav flex-column">
        <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
        <a class="nav-link" href="manage-products.php"><i class="bi bi-box-seam me-3"></i> Manage Products</a>
        <a class="nav-link" href="manage-orders.php"><i class="bi bi-cart-check me-3"></i> View Orders</a>
        <a class="nav-link" href="manage-users.php"><i class="bi bi-people me-3"></i> Manage Users</a>
        <hr class="my-4 text-secondary">
        <a class="nav-link" href="../index.php"><i class="bi bi-house me-3"></i> Back to Site</a>
        <a class="nav-link text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
    </nav>
</div>

<main class="admin-main">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-0">Dashboard Overview</h1>
            <p class="text-muted">Welcome back, Super Admin!</p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-white shadow-sm rounded-pill px-4"><i class="bi bi-bell me-2"></i> Update</button>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm">Generate Report</button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card bg-white shadow-sm">
                <span class="text-muted small fw-bold text-uppercase">Total Revenue</span>
                <h2 class="fw-bold mt-2 display-6 text-primary">$<?php echo number_format($total_revenue, 2); ?></h2>
                <div class="text-success small mt-2"><i class="bi bi-arrow-up"></i> 15% increase</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-white shadow-sm">
                <span class="text-muted small fw-bold text-uppercase">Active Users</span>
                <h2 class="fw-bold mt-2 display-6"><?php echo $user_count; ?></h2>
                <div class="text-success small mt-2"><i class="bi bi-arrow-up"></i> 8% increase</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-white shadow-sm">
                <span class="text-muted small fw-bold text-uppercase">Total Orders</span>
                <h2 class="fw-bold mt-2 display-6"><?php echo $order_count; ?></h2>
                <div class="text-primary small mt-2"><i class="bi bi-graph-up"></i> Growing daily</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-white shadow-sm">
                <span class="text-muted small fw-bold text-uppercase">Total Products</span>
                <h2 class="fw-bold mt-2 display-6"><?php echo $product_count; ?></h2>
                <div class="text-muted small mt-2">Recently updated</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Recent Orders</h5>
                    <a href="manage-orders.php" class="text-decoration-none small fw-bold">View all orders</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Order ID</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = mysqli_fetch_assoc($recent_orders)): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['username']; ?></td>
                                <td class="fw-bold">$<?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <?php
                                    $badge = 'bg-warning';
                                    if($order['status'] == 'delivered') $badge = 'bg-success';
                                    ?>
                                    <span class="badge rounded-pill <?php echo $badge; ?> px-3 small">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?php echo date('M d', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-bold mb-4">Popular Categories</h5>
                <?php
                $cat_q = mysqli_query($conn, "SELECT c.name, COUNT(p.id) as p_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id LIMIT 4");
                while($cat = mysqli_fetch_assoc($cat_q)):
                ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold"><?php echo $cat['name']; ?></span>
                        <span class="small text-muted"><?php echo $cat['p_count']; ?> Products</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-primary" style="width: <?php echo ($cat['p_count']*20); ?>%"></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
