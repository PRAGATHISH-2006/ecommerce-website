<?php
require_once '../includes/db_connect.php';

$email_err = $success_msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    if(empty($email_err)){
        $sql = "SELECT id FROM users WHERE email = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Generate reset token
                    $token = bin2hex(random_bytes(50));
                    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
                    
                    $update_sql = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?";
                    if($update_stmt = mysqli_prepare($conn, $update_sql)){
                        mysqli_stmt_bind_param($update_stmt, "sss", $token, $expires, $email);
                        mysqli_stmt_execute($update_stmt);
                        mysqli_stmt_close($update_stmt);
                    }
                    
                    // In a real app, send email here. For now we simulate it.
                    $reset_link = SITE_URL . "/auth/reset-password.php?token=" . $token;
                    $success_msg = "Password reset instructions have been sent to your email. <br><a href='$reset_link'>Click here to reset (Simulation)</a>";
                } else {
                    $email_err = "No account found with that email.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}
require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="p-5" style="background: var(--primary-gradient); color: white; text-align: center;">
                    <h2 class="fw-bold">Forgot Password</h2>
                    <p>Enter your email to reset password</p>
                </div>
                <div class="card-body p-5">
                    <?php if(!empty($success_msg)): ?>
                        <div class="alert alert-success"><?php echo $success_msg; ?></div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0 <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" placeholder="name@example.com">
                            <span class="invalid-feedback d-block"><?php echo $email_err; ?></span>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-custom btn-lg py-3">Send Reset Link</button>
                        </div>
                        <p class="text-center mb-0"><a href="login.php" class="text-decoration-none fw-bold">Back to Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
