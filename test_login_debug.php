<?php
include("db.php");

echo "<h2>🔍 Login Debug Test</h2>";
echo "<hr>";

// Check admin users
echo "<h3>Admin Users:</h3>";
$result = mysqli_query($conn, "SELECT username, password FROM admin");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "Username: " . htmlspecialchars($row['username']) . "<br>";
        echo "Password Hash: " . substr($row['password'], 0, 30) . "...<br>";
        echo "Testing password_verify('admin123', hash):<br>";
        $verify_result = password_verify('admin123', $row['password']);
        echo "Result: " . ($verify_result ? "✅ SUCCESS" : "❌ FAILED") . "<br><br>";
    }
} else {
    echo "❌ No admin users found<br><br>";
}

// Check doctor users
echo "<h3>Doctor Users:</h3>";
$result = mysqli_query($conn, "SELECT email, password FROM doctor");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "Email: " . htmlspecialchars($row['email']) . "<br>";
        echo "Password Hash: " . substr($row['password'], 0, 30) . "...<br>";
        echo "Testing password_verify('doctor123', hash):<br>";
        $verify_result = password_verify('doctor123', $row['password']);
        echo "Result: " . ($verify_result ? "✅ SUCCESS" : "❌ FAILED") . "<br><br>";
    }
} else {
    echo "❌ No doctor users found<br><br>";
}

// Check patient users
echo "<h3>Patient Users:</h3>";
$result = mysqli_query($conn, "SELECT email, password FROM patient");
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "Email: " . htmlspecialchars($row['email']) . "<br>";
        echo "Password Hash: " . substr($row['password'], 0, 30) . "...<br>";
        echo "Testing password_verify('patient123', hash):<br>";
        $verify_result = password_verify('patient123', $row['password']);
        echo "Result: " . ($verify_result ? "✅ SUCCESS" : "❌ FAILED") . "<br><br>";
    }
} else {
    echo "❌ No patient users found<br><br>";
}

// Test database connection
echo "<h3>Database Connection:</h3>";
if($conn) {
    echo "✅ Connected to: " . $GLOBALS['database'] . "<br>";
} else {
    echo "❌ Connection Failed<br>";
}
?>
