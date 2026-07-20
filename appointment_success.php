<?php
session_start();

if(!isset($_SESSION['appointment_success'])) {
    header("Location: patient_dashboard.php");
    exit();
}

$patient_name = $_SESSION['appointment_patient'] ?? 'Patient';
$doctor_name = $_SESSION['appointment_doctor'] ?? 'Doctor';
$doctor_specialization = $_SESSION['appointment_specialization'] ?? '';
$appointment_date = $_SESSION['appointment_date'] ?? '';
$appointment_time = $_SESSION['appointment_time'] ?? '';

unset($_SESSION['appointment_success']);
unset($_SESSION['appointment_patient']);
unset($_SESSION['appointment_doctor']);
unset($_SESSION['appointment_specialization']);
unset($_SESSION['appointment_date']);
unset($_SESSION['appointment_time']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h1 class="mb-4 text-center">Appointment Confirmed</h1>
                <div class="mb-4">
                    <h4>Patient:</h4>
                    <p><?php echo htmlspecialchars($patient_name); ?></p>
                </div>
                <div class="mb-4">
                    <h4>Doctor:</h4>
                    <p><?php echo htmlspecialchars($doctor_name); ?><?php if($doctor_specialization): ?> - <?php echo htmlspecialchars($doctor_specialization); ?><?php endif; ?></p>
                </div>
                <div class="mb-4">
                    <h4>Date & Time:</h4>
                    <p><?php echo htmlspecialchars($appointment_date); ?> at <?php echo htmlspecialchars($appointment_time); ?></p>
                </div>
                <div class="text-center">
                    <a href="patient_dashboard.php" class="btn btn-success me-2">Go to Dashboard</a>
                    <a href="appointment.php" class="btn btn-primary">Book Another Appointment</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>