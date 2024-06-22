<?php
include 'database.php';

// Get the requested grade type from the query parameter
$gradeType = isset($_GET['grade_type']) ? $_GET['grade_type'] : '';

// Validate the grade type
$validGrades = ['midterm', 'final', 'assignment', 'quiz'];
if (!in_array($gradeType, $validGrades)) {
    echo json_encode(["status" => "error", "message" => "Invalid grade type."]);
    exit();
}

// Prepare the SQL statement to include all students and join with tracks table
$stmt = $db->prepare("
    SELECT 
        students.id AS student_id, 
        students.name AS student_name, 
        students.s_number AS student_number, 
        students.email AS student_email, 
        tracks.id AS subject_id, 
        tracks.name AS subject_name, 
        COALESCE(student_grads.$gradeType, 'No Grade') AS grade
    FROM 
        students
    LEFT JOIN 
        student_grads ON student_grads.s_id = students.id AND student_grads.state = 1
    LEFT JOIN 
        tracks ON student_grads.track_id = tracks.id
    WHERE 
        students.state = 1");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $grades = array();

    while ($row = $result->fetch_assoc()) {
        // Rename the grade column to the specified grade type
        $row[$gradeType] = $row['grade'];
        unset($row['grade']);
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
