<?php
require_once "../config/database.php";

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {

    // ------------------------------
    // GET /api/meters?customer_id=1
    // ------------------------------
    case "GET":
        $customerId = $_GET['customer_id'] ?? null;

        if (!$customerId) {
            http_response_code(400);
            echo json_encode(["message" => "Customer ID required"]);
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT * FROM meters WHERE customer_id = :id"
        );
        $stmt->execute([":id" => $customerId]);

        echo json_encode([
            "status" => "success",
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        break;

    // ------------------------------
    // POST /api/meters
    // ------------------------------
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['customer_id']) ||
            empty($data['utility_type']) ||
            empty($data['meter_number']) ||
            empty($data['installation_date'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Required fields missing"]);
            exit;
        }

        $sql = "INSERT INTO meters
                (customer_id, utility_type, meter_number, installation_date)
                VALUES (:cid, :utility, :meter, :date)";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":cid" => $data['customer_id'],
                ":utility" => $data['utility_type'],
                ":meter" => $data['meter_number'],
                ":date" => $data['installation_date']
            ]);

            echo json_encode([
                "status" => "success",
                "message" => "Meter registered successfully"
            ]);

        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode([
                "status" => "error",
                "message" => "Meter number already exists"
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
}
