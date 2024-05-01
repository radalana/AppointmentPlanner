<?php
require __DIR__ . '/businesslogic/simpleLogic.php';

// Query Parameters 
// ... POST-Requests either use $_POST or php://input
$method = $_GET["method"] ?? false;
$param = null;


// handle request in SimpleLogic
$logic = new SimpleLogic();
$result = $logic->handleRequest($method);
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
            $param = $_GET["param"] ?? false;
            echo json_encode($data);
            break;
        case "POST":
            $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $param = json_decode(file_get_contents("php://input"), true);
        } else {
            $param = $_POST;
        }
        break;
            break;
        default:
            http_response_code(405);
            echo ("Method not supported yet!");
    }
}
