<?php 
require_once 'includes/db_connect.php';
require_once 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-text">
                <h1>Discover the Future of <span style="color: #ffde59;">Shopping</span></h1>
                <p class="lead mb-4">Explore our curated collection of premium gadgets and stylish fashion. Experience luxury like never before.</p>
                <div class="d-flex gap-3">
                    <a href="products/index.php" class="btn btn-light btn-lg px-4 fw-bold shadow">Shop Now</a>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-block">
                <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<section class="container py-5 mt-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold mb-0">Shop by Category</h2>
            <p class="text-muted">Find exactly what you're looking for</p>
        </div>
    </div>
    <div class="row g-4">
        <?php
        $cat_query = "SELECT * FROM categories LIMIT 4";
        $cat_result = mysqli_query($conn, $cat_query);
        while($cat = mysqli_fetch_assoc($cat_result)):
        ?>
        <div class="col-md-3 col-6">
            <a href="products/index.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark category-item">
                <div class="card border-0 shadow-sm text-center p-4 h-100" style="border-radius: 20px;">
                    <div class="display-4 text-primary mb-3">
                        <i class="bi <?php echo $cat['icon']; ?>"></i>
                    </div>
                    <h5 class="fw-bold mb-0"><?php echo $cat['name']; ?></h5>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="container py-5 mt-5 bg-light rounded-4 px-4 px-md-5">
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h2 class="fw-bold mb-0">Trending Now</h2>
            <p class="text-muted">Our most popular products this week</p>
        </div>
        <a href="products/index.php" class="text-decoration-none fw-bold text-primary">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        <?php
        $prod_query = "SELECT * FROM products LIMIT 4";
        $prod_result = mysqli_query($conn, $prod_query);
        while($prod = mysqli_fetch_assoc($prod_result)):
        ?>
        <div class="col-md-3">
            <div class="product-card">
                <div class="position-relative overflow-hidden">
                    <img src="https://via.placeholder.com/400x400" alt="<?php echo $prod['name']; ?>" class="product-image w-100">
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge bg-danger rounded-pill">Featured</span>
                    </div>
                </div>
                <div class="product-details">
                    <span class="text-muted small text-uppercase">Gadgets</span>
                    <h5 class="fw-bold mt-1"><?php echo $prod['name']; ?></h5>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fs-5 fw-bold text-primary">$<?php echo $prod['price']; ?></span>
                        <button onclick="addToCart(<?php echo $prod['id']; ?>)" class="btn btn-custom btn-sm">
                            <i class="bi bi-cart-plus me-1"></i> Add
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="container py-5">
    <div class="card border-0 shadow-lg p-5" style="background: var(--primary-gradient); border-radius: 30px;">
        <div class="row align-items-center text-white">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold">Stay in the Loop!</h2>
                <p class="mb-0">Subscribe to our newsletter and get 10% off your first purchase.</p>
            </div>
            <div class="col-lg-6">
                <form class="d-flex gap-2">
                    <input type="email" class="form-control form-control-lg border-0 shadow-sm" placeholder="Enter your email" style="border-radius: 12px;">
                    <button type="submit" class="btn btn-warning btn-lg fw-bold px-4" style="border-radius: 12px;">Join Now</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
