<?php
require_once 'includes/db_connect.php';

$username_err = $email_err = $password_err = "";
$success_msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($email_err) && empty($password_err)){
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
            
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Account created successfully! You can now login.";
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="p-5" style="background: var(--primary-gradient); color: white; text-align: center;">
                    <h2 class="fw-bold">Create Account</h2>
                    <p>Join the VibrantShop community</p>
                </div>
                <div class="card-body p-5">
                    <?php if(!empty($success_msg)): ?>
                        <div class="alert alert-success"><?php echo $success_msg; ?></div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control bg-light border-0 <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($username) ? $username : ''; ?>" placeholder="e.g. johndoe">
                            </div>
                            <span class="invalid-feedback d-block"><?php echo $username_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-0 <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($email) ? $email : ''; ?>" placeholder="name@example.com">
                            </div>
                            <span class="invalid-feedback d-block"><?php echo $email_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="••••••••">
                            </div>
                            <span class="invalid-feedback d-block"><?php echo $password_err; ?></span>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-custom btn-lg py-3">Register Now</button>
                        </div>
                        <p class="text-center mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-bold">Login here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
