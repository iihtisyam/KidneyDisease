<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
    $response->error = "Database connection error: " . $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Initial response code
http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get the raw POST data
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        // Check if the name is set in the POST request
        if (isset($data['name'])) {
            $name = $data['name'];

            // Prepare the search term with wildcards
            $searchTerm = "%$name%";

            // Prepare a statement to search for the doctor by name, which can be either firstName or lastName
            $stmt = $db->prepare("SELECT * FROM doctor WHERE firstName LIKE :name OR lastName LIKE :name");
            $stmt->bindParam(':name', $searchTerm);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
            } else {
                http_response_code(404);  // Not Found
                $response->error = "No doctor found with the provided name.";
            }
        } else {
            http_response_code(400);  // Bad Request
            $response->error = "Name required.";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred: " . $ee->getMessage();
    }
}
 else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $db->query("SELECT * FROM doctor");
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($doctors) {
            $response = $doctors;
            http_response_code(200);
        } else {
            http_response_code(404);  // Not Found
            $response->error = "No doctors found.";
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response->error = "Error occurred: " . $e->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Handle PUT requests
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');

// Then send the JSON response
echo json_encode($response);
exit();
?>