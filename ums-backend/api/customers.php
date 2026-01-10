<?php
require_once "../config/database.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    // ------------------------------
    // GET /api/customers
    // ------------------------------
    case "GET":
        $stmt = $pdo->query("SELECT * FROM customers ORDER BY customer_id DESC");
        echo json_encode([
            "status" => "success",
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ------------------------------
    // POST /api/customers
    // ------------------------------
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['customer_type']) ||
            empty($data['full_name']) ||
            empty($data['address'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Required fields missing"]);
            exit;
        }

        $sql = "INSERT INTO customers 
                (customer_type, full_name, address, phone, email)
                VALUES (:type, :name, :address, :phone, :email)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":type" => $data['customer_type'],
            ":name" => $data['full_name'],
            ":address" => $data['address'],
            ":phone" => $data['phone'] ?? null,
            ":email" => $data['email'] ?? null
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Customer created successfully"
        ]);
        break;

    // ------------------------------
    // PUT /api/customers?id=1
    // ------------------------------
    case "PUT":
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "Customer ID required"]);
            exit;
        }

        $sql = "UPDATE customers SET
                full_name = :name,
                address = :address,
                phone = :phone,
                email = :email
                WHERE customer_id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":name" => $data['full_name'],
            ":address" => $data['address'],
            ":phone" => $data['phone'],
            ":email" => $data['email'],
            ":id" => $id
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Customer updated successfully"
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
}
