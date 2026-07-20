<?php
session_start();
include("db.php");

if(isset($_SESSION['patient'])) {
    header("Location: patient_dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

if(isset($_POST['register']))
{
    // Input Validation
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $age = $_POST['age'] ?? '';
    
    // Validate name
    if (empty($name) || strlen($name) < 2 || strlen($name) > 100) {
        $error_message = "Name must be 2-100 characters";
    }
    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    }
    // Validate password
    elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters";
    }
    // Validate age
    elseif (!is_numeric($age) || $age < 1 || $age > 120) {
        $error_message = "Age must be a number between 1 and 120";
    }
    else {
        // Check if email already exists
        $check_query = $conn->prepare("SELECT id FROM patient WHERE email = ?");
        $check_query->bind_param("s", $email);
        $check_query->execute();
        $check_result = $check_query->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Email already registered";
        }
        else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Use prepared statement
            $register_query = $conn->prepare("INSERT INTO patient(name, email, password, age) VALUES(?, ?, ?, ?)");
            $register_query->bind_param("sssi", $name, $email, $hashed_password, $age);
            
            if($register_query->execute())
            {
                $success_message = "Registration successful! Redirecting to login...";
                header("Refresh: 2; url=patient_login.php");
            }
            else
            {
                $error_message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
	body{background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;}
	</style>
</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow p-4">

                <h2 class="text-center mb-4">Patient Registration</h2>

                <?php if($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <input type="text"
                    name="name"
                    class="form-control mb-3"
                    placeholder="Enter Name"
                    maxlength="100"
                    required>

                    <input type="email"
                    name="email"
                    class="form-control mb-3"
                    placeholder="Enter Email"
                    required>

                    <input type="password"
                    name="password"
                    class="form-control mb-3"
                    placeholder="Enter Password (min 6 characters)"
                    minlength="6"
                    required>

                    <input type="number"
                    name="age"
                    class="form-control mb-3"
                    placeholder="Enter Age"
                    min="1"
                    max="120"
                    required>

                    <button type="submit"
                    name="register"
                    class="btn btn-primary w-100">
                    Register
                    </button>

                </form>

                <hr>
                <p class="text-center text-muted mb-0">
                    Already have an account? <a href="patient_login.php">Login here</a>
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>