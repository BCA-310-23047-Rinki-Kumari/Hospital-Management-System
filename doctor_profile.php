<?php
session_start();
include("db.php");

if(!isset($_SESSION['doctor']))
{
    header("Location: doctor_login.php");
    exit();
}

$doctor_email = mysqli_real_escape_string($conn, $_SESSION['doctor']);
$doctor_result = mysqli_query($conn, "SELECT * FROM doctor WHERE email='$doctor_email'");
$doctor = mysqli_fetch_assoc($doctor_result);

if(!$doctor) {
    header("Location: doctor_login.php");
    exit();
}

$message = '';

if(isset($_POST['update']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $password = trim($_POST['password']);

    $update_sql = "UPDATE doctor SET name='$name', specialization='$specialization'";
    if(!empty($password)) {
        $password_safe = mysqli_real_escape_string($conn, $password);
        $update_sql .= ", password='$password_safe'";
    }
    $update_sql .= " WHERE email='$doctor_email'";

    if(mysqli_query($conn, $update_sql)) {
        $message = 'Profile updated successfully.';
        $doctor['name'] = $name;
        $doctor['specialization'] = $specialization;
    } else {
        $message = 'Unable to update profile. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h2 class="mb-4">Doctor Profile</h2>
                <?php if($message): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($doctor['email']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary w-100">Save Changes</button>
                </form>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="doctor_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>