<?php
/**
 * Template Name: D Transactions
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
				<select class="filterActions__account" type="text" name="account">
					<option value="" selected disabled hidden>Select an Account/Collector</option>
					<?php 
					while($row = mysqli_fetch_array($result)){
					echo "<option value='" . $row['account'] . "'>" . $row['account'] . "</option>"; 
					} 
					?>
				</select>
				<div class="filterActions__loanNo">
					<input style="width: 120px;" type="text" placeholder="Loan No." value="" name="loan_no">
				</div>
				<div class="filterActions__custNo">
					<input style="width: 120px;" type="text" placeholder="Customer No." value="" name="cust_no">
				</div>
				<div class="filterActions__btn">
					<input type="submit" value="Filter" name="filter">
					<input class="reset" type="reset" value="Reset">
					<button class="btn btn-primary px-4 printPDF" type="button">Print</button>
				</div>
			</form>
			
				
			<div class="transactionDate mb-4">
				<label>Transaction Date:</label>
				<input disabled type="date" id="transaction_date" class="transaction_date" onfocus="this.showPicker()">
			</div>
			
		</div>
		
<?php
}

if(isset($_POST['filter'])){
		
	$sel_account = $_POST['account'];  // Storing Selected Value In Variable
	$loan_no = $_POST['loan_no'];
	$cust_no = $_POST['cust_no'];
	echo "You searched for the account <span class='searched_account text-bold'>" . $sel_account . '</span>';  // Displaying Selected Value

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

	/*
	**	Query all Loans based on the selected Account from dropdown
	*/
	$sql_get_loans = "SELECT DISTINCT loans.loan_no, loans.account, loans.route_no, loans.cust_no, cust.fname, cust.lname, cust.bname, loans.loan_date, loans.dailyrate, loans.totalloanamt, loans.balance, loans.id
	FROM lfr_loans loans INNER JOIN lfr_customers cust 
	ON cust.account = loans.account " . $queryAccount . $queryLoanNo . $queryCustNo .
	" AND loans.status = 'ACTIVE' GROUP BY loans.loan_no ORDER BY cust.fname";
	
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

	if($result = mysqli_query($conn, $sql_get_loans)){
		$rowNum = 1;
		$num_rows = mysqli_num_rows($result);
		echo '<div class="infoMsg">
			<p style="margin:10px 0 0;" >There are ' . $num_rows . ' record/s found for this filter.</p>
			<p style="margin:10px 0 0;" class="infoMsg__transDate"></p>
			<p style="margin:10px 0 0;" class="infoMsg__totalPayments"></p>
			</div>';

		if(mysqli_num_rows($result) > 0){
			echo '<div class="filterResults"><table class="table table-bordered table-striped">';
				echo "<thead>";
					echo "<tr>";
						echo "<th>#</th>";
						echo "<th style='width: 95px;'>Loan No.</th>";
						echo "<th>Customer No.</th>";
						echo "<th>Account</th>";
						echo "<th>Route No.</th>";
						echo "<th>Customer Name</th>";
						echo "<th>Business Name</th>";
						echo "<th style='width: 110px;'>Loan Date</th>";
						echo "<th>Daily Rate</th>";
						echo "<th style='width: 120px;'>Amt Received</th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				while($row = mysqli_fetch_array($result)){
					
					$desc1 = ($row['balance'] <= 0) ? 'LOAN PAYMENT' : 'UNPAID LOAN';
					
					echo "<tr data-test='" . $row['cust_no'] . " " . $row['id'] . "' class='" . $row['loan_no'] . "' data-loan_no='" . $row['loan_no'] . "' data-cust_no='" . $row['cust_no'] . "' >";
						echo "<td>" . $rowNum . "</td>";
						echo "<td>" . $row['loan_no'] . "</td>";
						echo "<td>" . $row['cust_no'] . "</td>";						
						echo "<td>" . $row['account'] . "</td>";
						echo "<td>" . $row['route_no'] . "</td>";
						echo "<td>" . $row['fname'] . "</td>";
						echo "<td>" . $row['bname'] . "</td>";
						echo "<td>" . $row['loan_date'] . "</td>";
						echo "<td>" . $row['dailyrate'] . "</td>";
						echo "<td id='amt_received'>" . $row['amt_received'] . "</td>";
						// echo "<td><input disabled id='amt_received' type='number' value='' /></td>";
						
							// echo '<button class="edit" data-bs-toggle="modal" data-bs-target="#editTransactionModal">
									// <i class="material-icons update" data-toggle="tooltip" 
									// data-id="' . $row["id"] . '"
									// data-name=' . $row["name"] . '"
									// title="Edit">Edit</i>
								// </button>';
							
							echo '<input name="totalloanamt" type="hidden" value="' . $row['totalloanamt'] . '" />';
							echo '<input name="balance" type="hidden" value="' . $row['balance'] . '" />';
							echo '<input name="description1" type="hidden" value="' . $desc1 . '" />';
							echo '<input name="route_no" type="hidden" value="' . $row['route_no'] . '" />';
							echo '<input name="dailyrate" type="hidden" value="' . $row['dailyrate'] . '" />';
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
<div id="editTransactionModal" class="modal fade">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form action="#" id="edit_form">
				<div class="modal-header">						
					<h4 class="modal-title">Edit Payment for <span class="edit_form__name"></span></h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="id_u" name="id" class="form-control" required>					
					<div class="form-group">
						<label>Loan No.</label>
						<input type="text" id="loanNo_u" name="loanNo" class="form-control" disabled>
					</div>
					<div class="form-group">
						<label>Name</label>
						<input type="text" id="name_u" name="name" class="form-control" disabled>
					</div>
					<div class="form-group">
						<label>Amount Received</label>
						<input type="number" id="amt_received_u" name="amt_received" class="form-control" required>
					</div>
					<div class="form-group paidRemarks">
						<label>Paid Remarks</label>
						<span style='display:block;'>
							<input type='radio' id = '' name='' value='TRUE' checked='checked' data-value='TRUE'  />
							<label for=''>TRUE</label>
						</span>
						<span style='display:block;'>
						<input type='radio' id = '' name='' value='FALSE' data-value='FALSE' />
						<label for=''>FALSE</label></span>
					</div>
		
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="update">Update</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				  </div>
			</form>
		</div>
	</div>
</div>

<div class="mb-5"></div>

	
	</main><!-- #primary -->

<?php
get_footer();
