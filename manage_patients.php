<?php
session_start();
include("db.php");

// Allow both admin and doctor access
$is_admin = isset($_SESSION['admin']);
$is_doctor = isset($_SESSION['doctor']);

if(!$is_admin && !$is_doctor){
    header("Location: admin_login.php");
    exit;
}

$res = mysqli_query($conn, "SELECT * FROM patient ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Manage Patients</h1>

    <div class="mb-3">
        <a href="<?php echo $is_admin ? 'admin_dashboard.php' : 'doctor_dashboard.php'; ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(!$res || mysqli_num_rows($res) == 0): ?>
        <div class="alert alert-info">No patients found.</div>
    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <?php if($is_admin): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? $row['full_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['age'] ?? ''); ?></td>
                    <?php if($is_admin): ?>
                        <td>
                            <a href="delete_patient.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this patient?');">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
