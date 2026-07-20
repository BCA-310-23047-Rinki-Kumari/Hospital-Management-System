<?php
session_start();
include("db.php");
include("report_functions.php");

if(!isset($_SESSION['patient']))
{
    header("Location: patient_login.php");
}

$patient_email = mysqli_real_escape_string($conn, $_SESSION['patient']);
$patient_result = mysqli_query($conn, "SELECT * FROM patient WHERE email='$patient_email'");
$patient = mysqli_fetch_assoc($patient_result);
$patient_id = $patient['id'];

// Get medical history
$medical_data = getPatientMedicalHistory($conn, $patient_id);

// Handle PDF generation
$generate_pdf = isset($_GET['pdf']) ? true : false;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical Report - <?php echo htmlspecialchars($patient['name']); ?></title>
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
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .patient-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 15px;
            border-left: 4px solid #0d6efd;
            padding-left: 10px;
        }
        
        .appointment-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 4px solid #28a745;
        }
        
        .prescription-card {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 4px solid #ffc107;
        }
        
        .btn-section {
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
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
        <a href="patient_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
    </div>

    <div class="report-container">
        <div class="report-header">
            <div class="report-title">📋 Medical Report</div>
            <small class="text-muted">Generated on <?php echo date("d M Y, h:i A"); ?></small>
        </div>

        <!-- Patient Information -->
        <div class="patient-info">
            <h5>Patient Information</h5>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
            <p><strong>Age:</strong> <?php echo $patient['age']; ?> years</p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
            <p><strong>Patient ID:</strong> <?php echo $patient['id']; ?></p>
        </div>

        <!-- Appointments History -->
        <div class="section-title">📅 Appointment History</div>
        <?php
        $appointment_count = 0;
        while($appointment = mysqli_fetch_assoc($medical_data['appointments'])) {
            $appointment_count++;
            ?>
            <div class="appointment-card">
                <strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name']); ?><br>
                <strong>Date:</strong> <?php echo formatDate($appointment['appointment_date']); ?><br>
                <strong>Time:</strong> <?php echo formatTime($appointment['appointment_time']); ?>
            </div>
            <?php
        }
        if($appointment_count == 0) {
            echo '<div class="no-data">No appointment records found</div>';
        }
        ?>

        <!-- Prescriptions -->
        <div class="section-title">💊 Prescription History</div>
        <?php
        $prescription_count = 0;
        while($prescription = mysqli_fetch_assoc($medical_data['prescriptions'])) {
            $prescription_count++;
            ?>
            <div class="prescription-card">
                <strong>Doctor:</strong> <?php echo htmlspecialchars($prescription['doctor_name']); ?><br>
                <strong>Medicine:</strong> <?php echo htmlspecialchars($prescription['medicine_name']); ?><br>
                <strong>Dosage:</strong> <?php echo htmlspecialchars($prescription['dosage']); ?> | 
                <strong>Frequency:</strong> <?php echo htmlspecialchars($prescription['frequency']); ?> | 
                <strong>Duration:</strong> <?php echo htmlspecialchars($prescription['duration']); ?><br>
                <strong>Date Issued:</strong> <?php echo formatDate($prescription['issued_date']); ?><br>
                <?php if($prescription['instructions']) { ?>
                    <strong>Instructions:</strong> <?php echo htmlspecialchars($prescription['instructions']); ?>
                <?php } ?>
            </div>
            <?php
        }
        if($prescription_count == 0) {
            echo '<div class="no-data">No prescriptions found</div>';
        }
        ?>

        <!-- Summary -->
        <div class="section-title">📊 Report Summary</div>
        <div class="patient-info">
            <p><strong>Total Appointments:</strong> <?php echo $appointment_count; ?></p>
            <p><strong>Total Prescriptions:</strong> <?php echo $prescription_count; ?></p>
            <p><strong>Report Generated:</strong> <?php echo date("d M Y, h:i A"); ?></p>
        </div>
    </div>
</div>

</body>
</html>
