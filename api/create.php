<?php
if (!defined("DOCUMENT_ROOT")) {
    define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);
}
require_once DOCUMENT_ROOT . '/api/settings.php';

/*
 * Set required REST api headers
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 30");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Determine the request method. Must be POST
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    /*
     * set response code - 400 bad request
     */
    http_response_code(400);
    echo json_encode(array('message'=>'Use POST REQUEST_METHOD', 'status'=>'error'));
    exit;
}
// get data
$data = (array) json_decode(file_get_contents("php://input"));  // accept JSON
if (!is_array($data) || count($data) < 1) {
    $data = $_POST;                                             // accept URLencoded form data
}
if (!is_array($data) || count($data) < 1) {
    $data = $_GET;                                              // accept Multipart form data
}

/*
 * Verify $data
 * username, first_name, last_name, email, age, role are compulsory field
 */
if (!isset($data['username']) || strlen(trim($data['username'])) < 1) {
    $errorMessage = 'username post field is a required';
} elseif (!isset($data['first_name']) || strlen(trim($data['first_name'])) < 1) {
    $errorMessage = 'first_name post field is a required';
} elseif (!isset($data['last_name']) || strlen(trim($data['last_name'])) < 1) {
    $errorMessage = 'last_name post field is a required';
} elseif (!isset($data['email']) || strlen(trim($data['email'])) < 1) {
    $errorMessage = 'email post field is a required';
} elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false || !preg_match("/[^\s<>\\,@\"';\/]+@[^\s<>\\,@\"'\/]+\.[^\s<>\\,@\"'\/]+[A-Za-z]/", $data['email'])) {
    $errorMessage = 'email is invalid';
} elseif (!isset($data['age']) || strlen(trim($data['age'])) < 1) {
    $errorMessage = 'age post field is a required';
} elseif (!isset($data['role']) || strlen(trim($data['role'])) < 1) {
    $errorMessage = 'role post field is a required';
} else {
    list($emailuser, $domain) = explode('@', $data['email']);
    if (!checkdnsrr($domain, 'MX')) {
        $errorMessage = 'email does not exist on ' . $domain . ' server';
    }
}

if (isset($errorMessage)) {
    /*
     * set response code - 400 bad request
     */
    http_response_code(400);
    echo json_encode(array('message'=>$errorMessage, 'status'=>'error'));
    exit;
}

try {
    /*
     * Form an SQL query with:
     * 1. Ignoring already existing username,email - UNIQUE KEYS
     */
    $stmt = $dbconn->prepare("INSERT into `employees` (`username`, `first_name`, `last_name`, `email`, `age`, `role`) VALUES(:username, :first_name, :last_name, :email, :age, :role)");
    $username = $data['username'];
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $email = $data['email'];
    $age = (int) $data['age'];
    $role = $data['role'];
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    /*
     * Execute statment
     * set response code - 200 OK
     */
    http_response_code(200);
    if ($stmt->execute()) {
        echo json_encode(array('message'=>'Employee inserted successfully', 'status'=>'success'));
    } else {
        echo json_encode(array('message'=>'No row was inserted', 'status'=>'success'));
    }
} catch (PDOException $e) {
    /*
     * If its is a duplicate entry. $e->errorInfo[1] will be 1062. Set response code - 409 (Conflict). Resource already exists.
     * Otherwise set response code - 500 Internal Server error
     */
    if ($e->errorInfo[1] === 1062) {
        http_response_code(409);
        echo json_encode(array('message'=>'Resource already exists. A user with same email or username exists.', 'status'=>'success'));
        exit;
    }
    http_response_code(500);
    echo json_encode(array('message'=>'Error occurred with your request. Please contact our admin', 'status'=>'error'));
    // TODO: Write logic to send mail to admin
    error_log($e->getMessage());
}
