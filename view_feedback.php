<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get all feedback with appointment details
$feedback_query = "SELECT f.*, a.appointment_date, a.appointment_time 
                   FROM feedback f 
                   LEFT JOIN appointment a ON f.appointment_id = a.id 
                   ORDER BY f.submitted_date DESC";

$feedback_result = mysqli_query($conn, $feedback_query);

// Get statistics
$total_feedback = mysqli_num_rows($feedback_result);
$avg_rating_query = "SELECT AVG(rating) as average_rating FROM feedback";
$avg_rating_result = mysqli_query($conn, $avg_rating_query);
$avg_rating_row = mysqli_fetch_assoc($avg_rating_result);
$average_rating = $avg_rating_row['average_rating'] ? round($avg_rating_row['average_rating'], 1) : 0;

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
            min-height: 100vh;
            padding: 20px 0;
        }

        .header-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
            color: white !important;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .header-section h1 {
            color: white !important;
            font-weight: bold;
        }

        .header-section p {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-box h5 {
            color: #666;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .stat-box .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .feedback-card {
            background: #4A90E2 !important;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            color: white !important;
        }

        .feedback-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            background: #2E5DA8 !important;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .patient-info {
            font-weight: 600;
            color: white !important;
            font-size: 1.1rem;
        }

        .doctor-info {
            color: #e0e0e0 !important;
            font-size: 0.95rem;
        }

        .rating-display {
            display: inline-block;
            background: #ffc107 !important;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .feedback-date {
            color: #e0e0e0 !important;
            font-size: 0.85rem;
        }

        .appointment-time {
            background: rgba(255, 255, 255, 0.2) !important;
            padding: 8px 12px;
            border-radius: 6px;
            color: white !important;
            font-size: 0.9rem;
        }

        .comments-section {
            background: rgba(255, 255, 255, 0.15) !important;
            padding: 15px;
            border-radius: 8px;
            margin-top: 12px;
            color: white !important;
            line-height: 1.6;
            border-left: 3px solid white;
        }

        .no-feedback {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .no-feedback-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .filter-section {
            background: white !important;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-section h5 {
            color: #333 !important;
        }

        .rating-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .filter-btn {
            padding: 6px 15px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #666;
        }

        .filter-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
    </style>
</head>

<body>

<div class="container mt-5">
    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

    <div class="header-section">
        <h1 class="mb-0">📊 Appointment Feedback</h1>
        <p class="mb-0 mt-2 opacity-75">View and analyze patient feedback for appointments</p>
    </div>

    <!-- Statistics Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-box">
                <h5>Total Feedback</h5>
                <div class="number"><?php echo $total_feedback; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <h5>Average Rating</h5>
                <div class="number" style="color: #ffc107;">
                    <?php echo $average_rating; ?> <span style="font-size: 1.5rem;">★</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-box">
                <h5>Patient Satisfaction</h5>
                <div class="number" style="color: #28a745;">
                    <?php echo $total_feedback > 0 ? round(($average_rating / 5) * 100) : 0; ?>%
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback List Section -->
    <div class="filter-section">
        <h5 class="mb-3">Filter by Rating</h5>
        <div class="rating-filter">
            <button class="filter-btn active" data-rating="all">All Ratings</button>
            <button class="filter-btn" data-rating="5">5 ★</button>
            <button class="filter-btn" data-rating="4">4 ★</button>
            <button class="filter-btn" data-rating="3">3 ★</button>
            <button class="filter-btn" data-rating="2">2 ★</button>
            <button class="filter-btn" data-rating="1">1 ★</button>
        </div>
    </div>

    <div id="feedbackContainer">
        <?php 
        if($total_feedback === 0): 
        ?>
            <div class="no-feedback">
                <div class="no-feedback-icon">📭</div>
                <h4>No Feedback Yet</h4>
                <p>Patients haven't submitted any feedback yet. Check back soon!</p>
            </div>
        <?php 
        else: 
            mysqli_data_seek($feedback_result, 0);
            while($feedback = mysqli_fetch_assoc($feedback_result)): 
                $stars = str_repeat('★', $feedback['rating']) . str_repeat('☆', 5 - $feedback['rating']);
                $submitted_date = new DateTime($feedback['submitted_date']);
                $formatted_date = $submitted_date->format('M d, Y \a\t h:i A');
                $appointment_date = $feedback['appointment_date'] ? date('M d, Y', strtotime($feedback['appointment_date'])) : 'N/A';
                $appointment_time = $feedback['appointment_time'] ? date('g:i A', strtotime($feedback['appointment_time'])) : 'N/A';
        ?>
            <div class="feedback-card" data-rating="<?php echo $feedback['rating']; ?>">
                <div class="feedback-header">
                    <div>
                        <div class="patient-info">
                            👤 <?php echo htmlspecialchars($feedback['patient_name']); ?>
                        </div>
                        <div class="doctor-info">
                            👨‍⚕️ Dr. <?php echo htmlspecialchars($feedback['doctor_name']); ?>
                        </div>
                    </div>
                    <div class="rating-display">
                        <?php echo $stars; ?> (<?php echo $feedback['rating']; ?>/5)
                    </div>
                </div>

                <div style="margin-bottom: 10px;">
                    <span class="appointment-time">
                        📅 Appointment: <?php echo $appointment_date; ?> at <?php echo $appointment_time; ?>
                    </span>
                </div>

                <div class="feedback-date">
                    🕐 Feedback submitted on <?php echo $formatted_date; ?>
                </div>

                <?php if(!empty($feedback['comments'])): ?>
                    <div class="comments-section">
                        <strong>Comments:</strong><br>
                        <?php echo nl2br(htmlspecialchars($feedback['comments'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php 
            endwhile; 
        endif; 
        ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Filter feedback by rating
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');

            // Update active button
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter feedback cards
            document.querySelectorAll('.feedback-card').forEach(card => {
                if(rating === 'all') {
                    card.style.display = 'block';
                } else {
                    card.style.display = card.getAttribute('data-rating') === rating ? 'block' : 'none';
                }
            });
        });
    });
</script>

</body>
</html>
