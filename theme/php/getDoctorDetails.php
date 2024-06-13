<?php
$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

$db = new PDO ("mysql:host=$hostname;dbname=$database", $username, $password);

$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Assuming the doctor is logged in and their ID is stored in the session
        session_start();
        $doctorId = $_SESSION['doctorID'];

        $stmt = $db->prepare("SELECT firstName, lastName, speciality, contact, email FROM doctor WHERE id = :id");
        $stmt->execute([':id' => $doctorId]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor) {
            http_response_code(200);
            $response->success = true;
            $response->details = $doctor;
        } else {
            http_response_code(404);
            $response->success = false;
            $response->error = "Doctor not found";
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response->success = false;
        $response->error = "Error occurred: " . $e->getMessage();
    }
}

echo json_encode($response);
exit();
?>
