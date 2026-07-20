<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

if(isset($_POST['register']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);

    // Hash doctor password before saving
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO doctor(name,email,password,specialization) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $specialization);

    if($stmt->execute())
    {
        echo "<script>alert('Doctor Registered Successfully'); window.location.href='manage_doctors.php';</script>";
    }
    else
    {
        echo "<script>alert('Registration Failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Doctor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow p-4">

                <h2 class="text-center mb-4">Register New Doctor</h2>

                <form method="POST">

                    <input type="text"
                    name="name"
                    class="form-control mb-3"
                    placeholder="Enter Name"
                    required>

                    <input type="email"
                    name="email"
                    class="form-control mb-3"
                    placeholder="Enter Email"
                    required>

                    <input type="password"
                    name="password"
                    class="form-control mb-3"
                    placeholder="Enter Password"
                    required>

                    <input type="text"
                    name="specialization"
                    class="form-control mb-3"
                    placeholder="Enter Specialization"
                    required>

                    <button type="submit"
                    name="register"
                    class="btn btn-primary w-100 mb-2">
                    Register Doctor
                    </button>

                    <a href="admin_dashboard.php" class="btn btn-secondary w-100">
                    Back to Dashboard
                    </a>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>
