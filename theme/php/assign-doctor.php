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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['appointmentId']) && isset($data['doctorId']) && isset($data['staffId'])) {
        try {
            $stmt = $db->prepare("UPDATE appointment SET doctorID = :doctorId, staffID = :staffId WHERE appointmentID = :appointmentId");
            $stmt->bindParam(':doctorId', $data['doctorId']);
            $stmt->bindParam(':staffId', $data['staffId']);
            $stmt->bindParam(':appointmentId', $data['appointmentId']);
            $stmt->execute();

            http_response_code(200);
            $response = new stdClass();
            $response->message = "Doctor assigned successfully.";
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (PDOException $e) {
            http_response_code(500);
            $response = new stdClass();
            $response->error = "Error occurred: ". $e->getMessage();
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    } else {
        http_response_code(400);  // Bad Request
        $response = new stdClass();
        $response->error = "Invalid request.";
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    http_response_code(405);  // Method Not Allowed
    $response = new stdClass();
    $response->error = "Method not allowed.";
    header('Content-Type: application/json');
    echo json_encode($response);
}
exit();
?>
