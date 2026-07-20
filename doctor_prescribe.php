<?php
session_start();

// Check if doctor is logged in
if (!isset($_SESSION['doctor'])) {
    header("Location: doctor_login.php");
    exit();
}

include 'db.php';

// Get doctor info
$doctor_email = $_SESSION['doctor'];
$doctor_query = "SELECT * FROM doctor WHERE email = '$doctor_email'";
$doctor_result = mysqli_query($conn, $doctor_query);
$doctor = mysqli_fetch_assoc($doctor_result);

$success_message = "";
$error_message = "";

// Handle prescription submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $medicine_name = mysqli_real_escape_string($conn, $_POST['medicine_name']);
    $dosage = mysqli_real_escape_string($conn, $_POST['dosage']);
    $frequency = mysqli_real_escape_string($conn, $_POST['frequency']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $instructions = mysqli_real_escape_string($conn, $_POST['instructions']);

    // Get patient details
    $patient_query = "SELECT * FROM patient WHERE id = $patient_id";
    $patient_result = mysqli_query($conn, $patient_query);
    $patient = mysqli_fetch_assoc($patient_result);

    if ($patient) {
        $insert_query = "INSERT INTO prescription (patient_id, patient_name, doctor_id, doctor_name, medicine_name, dosage, frequency, duration, instructions) 
                        VALUES ($patient_id, '{$patient['name']}', {$doctor['id']}, '{$doctor['name']}', '$medicine_name', '$dosage', '$frequency', '$duration', '$instructions')";

        if (mysqli_query($conn, $insert_query)) {
            $success_message = "✓ Prescription issued successfully to " . $patient['name'];
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Patient not found!";
    }
}

// Get all patients
$patients_query = "SELECT id, name, email, age FROM patient";
$patients_result = mysqli_query($conn, $patients_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Prescription - Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 10px 0;
        }

        .container {
            max-width: 600px;
        }

        .prescription-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 25px;
            animation: slideInUp 0.6s ease-out;
        }

        .prescription-header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 12px;
        }

        .prescription-header h1 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 24px;
        }

        .prescription-header p {
            color: #666;
            margin: 0;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 12px;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        .form-group:nth-child(7) { animation-delay: 0.7s; }

        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            display: block;
            font-size: 13px;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 8px 10px;
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .form-control:hover, .form-select:hover {
            border-color: #667eea;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            width: 100%;
            margin-top: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 14px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .back-link {
            text-align: center;
            margin-top: 12px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            animation: slideInUp 0.6s ease-out;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .alert-success {
            background-color: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .doctor-info {
            background: #f8f9fa;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            animation: fadeInUp 0.6s ease-out;
        }

        .doctor-info p {
            margin: 3px 0;
            color: #666;
            font-size: 13px;
        }

        .doctor-info strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <div class="prescription-card">
            <div class="prescription-header">
                <h1>💊 Issue Prescription</h1>
                <p>Prescribe medicines to patients</p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="doctor-info">
                <p><strong>Doctor:</strong> <?php echo $doctor['name']; ?></p>
                <p><strong>Specialization:</strong> <?php echo $doctor['specialization']; ?></p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="patient_id">Select Patient *</label>
                    <select class="form-select" id="patient_id" name="patient_id" required>
                        <option value="">-- Choose a Patient --</option>
                        <?php while ($row = mysqli_fetch_assoc($patients_result)): ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['name'] . " (Age: " . $row['age'] . ") - " . $row['email']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="medicine_name">Medicine Name *</label>
                    <input type="text" class="form-control" id="medicine_name" name="medicine_name" placeholder="e.g., Aspirin, Amoxicillin" required>
                </div>

                <div class="form-group">
                    <label for="dosage">Dosage *</label>
                    <input type="text" class="form-control" id="dosage" name="dosage" placeholder="e.g., 500mg, 1 tablet" required>
                </div>

                <div class="form-group">
                    <label for="frequency">Frequency *</label>
                    <input type="text" class="form-control" id="frequency" name="frequency" placeholder="e.g., Twice daily, 3 times a day" required>
                </div>

                <div class="form-group">
                    <label for="duration">Duration *</label>
                    <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 7 days, 2 weeks" required>
                </div>

                <div class="form-group">
                    <label for="instructions">Special Instructions</label>
                    <textarea class="form-control" id="instructions" name="instructions" rows="3" placeholder="Any special instructions (optional)"></textarea>
                </div>

                <button type="submit" class="submit-btn">Issue Prescription</button>
            </form>

            <div class="back-link">
                <a href="doctor_dashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
