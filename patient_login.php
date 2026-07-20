<?php
session_start();
include("db.php");

$error_message = '';

if(isset($_POST['login']))
{
    // Input Validation
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    }
    // Validate password
    elseif (strlen($password) < 6) {
        $error_message = "Invalid email or password";
    }
    else {
        // Use prepared statement to prevent SQL injection
        $query = $conn->prepare("SELECT id, email, password FROM patient WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        
        if($result->num_rows > 0)
        {
            $patient = $result->fetch_assoc();
            
            // Verify password using password_verify
            if(password_verify($password, $patient['password']))
            {
                session_regenerate_id(true);
                $_SESSION['patient'] = $patient['email'];
                header("Location: patient_dashboard.php");
                exit();
            }
            else
            {
                $error_message = "Invalid email or password";
            }
        }
        else
        {
            $error_message = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Login</title>
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
        <h2 class="text-center mb-4">👤 Patient Login</h2>

        <?php if($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email"
                name="email"
                class="form-control"
                placeholder="Enter Email"
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
        <p class="text-center text-muted">
            Don't have an account? <a href="patient_register.php" class="text-primary">Register here</a>
        </p>
        <small class="text-muted d-block text-center">
            <a href="index.html" class="text-primary">← Back to Home</a>
        </small>
    </div>
</div>

</body>
</html>