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
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 30");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Determine the request method. Must be GET
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'PUT') {
    /*
     * tell the user Request method is wrong
     * i. set response code - 400 bad request
     */
    http_response_code(400);
    echo json_encode(array('message'=>'Use PUT REQUEST_METHOD', 'status'=>false));
    exit;
}
// get data
$data = (array) json_decode(file_get_contents("php://input"));
if (!is_array($data) || count($data) < 1) {
    $data = $_PUT;
}
/*
    Ensure the request must have a userid
 */
$userid = $data['userid'];
if (!isset($userid) || empty($userid) || !is_numeric($userid)) {
    /*
     * tell the user Request method is wrong
     * i. set response code - 400 bad request
     */
    http_response_code(400);
    echo json_encode(array('message'=>'Userid is required', 'status'=>false));
    exit;
}

try {
    /*
     * Form an SQL query with:
     * 1. Where clause (optional)
     * 2. Limit clause (optional)
     * 4. Order by (optional). Default [userid]
     */
    $query = "UPDATE `employees` SET ";
    $comma = "";
    if (isset($data['username'])) {
        $query .= $comma . "`username` = :username";
        $username = $data['username'];
        $comma = ", ";
    }
    if (isset($data['first_name'])) {
        $query .= $comma . "`first_name` = :first_name";
        $first_name = $data['first_name'];
        $comma = ", ";
    }
    if (isset($data['last_name'])) {
        $query .= $comma . "`last_name` = :last_name";
        $last_name = $data['last_name'];
        $comma = ", ";
    }
    if (isset($data['email'])) {
        $query .= $comma . "`email` = :email";
        $email = $data['email'];
        $comma = ", ";
    }
    if (isset($data['country'])) {
        $query .= $comma . "`country` = :country";
        $country = $data['country'];
        $comma = ", ";
    }
    if (isset($data['age'])) {
        $query .= $comma . "`age` = :age";
        $age = $data['age'];
        $comma = ", ";
    }
    if (isset($data['role'])) {
        $query .= $comma . "`role` = :role";
        $role = $data['role'];
        $comma = ", ";
    }
    $query .= " where `userid` = :userid";

    if ($comma === '') {
        /*
         * tell the user Request incomplete data
         * i. set response code - 400 bad request
         */
        http_response_code(400);
        echo json_encode(array('message'=>'Incomplete data. Provide a parameter to update', 'status'=>false));
        exit;
    }

    // Prepare the statment
    $stmt = $dbconn->prepare($query);
    // Bind Parameters by reference
    if (isset($userid)) {
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    }
    if (isset($username)) {
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    }
    if (isset($first_name)) {
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    }
    if (isset($last_name)) {
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    }
    if (isset($email)) {
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    }
    if (isset($country)) {
        $stmt->bindParam(':country', $country, PDO::PARAM_STR);
    }
    if (isset($age)) {
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    }
    if (isset($role)) {
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    }
    /*
     * Execute statment
     * Tell the user the responses
     * i. set response code - 200 OK
     */
    http_response_code(200);
    if ($stmt->execute()) {
        echo json_encode(array('message'=>'Employee updated successfully', 'status'=>true));
    } else {
        echo json_encode(array('message'=>'No row was updated', 'status'=>false));
    }
} catch (PDOException $e) {
    /*
     * Tell the user the responses
     * i. set response code - 500 Internal Server error
     */
    http_response_code(500);
    echo json_encode(array('message'=>'Error occurred with your request. Please contact our admin', 'status'=>false));
    // TODO: Write logic to send mail to admin
    error_log($e->getMessage());
}
$stmt = null;
$dbconn = null;
