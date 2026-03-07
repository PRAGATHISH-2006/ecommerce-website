<?php
require_once '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

$success_msg = "";

// Handle Role Update
if(isset($_GET['toggle_role'])) {
    $user_id = (int)$_GET['toggle_role'];
    $current_role = mysqli_fetch_row(mysqli_query($conn, "SELECT role FROM users WHERE id = $user_id"))[0];
    $new_role = ($current_role == 'admin') ? 'user' : 'admin';
    
    if(mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE id = $user_id")) {
        $success_msg = "User role updated successfully.";
    }
}

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - VibrantShop Admin</title>
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
        <a class="nav-link" href="manage-orders.php"><i class="bi bi-cart-check me-3"></i> View Orders</a>
        <a class="nav-link active" href="manage-users.php"><i class="bi bi-people me-3"></i> Manage Users</a>
        <hr class="my-4 text-secondary">
        <a class="nav-link" href="../index.php"><i class="bi bi-house me-3"></i> Back to Site</a>
        <a class="nav-link text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
    </nav>
</div>

<main class="admin-main">
    <div class="mb-5">
        <h1 class="fw-bold mb-0">Manage Users</h1>
        <p class="text-muted">View platform users and manage access levels</p>
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
                        <th class="ps-4">User ID</th>
                        <th>User Info</th>
                        <th>Joined Date</th>
                        <th>Role</th>
                        <th class="pe-4 text-end">Security</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td class="ps-4 text-muted">#<?php echo $user['id']; ?></td>
                        <td>
                            <div class="fw-bold"><?php echo $user['username']; ?></div>
                            <small class="text-muted"><?php echo $user['email']; ?></small>
                        </td>
                        <td class="text-muted small"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <span class="badge rounded-pill <?php echo ($user['role'] == 'admin') ? 'bg-primary' : 'bg-light text-dark'; ?> px-3">
                                <?php echo strtoupper($user['role']); ?>
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <?php if($user['username'] !== 'admin'): // Prevent removing own admin status if you are the main admin ?>
                                <a href="?toggle_role=<?php echo $user['id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold">
                                    Toggle Role
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">System Owner</span>
                            <?php endif; ?>
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
