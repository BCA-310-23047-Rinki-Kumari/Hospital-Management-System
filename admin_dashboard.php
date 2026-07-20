<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
}

$doctor_count = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM doctor"));
$patient_count = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM patient"));
$appointment_count = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM appointment"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e8f4f8;
            min-height: 100vh;
            padding: 20px 0;
        }

        .card {
            transition: all 0.3s ease !important;
            border: none !important;
        }

        .card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2) !important;
            background-color: #f0f9fc !important;
        }

        .card h3 {
            color: #2c3e50;
            font-weight: 600;
        }

        .card h2 {
            color: #3498db;
            font-weight: bold;
            font-size: 2.5rem;
        }

        .card:hover h2 {
            color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container mt-5">

    <h1 class="text-center mb-5">Admin Dashboard</h1>

    <div class="row">

        <div class="col-md-4">
            <div class="card shadow p-4 text-center">
                <h3>Total Doctors</h3>
                <h2><?php echo $doctor_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-4 text-center">
                <h3>Total Patients</h3>
                <h2><?php echo $patient_count; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-4 text-center">
                <h3>Total Appointments</h3>
                <h2><?php echo $appointment_count; ?></h2>
            </div>
        </div>

    </div>

    <div class="text-center mt-5">

        <a href="manage_doctors.php" class="btn btn-info me-2">
        Manage Doctors
        </a>

        <a href="manage_patients.php" class="btn btn-success me-2">
        Manage Patients
        </a>

        <a href="invoice/invoice_form.php" class="btn btn-dark me-2">
        🧾 Create Invoice
        </a>

        <a href="invoice/invoice_list.php" class="btn btn-secondary me-2">
        📄 View Invoices
        </a>

        <a href="view_appointments.php" class="btn btn-primary me-2">
        View Appointments
        </a>

        <a href="view_feedback.php" class="btn btn-secondary me-2">
        📝 View Feedback
        </a>

        <a href="admin_reports.php" class="btn btn-warning me-2">
        📊 Generate Reports
        </a>

        <a href="logout.php" class="btn btn-danger">
        Logout
        </a>
    </div>

</div>

</body>
</html>