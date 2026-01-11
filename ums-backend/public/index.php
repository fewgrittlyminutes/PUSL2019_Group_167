<?php

$requestUri = explode("/", trim($_SERVER['REQUEST_URI'], "/"));
$resource = $requestUri[1] ?? null;

switch ($resource) {
    case "customers":
        require "../api/customers.php";
        break;

    case "meters":
        require "../api/meters.php";
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid API endpoint"
        ]);
}
