<?php
  $conn = new mysqli("localhost", "imrodmar_admin", "@Q(.@1MQ(Eqe", "imrodmar_dbase");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); }
$results = $conn->query("select * from queries"); 
  while($row = $results->fetch_assoc()) {
    echo $row['number'];
    }

?>