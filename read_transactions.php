<?php

error_reporting (E_ALL ^ E_NOTICE); 

include 'db-config.php';

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
}


while($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);

mysqli_free_result($result);

mysqli_close($conn);


?>