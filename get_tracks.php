<?php
include 'database.php';

$stmt = $db->prepare("SELECT * FROM tracks");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch all tracks data
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $response = array("status" => "success", "data" => $data);
    } else {
        $response = array("status" => "error", "message" => "No tracks.");
    }
} else {
    $response = array("status" => "error", "message" => "Failed to retrieve tracks data.");
}

$stmt->close();
$db->close();

// Return JSON response
echo json_encode($response);
?>
