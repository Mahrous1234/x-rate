<?php
include 'database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['student_id'])) {
    $student_id = $db->real_escape_string($input['student_id']);

    $sql = "SELECT notifications.message, notifications.track_id, tracks.name 
            FROM notifications 
            JOIN tracks ON notifications.track_id = tracks.id 
            WHERE notifications.student_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $notifications = array();
        while ($row = $result->fetch_assoc()) {
            $notifications[] = array(
                "message" => $row['message'], 
                "track_id" => $row['track_id'], 
                "track_name" => $row['name']
            );
        }
        echo json_encode(array("success" => true, "notifications" => $notifications));
    } else {
        echo json_encode(array("success" => false, "message" => "No notifications found for the given student_id"));
    }

    $stmt->close();
} else {
    echo json_encode(array("success" => false, "message" => "student_id is required"));
}

$db->close();
?>
