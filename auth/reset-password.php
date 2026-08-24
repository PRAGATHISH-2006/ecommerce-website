<?php
require_once '../includes/db_connect.php';

$password_err = $success_msg = $token_err = "";

if(isset($_GET["token"]) && !empty(trim($_GET["token"]))){
    $token = trim($_GET["token"]);
} else {
    $token_err = "Invalid or missing reset token.";
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $token = trim($_POST["token"]);
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if(empty($password_err) && empty($token_err)){
        // Verify token and expiration
        $sql = "SELECT id, reset_expires FROM users WHERE reset_token = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_token);
            $param_token = $token;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $user_id, $reset_expires);
                    mysqli_stmt_fetch($stmt);
                    
                    if(strtotime($reset_expires) > time()){
                        // Token valid, update password
                        $update_sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?";
                        if($update_stmt = mysqli_prepare($conn, $update_sql)){
                            mysqli_stmt_bind_param($update_stmt, "si", $param_password, $param_id);
                            $param_password = password_hash($password, PASSWORD_DEFAULT);
                            $param_id = $user_id;
                            
                            if(mysqli_stmt_execute($update_stmt)){
                                $success_msg = "Password updated successfully. <a href='login.php'>Login here</a>";
                            }
                            mysqli_stmt_close($update_stmt);
                        }
                    } else {
                        $token_err = "Reset token has expired.";
                    }
                } else {
                    $token_err = "Invalid reset token.";
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
                    <h2 class="fw-bold">Reset Password</h2>
                    <p>Enter your new password</p>
                </div>
                <div class="card-body p-5">
                    <?php if(!empty($success_msg)): ?>
                        <div class="alert alert-success"><?php echo $success_msg; ?></div>
                    <?php elseif(!empty($token_err)): ?>
                        <div class="alert alert-danger"><?php echo $token_err; ?></div>
                    <?php else: ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold">New Password</label>
                                <input type="password" name="password" class="form-control bg-light border-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="••••••••">
                                <span class="invalid-feedback d-block"><?php echo $password_err; ?></span>
                            </div>
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-custom btn-lg py-3">Reset Password</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
