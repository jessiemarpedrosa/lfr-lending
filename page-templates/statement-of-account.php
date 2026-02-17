<?php
/**
 * Template Name: Statement of Account
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
include __DIR__ . '/../includes/db-config.php';

$sql_get_accounts = "SELECT DISTINCT account FROM lfr_loans";

/*
**	Get Accounts from Loan Table, and display in a dropdown
*/
if($result = mysqli_query($conn, $sql_get_accounts)){
$account = $_POST['account'];
		?>
		<div class="filterActions d-flex">
			<form class="d-flex m-0" action="#" method="post">
				
				<div class="filterActions__loanNo">
					<input type="text" placeholder="Search Loan No." value="" name="loan_no">
				</div>
				
				
				<div class="filterActions__btn">
					<input type="submit" value="Search" name="filter">
					<input class="reset" type="reset" value="Clear">
					<button id="printPromi" class="btn btn-primary px-4" type="button">Print</button>
				</div>
			</form>
			
		</div>
		
<?php
}

if(isset($_POST['filter'])){
		
	$sel_account = $_POST['account'];  // Storing Selected Value In Variable
	$loan_no = $_POST['loan_no'];
	$cust_no = '';
	// echo "You searched for the account <span class='searched_account text-bold'>" . $sel_account . '</span>';  // Displaying Selected Value
	
	if (isset($cust_no) && $cust_no!=''){
		echo "<br>Customer Number containing <span class='searched_cust_no text-bold'>" . $cust_no . '</span>'; 
		
		// if (isset($loan_no))
		// $queryCustNo = " AND loans.cust_no LIKE '%" . $cust_no . "%' ";
		// else
		$queryCustNo = " WHERE loans.cust_no LIKE '%" . $cust_no . "%' ";	
	}
	
	if (isset($loan_no) && $loan_no!=''){
		echo "Loan Number <span class='searched_loan_no text-bold'>" . $loan_no . '</span>'; 
		
		if (isset($cust_no))
		$queryLoanNo = " AND loans.loan_no = '" . $loan_no . "'";
		else
		$queryLoanNo = " WHERE loans.loan_no = '" . $loan_no . "'";	
	}
	

	/*
	**	Query all Loans based on the values inputted on the fields above
	*/
	$sql_get_loans = "SELECT DISTINCT loans.loan_no, loans.account, loans.route_no, loans.cust_no, cust.fname, cust.lname, cust.bname, cust.waddress1, loans.loan_date, loans.dailyrate, 
	loans.totalloanamt, loans.balance, loans.id, loans.status, loans.durationofloan, loans.tlapayback, loans.loaninterestrate
	FROM lfr_loans loans INNER JOIN lfr_customers cust 
	ON cust.custnum = loans.cust_no " . $queryLoanNo . $queryCustNo .
	" GROUP BY loans.loan_no ORDER BY loans.status, loans.loan_date DESC";
	
	if($result = mysqli_query($conn, $sql_get_loans)){
		$rowNum = 1;
		$num_rows = mysqli_num_rows($result);
	
		echo '<div class="infoMsg">
			<p style="margin:10px 0 0;" >There\'s ' . $num_rows . ' record found for this search.</p>
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
						echo "<th>Loan Int Rate in %</th>";
						echo "<th>TLA Payback</th>";
						echo "<th style='width: 110px;'>Loan Date</th>";
						echo "<th>Duration(days)</th>";
						echo "<th>Status</th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				while($row = mysqli_fetch_array($result)){

					$desc1 = ($row['balance'] <= 0) ? 'LOAN PAYMENT' : 'UNPAID LOAN';
					$cust_no = $row['cust_no'];
					$loan_no_query = $row['loan_no'];

					// Get total payments for this loan
					$sql_total_payments = "SELECT SUM(amt_received) as total_payments FROM lfr_transactions WHERE loan_no = '" . mysqli_real_escape_string($conn, $loan_no_query) . "'";
					$result_payments = mysqli_query($conn, $sql_total_payments);
					$total_payments = 0;
					if ($result_payments && mysqli_num_rows($result_payments) > 0) {
						$payment_row = mysqli_fetch_assoc($result_payments);
						$total_payments = $payment_row['total_payments'] ? $payment_row['total_payments'] : 0;
					}

					// Get last payment date and amount for this loan
					$sql_last_payment = "SELECT transaction_date, amt_received FROM lfr_transactions WHERE loan_no = '" . mysqli_real_escape_string($conn, $loan_no_query) . "' ORDER BY transaction_date DESC LIMIT 1";
					$result_last_payment = mysqli_query($conn, $sql_last_payment);
					$last_payment_date = null;
					$last_payment_amount = 0;
					if ($result_last_payment && mysqli_num_rows($result_last_payment) > 0) {
						$last_payment_row = mysqli_fetch_assoc($result_last_payment);
						$last_payment_date = $last_payment_row['transaction_date'];
						$last_payment_amount = $last_payment_row['amt_received'];
					}

					// Get all active loans for this customer
					$sql_active_loans = "SELECT loan_no FROM lfr_loans WHERE cust_no = '" . mysqli_real_escape_string($conn, $cust_no) . "' AND status = 'Active' ORDER BY loan_no";
					$result_active_loans = mysqli_query($conn, $sql_active_loans);
					$active_loans = array();
					if ($result_active_loans && mysqli_num_rows($result_active_loans) > 0) {
						while ($loan_row = mysqli_fetch_assoc($result_active_loans)) {
							$active_loans[] = $loan_row['loan_no'];
						}
					}
					$active_loans_display = !empty($active_loans) ? implode(', ', $active_loans) : 'None';

					// Calculate duration display (e.g., "60 days (2 months)")
					$duration_days = $row['durationofloan'];
					$duration_months = round($duration_days / 30, 1);
					$duration_display = $duration_days . " days (" . $duration_months . " months)";

					// Calculate interest amount (Capital × Annual Rate / 100)
					$interest_amount = $row['totalloanamt'] * ($row['loaninterestrate'] / 100);

					// Calculate penalty (2% per month from last payment date to today)
					$penalty_amount = 0;
					$months_elapsed = 0;
					if ($last_payment_date) {
						$last_payment_datetime = new DateTime($last_payment_date);
						$today = new DateTime();
						$interval = $last_payment_datetime->diff($today);
						$days_elapsed = $interval->days;
						$months_elapsed = $days_elapsed / 30;
						$penalty_rate = 2; // 2% per month
						$penalty_amount = (($row['totalloanamt'] + $interest_amount) * ($penalty_rate / 100)) * $months_elapsed;
					}

					// Calculate total with interest and penalty
					$total_with_interest_penalty = $row['totalloanamt'] + $interest_amount + $penalty_amount;

					// Calculate final outstanding balance
					$final_outstanding_balance = $total_with_interest_penalty - $total_payments;

				?>
		
        		<!-- STATEMENT OF ACCOUNT Content -->		
                <div id="promi_note" class="promi_content">
                    <div class="head mb-4">
                        <img style="height: 80px;" src="https://lfrlending.com/wp-content/uploads/2023/06/site-logo-120.png" />
                        <div class="title">
                            <h3>Land of Five Rivers Lending, INC.</h3>
                            <p>C. Ouano Street, Centro, Mandaue City<br>
                            Contact us @ 09772577244</p>
                        </div>
                    </div>
                    
                    <h4 class="text-center mb-3">STATEMENT OF ACCOUNT</h4>

                    <div class="statementOfAccount">
                        <div class="statementOfAccount_top">
                            <div class="row mb-3">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <strong>Name:</strong>
                                        <span class=""><?= $row['fname'] . ' ' . $row['lname']; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Address:</strong>
                                        <span><?= $row['waddress1']; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Date of Loan:</strong>
                                        <span><?= date('F d, Y', strtotime($row['loan_date'])); ?></span>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <strong>Acct. Reference #:</strong>
                                        <span class=""><?= $row['cust_no']; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Active Loans:</strong>
                                        <span class=""><?= $active_loans_display; ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Total Payments:</strong>
                                        <span class=" text-success">₱ <?= number_format($total_payments, 2, '.', ','); ?></span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Outstanding Balance:</strong>
                                        <span class=" text-danger">₱ <?= number_format($row['balance'], 2, '.', ','); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Full Width Note -->
                            <div class="mt-3">
                                <strong>NOTE:</strong> See attachment with the following Loan details and Payments. (Payments Summary)
                            </div>

                            <!-- Last Payment Info -->
                            <?php if ($last_payment_date): ?>
                            <div class="mt-2 last-payment-info d-none">
                                Your last payment date was <strong><?= date('F d, Y', strtotime($last_payment_date)); ?></strong> amounting to <strong>₱ <?= number_format($last_payment_amount, 2, '.', ','); ?></strong>.
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="statementOfAccount_bot">
                            <!-- Loan Calculations Title -->
                            <h5 class="text-center fw-bold mb-4 mt-4">Simple Loan Calculator</h5>

                            <!-- Two Column Section -->
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="calc-item">
                                        <strong>Loan Amount:</strong>
                                        <span>₱ <?= number_format($row['totalloanamt'], 2, '.', ','); ?></span>
                                    </div>
                                    <div class="calc-item">
                                        <strong>Monthly Interest Rate:</strong>
                                        <span><?= number_format($row['loaninterestrate'] / $duration_months, 2); ?>%</span>
                                    </div>
                                    <div class="calc-item">
                                        <strong>Daily Payments:</strong>
                                        <span>₱ <?= number_format($row['dailyrate'], 2, '.', ','); ?></span>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <div class="calc-item">
                                        <strong>Loan Duration:</strong>
                                        <span><?= $duration_display; ?></span>
                                    </div>
                                    <div class="calc-item">
                                        <strong>Start Date:</strong>
                                        <span><?= date('F d, Y', strtotime($row['loan_date'])); ?></span>
                                    </div>
                                    <div class="calc-item">
                                        <strong>Total Payments:</strong>
                                        <span class="text-success">₱ <?= number_format($total_payments, 2, '.', ','); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Breakdown Section -->
                            <div class="breakdown-section">
                                <h6 class="fw-bold mb-2">Breakdown</h6>
                                <div class="breakdown-list">
                                    <div class="breakdown-item">
                                        <span class="amount">₱ <?= number_format($row['totalloanamt'], 2, '.', ','); ?></span>
                                        <span class="description">- Capital Loan Amount</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span class="amount">x <?= $row['loaninterestrate']; ?>%</span>
                                        <span class="description">- Interest for (<?= $duration_months; ?>) months</span>
                                    </div>
                                    <div class="breakdown-item">
                                        <span class="amount">+ ₱ <?= number_format($penalty_amount, 2, '.', ','); ?></span>
                                        <span class="description">- Penalty (2% per month)<?php if ($months_elapsed > 0) { echo ' - ' . number_format($months_elapsed, 1) . ' mos unpaid'; } ?></span>
                                    </div>
                                    <div class="breakdown-item subtotal">
                                        <span class="amount">= ₱ <?= number_format($total_with_interest_penalty, 2, '.', ','); ?></span>
                                        <span class="description">- Total w/ Interest + Penalty</span>
                                    </div>
                                    <div class="breakdown-item negative">
                                        <span class="amount">- ₱ <?= number_format($total_payments, 2, '.', ','); ?></span>
                                        <span class="description">- Payments Received</span>
                                    </div>
                                    <div class="breakdown-divider"></div>
                                    <div class="breakdown-item total">
                                        <span class="amount fw-bold text-danger">₱ <?= number_format($final_outstanding_balance, 2, '.', ','); ?></span>
                                        <span class="description fw-bold">- Final Outstanding Balance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        		
        		<?php
					
					echo "<tr data-test='" . $row['cust_no'] . " " . $row['id'] . "' class='" . $row['loan_no'] . "' data-loan_no='" . $row['loan_no'] . "' data-cust_no='" . $row['cust_no'] . "' >";
						echo "<td>" . $rowNum . "</td>";
						echo "<td>" . $row['cust_no'] . "</td>";
						echo "<td>" . $row['account'] . "</td>";
						echo "<td>" . $row['route_no'] . "</td>";
						echo "<td>" . $row['loan_no'] . "</td>";
						echo "<td>" . $row['fname'] . "</td>";
						echo "<td>" . $row['bname'] . "</td>";
						echo "<td class='loanTotalAmt'>" . number_format(( $row['totalloanamt'] ), 2, '.', ',') . "</td>";
						echo "<td class='loanIntRate'>" . $row['loaninterestrate'] . "</td>";
						echo "<td class='loanTLAPayback'>" . number_format(( $row['tlapayback'] ), 2, '.', ',') . "</td>";
						echo "<td class='loanDate'>" . $row['loan_date'] . "</td>";
						echo "<td>" . $row['durationofloan'] . "</td>";
						echo "<td class='" . $row['status'] . "'>" . $row['status'] . "</td>";
						
					echo "</tr>";
					
					$rowNum++;
				}
				echo "</tbody>";                            
			echo "</table></div>";
			
			
	

	// Get Customer Info based on the searched Loan Number
	$sql_get_cust = "SELECT * FROM lfr_customers WHERE custnum = '" . $cust_no . "'";
	
	if($resultCust = mysqli_query($conn, $sql_get_cust)){
			
		while($row = mysqli_fetch_array($resultCust)){
			
			//var_dump($row);
			// echo $row['fname'] . ' ' . $row['lname'] . '<br>';
			// echo $row['waddress1'] . '<br>';
			// echo $row['wcell'] . '<br>';
			//echo "<img src='" . $row['profile_picture'] . "'/>";
			
			// Explore BLOB string and get the first image , 250x250 size
			$arr = explode("||", $row['profile_picture'], 2);
			
			if ( $arr[0] ){
				$imageBig = $arr[0];
			} else {
				$imageBig = 'https://lfrlending.com/wp-content/uploads/2023/06/no-image-placeholder2.jpg';
			}
			
			?>
			
			<div id="loanCardFront" class="hidden">
			  <div class="col3" id="canvasWrapper">
				<!-- Canvas element here -->
				<img style="display:none;" id="lfr_logo" src="https://lfrlending.com/wp-content/uploads/2023/06/site-logo-120.png" />
				
				<img style="display:none;width:250px;height:250px;max-width: 250px;" id="lfrCustPhoto" src="<? echo $imageBig; ?>" />
				
				<div class="custInfo" style="display:block;">
					<span class="custInfo__name"><?php echo $row['fname'] . ' ' . $row['lname']; ?></span>
					<span class="custInfo__address"><?php echo $row['waddress1']; ?></span>
					<span class="custInfo__mobile"><?php echo $row['wcell']; ?></span>
					<span class="custInfo__custNum"><?php echo $row['custnum']; ?></span>
				</div>
			  </div>
			</div>

			<?php
			
		}
		
	}
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

?>

<script>

jQuery(function($){

	$("#printPromi").click(function(){
    	$("#promi_note").printThis();
	})

	// Only convert to words if the element exists
	if ($('.totalLoanAmtWords').length > 0) {
		var totalLoanAmt = $('.totalLoanAmtWords').data('value');
		if (totalLoanAmt && !isNaN(totalLoanAmt)) {
			$('.totalLoanAmtWords').text(numToWords(totalLoanAmt));
		}
	}

	var print = document.createElement('button')
	var canvas = document.createElement('canvas')
	var ctx = canvas.getContext('2d', { willReadFrequently: true })

	let lfrLogo = document.getElementById("lfr_logo");
	
	// Prepare and Create table with rows for the Back Part of the Loan Card
	var number_of_rows = 25;
	var number_of_cols = 3;
	var table_body = '';
	
	var custName = $('span.custInfo__name').text();
	var custAddr = $('span.custInfo__address').text();
	var custMobile = $('span.custInfo__mobile').text();
	var custNo = $('span.custInfo__custNum').text();
	var loanNo = $('.loanDetailsList table tr:nth-child(1) td:nth-child(5)').text();
	
	for(var i=0;i<number_of_rows;i++){
		table_body+='<tr>';
		for(var j=0;j<number_of_cols;j++){
			table_body +='<td>';
			table_body +='&nbsp;';
			table_body +='</td>';
		}
		table_body+='</tr>';
	}
	
	$('.loanCardBack_col table tbody').html(table_body);
  
	$("#loanCardFront").append('<div style="text-align: center;" class="loadingSection"><h5 >Generating Loan Passbook Card...</h5><div class="loadingio-spinner-gear-q3m74koo6mp"><div class="ldio-4ylvkgi6cim"><div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></div></div>');
	
	$('.loanCardBack__headerTotAmt').append( $('.loanTLAPayback').text() );
	$('.loanCardBack__headerDate').append( $('.loanDate').text() );
	
	setTimeout(function() {
		// Canvas for Front Page Loan Passbook Card
	
		canvas.width = 1650;
		canvas.height  = 800;
		moveRight = 105;
		
		ctx.font = "45px sans-serif";
		ctx.fillText("LFR Lending", 1150 + moveRight, 50); // Main Header , Format (Value, X pos, Y pos)

		
		ctx.drawImage(lfrLogo, 1220 + moveRight,75);  // LFR Logo - 120px
		
		ctx.font = "20px sans-serif";
		ctx.fillText("Customer Number: " + custNo, 1165 + moveRight, 225);
		ctx.fillText("Loan Number: " + loanNo, 1185 + moveRight, 255);
		
		ctx.drawImage(lfrCustPhoto, 1160 + moveRight, 275, 250, 250); // Customer Photo - 180px

		ctx.fillStyle = '#000';
		ctx.font = '30px sans-serif';
		ctx.fillText('LOAN PASSBOOK CARD', 1090 + moveRight, 575);

		// LFR Contact Details
		ctx.font = '20px sans-serif';
		ctx.fillText('Email: lfr.lending@gmail.com', 1140 + moveRight, 610);
		ctx.fillText('Tel No.: (032) 407 9397', 1170 + moveRight, 640);
		ctx.fillText('Mobile: 0932-617-1314', 1170 + moveRight, 670);

		// Customer Name
		ctx.fillText('Name: ' + custName, 1060 + moveRight, 730);
		ctx.fillText('Address: ' + custAddr, 1060 + moveRight, 758);
		ctx.fillText('Cellphone: ' + custMobile, 1060 + moveRight, 788);

		var textMeasurement = 200;
		let startX = 1095;
		ctx.fillStyle = "black";
		ctx.fillRect(1120 + moveRight, 735, 350, 2);
		ctx.fillRect(1145 + moveRight, 761, 330, 2);
		ctx.fillRect(1160 + moveRight, 791, 315, 2);

		$('.loadingSection').remove();
		document.getElementById('loanCardFront').appendChild(canvas)
		
	}, 3000);
	
});


function numToWords(n) {
    if (n < 0)
      return false;
	 single_digit = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine']
	 double_digit = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen']
	 below_hundred = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety']
	if (n === 0) return 'Zero'
	function translate(n) {
		word = ""
		if (n < 10) {
			word = single_digit[n] + ' '
		}
		else if (n < 20) {
			word = double_digit[n - 10] + ' '
		}
		else if (n < 100) {
			rem = translate(n % 10)
			word = below_hundred[(n - n % 10) / 10 - 2] + ' ' + rem
		}
		else if (n < 1000) {
			word = single_digit[Math.trunc(n / 100)] + ' Hundred ' + translate(n % 100)
		}
		else if (n < 1000000) {
			word = translate(parseInt(n / 1000)).trim() + ' Thousand ' + translate(n % 1000)
		}
		else if (n < 1000000000) {
			word = translate(parseInt(n / 1000000)).trim() + ' Million ' + translate(n % 1000000)
		}
		else {
			word = translate(parseInt(n / 1000000000)).trim() + ' Billion ' + translate(n % 1000000000)
		}
		return word
	}
	 result = translate(n) 
	return result.trim()+' Pesos'
}

</script>

<style>
@media print {
  button {
    content: none !important;
  }
  #loanCardFront canvas{
	  border: none;
  }
  .promi_content{
      border: none !important;
      padding: 0 !important;
  }
  .filterActions {
      display: none !important;
  }
  .filterResults {
      display: none !important;
  }

  /* Fix two-column layout for printing */
  .statementOfAccount_top .row,
  .statementOfAccount_bot .row {
      display: flex !important;
      flex-wrap: nowrap !important;
  }
  .statementOfAccount_top .col-md-6,
  .statementOfAccount_bot .col-md-6 {
      width: 50% !important;
      max-width: 50% !important;
      flex: 0 0 50% !important;
      float: left !important;
  }
  .statementOfAccount_top .col-md-6:first-child,
  .statementOfAccount_bot .col-md-6:first-child {
      padding-right: 15px !important;
  }
  .statementOfAccount_top .col-md-6:last-child,
  .statementOfAccount_bot .col-md-6:last-child {
      padding-left: 15px !important;
  }

  /* Breakdown section print styling */
  .breakdown-section {
      page-break-inside: avoid;
      width: 70% !important;
  }
}

