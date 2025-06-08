<?php
header('Content-Type: application/json');


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit();
}

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

$user = trim($conn->real_escape_string($user));
$pass = trim($pass); 

if (empty($user) || empty($pass)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter both username and password."
    ]);
    exit();
}


$stmt = $conn->prepare("SELECT password FROM register WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    if (password_verify($pass, $hashedPassword)) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid username or password."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password."
    ]);
}

$stmt->close();
$conn->close();
?>
