<?php
// =====================================================
// APPOINTMENT BOOKING PAGE - WITHOUT PAYMENT
// =====================================================
// This page allows patients to book appointments

session_start();
include("db.php");

// =====================================================
// CHECK IF PATIENT IS LOGGED IN
// =====================================================
if(!isset($_SESSION['patient'])) {
    header("Location: patient_login.php");
    exit();
}

// =====================================================
// GET PATIENT INFORMATION
// =====================================================
$patient_name = '';
$patient_email = mysqli_real_escape_string($conn, $_SESSION['patient']);
$patient_result = mysqli_query($conn, "SELECT * FROM patient WHERE email='$patient_email'");
$patient = mysqli_fetch_assoc($patient_result);

if(!$patient) {
    session_destroy();
    header("Location: patient_login.php");
    exit();
}

$patient_name = $patient['name'];
date_default_timezone_set('Asia/Kolkata');

// =====================================================
// HANDLE APPOINTMENT BOOKING
// =====================================================
$error_message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_name = mysqli_real_escape_string($conn, $_POST['doctor_name']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['time']);
    
    // Validate inputs
    if(empty($doctor_name) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = "Please fill all fields";
    } else {
        // Check if doctor exists and get specialization
        $doctor_check = mysqli_query($conn, "SELECT id, specialization FROM doctor WHERE name='$doctor_name' LIMIT 1");
        if(mysqli_num_rows($doctor_check) == 0) {
            $error_message = "Selected doctor not found";
        } else {
            $doctor_data = mysqli_fetch_assoc($doctor_check);
            
            // Insert appointment
            $insert_query = "INSERT INTO appointment 
                           (patient_name, doctor_name, appointment_date, appointment_time)
                           VALUES 
                           ('$patient_name', '$doctor_name', '$appointment_date', '$appointment_time')";
            
            if(mysqli_query($conn, $insert_query)) {
                // Set session variables for success page
                $_SESSION['appointment_success'] = true;
                $_SESSION['appointment_patient'] = $patient_name;
                $_SESSION['appointment_doctor'] = $doctor_name;
                $_SESSION['appointment_specialization'] = $doctor_data['specialization'];
                $_SESSION['appointment_date'] = $appointment_date;
                $_SESSION['appointment_time'] = $appointment_time;
                
                // Redirect to success page
                header("Location: appointment_success.php");
                exit();
            } else {
                $error_message = "Error booking appointment: " . mysqli_error($conn);
            }
        }
    }
}

// =====================================================
// GET ALL DOCTORS
// =====================================================
$doctors = mysqli_query($conn, "SELECT name, specialization FROM doctor ORDER BY name ASC");
$doctors_array = array();
while($doctor = mysqli_fetch_assoc($doctors)) {
    $doctors_array[] = $doctor;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .card-body {
            padding: 30px;
        }

        h2 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-book {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 8px;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .appointment-note {
            background-color: #f0f8ff;
            border-left: 4px solid #667eea;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 13px;
            color: #333;
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card">

                <div class="card-body">

                    <h2>📅 Book Appointment</h2>

                    <div class="appointment-note">
                        📝 Please fill in your appointment details. Your appointment will be confirmed by the hospital.
                    </div>

                    <?php if($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- Patient Name (Read Only) -->
                        <div class="mb-3">
                            <label class="form-label">Patient Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient_name); ?>" readonly>
                        </div>

                        <!-- Doctor Selection -->
                        <div class="mb-3">
                            <label class="form-label">Select Doctor *</label>
                            <select name="doctor_name" class="form-select" required>
                                <option value="">-- Choose a doctor --</option>
                                <?php foreach($doctors_array as $doctor): ?>
                                    <option value="<?php echo htmlspecialchars($doctor['name']); ?>">
                                        <?php echo htmlspecialchars($doctor['name']); ?> (<?php echo htmlspecialchars($doctor['specialization']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Appointment Date -->
                        <div class="mb-3">
                            <label class="form-label">Select Date *</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <!-- Appointment Time -->
                        <div class="mb-3">
                            <label class="form-label">Select Time *</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>

                        <!-- Book Appointment Button -->
                        <button type="submit" class="btn-book">
                            ✓ Book Appointment
                        </button>

                    </form>

                    <div class="back-link">
                        <a href="patient_dashboard.php">← Back to Dashboard</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Set minimum date to today -->
<script>
    document.querySelector('input[type="date"]').min = new Date().toISOString().split('T')[0];
</script>

</body>
</html>