<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

// Get all data for export
$doctors = mysqli_query($conn, "SELECT id, name, email, specialization FROM doctor");
$patients = mysqli_query($conn, "SELECT id, name, email, age FROM patient");
$appointments = mysqli_query($conn, "SELECT id, patient_name, doctor_name, appointment_date, appointment_time FROM appointment ORDER BY appointment_date DESC LIMIT 50");
$prescriptions = mysqli_query($conn, "SELECT id, patient_name, doctor_name, medicine_name, dosage, frequency, duration, issued_date FROM prescription ORDER BY issued_date DESC LIMIT 50");

// Create CSV content
$output = "HOSPITAL MANAGEMENT SYSTEM - EXPORT REPORT\n";
$output .= "Generated on: " . date("d-m-Y H:i:s") . "\n";
$output .= "================================================================\n\n";

// Doctors Section
$output .= "REGISTERED DOCTORS\n";
$output .= "================================================================\n";
$output .= "ID,Name,Email,Specialization\n";
while($doctor = mysqli_fetch_assoc($doctors)) {
    $output .= $doctor['id'] . "," . 
               "\"".$doctor['name']."\"," . 
               "\"".$doctor['email']."\"," . 
               "\"".$doctor['specialization']."\"\n";
}
$output .= "\n\n";

// Patients Section
$output .= "REGISTERED PATIENTS\n";
$output .= "================================================================\n";
$output .= "ID,Name,Email,Age\n";
while($patient = mysqli_fetch_assoc($patients)) {
    $output .= $patient['id'] . "," . 
               "\"".$patient['name']."\"," . 
               "\"".$patient['email']."\"," . 
               $patient['age'] . "\n";
}
$output .= "\n\n";

// Appointments Section
$output .= "APPOINTMENTS (RECENT 50)\n";
$output .= "================================================================\n";
$output .= "ID,Patient Name,Doctor Name,Date,Time\n";
while($appointment = mysqli_fetch_assoc($appointments)) {
    $output .= $appointment['id'] . "," . 
               "\"".$appointment['patient_name']."\"," . 
               "\"".$appointment['doctor_name']."\"," . 
               $appointment['appointment_date'] . "," . 
               $appointment['appointment_time'] . "\n";
}
$output .= "\n\n";

// Prescriptions Section
$output .= "PRESCRIPTIONS (RECENT 50)\n";
$output .= "================================================================\n";
$output .= "ID,Patient Name,Doctor Name,Medicine,Dosage,Frequency,Duration,Date Issued\n";
while($prescription = mysqli_fetch_assoc($prescriptions)) {
    $output .= $prescription['id'] . "," . 
               "\"".$prescription['patient_name']."\"," . 
               "\"".$prescription['doctor_name']."\"," . 
               "\"".$prescription['medicine_name']."\"," . 
               "\"".$prescription['dosage']."\"," . 
               "\"".$prescription['frequency']."\"," . 
               "\"".$prescription['duration']."\"," . 
               $prescription['issued_date'] . "\n";
}

// Set headers for file download
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="HMS_Report_' . date('d-m-Y_H-i-s') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output the CSV
echo $output;
exit();
?>
