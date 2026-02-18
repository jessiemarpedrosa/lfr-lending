<?php
// Turn off error display, only log errors
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Start output buffering to catch any unwanted output
ob_start();

try {
    // Load WordPress to access DB constants
    require_once(__DIR__ . '/../../../../../wp-load.php');

    include __DIR__ . '/../db-config.php';

    $transDate = isset($_REQUEST['transDate']) ? $_REQUEST['transDate'] : null;
    $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : null;
    $loanNo = isset($_REQUEST['loan_no']) ? $_REQUEST['loan_no'] : null;

    // Initialize data array
    $data = array();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    if (isset($loanNo) && !empty($loanNo)) {
        $sql = "SELECT * FROM lfr_transactions WHERE loan_no = '" . mysqli_real_escape_string($conn, $loanNo) . "' ORDER BY transaction_date DESC";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            throw new Exception('Query failed: ' . mysqli_error($conn));
        }
    }
    elseif (isset($transDate) && !empty($transDate) && isset($account) && !empty($account)) {
        $sql = "SELECT * FROM lfr_transactions WHERE transaction_date = '" . mysqli_real_escape_string($conn, $transDate) . "' AND account = '" . mysqli_real_escape_string($conn, $account) . "'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            throw new Exception('Query failed: ' . mysqli_error($conn));
        }
    }
    else {
        // No valid search criteria provided
        $result = false;
    }

    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    mysqli_free_result($result);
    mysqli_close($conn);

    // Clear any unwanted output
    ob_end_clean();

    // Output JSON
    echo json_encode($data);

} catch (Exception $e) {
    // Clear any unwanted output
    ob_end_clean();

    // Return error as JSON
    echo json_encode(array('error' => $e->getMessage()));
}
?>