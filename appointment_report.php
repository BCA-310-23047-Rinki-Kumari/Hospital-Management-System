<?php
session_start();
include("db.php");
include("report_functions.php");

// Determine user role
$user_role = '';
if(isset($_SESSION['admin'])) {
    $user_role = 'admin';
} elseif(isset($_SESSION['doctor'])) {
    $user_role = 'doctor';
} elseif(isset($_SESSION['patient'])) {
    $user_role = 'patient';
} else {
    header("Location: index.html");
}

// Get date range from GET
$from_date = isset($_GET['from_date']) ? mysqli_real_escape_string($conn, $_GET['from_date']) : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? mysqli_real_escape_string($conn, $_GET['to_date']) : date('Y-m-t');

// Build query based on user role
$query = "SELECT * FROM appointment WHERE appointment_date BETWEEN '$from_date' AND '$to_date'";

if($user_role === 'doctor') {
    $doctor_email = mysqli_real_escape_string($conn, $_SESSION['doctor']);
    $doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctor WHERE email='$doctor_email'"));
    $doctor_name = $doctor['name'];
    $query .= " AND doctor_name='$doctor_name'";
} elseif($user_role === 'patient') {
    $patient_email = mysqli_real_escape_string($conn, $_SESSION['patient']);
    $patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patient WHERE email='$patient_email'"));
    $patient_name = $patient['name'];
    $query .= " AND patient_name='$patient_name'";
}

$query .= " ORDER BY appointment_date DESC, appointment_time DESC";
$appointments = mysqli_query($conn, $query);
$total_appointments = mysqli_num_rows($appointments);

// Get statistics
$today_query = "SELECT COUNT(*) as count FROM appointment WHERE appointment_date=CURDATE()";
if($user_role === 'doctor') {
    $today_query .= " AND doctor_name='$doctor_name'";
} elseif($user_role === 'patient') {
    $today_query .= " AND patient_name='$patient_name'";
}
$today_count = mysqli_fetch_assoc(mysqli_query($conn, $today_query));

$upcoming_query = "SELECT COUNT(*) as count FROM appointment WHERE appointment_date>CURDATE()";
if($user_role === 'doctor') {
    $upcoming_query .= " AND doctor_name='$doctor_name'";
} elseif($user_role === 'patient') {
    $upcoming_query .= " AND patient_name='$patient_name'";
}
$upcoming_count = mysqli_fetch_assoc(mysqli_query($conn, $upcoming_query));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Report</title>
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
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            font-size: 14px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            padding-left: 10px;
        }
        
        .appointment-card {
            background: #f0f8f4;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .appointment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-upcoming {
            background: #d1f2eb;
            color: #0b5345;
        }
        
        .status-today {
            background: #ffeaa7;
            color: #d63031;
        }
        
        .status-past {
            background: #dfe6e9;
            color: #636e72;
        }
        
        .appointment-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        
        .detail-item {
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .btn-section {
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        
        @media print {
            body {
                background: white;
            }
            .btn-section, .filter-section {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="btn-section mt-4">
        <a href="<?php echo ($user_role === 'admin') ? 'admin_dashboard.php' : (($user_role === 'doctor') ? 'doctor_dashboard.php' : 'patient_dashboard.php'); ?>" class="btn btn-secondary">← Back to Dashboard</a>
        <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
    </div>

    <div class="report-container">
        <div class="report-header">
            <div class="report-title">📅 Appointment Report</div>
            <small class="text-muted">Generated on <?php echo date("d M Y, h:i A"); ?></small>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h6 class="mb-3">🔍 Filter by Date Range</h6>
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>">
                </div>
                <div class="col-md-4">
                    <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="appointment_report.php" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box">
                <h3><?php echo $total_appointments; ?></h3>
                <p>Total Appointments</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $today_count['count']; ?></h3>
                <p>Today's Appointments</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $upcoming_count['count']; ?></h3>
                <p>Upcoming Appointments</p>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="section-title">📋 Appointment Details</div>
        <?php
        if($total_appointments == 0) {
            echo '<div class="no-data">No appointments found for the selected date range</div>';
        } else {
            mysqli_data_seek($appointments, 0);
            while($appointment = mysqli_fetch_assoc($appointments)) {
                $apt_date = strtotime($appointment['appointment_date']);
                $today = strtotime(date('Y-m-d'));
                
                if($apt_date > $today) {
                    $status_class = 'status-upcoming';
                    $status_text = 'Upcoming';
                } elseif($apt_date == $today) {
                    $status_class = 'status-today';
                    $status_text = 'Today';
                } else {
                    $status_class = 'status-past';
                    $status_text = 'Completed';
                }
                ?>
                <div class="appointment-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h5 style="margin: 0;">Appointment #{<?php echo $appointment['id']; ?>}</h5>
                        <span class="appointment-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </div>
                    <div class="appointment-details">
                        <div class="detail-item">
                            <strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Date:</strong> <?php echo formatDate($appointment['appointment_date']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Time:</strong> <?php echo formatTime($appointment['appointment_time']); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

</body>
</html>
