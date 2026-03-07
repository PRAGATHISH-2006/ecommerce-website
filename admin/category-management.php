<?php
require_once '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("location: ../login.php");
    exit;
}

$success_msg = "";

// Handle Add/Edit Category
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);
    
    if(isset($_POST['category_id']) && !empty($_POST['category_id'])) {
        $id = (int)$_POST['category_id'];
        $sql = "UPDATE categories SET name='$name', icon='$icon' WHERE id=$id";
    } else {
        $sql = "INSERT INTO categories (name, icon) VALUES ('$name', '$icon')";
    }
    
    if(mysqli_query($conn, $sql)) {
        $success_msg = "Category saved successfully.";
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - VibrantShop Admin</title>
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
        <a class="nav-link active" href="category-management.php"><i class="bi bi-tags me-3"></i> Manage Categories</a>
        <a class="nav-link" href="manage-orders.php"><i class="bi bi-cart-check me-3"></i> View Orders</a>
        <a class="nav-link" href="manage-users.php"><i class="bi bi-people me-3"></i> Manage Users</a>
        <hr class="my-4 text-secondary">
        <a class="nav-link" href="../index.php"><i class="bi bi-house me-3"></i> Back to Site</a>
    </nav>
</div>

<main class="admin-main">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-0">Manage Categories</h1>
            <p class="text-muted">Organize your products with categories</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg me-2"></i> Add New Category
        </button>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Icon</th>
                            <th>Category Name</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <tr>
                            <td class="ps-4">#<?php echo $cat['id']; ?></td>
                            <td><i class="bi <?php echo $cat['icon']; ?> fs-4 text-primary"></i></td>
                            <td class="fw-bold"><?php echo $cat['name']; ?></td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-light btn-sm rounded-pill" onclick='editCategory(<?php echo json_encode($cat); ?>)'><i class="bi bi-pencil"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="catModalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="category-management.php" method="POST" id="catForm">
                    <input type="hidden" name="category_id" id="cat_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Category Name</label>
                        <input type="text" name="name" id="cat_name" class="form-control bg-light border-0 py-3" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Bootstrap Icon Class</label>
                        <input type="text" name="icon" id="cat_icon" class="form-control bg-light border-0 py-3" placeholder="e.g. bi-laptop" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="save_category" class="btn btn-primary btn-lg rounded-pill py-3 fw-bold">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editCategory(cat) {
    document.getElementById('catModalTitle').innerText = 'Edit Category';
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_name').value = cat.name;
    document.getElementById('cat_icon').value = cat.icon;
    var modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    modal.show();
}
</script>
</body>
</html>
