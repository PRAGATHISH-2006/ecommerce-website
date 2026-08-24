<?php
require_once '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("location: ../auth/login.php");
    exit;
}

$success_msg = "";
$error_msg = "";

if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if(mysqli_query($conn, "DELETE FROM coupons WHERE id = $id")) {
        $success_msg = "Coupon deleted successfully.";
    } else {
        $error_msg = "Error deleting coupon.";
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_coupon'])) {
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $type = mysqli_real_escape_string($conn, $_POST['discount_type']);
    $val = (float)$_POST['discount_value'];
    $min_order = (float)$_POST['minimum_order'];
    $max_disc = isset($_POST['maximum_discount']) && $_POST['maximum_discount'] !== '' ? (float)$_POST['maximum_discount'] : 'NULL';
    $limit = isset($_POST['usage_limit']) && $_POST['usage_limit'] !== '' ? (int)$_POST['usage_limit'] : 'NULL';
    $start = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if(isset($_POST['coupon_id']) && !empty($_POST['coupon_id'])) {
        $id = (int)$_POST['coupon_id'];
        $sql = "UPDATE coupons SET code='$code', discount_type='$type', discount_value=$val, minimum_order=$min_order, maximum_discount=$max_disc, usage_limit=$limit, start_date='$start', expiry_date='$end', status='$status' WHERE id=$id";
    } else {
        $sql = "INSERT INTO coupons (code, discount_type, discount_value, minimum_order, maximum_discount, usage_limit, start_date, expiry_date, status) VALUES ('$code', '$type', $val, $min_order, $max_disc, $limit, '$start', '$end', '$status')";
    }
    
    if(mysqli_query($conn, $sql)) {
        $success_msg = "Coupon saved successfully.";
    } else {
        $error_msg = "Error saving coupon (Code might not be unique).";
    }
}

$coupons = mysqli_query($conn, "SELECT * FROM coupons ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Coupons - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
        <a class="nav-link" href="category-management.php"><i class="bi bi-tags me-3"></i> Manage Categories</a>
        <a class="nav-link" href="manage-orders.php"><i class="bi bi-cart-check me-3"></i> View Orders</a>
        <a class="nav-link active" href="coupons.php"><i class="bi bi-ticket-perforated me-3"></i> Coupons</a>
        <a class="nav-link" href="manage-users.php"><i class="bi bi-people me-3"></i> Manage Users</a>
        <hr class="my-4 text-secondary">
        <a class="nav-link" href="../index.php"><i class="bi bi-house me-3"></i> Back to Site</a>
    </nav>
</div>

<main class="admin-main">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-0">Manage Coupons</h1>
            <p class="text-muted">Create discount codes for your customers</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#couponModal">
            <i class="bi bi-plus-lg me-2"></i> Add Coupon
        </button>
    </div>

    <?php if($success_msg): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm overflow-hidden rounded-4">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Code</th>
                    <th>Discount</th>
                    <th>Min Order</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = mysqli_fetch_assoc($coupons)): ?>
                <tr>
                    <td class="ps-4 fw-bold text-primary"><?php echo $c['code']; ?></td>
                    <td>
                        <?php 
                        echo $c['discount_type'] == 'percentage' ? $c['discount_value'] . '%' : '$' . $c['discount_value']; 
                        if ($c['maximum_discount']) echo " (Max: $" . $c['maximum_discount'] . ")";
                        ?>
                    </td>
                    <td>$<?php echo $c['minimum_order']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($c['expiry_date'])); ?></td>
                    <td>
                        <span class="badge <?php echo $c['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo ucfirst($c['status']); ?>
                        </span>
                    </td>
                    <td class="pe-4 text-end">
                        <button class="btn btn-light btn-sm rounded-pill" onclick='editCoupon(<?php echo json_encode($c); ?>)'><i class="bi bi-pencil"></i></button>
                        <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-light btn-sm rounded-pill text-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="fw-bold modal-title" id="mTitle">Add Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="coupons.php" method="POST" id="cForm">
                    <input type="hidden" name="coupon_id" id="c_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Coupon Code*</label>
                            <input type="text" name="code" id="c_code" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Status</label>
                            <select name="status" id="c_status" class="form-select bg-light border-0 py-2">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Discount Type*</label>
                            <select name="discount_type" id="c_type" class="form-select bg-light border-0 py-2">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Discount Value*</label>
                            <input type="number" step="0.01" name="discount_value" id="c_val" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Minimum Order ($)</label>
                            <input type="number" step="0.01" name="minimum_order" id="c_min" class="form-control bg-light border-0 py-2" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Maximum Discount ($) (Optional)</label>
                            <input type="number" step="0.01" name="maximum_discount" id="c_max" class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Usage Limit (Optional)</label>
                            <input type="number" name="usage_limit" id="c_limit" class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Start Date*</label>
                            <input type="datetime-local" name="start_date" id="c_start" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Expiry Date*</label>
                            <input type="datetime-local" name="expiry_date" id="c_end" class="form-control bg-light border-0 py-2" required>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" name="save_coupon" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">Save Coupon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editCoupon(c) {
    document.getElementById('mTitle').innerText = 'Edit Coupon';
    document.getElementById('c_id').value = c.id;
    document.getElementById('c_code').value = c.code;
    document.getElementById('c_type').value = c.discount_type;
    document.getElementById('c_val').value = c.discount_value;
    document.getElementById('c_min').value = c.minimum_order;
    document.getElementById('c_max').value = c.maximum_discount || '';
    document.getElementById('c_limit').value = c.usage_limit || '';
    document.getElementById('c_start').value = c.start_date.slice(0,16);
    document.getElementById('c_end').value = c.expiry_date.slice(0,16);
    document.getElementById('c_status').value = c.status;
    new bootstrap.Modal(document.getElementById('couponModal')).show();
}
document.getElementById('couponModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('cForm').reset();
    document.getElementById('c_id').value = '';
    document.getElementById('mTitle').innerText = 'Add Coupon';
});
</script>
</body>
</html>
