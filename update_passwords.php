<?php
include("db.php");

if(isset($_POST['update_passwords'])) {
    // Generate fresh hashes
    $admin_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $doctor_hash = password_hash('doctor123', PASSWORD_BCRYPT);
    $patient_hash = password_hash('patient123', PASSWORD_BCRYPT);
    
    // Update admin
    $query1 = $conn->prepare("UPDATE admin SET password = ? WHERE username = 'admin'");
    $query1->bind_param("s", $admin_hash);
    $result1 = $query1->execute();
    
    // Update doctor
    $query2 = $conn->prepare("UPDATE doctor SET password = ? WHERE email = 'doctor@gmail.com'");
    $query2->bind_param("s", $doctor_hash);
    $result2 = $query2->execute();
    
    // Update all patients
    $query3 = $conn->prepare("UPDATE patient SET password = ?");
    $query3->bind_param("s", $patient_hash);
    $result3 = $query3->execute();
    
    if($result1 && $result2 && $result3) {
        echo "<div class='alert alert-success'><h4>✅ Success!</h4>All passwords have been updated with correct BCrypt hashes!<br><br>";
        echo "<strong>Test Credentials:</strong><br>";
        echo "- Admin: username=<code>admin</code>, password=<code>admin123</code><br>";
        echo "- Doctor: email=<code>doctor@gmail.com</code>, password=<code>doctor123</code><br>";
        echo "- Patient: email=<code>patient@gmail.com</code>, password=<code>patient123</code><br>";
        echo "<a href='admin_login.php' class='btn btn-primary mt-3'>Go to Admin Login</a>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Update failed. Check database connection.</div>";
    }
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Passwords</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card" style="max-width: 600px; margin: auto;">
        <div class="card-body">
            <h2 class="mb-4">🔐 Update Database Passwords</h2>
            <p class="alert alert-warning">This will update all passwords in the database with correct BCrypt hashes.</p>
            
            <form method="POST">
                <button type="submit" name="update_passwords" class="btn btn-danger btn-lg w-100">
                    ⚠️ Update Passwords Now
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
<?php
}
?>
