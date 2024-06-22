<?php 
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);

$dealing_teach = $input['dealing_teach'];
$student_id = intval($input['student_id']); // Convert to integer
$dealing_other = $input['dealing_other'];
$attendance = $input['attendance'];

// Check if the student_id already exists in the student_behavior table
$check_stmt = $db->prepare("SELECT s_id FROM student_behavior WHERE s_id = ?");
$check_stmt->bind_param("i", $student_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Student exists, update the record
    $update_stmt = $db->prepare("UPDATE student_behavior SET dealing_teach = ?, dealing_other = ?, attendance = ? WHERE s_id = ?");
    $update_stmt->bind_param("iiii", $dealing_teach, $dealing_other, $attendance, $student_id);

    if ($update_stmt->execute()) {
        $response = array("status" => "success", "message" => "student_behavior updated successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to update student_behavior data.");
    }

    $update_stmt->close();
} else {
    // Student does not exist, insert a new record
    $insert_stmt = $db->prepare("INSERT INTO student_behavior (s_id, dealing_teach, dealing_other, attendance) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("iiii", $student_id, $dealing_teach, $dealing_other, $attendance);

    if ($insert_stmt->execute()) {
        $response = array("status" => "success", "message" => "student_behavior inserted successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to insert student_behavior data.");
    }

    $insert_stmt->close();
}

$check_stmt->close();
$db->close();
echo json_encode($response);
?>
