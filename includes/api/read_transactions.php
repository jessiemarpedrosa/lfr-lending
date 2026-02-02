<?php

error_reporting (E_ALL ^ E_NOTICE); 

include __DIR__ . '/../db-config.php';

$transDate = $_REQUEST['transDate'];
$account = $_REQUEST['account'];
$loanNo = $_REQUEST['loan_no'];

if ( isset($loanNo) ){
	$sql = "SELECT * FROM lfr_transactions WHERE loan_no = '" . $loanNo . "' ORDER BY transaction_date DESC";
	$result = mysqli_query($conn, $sql);
}
else {
	$sql = "SELECT * FROM lfr_transactions WHERE transaction_date = '" . $transDate . "' AND account = '" . $account . "'";
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