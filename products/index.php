<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Build Query based on filters
$search = isset($_GET['search']) ? trim(mysqli_real_escape_string($conn, $_GET['search'])) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand = isset($_GET['brand']) ? trim(mysqli_real_escape_string($conn, $_GET['brand'])) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$where_clauses = ["p.status = 'active'"];
if ($search) {
    $where_clauses[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.brand LIKE '%$search%')";
}
if ($category > 0) {
    $where_clauses[] = "p.category_id = $category";
}
if ($brand) {
    $where_clauses[] = "p.brand = '$brand'";
}
if ($min_price > 0) {
    $where_clauses[] = "p.price >= $min_price";
}
if ($max_price > 0 && $max_price > $min_price) {
    $where_clauses[] = "p.price <= $max_price";
}

$where_sql = "WHERE " . implode(" AND ", $where_clauses);

$order_sql = "ORDER BY p.created_at DESC";
switch ($sort) {
    case 'price_asc':
        $order_sql = "ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $order_sql = "ORDER BY p.price DESC";
        break;
    case 'oldest':
        $order_sql = "ORDER BY p.created_at ASC";
        break;
}

$prod_query = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id $where_sql $order_sql";
$prod_result = mysqli_query($conn, $prod_query);

// Fetch categories for sidebar
$cat_result = mysqli_query($conn, "SELECT * FROM categories WHERE status='active'");

// Fetch distinct brands for sidebar
$brand_result = mysqli_query($conn, "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' AND status='active'");
?>

<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h1 class="fw-bold fs-2 mb-0">Our Collection</h1>
            <p class="text-muted">Explore our wide range of premium products</p>
        </div>
        <div class="col-md-6">
            <form action="index.php" method="GET" class="d-flex gap-2">
                <input type="hidden" name="category" value="<?php echo $category; ?>">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 shadow-sm"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-sm ps-0" placeholder="Search products, brands, SKU..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn btn-custom px-4">Search</button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <form action="index.php" method="GET">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Filters</h5>
                        <a href="index.php" class="text-muted small text-decoration-none">Clear</a>
                    </div>
                    
                    <h6 class="fw-bold mb-3">Categories</h6>
                    <div class="list-group list-group-flush mb-4">
                        <a href="index.php?search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" class="list-group-item list-group-item-action border-0 px-0 <?php echo $category == 0 ? 'fw-bold text-primary' : ''; ?>">
                            All Categories
                        </a>
                        <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                            <a href="index.php?category=<?php echo $cat['id']; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" class="list-group-item list-group-item-action border-0 px-0 <?php echo $category == $cat['id'] ? 'fw-bold text-primary' : ''; ?>">
                                <i class="bi <?php echo $cat['icon']; ?> me-2"></i> <?php echo $cat['name']; ?>
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <h6 class="fw-bold mb-3">Brands</h6>
                    <select name="brand" class="form-select mb-4 bg-light border-0" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        <?php while($b = mysqli_fetch_assoc($brand_result)): ?>
                            <option value="<?php echo htmlspecialchars($b['brand']); ?>" <?php echo $brand === $b['brand'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['brand']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <h6 class="fw-bold mb-3">Price Range ($)</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control bg-light border-0" placeholder="Min" value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control bg-light border-0" placeholder="Max" value="<?php echo $max_price > 0 ? $max_price : ''; ?>">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Sort By</h6>
                    <select name="sort" class="form-select mb-4 bg-light border-0" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest</option>
                        <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Apply Filters</button>
                </div>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="row g-4">
                <?php if($prod_result && mysqli_num_rows($prod_result) > 0): ?>
                    <?php while($prod = mysqli_fetch_assoc($prod_result)): ?>
                    <div class="col-md-4">
                        <div class="product-card">
                            <div class="position-relative overflow-hidden">
                                <a href="details.php?id=<?php echo $prod['id']; ?>">
                                    <img src="<?php echo SITE_URL . '/' . (empty($prod['image']) ? 'assets/images/default.jpg' : $prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="product-image w-100">
                                </a>
                                <?php if($prod['discount_price']): ?>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-2 py-1">SALE</span>
                                <?php endif; ?>
                                <div class="position-absolute bottom-0 start-0 p-3 w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);">
                                    <div class="d-flex gap-2">
                                        <a href="details.php?id=<?php echo $prod['id']; ?>" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Quick View"><i class="bi bi-eye"></i></a>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" title="Wishlist"><i class="bi bi-heart"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="product-details">
                                <span class="badge bg-light text-primary mb-2"><?php echo htmlspecialchars($prod['cat_name']); ?></span>
                                <?php if($prod['brand']): ?>
                                    <span class="badge bg-light text-secondary mb-2"><?php echo htmlspecialchars($prod['brand']); ?></span>
                                <?php endif; ?>
                                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($prod['name']); ?></h5>
                                <div class="mb-3">
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-half text-warning small"></i>
                                    <span class="text-muted small ms-1">(4.5)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if($prod['discount_price'] > 0): ?>
                                            <span class="fs-4 fw-bold text-primary">$<?php echo number_format($prod['discount_price'], 2); ?></span>
                                            <span class="text-muted text-decoration-line-through small ms-1">$<?php echo number_format($prod['price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="fs-4 fw-bold text-primary">$<?php echo number_format($prod['price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($prod['stock'] > 0): ?>
                                        <button onclick="addToCart(<?php echo $prod['id']; ?>)" class="btn btn-custom px-3">
                                            <i class="bi bi-cart-plus me-1"></i> Add
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary px-3" disabled>Out of Stock</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted">No products found matching your criteria.</h4>
                        <a href="index.php" class="btn btn-primary mt-3">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
