<?php
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//header("Access-Control-Allow-Headers: *");
//header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

$db = new PDO ("mysql:host=$hostname;dbname=$database",$username,$password);
// initial response code
// response code will be changed if the request goes into any of the process

http_response_code(404);
$response = new stdClass();

{
	$jsonbody = json_decode(file_get_contents('php://input'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($jsonbody->firstName) &&
			isset($jsonbody->lastName) &&
			isset($jsonbody->speciality) &&
            isset($jsonbody->contact) &&
			isset($jsonbody->email) &&
			isset($jsonbody->password)){
			
            // Check if the email already exists
            $stmt = $db->prepare("SELECT email FROM doctor WHERE email=:email");
            $stmt->execute([':email' => $jsonbody->email]);
			
			
            if ($stmt->rowCount() > 0) {
//$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
               // Bad Request
                $response->error = "Email is already registered";
            } else {
                // Insert the new user
                $stmt = $db->prepare("INSERT INTO doctor 
				(`firstName`,`lastName`,`speciality`,`contact`,`email`, `password`) 
                    VALUES (:firstName, :lastName, :speciality, :contact,:email, :password)");

                $stmt->execute([
                    ':firstName' => $jsonbody->firstName,
                    ':lastName' => $jsonbody->lastName,
                    ':speciality' => $jsonbody->speciality,
                    ':contact' => $jsonbody->contact,
                    ':email' => $jsonbody->email,
					':password' => $jsonbody->password
                ]);

                http_response_code(200);
				$response->error = "Successfully registered";
            }
        } else {
            http_response_code(400); // Bad Request
            $response->error = "Missing required parameters";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}
echo json_encode($response);
exit();
?>