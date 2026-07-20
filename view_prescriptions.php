<?php
session_start();

// Check if patient is logged in
if (!isset($_SESSION['patient'])) {
    header("Location: patient_login.php");
    exit();
}

include 'db.php';

// Get patient info
$patient_email = $_SESSION['patient'];
$patient_query = "SELECT * FROM patient WHERE email = '$patient_email'";
$patient_result = mysqli_query($conn, $patient_query);
$patient = mysqli_fetch_assoc($patient_result);

// Get all prescriptions for this patient
$prescriptions_query = "SELECT * FROM prescription WHERE patient_id = {$patient['id']} ORDER BY issued_date DESC";
$prescriptions_result = mysqli_query($conn, $prescriptions_query);
$prescriptions = mysqli_fetch_all($prescriptions_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions - Patient Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .container {
            max-width: 900px;
        }

        .prescriptions-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideInDown 0.6s ease-out;
        }

        .prescriptions-header h1 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .patient-info {
            color: #666;
            font-size: 14px;
        }

        .prescription-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
            animation: slideInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .prescription-card:nth-child(1) { animation-delay: 0.1s; }
        .prescription-card:nth-child(2) { animation-delay: 0.2s; }
        .prescription-card:nth-child(3) { animation-delay: 0.3s; }
        .prescription-card:nth-child(4) { animation-delay: 0.4s; }
        .prescription-card:nth-child(5) { animation-delay: 0.5s; }

        .prescription-card:hover {
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            transform: translateY(-5px);
            border-left-color: #764ba2;
        }

        .rx-title {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .prescription-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .prescription-row-full {
            display: grid;
            grid-template-columns: 1fr;
            margin-bottom: 15px;
        }

        .prescription-field {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
        }

        .prescription-label {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            display: block;
        }

        .prescription-value {
            color: #555;
            font-size: 15px;
        }

        .doctor-signature {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            font-size: 13px;
            color: #777;
        }

        .doctor-signature strong {
            color: #667eea;
        }

        .issued-date {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
            text-align: right;
        }

        .empty-state {
            background: white;
            border-radius: 15px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideInUp 0.6s ease-out;
        }

        .empty-state-icon {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state-text {
            color: #666;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }

        .back-link a:hover {
            background: rgba(255, 255, 255, 0.4);
            color: white;
        }

        .print-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            float: right;
            margin-top: -45px;
        }

        .print-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        @media print {
            body {
                background: white;
            }
            .print-btn, .back-link, .prescriptions-header {
                display: none;
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="prescriptions-header">
            <h1>📋 My Prescriptions</h1>
            <div class="patient-info">
                <strong>Patient Name:</strong> <?php echo $patient['name']; ?> | 
                <strong>Age:</strong> <?php echo $patient['age']; ?>
            </div>
        </div>

        <?php if (count($prescriptions) > 0): ?>
            <?php foreach ($prescriptions as $index => $prescription): ?>
                <div class="prescription-card">
                    <button class="print-btn" onclick="window.print()">🖨 Print</button>
                    
                    <div class="rx-title">
                        💊 Prescription <?php echo count($prescriptions) - $index; ?>
                    </div>

                    <div class="prescription-row">
                        <div class="prescription-field">
                            <span class="prescription-label">Medicine Name</span>
                            <span class="prescription-value"><?php echo $prescription['medicine_name']; ?></span>
                        </div>
                        <div class="prescription-field">
                            <span class="prescription-label">Dosage</span>
                            <span class="prescription-value"><?php echo $prescription['dosage']; ?></span>
                        </div>
                    </div>

                    <div class="prescription-row">
                        <div class="prescription-field">
                            <span class="prescription-label">Frequency</span>
                            <span class="prescription-value"><?php echo $prescription['frequency']; ?></span>
                        </div>
                        <div class="prescription-field">
                            <span class="prescription-label">Duration</span>
                            <span class="prescription-value"><?php echo $prescription['duration']; ?></span>
                        </div>
                    </div>

                    <?php if ($prescription['instructions']): ?>
                        <div class="prescription-row-full">
                            <div class="prescription-field">
                                <span class="prescription-label">Special Instructions</span>
                                <span class="prescription-value"><?php echo $prescription['instructions']; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="doctor-signature">
                        <strong>Prescribed by:</strong> Dr. <?php echo $prescription['doctor_name']; ?>
                    </div>

                    <div class="issued-date">
                        Issued: <?php echo date('d M Y, H:i', strtotime($prescription['issued_date'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">No prescriptions found yet</div>
                <p style="color: #999; margin-bottom: 0;">Once a doctor prescribes you medicine, it will appear here.</p>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="patient_dashboard.php">← Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
