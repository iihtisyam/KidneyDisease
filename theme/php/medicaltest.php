<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
// initial response code
// response code will be changed if the request goes into any of the process

http_response_code(404);
$response = new stdClass();

$jsonbody = json_decode(file_get_contents('php://input'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($jsonbody->ic_number) && isset($jsonbody->result)) {

            // Query to get the patient ID
            $stmt = $db->prepare("SELECT patientID FROM patient WHERE ICNumber=:ic_number");
            $stmt->execute([':ic_number' => $jsonbody->ic_number]);

            if ($stmt->rowCount() > 0) {
                // Capture the current date and time
                $date = date('Y-m-d H:i:s');

                // Fetch patientID
                $patientID = $stmt->fetch(PDO::FETCH_ASSOC)['patientID'];

                // Insert the result into the MedicalTest table
                $stmt = $db->prepare("INSERT INTO medicaltest (result, date, patientID) VALUES (:result, :date, :patientID)");
                $stmt->execute([':result' => $jsonbody->result, ':date' => $date, ':patientID' => $patientID]);

                http_response_code(200);
                $response->status = "success";
                $response->message = "Result saved successfully";
            } else {
                http_response_code(404);
                $response->status = "error";
                $response->message = "Patient not found";
            }
        } else {
            http_response_code(400); // Bad Request
            $response->status = "error";
            $response->message = "Missing required parameters";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->status = "error";
        $response->message = "Error occurred " . $ee->getMessage();
    }
}

echo json_encode($response);
exit();
?>
