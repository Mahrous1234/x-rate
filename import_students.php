<?php
include 'database.php';

$input = json_decode(file_get_contents('php://input'), true);
$students = $input['students'];

foreach ($students as $student) {
    $name = $student['name'];
    $phone = $student['phone'];
    $email = $student['email'];
    $password = $student['password'];
    $s_number = $student['s_number'];

    // Check if student exists
    $stmt = $db->prepare("SELECT id, phone, s_number FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Student exists, fetch current data
        $stmt->bind_result($id, $current_phone, $current_s_number);
        $stmt->fetch();
        
        // Check if data has changed
        if ($phone !== $current_phone || $s_number !== $current_s_number) {
            // Update student data
            $stmt = $db->prepare("UPDATE students SET name = ?, phone = ?, s_number = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $phone, $s_number, $id);
            $stmt->execute();
        }
    } else {
        // Insert new student
        $stmt = $db->prepare("INSERT INTO students (name, phone, email, s_number) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $s_number);
        $stmt->execute();

        // Get the new student's ID
        $student_id = $stmt->insert_id;

        // Hash the password and insert into users table
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $type = 'student';
        $stmt = $db->prepare("INSERT INTO users (s_id, name, email, password, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $student_id, $name, $email, $hashed_password, $type);
        $stmt->execute();
    }
}

$stmt->close();
$db->close();

$response = array("status" => "success", "message" => "Students data processed successfully.");
echo json_encode($response);
?>
