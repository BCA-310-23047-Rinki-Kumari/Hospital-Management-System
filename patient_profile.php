<?php
session_start();
include("db.php");

if(!isset($_SESSION['patient']))
{
    header("Location: patient_login.php");
    exit();
}

$patient_email = mysqli_real_escape_string($conn, $_SESSION['patient']);
$patient_result = mysqli_query($conn, "SELECT * FROM patient WHERE email='$patient_email'");
$patient = mysqli_fetch_assoc($patient_result);

if(!$patient) {
    header("Location: patient_login.php");
    exit();
}

$message = '';

if(isset($_POST['update']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = intval($_POST['age']);
    $password = trim($_POST['password']);

    $update_sql = "UPDATE patient SET name='$name', age='$age'";
    if(!empty($password)) {
        $password_safe = mysqli_real_escape_string($conn, $password);
        $update_sql .= ", password='$password_safe'";
    }
    $update_sql .= " WHERE email='$patient_email'";

    if(mysqli_query($conn, $update_sql)) {
        $message = 'Profile updated successfully.';
        $patient['name'] = $name;
        $patient['age'] = $age;
    } else {
        $message = 'Unable to update profile. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4">Patient Profile</h2>
                <?php if($message): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($patient['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($patient['email']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($patient['age']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary w-100">Save Changes</button>
                </form>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="patient_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>