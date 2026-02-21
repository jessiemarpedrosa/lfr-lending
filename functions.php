<?php 
 add_action( 'wp_enqueue_scripts', 'lfr_lending_enqueue_styles' );
 function lfr_lending_enqueue_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); 
  wp_enqueue_style( 'slide-out-panel', get_stylesheet_directory_uri() . '/css/slide-out-panel.css' ); 
  wp_enqueue_style( 'print-js', get_stylesheet_directory_uri() . '/css/print.min.css' ); 
} 
	
// /**
 // * Enqueue a script in the WordPress admin on edit.php.
 // *
 // * @param int $hook Hook suffix for the current admin page.
 // */
// function custom_script_enqueue_admin_script( $hook ) {

	// wp_enqueue_script( 'jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array('jquery'), true );
	// wp_enqueue_script( 'jspdf_autotable', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js', array('jquery'), true );
	// wp_enqueue_script( 'my_custom_script', get_stylesheet_directory_uri() . '/js/myscript.js', array('jquery'), '1.0', true );
// }
// add_action( 'admin_enqueue_scripts', 'custom_script_enqueue_admin_script' );

function load_my_scripts( $hook ) {
	wp_enqueue_script( 'jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array('jquery'), true );
	wp_enqueue_script( 'jspdf_autotable', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js', array('jquery'), true );
	wp_enqueue_script( 'slide-out-panel', get_stylesheet_directory_uri() . '/js/slide-out-panel.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'print-js', get_stylesheet_directory_uri() . '/js/print.min.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'my_custom_script', get_stylesheet_directory_uri() . '/js/myscript.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'printThis', get_stylesheet_directory_uri() . '/js/printThis.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'html2canvas', get_stylesheet_directory_uri() . '/js/html2canvas.min.js', array('jquery'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'load_my_scripts' );

/**
* Register and enqueue a custom stylesheet in the WordPress admin.
*/
function wpdocs_enqueue_custom_admin_style() {
	wp_register_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/css/myscript.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );


add_action( 'init', 'loadDB' );
function loadDB(){
	$servername = "localhost";
	$db="lfrlxope_lfr2022";
	$username = "lfrlxope_lfruser";
	$password = "34qIQ!i*fRK?";
	$conn = mysqli_connect($servername, $username, $password,$db);
}

add_filter( 'body_class', function( $classes ) { 
	$user = wp_get_current_user(); $roles = $user->roles; return array_merge( $classes, $roles ); 
});

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
		height:230px;
		width:230px;
		background-size: 230px 230px;
		background-repeat: no-repeat;
        	padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


//Page Slug Body Class
function add_slug_body_class( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

// Track Demand Letter 1 Print
add_action('wp_ajax_track_dl1_print', 'track_dl1_print');
add_action('wp_ajax_nopriv_track_dl1_print', 'track_dl1_print');

function track_dl1_print() {
	include get_stylesheet_directory() . '/includes/db-config.php';
	$loan_id = intval($_POST['loan_id']);
	if ($loan_id > 0) {
		$sql = "UPDATE lfr_loans SET tracked_dl1 = NOW() WHERE id = $loan_id";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		wp_send_json_success();
	} else {
		wp_send_json_error('Invalid loan ID');
	}
}

// Track Demand Letter 2 Print
add_action('wp_ajax_track_dl2_print', 'track_dl2_print');
add_action('wp_ajax_nopriv_track_dl2_print', 'track_dl2_print');

function track_dl2_print() {
	include get_stylesheet_directory() . '/includes/db-config.php';
	$loan_id = intval($_POST['loan_id']);
	if ($loan_id > 0) {
		$sql = "UPDATE lfr_loans SET tracked_dl2 = NOW() WHERE id = $loan_id";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		wp_send_json_success();
	} else {
		wp_send_json_error('Invalid loan ID');
	}
}