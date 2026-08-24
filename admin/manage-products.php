<?php
require_once '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("location: ../auth/login.php");
    exit;
}

$success_msg = "";
$error_msg = "";

// Handle Delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if(mysqli_query($conn, "DELETE FROM products WHERE id = $id")) {
        $success_msg = "Product deleted successfully.";
    } else {
        $error_msg = "Error deleting product.";
    }
}

// Handle Add/Edit
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = isset($_POST['discount_price']) && $_POST['discount_price'] !== '' ? (float)$_POST['discount_price'] : 'NULL';
    $stock = (int)$_POST['stock'];
    $cat_id = (int)$_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $specifications = mysqli_real_escape_string($conn, $_POST['specifications']);
    
    if(isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        // Update
        $id = (int)$_POST['product_id'];
        $sql = "UPDATE products SET name='$name', description='$desc', price=$price, discount_price=$discount_price, stock=$stock, category_id=$cat_id, brand='$brand', sku='$sku', status='$status', specifications='$specifications' WHERE id=$id";
    } else {
        // Insert
        $sql = "INSERT INTO products (name, description, price, discount_price, stock, category_id, brand, sku, status, specifications, image) VALUES ('$name', '$desc', $price, $discount_price, $stock, $cat_id, '$brand', '$sku', '$status', '$specifications', 'assets/images/default.jpg')";
    }
    
    if(mysqli_query($conn, $sql)) {
        $success_msg = "Product saved successfully.";
    } else {
        $error_msg = "Error saving product: " . mysqli_error($conn);
    }
}

// Fetch all products
$products = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - VibrantShop Admin</title>
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
        <a class="nav-link active" href="manage-products.php"><i class="bi bi-box-seam me-3"></i> Manage Products</a>
        <a class="nav-link" href="manage-orders.php"><i class="bi bi-cart-check me-3"></i> View Orders</a>
        <a class="nav-link" href="manage-users.php"><i class="bi bi-people me-3"></i> Manage Users</a>
        <hr class="my-4 text-secondary">
        <a class="nav-link" href="../index.php"><i class="bi bi-house me-3"></i> Back to Site</a>
        <a class="nav-link text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
    </nav>
</div>

<main class="admin-main">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-0">Manage Products</h1>
            <p class="text-muted">Add, edit, or remove products from your store</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg me-2"></i> Add New Product
        </button>
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
                        <th class="ps-4">ID</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($prod = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td class="ps-4 text-muted">#<?php echo $prod['id']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../<?php echo empty($prod['image']) ? 'assets/images/default.jpg' : $prod['image']; ?>" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div class="fw-bold"><?php echo htmlspecialchars($prod['name']); ?></div>
                            </div>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($prod['sku'] ?? 'N/A'); ?></td>
                        <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($prod['cat_name']); ?></span></td>
                        <td class="fw-bold text-primary">$<?php echo number_format($prod['price'], 2); ?></td>
                        <td>
                            <span class="<?php echo $prod['stock'] < 10 ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo $prod['stock']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $prod['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                <?php echo ucfirst($prod['status']); ?>
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-light btn-sm rounded-pill me-1" onclick='editProduct(<?php echo json_encode($prod); ?>)' title="Edit"><i class="bi bi-pencil"></i></button>
                            <a href="?delete=<?php echo $prod['id']; ?>" class="btn btn-light btn-sm rounded-pill text-danger" onclick="return confirm('Are you sure?')" title="Delete"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="manage-products.php" method="POST" id="productForm">
                    <input type="hidden" name="product_id" id="prod_id">
                    
                    <div class="row g-4">
                        <!-- Left Column: Basic Info -->
                        <div class="col-lg-8">
                            <div class="card border-0 bg-light p-4 rounded-4 h-100">
                                <h6 class="fw-bold mb-3">Basic Information</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Product Name*</label>
                                        <input type="text" name="name" id="prod_name" class="form-control border-0 py-2" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Description</label>
                                        <textarea name="description" id="prod_desc" class="form-control border-0 py-2" rows="4"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Specifications (One per line)</label>
                                        <textarea name="specifications" id="prod_specs" class="form-control border-0 py-2" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Pricing & Organization -->
                        <div class="col-lg-4">
                            <div class="card border-0 bg-light p-4 rounded-4 mb-4">
                                <h6 class="fw-bold mb-3">Pricing & Inventory</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small text-muted">Price ($)*</label>
                                        <input type="number" step="0.01" name="price" id="prod_price" class="form-control border-0 py-2" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted">Discount ($)</label>
                                        <input type="number" step="0.01" name="discount_price" id="prod_discount" class="form-control border-0 py-2">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Stock Quantity*</label>
                                        <input type="number" name="stock" id="prod_stock" class="form-control border-0 py-2" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 bg-light p-4 rounded-4">
                                <h6 class="fw-bold mb-3">Organization</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Category*</label>
                                        <select name="category_id" id="prod_cat" class="form-select border-0 py-2" required>
                                            <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Brand</label>
                                        <input type="text" name="brand" id="prod_brand" class="form-control border-0 py-2">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">SKU</label>
                                        <input type="text" name="sku" id="prod_sku" class="form-control border-0 py-2">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Status</label>
                                        <select name="status" id="prod_status" class="form-select border-0 py-2">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-light px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="save_product" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editProduct(prod) {
    document.getElementById('modalTitle').innerText = 'Edit Product';
    document.getElementById('prod_id').value = prod.id;
    document.getElementById('prod_name').value = prod.name;
    document.getElementById('prod_cat').value = prod.category_id;
    document.getElementById('prod_price').value = prod.price;
    document.getElementById('prod_discount').value = prod.discount_price || '';
    document.getElementById('prod_stock').value = prod.stock;
    document.getElementById('prod_desc').value = prod.description || '';
    document.getElementById('prod_brand').value = prod.brand || '';
    document.getElementById('prod_sku').value = prod.sku || '';
    document.getElementById('prod_status').value = prod.status;
    document.getElementById('prod_specs').value = prod.specifications || '';
    
    var modal = new bootstrap.Modal(document.getElementById('addProductModal'));
    modal.show();
}

// Reset form when modal hidden
document.getElementById('addProductModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('productForm').reset();
    document.getElementById('prod_id').value = '';
    document.getElementById('modalTitle').innerText = 'Add New Product';
});
</script>

</body>
</html>
