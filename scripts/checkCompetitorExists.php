<?php
require_once('./rconfig.php');

$data = json_decode(file_get_contents("php://input"));
$gamertag = $data->gamertag;

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$query = "select * from `ohn13_registrants` 
          where `Gamertag` = '$gamertag' 
          and `Pass_type` = 'Competitor'
          and `Payment_status` = 'Paid'; ";

$result = $query;

$qryresult = $mysqli->query($query);
if ($qryresult->num_rows > 0) {
  $result = 1;
} else {
  $result = 0;
}

$mysqli->close();

echo $result;

?>