/* Statement of Account Styling */
.statementOfAccount .row {
    padding-bottom: 20px;
}
.statementOfAccount .col-md-6 > div {
    padding: 0;
}
.statementOfAccount strong {
    text-transform: uppercase;
}
.statementOfAccount span {
    margin-top: 5px;
}
.statementOfAccount .text-success {
    font-weight: 600;
}
.statementOfAccount .text-danger {
    font-weight: 600;
}
.statementOfAccount_top {
    width: 85%;
    margin: 0 auto;
}

/* Loan Calculations Section */
.statementOfAccount_bot {
    width: 85%;
    margin: 0 auto;
    margin-top: 0px;
}
.statementOfAccount_bot .calc-item {
    margin-bottom: 15px;
    display: flex;
    column-gap: 10px;
}
.statementOfAccount_bot .calc-item strong {
    display: flex;
}
.statementOfAccount_bot .calc-item span {
    margin: 0;
}

/* Breakdown Section */
.breakdown-section {
    width: 40%;
    margin: 10px auto 0;
}
.breakdown-section h6 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
    font-size: 20px;
}
.breakdown-list {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
}
.breakdown-item {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 5px;
}
.breakdown-item .amount {
    min-width: 130px;
    font-weight: 600;
}
.breakdown-item.subtotal {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #ddd;
}
.breakdown-item.subtotal .amount {
    font-weight: 700;
}
.breakdown-item.negative .amount {
    color: #dc3545;
}
.breakdown-divider {
    border-top: 2px solid #333;
    margin: 15px 0;
}
.breakdown-item.total {
    margin-top: 10px;
    font-size: 18px;
}
.breakdown-item.total .amount {
    font-size: 18px;
}

