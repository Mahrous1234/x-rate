<?php
include 'database.php';

$student_id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM students WHERE s_number = ? AND state=1");
$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch the student data
        $student_data = $result->fetch_assoc();
        $response = array("status" => "success", "data" => $student_data);
    } else {
        $response = array("status" => "error", "message" => "Student not found.");
    }
} else {
    $response = array("status" => "error", "message" => "Failed to retrieve student data.");
}

$stmt->close();
$db->close();

// Return JSON response
echo json_encode($response);
?>
