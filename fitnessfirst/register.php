<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    
    if (empty($full_name) || empty($email) || empty($username) || empty($password) || empty($cpassword)) {
        echo json_encode(['success' => false, 'error' => "Please fill all fields."]);
        exit();
    }

  
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => "Invalid email format."]);
        exit();
    }

    
    if ($password !== $cpassword) {
        echo json_encode(['success' => false, 'error' => "Passwords do not match."]);
        exit();
    }

    
    $check = $conn->prepare("SELECT * FROM register WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => "Username or email already exists."]);
        exit();
    }

 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  
    $stmt = $conn->prepare("INSERT INTO register (full_name, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => "Failed to register."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => "Invalid request method."]);
}
?>
