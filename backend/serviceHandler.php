<?php
include("businesslogic/simpleLogic.php");

// Query Parameters 
// ... POST-Requests either use $_POST or php://input
$method = $_GET["method"] ?? false;
$param = $_GET["param"] ?? false;



// handle request in SimpleLogic
$logic = new SimpleLogic();
$result = $logic->handleRequest($method, $param);
if ($result == null) {
    response("GET", 400, null);
} else {
    response("GET", 200, $result);
}

// send response back ro browser
function response($method, $httpStatus, $data)
{
    header('Content-Type: application/json');
    switch ($method) {
        case "GET":
            http_response_code($httpStatus);
            echo json_encode($data);
            break;
        default:
            http_response_code(405);
            echo ("Method not supported yet!");
    }
}
