<?php
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);
$students = $input['students'];
$gradeType = $input['gradeType'];

foreach ($students as $student) {
    $studentName = $student['student_name'];
    $studentNumber = $student['student_number'];
    $grade = $student[$gradeType];

    // Fetch student ID based on student number
    $stmt = $db->prepare("SELECT id FROM students WHERE s_number = ?");
    $stmt->bind_param("s", $studentNumber);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($student_id);
        $stmt->fetch();

        // Check if the grade already exists for the student in the specified track
        $stmt2 = $db->prepare("SELECT id FROM student_grads WHERE s_id = ? AND track_id = ? AND state = 1");
        $stmt2->bind_param("ii", $student_id, $track_id);
        $stmt2->execute();
        $stmt2->store_result();

        if ($stmt2->num_rows > 0) {
            // Update existing grade
            $stmt2->bind_result($grade_id);
            $stmt2->fetch();

            $stmt3 = $db->prepare("UPDATE student_grads SET $gradeType = ? WHERE id = ?");
            $stmt3->bind_param("si", $grade, $grade_id);
            $stmt3->execute();
        } else {
            // Insert new grade record
            $stmt3 = $db->prepare("INSERT INTO student_grads (s_id, track_id, $gradeType, state) VALUES (?, ?, ?, 1)");
            $stmt3->bind_param("iis", $student_id, $track_id, $grade);
            $stmt3->execute();
        }

        $stmt2->close();
    }
}

$stmt->close();
$db->close();

$response = array("status" => "success", "message" => "Grades data processed successfully.");
echo json_encode($response);
?>
