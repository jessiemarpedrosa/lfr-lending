<?php
/**
 * Template Name: Delinquent Customers
 * Template Post Type: page, post
 */

get_header(); 
?>

<script src="https://momentjs.com/downloads/moment.min.js" referrerpolicy="no-referrer"></script>
	<main id="primary" class="site-main" role="main">
		
		<?php
		while ( have_posts() ) :
			the_post();
			
			if ( is_single() ) {
				get_template_part( 'template-parts/content', get_post_type() );
			} else {
				get_template_part( 'template-parts/content', 'page' );
			}

		endwhile; // End of the loop.
		?>
		
<?php

if (isset($_POST['filter'])){
	$account = $_GET['id'];
}

// $perPage = 20;
// $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
// $startAt = $perPage * ($page - 1);

/*
**  DB CONFIG 
*/
include 'db-config.php';

$sql_get_accounts = "SELECT DISTINCT account FROM lfr_loans WHERE `status` = 'Active'";

/*
**	Get Accounts from Loan Table, and display in a dropdown
*/
if($result = mysqli_query($conn, $sql_get_accounts)){
$account = $_POST['account'];
		?>
		<div class="filterActions d-flex">
			<form class="d-flex m-0" action="#" method="post">
				<div>
					<label>Account/Collector:</label>
					<select class="filterActions__account" type="text" name="account">
						<option value="" selected disabled hidden>Select an Account/Collector</option>
						<?php 
						while($row = mysqli_fetch_array($result)){
						echo "<option value='" . $row['account'] . "'>" . $row['account'] . "</option>"; 
						} 
						?>
					</select>
				</div>
				<div class="transactionDate mb-4 mx-2">
					<label>Start Date:</label>
					<input type="date" name="delinq_start_date" id="delinq_start_date" class="delinq_start_date" onfocus="this.showPicker()">
				</div>	
					
				<div class="transactionDate mb-4">
					<label>End Date:</label>
					<input type="date" name="delinq_end_date" id="delinq_end_date" class="delinq_end_date" onfocus="this.showPicker()">
				</div>
	
				<!--
				<div class="filterActions__custNo">
					<input style="width: 120px;" type="text" placeholder="Customer No." value="" name="cust_no">
				</div>
				-->
				
				<div class="filterActions__btn" style="display: flex;">
					<input type="submit" value="Filter" name="filter">
					<input class="reset" type="reset" value="Reset">
					<button class="btn btn-primary px-4 printPDF" type="button">Print</button>
				</div>
				
				
			</form>
			
		</div>
		
<?php
}

