<?php 

/*
**  CONFIG
*/
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$servername = "localhost";
$db="local";
$username = "root";
$password = "root";

$conn = mysqli_connect($servername, $username, $password, $db);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

?>