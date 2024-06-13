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
			isset($jsonbody->username) &&
            isset($jsonbody->ICNumber) &&
			isset($jsonbody->dateOfBirth) &&
            isset($jsonbody->gender) &&
			isset($jsonbody->contact) &&
            isset($jsonbody->address) &&
            isset($jsonbody->medicineAllergy) &&
			isset($jsonbody->email) &&
			isset($jsonbody->password)){
			
            // Check if the email already exists
            $stmt = $db->prepare("SELECT email FROM patient WHERE email=:email");
            $stmt->execute([':email' => $jsonbody->email]);
			
			
            if ($stmt->rowCount() > 0) {
//$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
               // Bad Request
                $response->error = "Email is already registered";
            } else {
                // Insert the new user
                $stmt = $db->prepare("INSERT INTO patient 
				(`firstName`,`lastName`,`username`,`ICNumber`,`dateOfBirth`,`gender`,`contact`,`address`,`medicineAllergy`,`email`, `password`) 
                    VALUES (:firstName, :lastName, :username, :ICNumber, :dateOfBirth, :gender, :contact, :address, :medicineAllergy, :email, :password)");

                $stmt->execute([
                    ':firstName' => $jsonbody->firstName,
                    ':lastName' => $jsonbody->lastName,
                    ':username' => $jsonbody->username,
                    ':ICNumber' => $jsonbody->ICNumber,
                    ':dateOfBirth' => $jsonbody->dateOfBirth,
                    ':gender' => $jsonbody->gender,
                    ':contact' => $jsonbody->contact,
					':address' => $jsonbody->address,
                    ':medicineAllergy' => $jsonbody->medicineAllergy,
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