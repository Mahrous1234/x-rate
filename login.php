<?php
include 'database.php';
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['email']) && isset($input['password'])) {
    $email = $input['email'];
    $password = $input['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $db->prepare("
        SELECT u.id AS user_id, u.password, u.type, u.name, s.id AS student_id
        FROM users u
        LEFT JOIN students s ON u.email = s.email  -- Adjust this line based on the actual relationship between users and students
        WHERE u.email = ? AND u.state = 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            echo json_encode(array(
                "success" => true,
                "message" => "Login successful",
                "user_id" => $row['user_id'],
                "student_id" => $row['student_id'],  // Return the student_id
                "type" => $row['type'],
                "name" => $row['name']
            ));
        } else {
            echo json_encode(array("success" => false, "message" => "Invalid password"));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "No user found with that email"));
    }

    $stmt->close();
} else {
    echo json_encode(array("success" => false, "message" => "Email and password required"));
}

$db->close();
?>
