<?php
$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $response = new stdClass();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $jsonbody = json_decode(file_get_contents('php://input'));

        if (isset($jsonbody->firstName) && isset($jsonbody->lastName) && isset($jsonbody->speciality) &&
            isset($jsonbody->contact) && isset($jsonbody->email) && isset($jsonbody->doctorID)) {

            $stmt = $db->prepare("UPDATE doctor SET firstName = :firstName, lastName = :lastName, speciality = :speciality, 
            contact = :contact, email = :email WHERE doctorID = :id");
            $stmt->execute([
                ':firstName' => $jsonbody->firstName,
                ':lastName' => $jsonbody->lastName,
                ':speciality' => $jsonbody->speciality,
                ':contact' => $jsonbody->contact,
                ':email' => $jsonbody->email,
                ':id' => $jsonbody->doctorID
            ]);

            http_response_code(200);
            $response->success = true;
            $response->message = "Details updated successfully";
        } else {
            http_response_code(400); // Bad Request
            $response->success = false;
            $response->error = "Missing required parameters";
        }
    } else {
        http_response_code(405); // Method Not Allowed
        $response->success = false;
        $response->error = "Invalid request method";
    }
} catch (Exception $e) {
    http_response_code(500);
    $response->success = false;
    $response->error = "Error occurred: " . $e->getMessage();
}

echo json_encode($response);
exit();
?>
