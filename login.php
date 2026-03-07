<?php
require_once 'includes/db_connect.php';

// Check if user is already logged in
session_start();
if(isset($_SESSION["user_id"])){
    header("location: index.php");
    exit;
}

$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, start a new session
                            session_start();
                            $_SESSION["user_id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;                            
                            
                            header("location: index.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p>Enter your details to login</p>
                </div>
                <div class="card-body p-5">
                    <?php if(!empty($login_err)): ?>
                        <div class="alert alert-danger"><?php echo $login_err; ?></div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control bg-light border-0 <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($username) ? $username : ''; ?>" placeholder="Username">
                            </div>
                            <span class="invalid-feedback d-block"><?php echo $username_err; ?></span>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-0 <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="••••••••">
                            </div>
                            <span class="invalid-feedback d-block"><?php echo $password_err; ?></span>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-custom btn-lg py-3">Login</button>
                        </div>
                        <p class="text-center mb-0">Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Register now</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
