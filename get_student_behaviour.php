<?php
include 'database.php';

// Check if 'id' parameter is set
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']); // Convert the id to an integer

    $stmt = $db->prepare("SELECT * FROM student_behavior WHERE s_id = ? AND state=1");
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
} else {
    $response = array("status" => "error", "message" => "No student ID provided.");
}

// Return JSON response
echo json_encode($response);
?>
