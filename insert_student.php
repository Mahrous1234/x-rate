<?php
include 'database.php';

// Retrieve input data and decode JSON
$input = json_decode(file_get_contents('php://input'), true);

// Check if input data is valid
if (is_null($input) || !is_array($input)) {
    $response = array("status" => "error", "message" => "Invalid input data.");
    echo json_encode($response);
    exit;
}

// Validate required fields
$required_fields = ['name', 'phone', 'email', 'password', 's_number'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        $response = array("status" => "error", "message" => "Missing required field: $field.");
        echo json_encode($response);
        exit;
    }
}

// Retrieve data from input array
$name = $input['name'];
$phone = $input['phone'];
$email = $input['email'];
$password = $input['password'];
$s_number = $input['s_number'];

// Check if email already exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $response = array("status" => "error", "message" => "Email already exists.");
} else {
    // Insert student data
    $stmt = $db->prepare("INSERT INTO students (name, phone, email, s_number) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $email, $s_number);

    if ($stmt->execute()) {
        $student_id = $stmt->insert_id;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $type='student';

        // Insert user data
        $stmt = $db->prepare("INSERT INTO users (s_id, name, email, password, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $student_id, $name, $email, $hashed_password, $type);

        if ($stmt->execute()) {
            $response = array("status" => "success", "message" => "Student and user data inserted successfully.");
        } else {
            $response = array("status" => "error", "message" => "Failed to insert user data.");
        }
    } else {
        $response = array("status" => "error", "message" => "Failed to insert student data.");
    }
}

$stmt->close();
$db->close();
echo json_encode($response);
?>
