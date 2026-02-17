<?php

/*
**  DATABASE CONFIG
**  Uses WordPress database constants - works on both local and live
*/

// Use WordPress's database credentials (defined in wp-config.php)
$servername = DB_HOST;
$db = DB_NAME;
$username = DB_USER;
$password = DB_PASSWORD;

$conn = mysqli_connect($servername, $username, $password, $db);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

?>