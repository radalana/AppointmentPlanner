<?php
require 'vendor/autoload.php';
//require __DIR__ . '/businesslogic/simpleLogic.php';

// Query Parameters 
// ... POST-Requests either use $_POST or php://input
$method = $_GET["method"] ??  false;
$param = $_GET["param"] ?? false;


$voteData = null;
if (isset($_POST["vote"])){
    $voteData = $_POST["vote"];
}
// handle request in SimpleLogic
$logic = new SimpleLogic();
$result = $logic->handleRequest($method, $voteData);
if ($result == null) {
    response("GET", 400, null);
}else {
    response("GET", 200, $result);
}

function response($method, $httpStatus, $data)
{
    header('Content-Type: application/json');
    switch ($method) {
        case "GET":
            http_response_code($httpStatus);
            echo json_encode($data);
            break;
        case "POST":
            http_response_code($httpStatus);
        default:
            http_response_code(405);
            echo ("Method not supported yet!");
    }
}
