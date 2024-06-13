<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    $response = new stdClass();
    $response->error = "Database connection error: ". $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['date']) && isset($data['startTime'])) {
    $date = $data['date'];
    $startTime = $data['startTime'];

    try {
        // Query to get all doctors with firstName and lastName
        $stmt = $db->prepare("SELECT doctorID, firstName, lastName FROM doctor");
        $stmt->execute();
        $allDoctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Query to get assigned doctors on the given date and time
        $stmt = $db->prepare("SELECT doctorID FROM appointment WHERE date = :date AND startTime = :startTime");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->execute();
        $assignedDoctors = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Filter out assigned doctors
        $availableDoctors = array_filter($allDoctors, function($doctor) use ($assignedDoctors) {
            return !in_array($doctor['doctorID'], $assignedDoctors);
        });

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(array_values($availableDoctors));
    } catch (PDOException $e) {
        http_response_code(500);
        $response = new stdClass();
        $response->error = "Error occurred during query execution: " . $e->getMessage();
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    http_response_code(400);  // Bad Request
    $response = new stdClass();
    $response->error = "Invalid request. 'date' and 'startTime' are required.";
    header('Content-Type: application/json');
    echo json_encode($response);
}
exit();
?>
