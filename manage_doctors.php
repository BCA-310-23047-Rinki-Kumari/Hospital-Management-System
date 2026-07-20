<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

// Handle doctor deletion
if(isset($_GET['delete']))
{
    $doctor_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = "DELETE FROM doctor WHERE id = '$doctor_id'";
    
    if(mysqli_query($conn, $delete_query))
    {
        echo "<script>alert('Doctor Deleted Successfully'); window.location.href='manage_doctors.php';</script>";
    }
    else
    {
        echo "<script>alert('Error Deleting Doctor');</script>";
    }
}

// Fetch all doctors
$doctors_result = mysqli_query($conn, "SELECT id, name, email, specialization FROM doctor");
$doctors = array();
while($row = mysqli_fetch_assoc($doctors_result))
{
    $doctors[] = $row;
}

$doctor_count = count($doctors);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Doctors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <h1 class="text-center mb-5">Manage Doctors</h1>

    <div class="text-center mb-3">
        <a href="doctor_register_admin.php" class="btn btn-success me-2">
        Add New Doctor
        </a>
        <a href="admin_dashboard.php" class="btn btn-secondary">
        Back to Dashboard
        </a>
    </div>

    <div class="table-container">
        <h3 class="mb-4">Total Doctors: <span class="badge bg-primary"><?php echo $doctor_count; ?></span></h3>
        
        <?php if(count($doctors) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($doctors as $doctor) { ?>
                        <tr>
                            <td><?php echo $doctor['id']; ?></td>
                            <td><?php echo $doctor['name']; ?></td>
                            <td><?php echo $doctor['email']; ?></td>
                            <td><?php echo $doctor['specialization']; ?></td>
                            <td>
                                <a href="manage_doctors.php?delete=<?php echo $doctor['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this doctor?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info text-center">
                <h5>No doctors found. <a href="doctor_register_admin.php">Register a new doctor</a></h5>
            </div>
        <?php } ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
