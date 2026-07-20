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
if(!$patient_result) {
    die("Database error: " . mysqli_error($conn));
}
$patient = mysqli_fetch_assoc($patient_result);
if(!$patient) {
    echo "<script>alert('Patient not found'); window.location='patient_login.php';</script>";
    exit();
}
$patient_name = $patient['name'];
$patient_age = $patient['age'];

$appointments_result = mysqli_query($conn, "SELECT * FROM appointment WHERE patient_name='$patient_name' ORDER BY appointment_date ASC, appointment_time ASC");
if(!$appointments_result) {
    die("Database error: " . mysqli_error($conn));
}
$appointment_count = mysqli_num_rows($appointments_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            min-height: 100vh;
            padding: 20px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, #27ae60 0%, #16a085 100%);
            color: white;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(39, 174, 96, 0.3);
        }

        .stat-card h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .stat-card p {
            margin: 0;
            font-size: 13px;
            opacity: 0.95;
        }

        .stat-card h2 {
            color: white;
            font-weight: bold;
            font-size: 2rem;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1>Welcome, <?php echo htmlspecialchars($patient_name); ?></h1>
            <?php if($patient_age): ?>
                <p class="text-muted">Age: <?php echo htmlspecialchars($patient_age); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="patient_profile.php" class="btn btn-secondary me-2">Profile</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card">
                <h5 class="mb-2">Appointments Booked</h5>
                <h2><?php echo $appointment_count; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card" onclick="window.location='view_prescriptions.php'">
                <h5 class="mb-2">🩺 My Prescriptions</h5>
                <p class="mb-0">View prescribed medicines</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card" onclick="window.location='invoice/invoice_list.php'">
                <h5 class="mb-2">🧾 My Invoices</h5>
                <p class="mb-0">View and download your invoices</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card" onclick="window.location='patient_medical_report.php'">
                <h5 class="mb-2">📋 Medical Report</h5>
                <p class="mb-0">View your medical history</p>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <a href="appointment_report.php" class="btn btn-info w-100 py-3">
                <h6 class="mb-0">📅 Appointment Report</h6>
                <small>View appointment history and status</small>
            </a>
        </div>
        <div class="col-md-6">
            <a href="submit_feedback.php" class="btn btn-warning w-100 py-3">
                <h6 class="mb-0">📝 Submit Feedback</h6>
                <small>Share your appointment experience</small>
            </a>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Upcoming Appointments</h5>
                    <a href="appointment.php" class="btn btn-success btn-sm">Book New</a>
                </div>
                <?php if($appointment_count > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0">No appointments booked yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>