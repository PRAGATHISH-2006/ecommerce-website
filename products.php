<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// Fetch categories for filter
$cat_query = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_query);

// Handle Filter and Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$where_clauses = [];
if($search) $where_clauses[] = "p.name LIKE '%$search%'";
if($category_filter) $where_clauses[] = "p.category_id = $category_filter";

$where_sql = "";
if(!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$prod_query = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id $where_sql";
$prod_result = mysqli_query($conn, $prod_query);
?>

<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h1 class="fw-bold fs-2 mb-0">Our Collection</h1>
            <p class="text-muted">Explore our wide range of premium products</p>
        </div>
        <div class="col-md-6">
            <form action="products.php" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 shadow-sm"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-sm ps-0" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn btn-custom px-4">Search</button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Categories</h5>
                <div class="list-group list-group-flush">
                    <a href="products.php" class="list-group-item list-group-item-action border-0 px-0 <?php echo $category_filter == 0 ? 'fw-bold text-primary' : ''; ?>">
                        All Categories
                    </a>
                    <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                        <a href="products.php?category=<?php echo $cat['id']; ?>&search=<?php echo urlencode($search); ?>" class="list-group-item list-group-item-action border-0 px-0 <?php echo $category_filter == $cat['id'] ? 'fw-bold text-primary' : ''; ?>">
                            <i class="bi <?php echo $cat['icon']; ?> me-2"></i> <?php echo $cat['name']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
                <hr class="my-4">
                <h5 class="fw-bold mb-4">Price Range</h5>
                <form>
                    <input type="range" class="form-range" min="0" max="2000" id="priceRange">
                    <div class="d-flex justify-content-between">
                        <span class="small">$0</span>
                        <span class="small">$2000</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="row g-4">
                <?php if(mysqli_num_rows($prod_result) > 0): ?>
                    <?php while($prod = mysqli_fetch_assoc($prod_result)): ?>
                    <div class="col-md-4">
                        <div class="product-card">
                            <div class="position-relative overflow-hidden">
                                <a href="product-details.php?id=<?php echo $prod['id']; ?>">
                                    <img src="https://via.placeholder.com/400x400" alt="<?php echo $prod['name']; ?>" class="product-image w-100">
                                </a>
                                <div class="position-absolute bottom-0 start-0 p-3 w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" title="Quick View"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" title="Wishlist"><i class="bi bi-heart"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="product-details">
                                <span class="badge bg-light text-primary mb-2"><?php echo $prod['cat_name']; ?></span>
                                <h5 class="fw-bold mb-1"><?php echo $prod['name']; ?></h5>
                                <div class="mb-3">
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-fill text-warning small"></i>
                                    <i class="bi bi-star-half text-warning small"></i>
                                    <span class="text-muted small ms-1">(4.5)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-4 fw-bold text-primary">$<?php echo $prod['price']; ?></span>
                                    <button onclick="addToCart(<?php echo $prod['id']; ?>)" class="btn btn-custom px-3">
                                        <i class="bi bi-cart-plus me-1"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted">No products found matching your search.</h4>
                        <a href="products.php" class="btn btn-primary mt-3">View All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
