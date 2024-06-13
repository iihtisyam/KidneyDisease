<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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
    $response->error = "Database connection error: " . $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Initial response code
http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Assuming 'appointments' table structure includes fields like date, startTime, endTime, details, patientID, and status
        $stmt = $db->prepare("SELECT * FROM appointment WHERE patientID = ? AND status = 0");
        // Assuming you're passing patientID as a query parameter
        $patientID = $_GET['patientID'];
        $stmt->execute([$patientID]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($appointments) {
            $response = $appointments;
            http_response_code(200);
        } else {
            http_response_code(404);  // Not Found
            $response->error = "No upcoming appointments found for the patient.";
        }
    } catch (PDOException $e) {
        http_response_code(500);
        $response->error = "Error occurred: " . $e->getMessage();
    }
} else {
    // Handle other HTTP methods if needed
    http_response_code(405);  // Method Not Allowed
    $response->error = "Method not allowed.";
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');

// Then send the JSON response
echo json_encode($response);
exit();
?>
