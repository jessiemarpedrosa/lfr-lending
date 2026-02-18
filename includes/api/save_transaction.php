<?php

// Start output buffering to catch any unwanted output
ob_start();

try {
    // Load WordPress to access DB constants
    require_once(__DIR__ . '/../../../../../wp-load.php');

    include __DIR__ . '/../db-config.php';

    // Clear any unwanted output from WordPress loading
    ob_end_clean();

    // Set JSON header
    header('Content-Type: application/json');
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(array("statusCode"=>201, "error"=>"Initialization error: " . $e->getMessage()));
    exit;
}

// Check connection
if($conn === false){
    echo json_encode(array("statusCode"=>201, "error"=>"Database connection failed: " . mysqli_connect_error()));
    exit;
}

$transDate = $_POST['transDate'] ?? null;
$desc1 = mysqli_real_escape_string($conn, $_POST['desc1'] ?? '');
$desc2 = floatval($_POST['desc2'] ?? 0); // It's a FLOAT column, not text!
$paidRemark = mysqli_real_escape_string($conn, $_POST['paidRemark'] ?? '');
$amt_received = mysqli_real_escape_string($conn, $_POST['amt_received'] ?? '0');
$custNo = mysqli_real_escape_string($conn, $_POST['custNo'] ?? '');
$loanNo = mysqli_real_escape_string($conn, $_POST['loanNo'] ?? '');
$balance = mysqli_real_escape_string($conn, $_POST['balance'] ?? '0');
$totalLoanAmt = mysqli_real_escape_string($conn, $_POST['totalLoanAmt'] ?? '0');
$account = mysqli_real_escape_string($conn, $_POST['account'] ?? '');
$route_no = mysqli_real_escape_string($conn, $_POST['route_no'] ?? '');
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$bName = mysqli_real_escape_string($conn, $_POST['bName'] ?? '');
$loanDate = $_POST['loanDate'] ?? null;

$sql = "INSERT INTO lfr_transactions ( transaction_date, description_1, description_2, paid, amt_received, customer_no, loan_no, balance, total_loan_amt, account, route_no, name, business_name, loan_date ) VALUES ( '$transDate','$desc1',$desc2,'$paidRemark','$amt_received','$custNo','$loanNo','$balance','$totalLoanAmt','$account','$route_no','$name','$bName','$loanDate' )";

// var_dump($sql);

if (mysqli_query($conn, $sql)) {
	$last_id = mysqli_insert_id($conn);
	echo json_encode(array( "statusCode"=>200, "last_id"=>$last_id ));
}
else {
	$error_msg = mysqli_error($conn);
	echo json_encode(array("statusCode"=>201, "error"=>$error_msg));
	error_log("Save transaction error: " . $error_msg);
}
mysqli_close($conn);


?>