if(isset($_POST['filter'])){
		
	$sel_account = $_POST['account'];  // Storing Selected Value In Variable
	$loan_no = $_POST['loan_no'];
	$cust_no = $_POST['cust_no'];
	$date_filter = $_POST['date_filter'];
	$d_start_date = $_POST['delinq_start_date'];
	$d_end_date = $_POST['delinq_end_date'];
	
	echo "Account: <span class='searched_account text-bold'>" . $sel_account . '</span>';  // Displaying Selected Value

	if (isset($account)){
		$queryAccount = "WHERE loans.account = '" . $sel_account . "' AND loans.cust_no = cust.custnum ";
	}
	if (isset($loan_no) && $loan_no!=''){
		echo "<br>Loan Number containing <span class='searched_loan_no text-bold'>" . $loan_no . '</span>'; 
		
		if (isset($account))
		$queryLoanNo = " AND loans.loan_no LIKE '%" . $loan_no . "%' ";
		else
		$queryLoanNo = " WHERE loans.loan_no LIKE '%" . $loan_no . "%' ";	
	}
	if (isset($cust_no) && $cust_no!=''){
		echo "<br>Customer Number containing <span class='searched_cust_no text-bold'>" . $cust_no . '</span>'; 
		
		if (isset($account))
		$queryCustNo = " AND loans.cust_no LIKE '%" . $cust_no . "%' ";
		else
		$queryCustNo = " WHERE loans.cust_no LIKE '%" . $cust_no . "%' ";	
	}
	
	if ( $d_start_date != '' && $d_end_date != ''){	
		$sql_get_payments = "SELECT * FROM lfr_transactions 
		WHERE (transaction_date BETWEEN '" . $d_start_date . "' AND '" . $d_end_date . "') 
		AND account = '" . $sel_account . "' ORDER BY transaction_date";
	}

	/*
	**	Query all Loans based on the selected Account from dropdown
	*/
	// $sql_get_loans = "SELECT DISTINCT loans.loan_no, loans.account, loans.route_no, loans.cust_no, cust.fname, cust.lname, cust.bname, loans.loan_date, loans.dailyrate, loans.totalloanamt, loans.balance, loans.id
	// FROM lfr_loans loans INNER JOIN lfr_customers cust 
	// ON cust.account = loans.account " . $queryAccount . $queryLoanNo . $queryCustNo .
	// " AND loans.status = 'ACTIVE' GROUP BY loans.loan_no ORDER BY loans.route_no";
	
	$sql_get_loans = "SELECT DISTINCT loans.loan_no, loans.account, loans.balance, loans.cust_no, cust.fname, cust.lname, cust.bname, cust.wcell, cust.waddress1, loans.loan_date, loans.dailyrate, loans.totalloanamt, loans.balance, loans.id
	FROM lfr_loans loans INNER JOIN lfr_customers cust " . $queryAccount . $queryLoanNo . $queryCustNo .
	" AND loans.status = 'ACTIVE' " .
	" GROUP BY loans.loan_no ORDER BY loans.route_no";
	
	/*
	**	Query Transactions Table for all saved Transactions of specific Account
	*/
	// $sql_get_trans = "SELECT * FROM lfr_transactions WHERE `account` = '" . $account . "' ";
	
	// if($result = mysqli_query($conn, $sql_get_trans)){
		// var_dump($results);
		
		// if(mysqli_num_rows($result) > 0){
			// while($row = mysqli_fetch_array($result)){
				// var_dump($row);
			// }
		// }
	// }

	
	echo "<h4>From <span class='delinq_start_date'>" . $d_start_date . "</span> to <span class='delinq_end_date'>" . $d_end_date . "</span></h4>";
	

	// Loop through the days, and get rid of Sundays,
	// Since they have no collections on Sundays
	$begin = new DateTime($d_start_date);
	$end = new DateTime($d_end_date);
	$end->modify('+1 day'); 
	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($begin, $interval, $end);
	$totalDaysCtr = 0;
	
	// if($result2 = mysqli_query($conn, $sql_get_payments)){
		// $num_rows = mysqli_num_rows($result2);
		// echo "There are " . $num_rows . " payments from the date provided";
		// while($row = mysqli_fetch_array($result2)){
			// echo "<tr><td>" . $row['loan_no'] . "</td>
				// <td>" . $row['customer_no'] . "</td>
				// <td>" . $row['transaction_date'] . "</td>
				// <td>" . $row['amt_received'] . "</td></tr>";
		// }
	// }
	
	foreach ($period as $d) {
		if ( $d->format("l") != 'Sunday' ) {
			$totalDaysCtr++;
			//echo $d->format("Y-m-d l") . "<br>";
		}
	}
	
	echo "Total number of days is : " . $totalDaysCtr;

	$multi = intdiv($totalDaysCtr, 6);
	$delinqBasisNo = $multi * 3;

	if($result = mysqli_query($conn, $sql_get_loans)){
		$rowNum = 1;
		$num_rows = mysqli_num_rows($result);
		echo '<div class="infoMsg">
			<p style="margin:10px 0 0;" >There are ' . $num_rows . ' record/s found for this filter.</p>
			<p style="margin:10px 0 0;" class="infoMsg__transDate"></p>
			<p style="margin:10px 0 0;" class="infoMsg__totalPayments"></p>
			</div>';

		if(mysqli_num_rows($result) > 0){
			echo '<div class="filterResults"><table class="table table-bordered">';
				echo "<thead>";
					echo "<tr>";
						echo "<th>#</th>";
						echo "<th>Customer No.</th>";
						echo "<th style='width: 95px;'>Loan No.</th>";
						echo "<th>Customer Name</th>";						
						echo "<th style='width: 140px;'>Mobile No.</th>";
						// echo "<th>Business Name</th>";
						echo "<th style='width: 220px;'>Address</th>";
						echo "<th>Total Loan Amt</th>";
						echo "<th>Delinquent?</th>";
						echo "<th style='width: 190px;'>Payment Remarks</th>";						
						echo "<th style='width: 155px;'></th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				while($row = mysqli_fetch_array($result)){
					
					$desc1 = ($row['balance'] <= 0) ? 'LOAN PAYMENT' : 'UNPAID LOAN';
					
					$loanNo = $row['loan_no'];
					$pmtCtr = 0;
					$delinqCust = 'NO';
					$delinqClass = '';
					
					// Loop through all transactions made from start to end date provided on the filter
					if($result2 = mysqli_query($conn, $sql_get_payments)){
						$num_rows = mysqli_num_rows($result2);
						// echo "There are " . $num_rows . " payments from the date provided";
						
						while($row2 = mysqli_fetch_array($result2)){
							
							$transDate = $row2['transaction_date'];
							
							if ($loanNo == $row2['loan_no'])
								$pmtCtr++;
							
						}
					}
					
					if ( ($totalDaysCtr - $pmtCtr) >= $delinqBasisNo ){
						$delinqCust = 'YES';
						$delinqClass = 'delinqCust';
					}
					
					
					echo "<tr class='" . $delinqClass . "' data-test='" . $row['cust_no'] . " " . $row['id'] . "' class='" . $row['loan_no'] . "' data-loan_no='" . $row['loan_no'] . "' data-cust_no='" . $row['cust_no'] . "' data-cust_name='" . $row['fname'] . "'>";
						echo "<td>" . $rowNum . "</td>";
						echo "<td>" . $row['cust_no'] . "</td>";	
						echo "<td>" . $row['loan_no'] . "</td>";					
						echo "<td>" . $row['fname'] . "</td>";
						echo "<td>" . $row['wcell'] . "</td>";
						// echo "<td>" . $row['bname'] . "</td>";
						echo "<td>" . $row['waddress1'] . "</td>";
						echo "<td>" . $row['totalloanamt'] . "</td>";
						echo "<td>" . $delinqCust . "</td>";
						echo "<td>" . $pmtCtr . " payments within " . $totalDaysCtr . " days</td>";
						echo "<td class='actions'>";
							echo '<button data-loan_no="' . $row['loan_no'] . '" data-cust_name="' . $row['fname'] . '" class="btn_view_delinqCust btn btn-primary" class="mr-3" title="Missed Payments" data-toggle="tooltip">View Payments</button>';
							
							// echo '<button class="edit" data-bs-toggle="modal" data-bs-target="#editTransactionModal">
									// <i class="material-icons update" data-toggle="tooltip" 
									// data-id="' . $row["id"] . '"
									// data-name=' . $row["name"] . '"
									// title="Edit">Edit</i>
								// </button>';
							
							echo '<input name="loan_no" type="hidden" value="' . $row['loan_no'] . '" />';
							echo '<input name="totalloanamt" type="hidden" value="' . $row['totalloanamt'] . '" />';
							echo '<input name="balance" type="hidden" value="' . $row['balance'] . '" />';
							echo '<input name="description1" type="hidden" value="' . $desc1 . '" />';
							echo '<input name="route_no" type="hidden" value="' . $row['route_no'] . '" />';
							echo '<input name="dailyrate" type="hidden" value="' . $row['dailyrate'] . '" />';
							echo '<a href="delete.php?id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
						echo "</td>";
						
						
					echo "</tr>";
					
					$rowNum++;
					
				}
				echo "</tbody>";                            
			echo "</table></div>";
			// Free result set
			mysqli_free_result($result);
		} else{
			echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
		}
	} else{
		echo "Oops! Something went wrong. Please try again later.";
	}

}

// Close connection
mysqli_close($conn);

function checkTransactionIfExist($account, $loan_no, $trans_date){
	
	// $exist = false;
	
	// return $exist;
}

?>

<!-- Edit Modal HTML -->
<div id="slide-out-panel" class="slide-out-panel">
	<section class="p-4">
		<h3 class="mb-4">Missed Payments for <span class='slidePanel_custName'></span> - Loan <span class='slidePanel_loanNo'></span></h3>
		<div class="loanDetails_main">
			<table class="table table-hover table-sm">
				<thead>
					<th>Payment Date</th>
					<th>Desc 1</th>
					<th>Desc 2</th>
					<th>Paid?</th>
					<th>Amt Received</th>
				</thead>
				<tbody>
				</tbody>
			</table>
			<div class="message_box" style='text-align: center;'></div>
		</div>
	</section>
</div>

<div class="mb-5"></div>

	
	</main><!-- #primary -->

<?php
get_footer();
