<?php
// include 'database.php';

// $student_id = $_GET['id'];

// $stmt = $db->prepare("SELECT * FROM students WHERE id = ? AND state=1");
// $stmt->bind_param("i", $student_id);

// if ($stmt->execute()) {
//     $result = $stmt->get_result();
    
//     if ($result->num_rows > 0) {
//         // Fetch the student data
//         $student_data = $result->fetch_assoc();
//         $response = array("status" => "success", "data" => $student_data);
//     } else {
//         $response = array("status" => "error", "message" => "Student not found.");
//     }
// } else {
//     $response = array("status" => "error", "message" => "Failed to retrieve student data.");
// }

// $stmt->close();
// $db->close();

// // Return JSON response
// echo json_encode($response);
?>


<?php
include 'database.php';

// Set the correct content type header
header('Content-Type: application/json');

$response = array();

if (isset($_GET['id'])) {
    // Fetch specific student
    $student_id = $_GET['id'];

    $stmt = $db->prepare("SELECT * FROM students WHERE id = ? AND state = 1");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Fetch the student data
            $student_data = $result->fetch_assoc();
            $response = array("status" => "success", "data" => $student_data);
        } else {
            $response = array("status" => "error", "message" => "Student not found.");
        }
    } else {
        $response = array("status" => "error", "message" => "Failed to retrieve student data.");
    }

    $stmt->close();
} else {
    // Fetch all students
    $stmt = $db->prepare("SELECT * FROM students WHERE state = 1");

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
