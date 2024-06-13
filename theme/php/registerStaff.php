<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "kidneydiseasestracking";
$username = "root";
$password = "wingerssrc1519";

$db = new PDO ("mysql:host=$hostname;dbname=$database",$username,$password);

$response = new stdClass();

{
	$jsonbody = json_decode(file_get_contents('php://input'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($jsonbody->firstName) &&
			isset($jsonbody->lastName) &&
            isset($jsonbody->contact) &&
			isset($jsonbody->email) &&
			isset($jsonbody->password) &&
			isset($jsonbody->department) &&
			isset($jsonbody->position)) {
			
			$stmt = $db->prepare("SELECT * FROM staff WHERE email = :email");
			$stmt->execute([':email' => $jsonbody->email]);

			if ($stmt->rowCount() > 0) {
				http_response_code(200);
				$response->message = "Email is already registered";
			} else {
				$stmt = $db->prepare("INSERT INTO staff 
				(`firstName`,`lastName`,`contact`,`email`, `password`,`department`,`position`) 
				VALUES (:firstName, :lastName, :contact,:email, :password, :department, :position)");

				$stmt->execute([
					':firstName' => $jsonbody->firstName,
					':lastName' => $jsonbody->lastName,
					':contact' => $jsonbody->contact,
					':email' => $jsonbody->email,
					':password' => $jsonbody->password,
					':department' => $jsonbody->department,
					':position' => $jsonbody->position
				]);

				http_response_code(200);
				$response->message = "Successfully registered";
				$response->data = [];
			}
		} else {
			http_response_code(400); // Bad Request
			$response->message = "Missing required parameters";
		}
	} catch (Exception $ee) {
		http_response_code(500);
		$response->message = "Error occurred " . $ee->getMessage();
	}
}

echo json_encode($response);
exit();
?>