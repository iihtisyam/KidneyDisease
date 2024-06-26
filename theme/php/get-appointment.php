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
        $patientID = $_GET['patientID'];

        // Fetch upcoming appointments
        $stmt = $db->prepare("SELECT * FROM appointment WHERE patientID = ? AND status = 0");
        $stmt->execute([$patientID]);
        $upcomingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch attended appointments
        $stmt = $db->prepare("SELECT * FROM appointment WHERE patientID = ? AND status = 1");
        $stmt->execute([$patientID]);
        $attendedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            'upcomingAppointments' => $upcomingAppointments,
            'attendedAppointments' => $attendedAppointments,
        ];
        http_response_code(200);

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
