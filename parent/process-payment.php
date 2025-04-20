<?php
require '../includes/db.php';
require '../includes/session.php';

header('Content-Type: application/json');

// ✅ Show full errors during dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Check if user is logged in and is a parent
if (!isLoggedIn() || !isParent()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fee_id = $_POST['fee_id'];
    $method = $_POST['payment_method'];
    $ref = $_POST['ref_display'];

    // ✅ Log for debugging
    // file_put_contents("debug.log", "Received: FeeID=$fee_id, Method=$method, Ref=$ref\n", FILE_APPEND);

    $conn->begin_transaction();

    try {
        // ✅ Step 1: Set fee status to Processing
        $conn->query("UPDATE fees SET status = 'Processing' WHERE id = $fee_id");

        // ✅ Step 2: Simulate delay (e.g. payment gateway)
        usleep(1000000); // 1 second

        // ✅ Step 3: Insert payment (may throw duplicate reference_no error)
        $stmt = $conn->prepare("INSERT INTO payments (fee_id, payment_method, reference_no) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $fee_id, $method, $ref);
        $stmt->execute();

        // ✅ Step 4: Update fee status to Paid
        $conn->query("UPDATE fees SET status = 'Paid' WHERE id = $fee_id");

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => "Payment processed successfully for Reference: $ref"
        ]);
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();

        // ✅ Log the actual error for debugging
        error_log("Payment error: " . $e->getMessage());

        if ($e->getCode() == 1062) {
            echo json_encode([
                'status' => 'error',
                'message' => "This reference number has already been used."
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "Payment failed. Error: " . $e->getMessage()
            ]);
        }
    }
}
?>
