<?php
include 'database.php';

$stmt = $db->prepare("SELECT name, phone, email, s_number FROM students WHERE state=1");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $row['password'] = '';  // Add a placeholder for password
        $students[] = $row;
    }

    $response = array("status" => "success", "data" => $students);
} else {
    $response = array("status" => "error", "message" => "Failed to retrieve student data.");
}

$stmt->close();
$db->close();

// Return JSON response
echo json_encode($response);
?>
