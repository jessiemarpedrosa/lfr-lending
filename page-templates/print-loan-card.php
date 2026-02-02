<?php
/**
 * Template Name: Print Loan Card
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
					<input type="text" placeholder="Loan No." value="" name="loan_no">
				</div>
				
				
				<div class="filterActions__btn">
					<input type="submit" value="Search" name="filter">
					<input class="reset" type="reset" value="Clear">
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
		echo "Loan Number containing <span class='searched_loan_no text-bold'>" . $loan_no . '</span>'; 
		
		if (isset($cust_no))
		$queryLoanNo = " AND loans.loan_no LIKE '%" . $loan_no . "%' ";
		else
		$queryLoanNo = " WHERE loans.loan_no LIKE '%" . $loan_no . "%' ";	
	}
	

	/*
	**	Query all Loans based on the values inputted on the fields above
	*/
	$sql_get_loans = "SELECT DISTINCT loans.loan_no, loans.account, loans.route_no, loans.cust_no, cust.fname, cust.lname, cust.bname, loans.loan_date, loans.dailyrate, loans.totalloanamt, loans.balance, loans.id, loans.status, loans.durationofloan, loans.tlapayback, loans.loaninterestrate
	FROM lfr_loans loans INNER JOIN lfr_customers cust 
	ON cust.custnum = loans.cust_no " . $queryLoanNo . $queryCustNo .
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
				
				<img style="display:none;width:250px;height:250px;max-width: 250px;" id="lfrCustPhoto" src="<?php echo $imageBig; ?>" />
				
				<div class="custInfo" style="display:none;">
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



<div class="printBtnWrapper hidden">
  <button class="printBtn" id="printBtnFront">
    Print Loan Card - FRONT
  </button>
  <button class="printBtn" id="printBtnBack">
    Print Loan Card - BACK
  </button>
</div>


<div id="loanCardBack" class="hidden container p-0">

	<div class="loanCardBack__header">
		<h4 class="loanCardBack__headerTotAmt">Total Amount: </h4>
		<h4 class="loanCardBack__headerDate">Date: </h4>
	</div>

	<div class="loanCardBack_colWrap">
		<div class="loanCardBack_col">
		  <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Date</th>
				<th width="95">Payment</th>
				<th width="95">Signature</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>
		<div class="loanCardBack_col">
		  <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Date</th>
				<th>Payment</th>
				<th>Signature</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>
		<div class="loanCardBack_col">
		  <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Date</th>
				<th>Payment</th>
				<th>Signature</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>
		<div class="loanCardBack_col">
		  <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Date</th>
				<th>Payment</th>
				<th>Signature</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>
		<div class="loanCardBack_col">
		  <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Date</th>
				<th>Payment</th>
				<th>Signature</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>
		<div class="loanCardBack_col last">
		  <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Date</th>
				<th>Payment</th>
				<th>Signature</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>

	</div>
  
</div>


<script>

jQuery(function($){
	
	
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

	
	
	$('#printBtnFront').click(function(){
		$('#loanCardFront').removeClass('makeCenter');
		$('#loanCardFront').printThis();	
	});

	// Print Function for printing Back portion of Loan Passbook Card
	$('#printBtnBack').on('click', function(e){
		// $('#divhidden').printThis();	
		console.log('You clicked the Print Back');
		
		html2canvas(document.querySelector("#loanCardBack")).then(canvas => {  
			var dataURL = canvas.toDataURL();
			var width = canvas.width;
			var printWindow = window.open("");
			
			$(printWindow.document.body)
			  .html("<img id='Image' src=" + dataURL + " style='" + width + "'></img>")
			  .ready(function() {
			  printWindow.focus();
			  printWindow.print();
			  // printWindow.close();
			});
		  });
	});

	if ( $('.loanDetailsList').length > 0 ){
		$('.printBtnWrapper, #loanCardFront, #loanCardBack').removeClass('hidden');
	}
	
});

</script>

<style>
@media print {
  button {
    content: none !important;
  }
  #loanCardFront canvas{
	  border: none;
  }
}

.container{
    max-width: 1680px;	
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
