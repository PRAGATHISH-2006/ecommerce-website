<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = $id AND p.status='active'";
$result = mysqli_query($conn, $query);
$prod = mysqli_fetch_assoc($result);

if(!$prod) {
    header("location: index.php");
    exit;
}

// Fetch Variants
$var_query = "SELECT * FROM product_variants WHERE product_id = $id AND status='active'";
$var_result = mysqli_query($conn, $var_query);
$variants = [];
while($row = mysqli_fetch_assoc($var_result)){
    $variants[] = $row;
}

// Fetch extra images
$img_query = "SELECT image_path FROM product_images WHERE product_id = $id ORDER BY is_primary DESC";
$img_result = mysqli_query($conn, $img_query);
$images = [];
if ($prod['image']) {
    $images[] = SITE_URL . '/' . $prod['image']; // Main image
}
while($row = mysqli_fetch_assoc($img_result)){
    $images[] = SITE_URL . '/' . $row['image_path'];
}
if(empty($images)) $images[] = SITE_URL . '/assets/images/default.jpg';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php">Shop</a></li>
            <li class="breadcrumb-item"><a href="index.php?category=<?php echo $prod['category_id']; ?>"><?php echo htmlspecialchars($prod['cat_name']); ?></a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($prod['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image Gallery -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm overflow-hidden mb-3" style="border-radius: 30px;">
                <img src="<?php echo $images[0]; ?>" id="mainImage" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="img-fluid w-100">
            </div>
            <?php if(count($images) > 1): ?>
            <div class="row g-3">
                <?php foreach($images as $img): ?>
                <div class="col-3">
                    <img src="<?php echo $img; ?>" class="img-fluid rounded-3 border" style="cursor:pointer;" onclick="document.getElementById('mainImage').src=this.src;">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="ps-lg-5">
                <span class="badge bg-light text-primary mb-3 px-3 py-2 fs-6"><?php echo htmlspecialchars($prod['cat_name']); ?></span>
                <?php if($prod['brand']): ?>
                    <span class="badge bg-light text-secondary mb-3 px-3 py-2 fs-6 ms-2"><?php echo htmlspecialchars($prod['brand']); ?></span>
                <?php endif; ?>
                <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($prod['name']); ?></h1>
                
                <?php if($prod['sku']): ?>
                    <p class="text-muted small mb-2">SKU: <?php echo htmlspecialchars($prod['sku']); ?></p>
                <?php endif; ?>

                <div class="d-flex align-items-center mb-4">
                    <div class="fs-5 text-warning me-2">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <span class="text-muted">(120 reviews)</span>
                </div>
                
                <h3 class="text-primary fw-bold mb-4 fs-2">
                    <?php if($prod['discount_price'] > 0): ?>
                        $<span id="displayPrice"><?php echo number_format($prod['discount_price'], 2); ?></span>
                        <small class="text-muted text-decoration-line-through fs-5 ms-2">$<?php echo number_format($prod['price'], 2); ?></small>
                        <?php 
                            $saved = $prod['price'] - $prod['discount_price'];
                            $pct = round(($saved / $prod['price']) * 100);
                        ?>
                        <span class="badge bg-success ms-2 fs-6"><?php echo $pct; ?>% OFF</span>
                    <?php else: ?>
                        $<span id="displayPrice"><?php echo number_format($prod['price'], 2); ?></span>
                    <?php endif; ?>
                </h3>
                
                <div class="text-muted fs-5 mb-4 lh-lg">
                    <?php echo nl2br(htmlspecialchars($prod['description'])); ?>
                </div>

                <?php if(!empty($variants)): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Available Variants</h6>
                        <select id="variantSelect" class="form-select bg-light border-0 py-2 w-75" onchange="updateVariantDetails()">
                            <option value="">Select an option</option>
                            <?php foreach($variants as $v): ?>
                                <?php 
                                    $label = [];
                                    if($v['size']) $label[] = "Size: " . $v['size'];
                                    if($v['color']) $label[] = "Color: " . $v['color'];
                                    if($v['storage']) $label[] = "Storage: " . $v['storage'];
                                    $label_str = implode(" | ", $label);
                                ?>
                                <option value="<?php echo $v['id']; ?>" data-price="<?php echo $v['price']; ?>" data-stock="<?php echo $v['stock']; ?>"><?php echo htmlspecialchars($label_str); ?> - $<?php echo $v['price']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="mb-5">
                    <h6 class="fw-bold mb-3">Quantity</h6>
                    <div class="d-flex align-items-center">
                        <div class="input-group me-3" style="width: 150px;">
                            <button class="btn btn-outline-secondary border-0 bg-light" type="button" onclick="const q = document.getElementById('qty'); if(q.value > 1) q.value--;">-</button>
                            <input type="number" id="qty" class="form-control border-0 bg-light text-center fw-bold" value="1" min="1">
                            <button class="btn btn-outline-secondary border-0 bg-light" type="button" onclick="const q = document.getElementById('qty'); q.value++;">+</button>
                        </div>
                        <span id="stockStatus" class="fw-bold <?php echo $prod['stock'] > 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $prod['stock'] > 0 ? 'In Stock (' . $prod['stock'] . ' available)' : 'Out of Stock'; ?>
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <button onclick="addCurrentToCart()" class="btn btn-custom btn-lg w-100 py-3 shadow" <?php echo $prod['stock'] == 0 ? 'disabled' : ''; ?> id="addToCartBtn">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-danger btn-lg w-100 py-3 shadow">
                            <i class="bi bi-heart me-2"></i> Add to Wishlist
                        </button>
                    </div>
                </div>

                <?php if($prod['specifications']): ?>
                <div class="mt-5">
                    <h5 class="fw-bold mb-3">Specifications</h5>
                    <div class="bg-light p-4 rounded-4 text-muted">
                        <?php echo nl2br(htmlspecialchars($prod['specifications'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
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

<script>
function updateVariantDetails() {
    const sel = document.getElementById('variantSelect');
    if(!sel || sel.value === "") return;
    
    const opt = sel.options[sel.selectedIndex];
    const price = opt.getAttribute('data-price');
    const stock = parseInt(opt.getAttribute('data-stock'));
    
    document.getElementById('displayPrice').innerText = price;
    
    const status = document.getElementById('stockStatus');
    const btn = document.getElementById('addToCartBtn');
    if(stock > 0) {
        status.className = 'fw-bold text-success';
        status.innerText = 'In Stock (' + stock + ' available)';
        btn.disabled = false;
    } else {
        status.className = 'fw-bold text-danger';
        status.innerText = 'Out of Stock';
        btn.disabled = true;
    }
}

function addCurrentToCart() {
    const qty = document.getElementById('qty').value;
    const sel = document.getElementById('variantSelect');
    let variant_id = 0;
    if(sel && sel.value !== "") {
        variant_id = sel.value;
    }
    
    // Call the original addToCart logic, adapted to support variants in Phase 4
    console.log("Adding product <?php echo $prod['id']; ?> to cart. Qty: " + qty + ", Variant: " + variant_id);
    alert("Added to cart! (Cart functionality update in Phase 4)");
}
</script>

<?php require_once '../includes/footer.php'; ?>
