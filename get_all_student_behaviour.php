<?php
include 'database.php';

// Set the correct content type header
header('Content-Type: application/json');

$response = array();

if (isset($_GET['id'])) {
    // Fetch specific student
    $student_id = $_GET['id'];

    $sql = "
        SELECT 
            s.id AS student_id,
            s.name AS student_name,       -- Replace 'name' with the actual column name for student name
            s.s_number AS student_number, -- Replace 's_number' with the actual column name for student number
            sb.attendance,
            sb.dealing_teach,
            sb.dealing_other
        FROM 
            students s
        LEFT JOIN 
            student_behavior sb ON s.id = sb.s_id
        WHERE 
            s.id = ? AND sb.state IS NOT NULL
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Fetch the student data
            $student_data = $result->fetch_assoc();
            $response = array("status" => "success", "data" => $student_data);
        } else {
            $response = array("status" => "error", "message" => "Student not found or no behavior record with non-null state.");
        }
    } else {
        $response = array("status" => "error", "message" => "Failed to retrieve student data.");
    }

    $stmt->close();
} else {
    // Fetch all students with non-null state in student_behavior
    $sql = "
        SELECT 
            s.id AS student_id,
            s.name AS student_name,       -- Replace 'name' with the actual column name for student name
            s.s_number AS student_number, -- Replace 's_number' with the actual column name for student number
            sb.attendance,
            sb.dealing_teach,
            sb.dealing_other
        FROM 
            students s
        LEFT JOIN 
            student_behavior sb ON s.id = sb.s_id
        WHERE 
            sb.state IS NOT NULL
    ";

    $stmt = $db->prepare($sql);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $students = array();
            while ($student_data = $result->fetch_assoc()) {
                $students[] = $student_data;
            }
            $response = array("status" => "success", "data" => $students);
        } else {
            $response = array("status" => "error", "message" => "No students found.");
        }
    } else {
        $response = array("status" => "error", "message" => "Failed to retrieve students data.");
    }

    $stmt->close();
}

$db->close();

// Return JSON response
echo json_encode($response);
?>
