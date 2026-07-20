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
if(!$doctor_result) {
    die("Database error: " . mysqli_error($conn));
}
$doctor = mysqli_fetch_assoc($doctor_result);
if(!$doctor) {
    echo "<script>alert('Doctor not found'); window.location='doctor_login.php';</script>";
    exit();
}
$doctor_name = $doctor['name'];
$specialization = $doctor['specialization'];

$appointments_result = mysqli_query($conn, "SELECT * FROM appointment WHERE doctor_name='$doctor_name' ORDER BY appointment_date ASC, appointment_time ASC");
if(!$appointments_result) {
    die("Database error: " . mysqli_error($conn));
}
$appointment_count = mysqli_num_rows($appointments_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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
            <h1>Welcome, <?php echo htmlspecialchars($doctor_name); ?></h1>
            <?php if($specialization): ?>
                <p class="text-muted">Specialization: <?php echo htmlspecialchars($specialization); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="doctor_profile.php" class="btn btn-secondary me-2">Profile</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card">
                <h5 class="mb-2">Total Appointments</h5>
                <h2><?php echo $appointment_count; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card" onclick="window.location='doctor_prescribe.php'">
                <h5 class="mb-2">💊 Issue Prescription</h5>
                <p class="mb-0">Prescribe medicines to patients</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 text-center stat-card" onclick="window.location='doctor_patient_report.php'">
                <h5 class="mb-2">📊 Patient Report</h5>
                <p class="mb-0">View all patient statistics</p>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <a href="appointment_report.php" class="btn btn-info w-100 py-3">
                <h6 class="mb-0">📅 Appointment Report</h6>
                <small>View appointment history and statistics</small>
            </a>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm p-4">
                <h5 class="mb-3">Your Upcoming Appointments</h5>
                <?php if($appointment_count > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0">No appointments scheduled yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>