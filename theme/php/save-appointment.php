<?php
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//header("Access-Control-Allow-Headers: *");
//header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

$db = new PDO ("mysql:host=$hostname;dbname=$database", $username, $password);
// initial response code
// response code will be changed if the request goes into any of the process

http_response_code(404);
$response = new stdClass();

$jsonbody = json_decode(file_get_contents('php://input'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($jsonbody->date) &&
            isset($jsonbody->startTime) &&
            isset($jsonbody->endTime) &&
            isset($jsonbody->details) &&
            isset($jsonbody->patientID)) {

            // Insert the new appointment
            $stmt = $db->prepare("INSERT INTO appointment 
                (`date`, `startTime`, `endTime`, `details`, `patientID`, `status`) 
                VALUES (:date, :startTime, :endTime, :details, :patientID, :status)");

            $stmt->execute([
                ':date' => $jsonbody->date,
                ':startTime' => $jsonbody->startTime,
                ':endTime' => $jsonbody->endTime,
                ':details' => $jsonbody->details,
                ':patientID' => $jsonbody->patientID,
                ':status' => 0 // Set initial status to 0
            ]);

            http_response_code(200);
            $response->error = "Appointment successfully registered";
        } else {
            http_response_code(400); // Bad Request
            $response->error = "Missing required parameters";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred: " . $ee->getMessage();
    }
}

echo json_encode($response);
exit();
?>
