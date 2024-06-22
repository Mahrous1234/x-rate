<?php
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);

$s_id = $input['s_id'];
$track_id = $input['track_id'];
if(isset($input['final'])){
    $grade = $input['final'];
}else if(isset($input['quiz'])){
    $grade = $input['quiz'];
}else if(isset($input['midterm'])){
    $grade = $input['midterm'];
}else if(isset($input['assignment'])){
    $grade = $input['assignment'];
}
$stmt = $db->prepare("SELECT id FROM student_grads WHERE s_id = ? AND track_id = ?");
$stmt->bind_param("ii", $s_id, $track_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($grade_id);
    $stmt->fetch();
    if(isset($input['final'])){
        $stmt = $db->prepare("UPDATE student_grads SET final = ? WHERE id = ?");
    }else if(isset($input['quiz'])){
        $stmt = $db->prepare("UPDATE student_grads SET quiz = ? WHERE id = ?");
    }else if(isset($input['midterm'])){
        $stmt = $db->prepare("UPDATE student_grads SET midterm = ? WHERE id = ?");
    }else if(isset($input['assignment'])){
        $stmt = $db->prepare("UPDATE student_grads SET assignment = ? WHERE id = ?");
    }

    $stmt->bind_param("di", $grade, $grade_id);

    if ($stmt->execute()) {
        $response = array("status" => "success", "message" => "Grade updated successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to update grade.");
    }
} else {
    // Insert new grade
    if(isset($input['final'])){
        $stmt = $db->prepare("INSERT INTO student_grads (s_id, track_id, final) VALUES (?, ?, ?)");
    }else if(isset($input['quiz'])){
        $stmt = $db->prepare("INSERT INTO student_grads (s_id, track_id, quiz) VALUES (?, ?, ?)");
    }else if(isset($input['midterm'])){
        $stmt = $db->prepare("INSERT INTO student_grads (s_id, track_id, midterm) VALUES (?, ?, ?)");
    }else if(isset($input['assignment'])){
        $stmt = $db->prepare("INSERT INTO student_grads (s_id, track_id, assignment) VALUES (?, ?, ?)");
    }
    $stmt->bind_param("iid", $s_id, $track_id, $grade);

    if ($stmt->execute()) {
        $response = array("status" => "success", "message" => "Grade inserted successfully.");
    } else {
        $response = array("status" => "error", "message" => "Failed to insert grade.");
    }
}

$stmt->close();
$db->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
