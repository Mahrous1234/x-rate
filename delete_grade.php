<?php
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);
$student_id = $input['student_id'];
$track_id = $input['track_id'];
$type = $input['type'];

$db->begin_transaction();

try {
    if($type == 'final'){
        $stmt = $db->prepare("
        UPDATE student_grads 
        SET final = 0
        WHERE s_id = ? AND track_id = ? AND state = 1");
    }else if($type == 'midterm'){
        $stmt = $db->prepare("
        UPDATE student_grads 
        SET midterm = 0 
        WHERE s_id = ? AND track_id = ? AND state = 1");
    }else if($type == 'assignment'){
        $stmt = $db->prepare("
        UPDATE student_grads 
        SET assignment = 0 
        WHERE s_id = ? AND track_id = ? AND state = 1");
    }else if($type == 'quiz'){
        $stmt = $db->prepare("
        UPDATE student_grads 
        SET quiz = 0
        WHERE s_id = ? AND track_id = ? AND state = 1");
    }
    $stmt->bind_param("ii", $student_id, $track_id);

    if ($stmt->execute()) {
        // Check if any rows were updated
        if ($stmt->affected_rows > 0) {
            // Commit transaction
            $db->commit();
            $response = array("status" => "success", "message" => "Grades updated successfully.");
        } else {
            // Rollback transaction if no grades were updated
            $db->rollback();
            $response = array("status" => "error", "message" => "No grades found for the given user and track.");
        }
    } else {
        // Rollback transaction in case of error
        $db->rollback();
        $response = array("status" => "error", "message" => "Failed to update grades.");
    }
} catch (Exception $e) {
    // Rollback transaction in case of error
    $db->rollback();
    $response = array("status" => "error", "message" => "Failed to update grades.");
}

$stmt->close();
$db->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
