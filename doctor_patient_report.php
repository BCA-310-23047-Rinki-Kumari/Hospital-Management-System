<?php
session_start();
include("db.php");
include("report_functions.php");

if(!isset($_SESSION['doctor']))
{
    header("Location: doctor_login.php");
}

$doctor_email = mysqli_real_escape_string($conn, $_SESSION['doctor']);
$doctor_result = mysqli_query($conn, "SELECT * FROM doctor WHERE email='$doctor_email'");
$doctor = mysqli_fetch_assoc($doctor_result);
$doctor_id = $doctor['id'];
$doctor_name = $doctor['name'];

// Get doctor's patients
$doctor_data = getDoctorPatients($conn, $doctor_id);

// Get prescriptions by this doctor
$prescriptions = mysqli_query($conn, "SELECT * FROM prescription WHERE doctor_id='$doctor_id' ORDER BY issued_date DESC LIMIT 20");
$prescription_count = mysqli_num_rows($prescriptions);

// Get appointments
$appointments = mysqli_query($conn, "SELECT * FROM appointment WHERE doctor_name='$doctor_name' ORDER BY appointment_date DESC LIMIT 20");
$appointment_count = mysqli_num_rows($appointments);

// Count unique patients
$unique_patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT patient_name) as count FROM appointment WHERE doctor_name='$doctor_name'"));
$patient_count = $unique_patients['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Report - Dr. <?php echo htmlspecialchars($doctor_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        
        .report-container {
            background: white;
            padding: 40px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .report-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-box h3 {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        
        .stat-box p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            padding-left: 10px;
        }
        
        .patient-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }
        
        .appointment-card {
            background: #e7f3ff;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .prescription-card {
            background: #fef3c7;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .btn-section {
            margin-bottom: 20px;
        }
        
        @media print {
            body {
                background: white;
            }
            .btn-section {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="btn-section mt-4">
        <a href="doctor_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
    </div>

    <div class="report-container">
        <div class="report-header">
            <div class="report-title">📊 Patient Report</div>
            <p class="mb-0">Dr. <?php echo htmlspecialchars($doctor_name); ?></p>
            <small class="text-muted">Specialization: <?php echo htmlspecialchars($doctor['specialization']); ?></small><br>
            <small class="text-muted">Generated on <?php echo date("d M Y, h:i A"); ?></small>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <h3><?php echo $patient_count; ?></h3>
                <p>Total Patients</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $appointment_count; ?></h3>
                <p>Total Appointments</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $prescription_count; ?></h3>
                <p>Prescriptions Issued</p>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="section-title">📅 Recent Appointments</div>
        <?php
        $apt_count = 0;
        while($appointment = mysqli_fetch_assoc($appointments)) {
            $apt_count++;
            ?>
            <div class="appointment-card">
                <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong> - 
                <?php echo formatDate($appointment['appointment_date']); ?> at <?php echo formatTime($appointment['appointment_time']); ?>
            </div>
            <?php
        }
        if($apt_count == 0) {
            echo '<p class="text-muted">No appointments found</p>';
        }
        ?>

        <!-- Recent Prescriptions -->
        <div class="section-title">💊 Recent Prescriptions</div>
        <?php
        $presc_count = 0;
        while($prescription = mysqli_fetch_assoc($prescriptions)) {
            $presc_count++;
            ?>
            <div class="prescription-card">
                <strong><?php echo htmlspecialchars($prescription['patient_name']); ?></strong><br>
                Medicine: <?php echo htmlspecialchars($prescription['medicine_name']); ?> | 
                Dosage: <?php echo htmlspecialchars($prescription['dosage']); ?><br>
                Issued: <?php echo formatDate($prescription['issued_date']); ?>
            </div>
            <?php
        }
        if($presc_count == 0) {
            echo '<p class="text-muted">No prescriptions found</p>';
        }
        ?>

        <!-- Patient List -->
        <div class="section-title">👥 Patient List</div>
        <?php
        $pat_count = 0;
        while($pat = mysqli_fetch_assoc($doctor_data['patients'])) {
            $pat_count++;
            ?>
            <div class="patient-card">
                <strong><?php echo htmlspecialchars($pat['name']); ?></strong> (Age: <?php echo $pat['age']; ?>)<br>
                <small class="text-muted">Email: <?php echo htmlspecialchars($pat['email']); ?></small>
            </div>
            <?php
        }
        if($pat_count == 0) {
            echo '<p class="text-muted">No patients found</p>';
        }
        ?>
    </div>
</div>

</body>
</html>
