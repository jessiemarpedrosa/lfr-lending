<?php

error_reporting (E_ALL ^ E_NOTICE); 

include 'db-config.php';

$transDate = $_REQUEST['transDate'];
$account = $_REQUEST['account'];
$loanNo = $_REQUEST['loan_no'];
$d_start_date = $_REQUEST['start_date'];
$d_end_date = $_REQUEST['end_date'];

if ( isset($loanNo) ){
	$sql = "SELECT * FROM lfr_transactions WHERE (transaction_date BETWEEN '" . $d_start_date . "' AND '" . $d_end_date . "') AND loan_no = '" . $loanNo . "' ORDER BY transaction_date DESC";
	$result = mysqli_query($conn, $sql);
}
else {
	$sql = "SELECT * FROM lfr_transactions WHERE (transaction_date BETWEEN '" . $d_start_date . "' AND '" . $d_end_date . "') AND account = '" . $account . "' ORDER BY transaction_date DESC";
	$result = mysqli_query($conn, $sql);

	// $totalPayments = "SELECT SUM(amt_received) FROM lfr_transactions WHERE transaction_date = '" . $transDate . "' AND account = '" . $account . "'";
	// $totalresult = mysqli_query($conn, $totalPayments);
	
	// var_dump($totalresult);
}

while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// if ( $totalresult )
// $data[] = array('total_payments' => $totalresult);

echo json_encode($data);


mysqli_free_result($result);

mysqli_close($conn);


?>