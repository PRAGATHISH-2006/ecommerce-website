<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if cart is empty
$q = "SELECT id FROM cart_items WHERE user_id = $user_id";
if (mysqli_num_rows(mysqli_query($conn, $q)) == 0) {
    header("Location: ../cart/index.php");
    exit;
}

// Fetch user addresses
$addresses = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = $user_id ORDER BY is_default DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_address'])) {
    $address_id = (int)$_POST['address_id'];
    $_SESSION['checkout_address_id'] = $address_id;
    header("Location: summary.php");
    exit;
}
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold">Checkout: Shipping Address</h2>
            <div class="progress mt-3 mx-auto" style="width: 50%; height: 5px;">
                <div class="progress-bar bg-primary" style="width: 33%;"></div>
            </div>
            <div class="d-flex justify-content-between mt-2 mx-auto text-muted small fw-bold" style="width: 50%;">
                <span class="text-primary">Address</span>
                <span>Summary & Payment</span>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="index.php" method="POST">
                <div class="row g-4">
                    <?php if (mysqli_num_rows($addresses) > 0): ?>
                        <?php while($addr = mysqli_fetch_assoc($addresses)): ?>
                            <div class="col-md-6">
                                <label class="card border p-3 rounded-4 h-100 cursor-pointer shadow-sm" style="cursor:pointer;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_id" value="<?php echo $addr['id']; ?>" <?php echo $addr['is_default'] ? 'checked' : ''; ?> required>
                                        <span class="fw-bold ms-2"><?php echo htmlspecialchars($addr['full_name']); ?></span>
                                        <?php if($addr['is_default']): ?>
                                            <span class="badge bg-primary ms-2">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-3 text-muted small">
                                        <?php echo htmlspecialchars($addr['street_address']); ?><br>
                                        <?php if($addr['apartment']) echo htmlspecialchars($addr['apartment']) . '<br>'; ?>
                                        <?php echo htmlspecialchars($addr['city']) . ', ' . htmlspecialchars($addr['state']) . ' ' . htmlspecialchars($addr['postal_code']); ?><br>
                                        <?php echo htmlspecialchars($addr['country']); ?><br>
                                        Phone: <?php echo htmlspecialchars($addr['phone_number']); ?>
                                    </div>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-4 bg-light rounded-4">
                            <p class="text-muted mb-3">You don't have any saved addresses.</p>
                            <!-- We can link to profile to add an address, or include an add form. For simplicity, just link to profile (assuming Phase 6 will cover this, or we just add a quick add button here) -->
                            <a href="../profile.php" class="btn btn-outline-primary rounded-pill px-4">Go to Profile to Add Address</a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (mysqli_num_rows($addresses) > 0): ?>
                    <div class="d-flex justify-content-end mt-5">
                        <a href="../cart/index.php" class="btn btn-light px-4 me-3 rounded-pill fw-bold">Back to Cart</a>
                        <button type="submit" name="select_address" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">Continue to Summary</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
