<?php

error_reporting (E_ALL ^ E_NOTICE);

// Load WordPress to access DB constants
require_once(__DIR__ . '/../../../../../wp-load.php');

include __DIR__ . '/../db-config.php';

$id = $_POST['id'];
$paidRemark = $_POST['paidRemark'];
$amt_received = $_POST['amt_received'];

$sql = "UPDATE lfr_transactions SET amt_received='$amt_received', paid='$paidRemark' WHERE id=$id";

// $sql_get_accounts = "SELECT DISTINCT account FROM lfr_loans WHERE `status` = 'Active'";

if (mysqli_query($conn, $sql)) {
	echo json_encode(array("statusCode"=>200, "new_amt_received"=>$amt_received, "new_paidRemark"=>$paidRemark ));
} 
else {
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);

?>