<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = $id";
$result = mysqli_query($conn, $query);
$prod = mysqli_fetch_assoc($result);

if(!$prod) {
    header("location: products.php");
    exit;
}
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Shop</a></li>
            <li class="breadcrumb-item active"><?php echo $prod['cat_name']; ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 30px;">
                <img src="https://via.placeholder.com/800x800" alt="<?php echo $prod['name']; ?>" class="img-fluid w-100">
            </div>
            <div class="row mt-4 g-3">
                <div class="col-3">
                    <img src="https://via.placeholder.com/200x200" class="img-fluid rounded-3 border">
                </div>
                <div class="col-3">
                    <img src="https://via.placeholder.com/200x200" class="img-fluid rounded-3 border">
                </div>
                <div class="col-3">
                    <img src="https://via.placeholder.com/200x200" class="img-fluid rounded-3 border">
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="ps-lg-5">
                <span class="badge bg-light text-primary mb-3 px-3 py-2 fs-6"><?php echo $prod['cat_name']; ?></span>
                <h1 class="display-4 fw-bold mb-3"><?php echo $prod['name']; ?></h1>
                <div class="d-flex align-items-center mb-4">
                    <div class="fs-5 text-warning me-2">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <span class="text-muted">(120 customer reviews)</span>
                </div>
                
                <h3 class="text-primary fw-bold mb-4 fs-2">$<?php echo $prod['price']; ?></h3>
                
                <p class="text-muted fs-5 mb-5 lh-lg">
                    <?php echo $prod['description']; ?>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </p>

                <div class="mb-5">
                    <h6 class="fw-bold mb-3">Quantity</h6>
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary border-0 bg-light" type="button" onclick="const q = document.getElementById('qty'); if(q.value > 1) q.value--;">-</button>
                        <input type="number" id="qty" class="form-control border-0 bg-light text-center fw-bold" value="1" min="1">
                        <button class="btn btn-outline-secondary border-0 bg-light" type="button" onclick="const q = document.getElementById('qty'); q.value++;">+</button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <button onclick="addToCart(<?php echo $prod['id']; ?>)" class="btn btn-custom btn-lg w-100 py-3 shadow">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-dark btn-lg w-100 py-3 shadow">
                            <i class="bi bi-lightning-fill me-2"></i> Buy Now
                        </button>
                    </div>
                </div>

                <div class="mt-5 p-4 border rounded-4 bg-light">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-truck fs-3 text-primary me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Free Shipping</h6>
                            <p class="mb-0 small text-muted">On all orders over $100</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-check fs-3 text-primary me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Secure Payment</h6>
                            <p class="mb-0 small text-muted">100% secure payment methods</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<section class="container py-5 mt-5">
    <h3 class="fw-bold mb-5">Product Reviews</h3>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/50?u=1" class="rounded-circle me-3">
                    <div>
                        <h6 class="fw-bold mb-0">John Doe</h6>
                        <small class="text-muted">2 days ago</small>
                    </div>
                </div>
                <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="mb-0">Absolutely amazing product! The quality is top-notch and it exceeds my expectations.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://i.pravatar.cc/50?u=2" class="rounded-circle me-3">
                    <div>
                        <h6 class="fw-bold mb-0">Jane Smith</h6>
                        <small class="text-muted">1 week ago</small>
                    </div>
                </div>
                <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></div>
                <p class="mb-0">Great value for money. Highly recommended for anyone looking for style and functionality.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
