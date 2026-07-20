<?php
session_start();
include("db.php");

$error_message = '';

if(isset($_POST['login']))
{
    // Input Validation
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate username
    if (empty($username) || strlen($username) < 3) {
        $error_message = "Invalid username";
    }
    // Validate password
    elseif (strlen($password) < 6) {
        $error_message = "Invalid username or password";
    }
    else {
        // Use prepared statement
        $query = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows > 0)
        {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if(password_verify($password, $admin['password']))
            {
                session_regenerate_id(true);
                $_SESSION['admin'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            }
            else
            {
                $error_message = "Invalid username or password";
            }
        }
        else
        {
            $error_message = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        body {
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .card {
            border-radius: 12px;
        }
        
        h2 {
            color: var(--primary-color);
        }
    </style>
</head>

<body>

<div class="login-container">
    <div class="card shadow p-5">
        <h2 class="text-center mb-4">👤 Admin Login</h2>

        <?php if($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text"
                name="username"
                class="form-control"
                placeholder="Enter Username"
                required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password"
                name="password"
                class="form-control"
                placeholder="Enter Password"
                required>
            </div>

            <button type="submit"
            name="login"
            class="btn btn-primary w-100">
            Login
            </button>
        </form>
        
        <hr>
        <small class="text-muted">
            <a href="index.html" class="text-primary">← Back to Home</a>
        </small>
    </div>
</div>

</body>
</html>