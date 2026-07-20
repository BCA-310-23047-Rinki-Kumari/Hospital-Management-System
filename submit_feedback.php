<?php
session_start();
include("db.php");

if(!isset($_SESSION['patient'])) {
    header("Location: patient_login.php");
    exit();
}

$patient_email = mysqli_real_escape_string($conn, $_SESSION['patient']);
$patient_result = mysqli_query($conn, "SELECT * FROM patient WHERE email='$patient_email'");
$patient = mysqli_fetch_assoc($patient_result);

if(!$patient) {
    session_destroy();
    header("Location: patient_login.php");
    exit();
}

$patient_name = $patient['name'];
$patient_id = $patient['id'];

// Get all appointments for this patient
$appointments = mysqli_query($conn, "SELECT id, doctor_name, appointment_date, appointment_time FROM appointment WHERE patient_name='$patient_name' ORDER BY appointment_date DESC");

// Check if feedback is already submitted for an appointment
$feedback_query = mysqli_query($conn, "SELECT appointment_id FROM feedback WHERE patient_id=$patient_id");
$submitted_appointments = [];
while($row = mysqli_fetch_assoc($feedback_query)) {
    $submitted_appointments[] = $row['appointment_id'];
}

if(isset($_POST['submit_feedback']))
{
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $doctor_name = mysqli_real_escape_string($conn, $_POST['doctor_name']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    if(empty($appointment_id) || $appointment_id === 'select') {
        echo "<script>alert('Please select an appointment');</script>";
    } else if(!$rating) {
        echo "<script>alert('Please select a rating');</script>";
    } else {
        $query = "INSERT INTO feedback (appointment_id, patient_id, patient_name, doctor_name, rating, comments)
                  VALUES ($appointment_id, $patient_id, '$patient_name', '$doctor_name', $rating, '$comments')";

        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Thank you! Your feedback has been submitted successfully.'); window.location='patient_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error submitting feedback. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Appointment Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        body {
            background: var(--gradient-primary);
            min-height: 100vh;
            padding: 20px 0;
        }

        .feedback-container {
            max-width: 600px;
            margin: 50px auto;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .rating-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .rating-btn {
            width: 50px;
            height: 50px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            background: white;
            color: #333;
        }

        .rating-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .rating-btn.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .star {
            color: #ffc107;
            font-size: 20px;
        }

        .feedback-note {
            background: #e8f4f8;
            border-left: 4px solid var(--primary-color);
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .btn-submit {
            background: var(--primary-color);
            border: none;
            padding: 10px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .btn-back {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-back:hover {
            text-decoration: underline;
        }

        .appointment-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .appointment-item.submitted {
            opacity: 0.6;
            background: #e9ecef;
        }

        .submitted-badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 8px;
        }
    </style>
</head>

<body>

<div class="feedback-container">
    <a href="patient_dashboard.php" class="btn-back mb-3">← Back to Dashboard</a>

    <div class="card p-5">
        <h2 class="text-center mb-4" style="color: var(--primary-color);">📝 Submit Appointment Feedback</h2>

        <div class="feedback-note">
            <strong>ℹ️ Note:</strong> Your feedback helps us improve our services. Please share your experience with the appointment.
        </div>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label fw-bold">Select Appointment</label>
                <select name="appointment_id" id="appointmentSelect" class="form-select" required onchange="updateDoctorName()">
                    <option value="select">Choose an appointment...</option>
                    <?php 
                    mysqli_data_seek($appointments, 0);
                    while($appt = mysqli_fetch_assoc($appointments)): 
                        $is_submitted = in_array($appt['id'], $submitted_appointments);
                    ?>
                        <option value="<?php echo $appt['id']; ?>" data-doctor="<?php echo htmlspecialchars($appt['doctor_name']); ?>" <?php echo $is_submitted ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($appt['doctor_name'] . ' - ' . date('M d, Y \a\t g:i A', strtotime($appt['appointment_date'] . ' ' . $appt['appointment_time']))); ?>
                            <?php echo $is_submitted ? ' (Feedback Submitted)' : ''; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Doctor Name</label>
                <input type="text" id="doctorName" class="form-control" readonly placeholder="Selected doctor will appear here">
                <input type="hidden" name="doctor_name" id="doctorNameHidden">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Rating <span class="star">★</span></label>
                <p class="text-muted mb-2">How would you rate your appointment experience?</p>
                <div class="rating-group">
                    <input type="radio" name="rating" value="1" id="rating1" style="display: none;">
                    <label for="rating1" class="rating-btn" data-rating="1" onclick="selectRating(1)">1</label>

                    <input type="radio" name="rating" value="2" id="rating2" style="display: none;">
                    <label for="rating2" class="rating-btn" data-rating="2" onclick="selectRating(2)">2</label>

                    <input type="radio" name="rating" value="3" id="rating3" style="display: none;">
                    <label for="rating3" class="rating-btn" data-rating="3" onclick="selectRating(3)">3</label>

                    <input type="radio" name="rating" value="4" id="rating4" style="display: none;">
                    <label for="rating4" class="rating-btn" data-rating="4" onclick="selectRating(4)">4</label>

                    <input type="radio" name="rating" value="5" id="rating5" style="display: none;">
                    <label for="rating5" class="rating-btn" data-rating="5" onclick="selectRating(5)">5</label>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Comments (Optional)</label>
                <textarea name="comments" class="form-control" rows="4" placeholder="Share your feedback and suggestions..." style="border-radius: 8px; border: 2px solid #e0e0e0;"></textarea>
            </div>

            <button type="submit" name="submit_feedback" class="btn btn-submit w-100">Submit Feedback</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function updateDoctorName() {
        const select = document.getElementById('appointmentSelect');
        const selectedOption = select.options[select.selectedIndex];
        const doctorName = selectedOption.getAttribute('data-doctor');
        
        document.getElementById('doctorName').value = doctorName || '';
        document.getElementById('doctorNameHidden').value = doctorName || '';
    }

    function selectRating(rating) {
        document.getElementById('rating' + rating).checked = true;
        
        // Update button styling
        document.querySelectorAll('.rating-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
        document.querySelector(`[data-rating="${rating}"]`).classList.add('selected');
    }
</script>

</body>
</html>
