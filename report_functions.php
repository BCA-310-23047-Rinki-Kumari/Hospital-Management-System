<?php
// Report Generation Functions

// Get patient medical history
function getPatientMedicalHistory($conn, $patient_id) {
    $patient = mysqli_query($conn, "SELECT * FROM patient WHERE id='$patient_id'");
    $patient_data = mysqli_fetch_assoc($patient);
    
    $appointments = mysqli_query($conn, "SELECT * FROM appointment WHERE patient_name='{$patient_data['name']}' ORDER BY appointment_date DESC");
    $prescriptions = mysqli_query($conn, "SELECT * FROM prescription WHERE patient_id='$patient_id' ORDER BY issued_date DESC");
    
    return array(
        'patient' => $patient_data,
        'appointments' => $appointments,
        'prescriptions' => $prescriptions
    );
}

// Get doctor's patients
function getDoctorPatients($conn, $doctor_id) {
    $doctor = mysqli_query($conn, "SELECT * FROM doctor WHERE id='$doctor_id'");
    $doctor_data = mysqli_fetch_assoc($doctor);
    
    $patients = mysqli_query($conn, "SELECT DISTINCT p.* FROM patient p JOIN appointment a ON p.name=a.patient_name WHERE a.doctor_name='{$doctor_data['name']}' ORDER BY p.name");
    
    return array(
        'doctor' => $doctor_data,
        'patients' => $patients
    );
}

// Get appointment statistics
function getAppointmentStats($conn) {
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointment"));
    $today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointment WHERE appointment_date=CURDATE()"));
    $upcoming = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointment WHERE appointment_date>CURDATE()"));
    
    return array(
        'total' => $total['count'],
        'today' => $today['count'],
        'upcoming' => $upcoming['count']
    );
}

// Get prescription statistics
function getPrescriptionStats($conn) {
    $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM prescription"));
    $this_month = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM prescription WHERE MONTH(issued_date)=MONTH(CURDATE()) AND YEAR(issued_date)=YEAR(CURDATE())"));
    
    return array(
        'total' => $total['count'],
        'this_month' => $this_month['count']
    );
}

// Format date
function formatDate($date) {
    return date("d M Y", strtotime($date));
}

// Format time
function formatTime($time) {
    return date("h:i A", strtotime($time));
}

?>
