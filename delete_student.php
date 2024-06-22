<?php
include 'database.php';

$student_id = $_GET['id'];
$db->begin_transaction();

try {
    $stmt = $db->prepare("UPDATE students SET state = 0 WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $stmt = $db->prepare("UPDATE users SET state = 0 WHERE s_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        
        $stmt = $db->prepare("UPDATE student_grads SET state = 0 WHERE s_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt = $db->prepare("UPDATE student_behavior SET state = 0 WHERE s_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $db->commit();
        $response = array("status" => "success", "message" => "State updated successfully.");
    } else {
        $db->rollback();
        $response = array("status" => "error", "message" => "Student not found.");
    }
} catch (Exception $e) {
    $db->rollback();
    $response = array("status" => "error", "message" => "Failed to update state.");
}

$stmt->close();
$db->close();

echo json_encode($response);
?>
