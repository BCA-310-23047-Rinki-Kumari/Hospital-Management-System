<?php
session_start();
include("db.php");
include("report_functions.php");

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
}

// Get all statistics
$doctor_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor"));
$patient_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patient"));
$appointment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointment"));
$prescription_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM prescription"));

// Recent appointments
$recent_appointments = mysqli_query($conn, "SELECT * FROM appointment ORDER BY appointment_date DESC LIMIT 10");

// Recent prescriptions
$recent_prescriptions = mysqli_query($conn, "SELECT * FROM prescription ORDER BY issued_date DESC LIMIT 10");

// Get doctors list
$doctors_list = mysqli_query($conn, "SELECT id, name, email, specialization FROM doctor");

// Get patients list
$patients_list = mysqli_query($conn, "SELECT id, name, email, age FROM patient");
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Reports - Hospital Management System</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 40px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 40px;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background-color: #0d6efd;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn-section {
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
        
        @media print {
            body {
                background: white;
            }
            .btn-section {
                display: none;
            }
            .stats-grid {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="btn-section mt-4">
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
        <a href="export_report.php" class="btn btn-success">💾 Export to Excel</a>
    </div>

    <div class="report-container">
        <div class="report-header">
            <div class="report-title">📊 Hospital Management System - Full Report</div>
            <small class="text-muted">Generated on <?php echo date("d M Y, h:i A"); ?></small>
        </div>

        <!-- System Statistics -->
        <div class="section-title">📈 System Statistics Overview</div>
        <div class="stats-grid">
            <div class="stat-card">
                <p>Total Doctors</p>
                <h3><?php echo $doctor_count['count']; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Patients</p>
                <h3><?php echo $patient_count['count']; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Appointments</p>
                <h3><?php echo $appointment_count['count']; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Prescriptions</p>
                <h3><?php echo $prescription_count['count']; ?></h3>
            </div>
        </div>

        <!-- Doctors List -->
        <div class="section-title">👨‍⚕️ Registered Doctors</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($doctor = mysqli_fetch_assoc($doctors_list)) {
                    echo "<tr>";
                    echo "<td>#{$doctor['id']}</td>";
                    echo "<td>" . htmlspecialchars($doctor['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($doctor['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($doctor['specialization']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Patients List -->
        <div class="section-title">👥 Registered Patients</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($patient = mysqli_fetch_assoc($patients_list)) {
                    echo "<tr>";
                    echo "<td>#{$patient['id']}</td>";
                    echo "<td>" . htmlspecialchars($patient['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($patient['email']) . "</td>";
                    echo "<td>" . $patient['age'] . " years</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Recent Appointments -->
        <div class="section-title">📅 Recent Appointments</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $apt_count = 0;
                while($appointment = mysqli_fetch_assoc($recent_appointments)) {
                    $apt_count++;
                    echo "<tr>";
                    echo "<td>#{$appointment['id']}</td>";
                    echo "<td>" . htmlspecialchars($appointment['patient_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($appointment['doctor_name']) . "</td>";
                    echo "<td>" . formatDate($appointment['appointment_date']) . "</td>";
                    echo "<td>" . formatTime($appointment['appointment_time']) . "</td>";
                    echo "</tr>";
                }
                if($apt_count == 0) {
                    echo '<tr><td colspan="5" class="no-data">No appointments found</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Recent Prescriptions -->
        <div class="section-title">💊 Recent Prescriptions</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Medicine</th>
                    <th>Date Issued</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $presc_count = 0;
                while($prescription = mysqli_fetch_assoc($recent_prescriptions)) {
                    $presc_count++;
                    echo "<tr>";
                    echo "<td>#{$prescription['id']}</td>";
                    echo "<td>" . htmlspecialchars($prescription['patient_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($prescription['doctor_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($prescription['medicine_name']) . "</td>";
                    echo "<td>" . formatDate($prescription['issued_date']) . "</td>";
                    echo "</tr>";
                }
                if($presc_count == 0) {
                    echo '<tr><td colspan="5" class="no-data">No prescriptions found</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Report Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #999; font-size: 12px;">
            <p>This is an official report generated from the Hospital Management System.</p>
            <p>Report Generated: <?php echo date("d M Y, h:i A"); ?></p>
        </div>
    </div>
</div>

</body>
</html>
