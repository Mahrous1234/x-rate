<?php 
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);

$assest_id = $input['assest_id'];
$student_id = $input['student_id'];
$message = $input['message'];
$track_id = $input['track_id'];

// Check if the track_id already exists in the database for the given student_id
$stmt_check = $db->prepare("SELECT * FROM notifications WHERE student_id = ? AND track_id = ?");
$stmt_check->bind_param("ii", $student_id, $track_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // If the track_id exists for the given student_id, update the message
    $stmt_update = $db->prepare("UPDATE notifications SET message = ? WHERE student_id = ? AND track_id = ?");
    $stmt_update->bind_param("sii", $message, $student_id, $track_id);

    if ($stmt_update->execute()) {
        $response = array("status" => "success", "message" => "Notification updated successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to update notification.");
    }

    $stmt_update->close();
} else {
    // If the track_id doesn't exist for the given student_id, insert a new record
    $stmt_insert = $db->prepare("INSERT INTO notifications (assest_id, student_id, message, track_id) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("iisi", $assest_id, $student_id, $message, $track_id);

    if ($stmt_insert->execute()) {
        $response = array("status" => "success", "message" => "Notification inserted successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to insert notification.");
    }

    $stmt_insert->close();
}

$stmt_check->close();
$db->close();
echo json_encode($response);
?>
