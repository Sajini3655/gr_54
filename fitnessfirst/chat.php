<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
  exit();
}

$msg = $_POST['message'] ?? '';
$trainer_name = $_POST['trainer_name'] ?? 'User';

$msg = trim($conn->real_escape_string($msg));
$trainer_name = trim($conn->real_escape_string($trainer_name));

if (empty($msg)) {
  echo json_encode(["success" => false, "message" => "Message cannot be empty."]);
  exit();
}

$sql = "INSERT INTO chat_messages (trainer_name, message) VALUES ('$trainer_name', '$msg')";
if ($conn->query($sql) === TRUE) {
  $message_id = $conn->insert_id;
  $result = $conn->query("SELECT timestamp FROM chat_messages WHERE id = $message_id");
  if ($result && $row = $result->fetch_assoc()) {
    echo json_encode([
      "success" => true,
      "message" => "Message sent successfully.",
      "timestamp" => $row['timestamp']
    ]);
  } else {
    echo json_encode(["success" => true, "message" => "Message sent, timestamp not found.", "timestamp" => ""]);
  }
} else {
  echo json_encode(["success" => false, "message" => "Failed to send message: " . $conn->error]);
}

$conn->close();
?>
