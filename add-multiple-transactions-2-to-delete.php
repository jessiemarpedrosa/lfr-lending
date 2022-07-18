<?php
/**
 * Template Name: Add Multiple Transactions v2
 * Template Post Type: page, post
 */

get_header(); ?>

<!--
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
-->

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

/*
**  CONFIG 
*/
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$servername = "localhost:3308";
$username = "root";
$password = "";
$dbname = "rekta";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// /* Attempt to connect to MySQL database */
// $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
// if($link === false){
    // die("ERROR: Could not connect. " . mysqli_connect_error());
// }

// Attempt select query execution
$sql = "SELECT * FROM lfr_loans";

// $result = mysqli_query($conn,$sql);


// var_dump($result);


if($result = mysqli_query($conn, $sql)){
	
	$num_rows = mysqli_num_rows($result);
	echo '<p>There are ' . $num_rows . ' records found.</p>';
	
	// $totalPages = ceil($num_rows['total'] / $perPage);
	
	// $total_pages_sql = mysqli_num_rows($result);
	// $result = mysqli_query($link,$total_pages_sql);
	// $total_rows = $num_rows;
	// $total_pages = ceil($total_rows / $no_of_records_per_page);
	
	if(mysqli_num_rows($result) > 0){
		echo '<table class="table table-bordered table-striped">';
			echo "<thead>";
				echo "<tr>";
					echo "<th>Loan No.</th>";
					echo "<th>Account</th>";
					echo "<th>Route No.</th>";
					echo "<th>Cust No.</th>";
					echo "<th>Name</th>";
					echo "<th>Business Name</th>";
					echo "<th>Loan Date</th>";
					echo "<th>Daily Rate</th>";
					echo "<th>TLA</th>";
					echo "<th>Balance</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while($row = mysqli_fetch_array($result)){
				echo "<tr>";
					echo "<td>" . $row['loan_no'] . "</td>";
					echo "<td>" . $row['account'] . "</td>";
					echo "<td>" . $row['route_no'] . "</td>";
					echo "<td>" . $row['cust_no'] . "</td>";
					echo "<td>" . $row['fname'] . "</td>";
					echo "<td>" . $row['bname'] . "</td>";
					echo "<td>" . $row['loan_date'] . "</td>";
					echo "<td>" . $row['dailyrate'] . "</td>";
					echo "<td>" . $row['totalloanamt'] . "</td>";
					echo "<td>" . $row['balance'] . "</td>";
					echo "<td>";
						echo '<a href="read.php?id='. $row['id'] .'" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
						echo '<a href="update.php?id='. $row['id'] .'" class="mr-3" title="Update Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
						echo '<a href="delete.php?id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
					echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";                            
		echo "</table>";
		// Free result set
		mysqli_free_result($result);
	} else{
		echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
	}
} else{
	echo "Oops! Something went wrong. Please try again later.";
}


// Close connection
mysqli_close($conn);

?>

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
