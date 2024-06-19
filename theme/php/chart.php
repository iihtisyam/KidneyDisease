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
    // Establish database connection
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle database connection error
    http_response_code(500);
    $response = new stdClass();
    $response->error = "Database connection error: " . $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Set initial response code
http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Prepare and execute the SQL query
        $stmt = $db->prepare("SELECT DATE_FORMAT(date, '%Y-%m') as month, result, COUNT(*) as count
                              FROM medicaltest
                              WHERE result IN ('Positive for CKD', 'Negative for CKD')
                              GROUP BY month, result");
        $stmt->execute();
        
        $data = [];

        // Process the query results
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $month = $row['month'];
            $result = $row['result'];
            $count = $row['count'];

            // Initialize data structure for each month if not already present
            if (!isset($data[$month])) {
                $data[$month] = ['positive' => 0, 'negative' => 0];
            }

            // Increment the count based on the result value
            if ($result === 'Positive for CKD') {
                $data[$month]['positive'] = $count;
            } else if ($result === 'Negative for CKD') {
                $data[$month]['negative'] = $count;
            }
        }

        // Set the response code and data
        http_response_code(200);
        $response = $data;
    } catch (Exception $e) {
        // Handle any other errors
        http_response_code(500);
        $response->error = "Error occurred: " . $e->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle POST requests if needed
} else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Handle PUT requests if needed
}

// Set the content type to JSON
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
exit();
?>
