<?php

error_reporting (E_ALL ^ E_NOTICE); 

include 'db-config.php';

$sql = "SELECT id FROM lfr_customers ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

while($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);


mysqli_free_result($result);

mysqli_close($conn);


?>