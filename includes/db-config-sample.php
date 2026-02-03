<?php

/*
**  DATABASE CONFIG TEMPLATE
**
**  IMPORTANT:
**  1. Copy this file to 'db-config.php' in the same directory
**  2. Update the values below with your environment-specific credentials
**  3. Never commit db-config.php to version control (it's in .gitignore)
*/

/* Database credentials - UPDATE THESE VALUES FOR YOUR ENVIRONMENT */
$servername = "localhost";        // Database host (e.g., localhost, 127.0.0.1, or remote host)
$db = "your_database_name";       // Database name
$username = "your_database_user"; // Database username
$password = "your_database_pass"; // Database password

$conn = mysqli_connect($servername, $username, $password, $db);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

?>
