<?php
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);

$dealing_teach = $input['dealing_teach'];
$student_id = $input['student_id'];
$dealing_other = $input['dealing_other'];
$attendance = $input['attendance'];

// Check if a record for the student already exists
$stmt = $db->prepare("SELECT id FROM student_behavior WHERE s_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Record exists, update it
    $stmt->bind_result($behavior_id);
    $stmt->fetch();
    $stmt->close();
    
    $stmt = $db->prepare("UPDATE student_behavior SET dealing_teach = ?, dealing_other = ?, attendance = ? WHERE id = ?");
    $stmt->bind_param("iiii", $dealing_teach, $dealing_other, $attendance, $behavior_id);
    
    if ($stmt->execute()) {
        $response = array("status" => "success", "message" => "Student behavior updated successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to update student behavior.");
    }
} else {
    // Record does not exist, insert it
    $stmt->close();
    
    $stmt = $db->prepare("INSERT INTO student_behavior (s_id, dealing_teach, dealing_other, attendance) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $student_id, $dealing_teach, $dealing_other, $attendance);
    
    if ($stmt->execute()) {
        $response = array("status" => "success", "message" => "Student behavior inserted successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to insert student behavior.");
    }
}

$stmt->close();
$db->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
