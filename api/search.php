<?php


if (!defined("DOCUMENT_ROOT")) {
    define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);
}
require_once DOCUMENT_ROOT . '/api/settings.php';

/**
 * Set required REST api headers
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 30");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Determine the request method. Must be GET
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET') {
    /**
     * tell the user Request method is wrong
     * i. set response code - 400 bad request
     */
    http_response_code(400);
    echo json_encode(array('message'=>'Use GET REQUEST_METHOD', 'status'=>false));
    exit;
}
// get data
$data = (array) json_decode(file_get_contents("php://input"));
if (!is_array($data) || count($data) < 1) {
    $data = $_GET;
}

try {
    /**
     * Form an SQL query with:
     * 1. Where clause (optional)
     * 2. Limit clause (optional)
     * 4. Order by (optional). Default [userid]
     */
    $query = "SELECT * from `employees`";
    $whereClause = " where ";
    if (isset($data['userid'])) {
        $query .= $whereClause . "`userid` = :userid";
        $userid = $data['userid'];
        $whereClause = " and ";
    }
    if (isset($data['username'])) {
        $query .= $whereClause . "`username` = :username";
        $username = $data['username'];
        $whereClause = " and ";
    }
    if (isset($data['first_name'])) {
        $query .= $whereClause . "`first_name` = :first_name";
        $first_name = $data['first_name'];
        $whereClause = " and ";
    }
    if (isset($data['last_name'])) {
        $query .= $whereClause . "`last_name` = :last_name";
        $last_name = $data['last_name'];
        $whereClause = " and ";
    }
    if (isset($data['email'])) {
        $query .= $whereClause . "`email` = :email";
        $email = $data['email'];
        $whereClause = " and ";
    }
    if (isset($data['country'])) {
        $query .= $whereClause . "`country` = :country";
        $country = $data['country'];
        $whereClause = " and ";
    }
    if (isset($data['age'])) {
        $query .= $whereClause . "`age` = :age";
        $age = $data['age'];
        $whereClause = " and ";
    }
    if (isset($data['role'])) {
        $query .= $whereClause . "`role` like :role";
        $role = "%" . $data['role'] . "%";
        $whereClause = " and ";
    }
    if (isset($data['limit'])) {
        $query .= " LIMIT :limit";
        $limit = (int) $data['limit'];
    }
    if (isset($data['sort'])) {
        $query .= " ORDER BY :orderby";
        $orderby = $data['sort'];
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
    if (isset($limit)) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    if (isset($orderby)) {
        $stmt->bindParam(':orderby', $orderby, PDO::PARAM_STR);
    }
    //Execute statment
    $stmt->execute();
    //FetchALl results
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //Response
    /**
     * Tell the user the responses
     * i. set response code - 200 OK
     */
    http_response_code(200);
    echo json_encode($result);
    $stmt = null;
    $dbconn = null;
} catch (PDOException $e) {
    /**
     * Tell the user the responses
     * i. set response code - 500 Internal Server error
     */
    http_response_code(500);
    echo json_encode(array('message'=>'Error occurred with your request. Please contact our admin', 'status'=>false));
    // TODO: Write logic to send mail to admin
    error_log($e->getMessage());
}
