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
// initial response code
// response code will be changed if the request goes into any of the process

http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM staff");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = ['count' => $data['count']];
        } else {
            http_response_code(404); // Not Found
            $response['error'] = "No records found in the staff table";
            $response['count'] = 0;
        }
		  
    } catch (Exception $ee) {
        http_response_code(500);
        $response['error'] = "Error occurred: " . $ee->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "PUT") {
    http_response_code(405); // Method Not Allowed
    $response->error = "Method not allowed";
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');

// Then send the JSON response
echo json_encode($response);
exit();
?>
