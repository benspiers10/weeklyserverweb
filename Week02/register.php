<?php

$conn = new mysqli('localhost', 'root', '', 'registration'); // Connect to database

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

// Get user input safely
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
$number = $_POST['number'] ?? 0;

// Check if the email already exists
$checkStmt = $conn->prepare("SELECT email FROM registration WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();


if ($checkStmt->num_rows > 0) {
    echo "<script>
    alert('Error: This email is already registered. Please use a different email.');
    window.location.href = 'index.html';
    </script>";
} else {
    // Secure SQL query using prepared statement
    $stmt = $conn->prepare("INSERT INTO registration (firstName, lastName, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $password, $number);

    if ($stmt->execute()) {
        echo "<script>
        alert('Registration successful');
        window.location.href = 'index.html';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$checkStmt->close();
}

$conn->close();

?>
