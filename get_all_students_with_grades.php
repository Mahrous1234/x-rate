<?php
include 'database.php';

// Prepare the SQL statement
$stmt = $db->prepare("
    SELECT 
        student_grads.id, 
        student_grads.s_id, 
        student_grads.final, 
        student_grads.midterm, 
        student_grads.quiz, 
        student_grads.assignment, 
        student_grads.track_id, 
        students.name AS student_name, 
        students.s_number AS student_number, 
        tracks.name AS track_name
    FROM 
        student_grads 
    JOIN 
        students ON student_grads.s_id = students.id 
    JOIN 
        tracks ON student_grads.track_id = tracks.id
    WHERE 
        student_grads.state = 1 
    AND 
        students.state = 1");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $grades = array();

    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }

    if (count($grades) > 0) {
        $response = array("status" => "success", "data" => $grades);
    } else {
        $response = array("status" => "error", "message" => "No grades found.");
    }
} else {
    $response = array("status" => "error", "message" => "Failed to retrieve grades.");
}

$stmt->close();
$db->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
