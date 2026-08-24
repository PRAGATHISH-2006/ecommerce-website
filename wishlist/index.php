<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];

// Get or create wishlist
$w_res = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id = $user_id");
if (mysqli_num_rows($w_res) == 0) {
    mysqli_query($conn, "INSERT INTO wishlist (user_id) VALUES ($user_id)");
    $wishlist_id = mysqli_insert_id($conn);
} else {
    $w = mysqli_fetch_assoc($w_res);
    $wishlist_id = $w['id'];
}

// Get items
$query = "
    SELECT wi.id as item_id, p.id as product_id, p.name, p.image, 
           COALESCE(v.price, p.discount_price, p.price) as final_price,
           COALESCE(v.stock, p.stock) as current_stock,
           v.size, v.color, v.storage, v.id as variant_id
    FROM wishlist_items wi
    JOIN products p ON wi.product_id = p.id
    LEFT JOIN product_variants v ON wi.variant_id = v.id
    WHERE wi.wishlist_id = $wishlist_id
    ORDER BY wi.created_at DESC
";
$result = mysqli_query($conn, $query);
$items = [];
while($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold display-5">Your Wishlist</h1>
            <p class="text-muted">Products you've saved for later</p>
        </div>
    </div>

    <?php if(empty($items)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-heart display-1 text-muted"></i>
            </div>
            <h3>Your wishlist is empty!</h3>
            <p class="text-muted mb-4">Explore our collection and add items you love to your wishlist.</p>
            <a href="../products/index.php" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">Explore Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($items as $item): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="position-relative">
                        <img src="../<?php echo empty($item['image']) ? 'assets/images/default.jpg' : $item['image']; ?>" class="card-img-top p-3" style="border-radius: 20px;">
                        <button onclick="removeFromWishlist(<?php echo $item['item_id']; ?>)" class="btn btn-light position-absolute top-0 end-0 m-3 rounded-circle shadow-sm text-danger" title="Remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <?php if($item['size'] || $item['color'] || $item['storage']): ?>
                            <p class="text-muted small mb-2">
                                <?php 
                                    $opts = [];
                                    if($item['size']) $opts[] = $item['size'];
                                    if($item['color']) $opts[] = $item['color'];
                                    if($item['storage']) $opts[] = $item['storage'];
                                    echo implode(' | ', $opts);
                                ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fs-5 fw-bold text-primary">$<?php echo number_format($item['final_price'], 2); ?></span>
                            <?php if($item['current_stock'] > 0): ?>
                                <span class="badge bg-success-subtle text-success">In Stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <button onclick="moveToCart(<?php echo $item['product_id']; ?>, <?php echo $item['variant_id'] ? $item['variant_id'] : 'null'; ?>, <?php echo $item['item_id']; ?>)" class="btn btn-custom w-100 mt-4 rounded-pill" <?php echo $item['current_stock'] <= 0 ? 'disabled' : ''; ?>>
                            Move to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function removeFromWishlist(item_id) {
    if(!confirm('Remove this item from your wishlist?')) return;
    fetch('remove.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'item_id=' + item_id
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) location.reload();
        else alert(data.message);
    });
}

function moveToCart(product_id, variant_id, item_id) {
    // Add to cart
    let bodyData = 'product_id=' + product_id + '&qty=1';
    if(variant_id !== null) bodyData += '&variant_id=' + variant_id;
    
    fetch('../cart/add.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: bodyData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Remove from wishlist automatically
            removeFromWishlist(item_id);
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
