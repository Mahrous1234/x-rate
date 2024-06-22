<?php
include 'database.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);

if ($input === null) {
    echo json_encode(array("status" => "error", "message" => "Invalid JSON input."));
    exit;
}

$student_id = isset($input['student_id']) ? intval($input['student_id']) : null;
$track_id = isset($input['track_id']) ? intval($input['track_id']) : null;

if ($student_id === null) {
    echo json_encode(array("status" => "error", "message" => "Student ID is required."));
    exit;
}

$stmt = null;

// Prepare the SQL statement
if ($track_id !== null) {
    $stmt = $db->prepare("
        SELECT student_grads.id, student_grads.s_id, student_grads.final, student_grads.midterm, student_grads.quiz, 
        student_grads.assignment, tracks.name AS track_name, tracks.id AS track_id
        FROM student_grads 
        JOIN tracks ON student_grads.track_id = tracks.id 
        JOIN users ON student_grads.s_id = users.s_id 
        WHERE student_grads.s_id = ? AND student_grads.track_id = ? AND student_grads.state = 1 AND users.state = 1");
    $stmt->bind_param("ii", $student_id, $track_id);
} else {
    $stmt = $db->prepare("
        SELECT student_grads.id, student_grads.s_id, student_grads.final, student_grads.midterm, student_grads.quiz, 
        student_grads.assignment, tracks.name AS track_name, tracks.id AS track_id
        FROM student_grads 
        JOIN tracks ON student_grads.track_id = tracks.id 
        JOIN users ON student_grads.s_id = users.s_id 
        WHERE student_grads.s_id = ? AND student_grads.state = 1 AND users.state = 1");
    $stmt->bind_param("i", $student_id);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $grades = array();

    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }

    if (count($grades) > 0) {
        $response = array("status" => "success", "data" => $grades);
    } else {
        $response = array("status" => "error", "message" => "No grades found for the given user.");
    }
} else {
    $response = array("status" => "error", "message" => "Failed to retrieve grades.");
}

$stmt->close();
$db->close();

echo json_encode($response);
?>