span.value{
    font-weight: 600;
}
.promi_content{
    border: 3px dashed #e0e0e0;
    padding: 30px;
}
.promi_content .head{
    display: flex;
    gap: 50px;
    justify-content: center;
}
.promi_content .head .title{
    text-align: center;
}
.promi_content p.text-indent{
    text-indent: 40px;
}

.container .hidden{
	display: none;
}
.filterResults.loanDetailsList {
    margin-bottom: 50px;
}
#mainLoanCard {
    display: flex;
    gap: 20px;
}
img#lfrCustPhoto {
    width: 250px;
    height: 250px;
	max-width: 250px;
    object-fit: cover;
}
.printBtnWrapper .printBtn{
	margin-right: 20px;
}

.printBtnWrapper {
    margin: 50px 0;
    text-align: center;
}
.printBtn {
    background: #479723;
    padding: 15px 30px;
    font-size: 20px;
    border-radius: 5px;
}
.printBtn:hover{
	background: darkgreen;
}

.loanCardBack__header {
    margin-top: 40px;
	display: flex;
    gap: 20px;
}
.loanCardBack_colWrap {
	margin-top: 10px;
    display: flex;
}
.loanCardBack_col {
	min-width: 268px;
    margin-right: 8px;
}
.loanCardBack__header *,
.loanCardBack_col *{
	color: black;
}
.loanCardBack_col table{
	border-color: black;
}
.loanCardBack_col table thead tr th:nth-child(2), 
.loanCardBack_col table thead tr th:nth-child(3) {
    width: 95px;
}
.loanCardBack_col.last{
	min-width: 235px;
	margin: 0;
}
.loanCardBack_col.last table thead tr th:nth-child(2),
.loanCardBack_col.last table thead tr th:nth-child(3) {
	width: 90px;
}

