<?php
// Generate correct BCrypt hashes
$passwords = array(
    'admin123' => 'admin123',
    'doctor123' => 'doctor123',
    'patient123' => 'patient123'
);

echo "<h2>🔐 Generated BCrypt Hashes</h2>";
echo "<p>Copy these hashes and use them in phpMyAdmin SQL queries:</p>";
echo "<hr>";

foreach($passwords as $password => $label) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    echo "<strong>" . htmlspecialchars($label) . ":</strong><br>";
    echo "<code>" . $hash . "</code><br>";
    echo "Test: " . (password_verify($password, $hash) ? "✅ VERIFIED" : "❌ FAILED") . "<br><br>";
}

echo "<hr>";
echo "<h3>SQL Update Queries:</h3>";
echo "<pre>";
$admin_hash = password_hash('admin123', PASSWORD_BCRYPT);
$doctor_hash = password_hash('doctor123', PASSWORD_BCRYPT);
$patient_hash = password_hash('patient123', PASSWORD_BCRYPT);

echo "UPDATE admin SET password = '" . $admin_hash . "' WHERE username = 'admin';\n\n";
echo "UPDATE doctor SET password = '" . $doctor_hash . "' WHERE email = 'doctor@gmail.com';\n\n";
echo "UPDATE patient SET password = '" . $patient_hash . "' WHERE email = 'patient@gmail.com';\n\n";
echo "UPDATE patient SET password = '" . $patient_hash . "' WHERE email = 'kaira@gmail.com';\n";
echo "</pre>";
?>
