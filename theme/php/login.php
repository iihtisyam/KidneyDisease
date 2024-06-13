<?php
$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);

$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $jsonbody = json_decode(file_get_contents('php://input'));

        if (isset($jsonbody->email) && isset($jsonbody->password)) {
            // Check for admin (staff table)
            $stmt = $db->prepare("SELECT * FROM staff WHERE email=:email AND password=:password");
            $stmt->execute([':email' => $jsonbody->email, ':password' => $jsonbody->password]);
            if ($stmt->rowCount() > 0) {
                $staffData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $response->error = "Successfully logged in";
                $response->role = "admin";
                $response->staffData = $staffData; 
                http_response_code(200);
            } else {
                // Check for doctor
                $stmt = $db->prepare("SELECT * FROM doctor WHERE email=:email AND password=:password");
                $stmt->execute([':email' => $jsonbody->email, ':password' => $jsonbody->password]);
                if ($stmt->rowCount() > 0) {
                    // Fetch doctor data
                    $doctorData = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Construct response object
                    $response->error = "Successfully logged in";
                    $response->role = "doctor";
                    $response->doctorData = $doctorData;
                    http_response_code(200);
                } else {
                    // Check for patient
                    $stmt = $db->prepare("SELECT * FROM patient WHERE email=:email AND password=:password");
                    $stmt->execute([':email' => $jsonbody->email, ':password' => $jsonbody->password]);
                    if ($stmt->rowCount() > 0) {
                        // Fetch patient data
                        $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Construct response object
                        $response->error = "Successfully logged in";
                        $response->role = "patient";
                        $response->patientData = $patientData; // Include patient data in the response
                        http_response_code(200);
                    } else {
                        http_response_code(401); // Unauthorized
                        $response->error = "Invalid email or password";
                    }
                }
            }
        } else {
            http_response_code(400); // Bad Request
            $response->error = "Missing required parameters";
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response->error = "Error occurred: " . $e->getMessage();
    }
}

echo json_encode($response);
exit();
?>