#loanCardFront.makeCenter {
    display: flex;
    align-items: center;
    text-align: center;
    width: 100%;
    justify-content: center;
}
#loanCardFront.makeCenter canvas{
    position: relative;
    width: 550px;
    height: 800px;
    object-fit: cover;
    object-position: 92%;
    padding: 30px 20px 30px;
    display: flex;
    justify-content: center;
    align-items: center;
}

/**
***  Loading Icon
**/
@keyframes ldio-4ylvkgi6cim {
    0% { transform: rotate(0deg) }
   50% { transform: rotate(22.5deg) }
  100% { transform: rotate(45deg) }
}
.ldio-4ylvkgi6cim > div {
  transform-origin: 52px 52px;
  animation: ldio-4ylvkgi6cim 0.25s infinite linear;
}
.ldio-4ylvkgi6cim > div div {
    position: absolute;
    width: 11.440000000000001px;
    height: 79.04px;
    background: #1d0e0b;
    left: 52px;
    top: 52px;
    transform: translate(-50%,-50%);
}
.ldio-4ylvkgi6cim > div div:nth-child(1) {
    width: 62.400000000000006px;
    height: 62.400000000000006px;
    border-radius: 50%;
}
.ldio-4ylvkgi6cim > div div:nth-child(6) {
    width: 41.6px;
    height: 41.6px;
    background: #f1f2f3;
    border-radius: 50%;
}.ldio-4ylvkgi6cim > div div:nth-child(3) {
  transform: translate(-50%,-50%) rotate(45deg)
}.ldio-4ylvkgi6cim > div div:nth-child(4) {
  transform: translate(-50%,-50%) rotate(90deg)
}.ldio-4ylvkgi6cim > div div:nth-child(5) {
  transform: translate(-50%,-50%) rotate(135deg)
}
.loadingio-spinner-gear-q3m74koo6mp {
  width: 104px;
  height: 104px;
  display: inline-block;
  overflow: hidden;
  background: none;
}
.ldio-4ylvkgi6cim {
  width: 100%;
  height: 100%;
  position: relative;
  transform: translateZ(0) scale(1);
  backface-visibility: hidden;
  transform-origin: 0 0; /* see note above */
}
.ldio-4ylvkgi6cim div { box-sizing: content-box; }


</style>


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
