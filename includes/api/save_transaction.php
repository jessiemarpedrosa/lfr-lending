<?php

include __DIR__ . '/../db-config.php';

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$transDate=$_POST['transDate'];
$desc1=$_POST['desc1'];
$desc2=$_POST['desc2'];
$paidRemark=$_POST['paidRemark'];
$amt_received=$_POST['amt_received'];
$custNo=$_POST['custNo'];
$loanNo=$_POST['loanNo'];
$balance=$_POST['balance'];
$totalLoanAmt=$_POST['totalLoanAmt'];
$account=$_POST['account'];
$route_no=$_POST['route_no'];
$name=$_POST['name'];
$bName=$_POST['bName'];

// $bName= mysqli_real_escape_string( trim($_POST['bName']) );
// $bName=$mysqli -> real_escape_string($_POST['bName']);

$loanDate=$_POST['loanDate'];

$sql = "INSERT INTO lfr_transactions ( transaction_date, description_1, description_2, paid, amt_received, customer_no, loan_no, balance, total_loan_amt, account, route_no, name, business_name, loan_date ) VALUES ( '$transDate','$desc1','$desc2','$paidRemark','$amt_received','$custNo','$loanNo','$balance','$totalLoanAmt','$account','$route_no','$name','$bName','$loanDate' )";

// var_dump($sql);

if (mysqli_query($conn, $sql)) {
	$last_id = mysqli_insert_id($conn);
	echo json_encode(array( "statusCode"=>200, "last_id"=>$last_id ));

	// echo "New record created successfully. Last inserted ID is: " . $last_id;
	// var_dump($sql);
} 
else {
	echo json_encode(array("statusCode"=>201));
	var_dump(mysqli_error($conn));
	var_dump(mysqli_error($sql));
}
mysqli_close($conn);


?>