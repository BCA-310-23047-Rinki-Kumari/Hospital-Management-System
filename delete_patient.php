<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit;
}

// Validate and sanitize ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id > 0) {
    // Fetch patient name so we can also delete related appointments
    $select_stmt = $conn->prepare("SELECT name FROM patient WHERE id = ?");
    $select_stmt->bind_param("i", $id);
    $select_stmt->execute();
    $select_result = $select_stmt->get_result();

    if($select_result && $select_result->num_rows > 0) {
        $patient = $select_result->fetch_assoc();
        $patient_name = $patient['name'];

        $conn->begin_transaction();

        $delete_feedback = $conn->prepare("DELETE FROM feedback WHERE patient_id = ?");
        $delete_feedback->bind_param("i", $id);
        if(!$delete_feedback->execute()) {
            $conn->rollback();
            die("Delete failed: " . $conn->error);
        }

        $delete_prescription = $conn->prepare("DELETE FROM prescription WHERE patient_id = ?");
        $delete_prescription->bind_param("i", $id);
        if(!$delete_prescription->execute()) {
            $conn->rollback();
            die("Delete failed: " . $conn->error);
        }

        $delete_appointments = $conn->prepare("DELETE FROM appointment WHERE patient_name = ?");
        $delete_appointments->bind_param("s", $patient_name);
        if(!$delete_appointments->execute()) {
            $conn->rollback();
            die("Delete failed: " . $conn->error);
        }

        $delete_patient = $conn->prepare("DELETE FROM patient WHERE id = ?");
        $delete_patient->bind_param("i", $id);
        if(!$delete_patient->execute()) {
            $conn->rollback();
            die("Delete failed: " . $conn->error);
        }

        $conn->commit();
    }
}

header('Location: manage_patients.php');
exit;
