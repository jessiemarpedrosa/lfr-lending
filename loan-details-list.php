<?php
/**
 * Template Name: Loan Details List
 * Template Post Type: page, post
 */

get_header(); 
?>

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

$sql_get_accounts = "SELECT DISTINCT account FROM lfr_loans";

/*
**	Get Accounts from Loan Table, and display in a dropdown
*/
if($result = mysqli_query($conn, $sql_get_accounts)){
$account = $_POST['account'];
		?>
		<div class="filterActions d-flex">
			<form class="d-flex m-0" action="#" method="post">
				
				<div class="filterActions__custNo">
					<input type="text" placeholder="Customer No." value="" name="cust_no">
				</div>
				
				<div class="filterActions__loanNo">
					<input type="text" placeholder="Loan No." value="" name="loan_no">
				</div>
				
				
				<div class="filterActions__btn">
					<input type="submit" value="Search" name="filter">
					<input class="reset" type="reset" value="Reset">
				</div>
			</form>
			
				
			<!-- <div class="transactionDate mb-4">
				<label>Set Transaction Date:</label>
				<input disabled type="date" id="transaction_date" class="transaction_date" onfocus="this.showPicker()">
			</div> -->
			
		</div>
		
<?php
}

if(isset($_POST['filter'])){
		
	$sel_account = $_POST['account'];  // Storing Selected Value In Variable
	$loan_no = $_POST['loan_no'];
	$cust_no = $_POST['cust_no'];
	// echo "You searched for the account <span class='searched_account text-bold'>" . $sel_account . '</span>';  // Displaying Selected Value
	
	if (isset($cust_no) && $cust_no!=''){
		echo "<br>Customer Number containing <span class='searched_cust_no text-bold'>" . $cust_no . '</span>'; 
		
		// if (isset($loan_no))
		// $queryCustNo = " AND loans.cust_no LIKE '%" . $cust_no . "%' ";
		// else
		$queryCustNo = " WHERE loans.cust_no LIKE '%" . $cust_no . "%' ";	
	}
	
	if (isset($loan_no) && $loan_no!=''){
		echo "Loan Number containing <span class='searched_loan_no text-bold'>" . $loan_no . '</span>'; 
		
		if (isset($cust_no))
		$queryLoanNo = " AND loans.loan_no LIKE '%" . $loan_no . "%' ";
		else
		$queryLoanNo = " WHERE loans.loan_no LIKE '%" . $loan_no . "%' ";	
	}
	

	/*
	**	Query all Loans based on the selected Account from dropdown
	*/
	$sql_get_loans = "SELECT DISTINCT loans.loan_no, loans.account, loans.route_no, loans.cust_no, cust.fname, cust.lname, cust.bname, loans.loan_date, loans.dailyrate, loans.totalloanamt, loans.balance, loans.id, loans.status, loans.durationofloan
	FROM lfr_loans loans INNER JOIN lfr_customers cust 
	ON cust.account = loans.account " . $queryLoanNo . $queryCustNo .
	" GROUP BY loans.loan_no ORDER BY loans.status, loans.loan_date DESC";
	
	if($result = mysqli_query($conn, $sql_get_loans)){
		$rowNum = 1;
		$num_rows = mysqli_num_rows($result);
		
		echo '<div class="infoMsg">
			<p style="margin:10px 0 0;" >There are ' . $num_rows . ' record/s found for this search.</p>
			<p style="margin:10px 0 0;" class="infoMsg__transDate"></p>
			</div>';

		if(mysqli_num_rows($result) > 0){
			echo '<div class="filterResults loanDetailsList"><table class="table table-bordered table-striped">';
				echo "<thead>";
					echo "<tr>";
						echo "<th>#</th>";
						echo "<th>Customer No.</th>";
						echo "<th>Account</th>";
						echo "<th>Route No.</th>";
						echo "<th >Loan  No.</th>";
						echo "<th>Name</th>";
						echo "<th>Business Name</th>";
						echo "<th>Loan Amount</th>";
						echo "<th style='width: 110px;'>Loan Date</th>";
						echo "<th>Duration(days)</th>";
						echo "<th>Status</th>";
						echo "<th></th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				while($row = mysqli_fetch_array($result)){
					
					$desc1 = ($row['balance'] <= 0) ? 'LOAN PAYMENT' : 'UNPAID LOAN';
					
					echo "<tr data-test='" . $row['cust_no'] . " " . $row['id'] . "' class='" . $row['loan_no'] . "' data-loan_no='" . $row['loan_no'] . "' data-cust_no='" . $row['cust_no'] . "' >";
						echo "<td>" . $rowNum . "</td>";
						echo "<td>" . $row['cust_no'] . "</td>";
						echo "<td>" . $row['account'] . "</td>";
						echo "<td>" . $row['route_no'] . "</td>";
						echo "<td>" . $row['loan_no'] . "</td>";
						echo "<td>" . $row['fname'] . "</td>";
						echo "<td>" . $row['bname'] . "</td>";
						echo "<td>" . number_format(( $row['totalloanamt'] ), 2, '.', ',') . "</td>";
						echo "<td>" . $row['loan_date'] . "</td>";
						echo "<td>" . $row['durationofloan'] . "</td>";
						echo "<td class='" . $row['status'] . "'>" . $row['status'] . "</td>";
						
						echo "<td class='actions'>";
							echo '<button data-loan_no="' . $row['loan_no'] . '" class="btn_view_payments btn btn-primary" class="mr-3" title="View Payments" data-toggle="tooltip">View Payments</button>';
							
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
		<h3 class="mb-4">Payments for Loan <span class='slidePanel_loanNo'></span></h3>
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


<!-- PAGINATION -->
<!--
<ul class="pagination">
	<li><a href="?pageno=1">First</a></li>
	<li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
		<a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
	</li>
	<li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
		<a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
	</li>
	<li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
</ul>
-->	
	
	</main><!-- #primary -->

<?php
get_footer();
