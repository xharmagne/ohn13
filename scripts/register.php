<?php
require_once('./rconfig.php');

error_log("Registerdata:".file_get_contents("php://input"));

$data = json_decode(file_get_contents("php://input"));

$registrants = $data->registrants;
$paymentId = $data->paymentId;

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$count = count($registrants);

$insertcount = 0;
$insertquery = 
  "insert into `ohn13_registrants` (`Gamertag`, `Pass_type`, `First_name`, `Last_name`, `Email`, `State`, 
                                    `USF4`, `TTT2`, `MKX`, `KOF`, `MARVEL`,
                                    `Payment_ID`, `Payment_status`) values ";

$updatequery = "";

for ($i=0; $i < $count; $i++) {

  $gamertag = $mysqli->real_escape_string($registrants[$i]->gamertag);
  $passtype = $registrants[$i]->type;
       
  $firstname = $mysqli->real_escape_string($registrants[$i]->firstName);
  $lastname = $mysqli->real_escape_string($registrants[$i]->lastName);
  $email = $mysqli->real_escape_string($registrants[$i]->email);
  $state = $mysqli->real_escape_string($registrants[$i]->state);
  if (trim($state) == 'Other') {
    $state = $mysqli->real_escape_string($registrants[$i]->otherLocation);
  }
  
  $usf4 = ($registrants[$i]->usf4) ? "true" : "false";
  $ttt2 = ($registrants[$i]->ttt2) ? "true" : "false";
  $mkx  = ($registrants[$i]->mkx) ? "true" : "false";
  $kof  = ($registrants[$i]->kof) ? "true" : "false";
  $mvc  = ($registrants[$i]->mvc) ? "true" : "false";
  
  if ($passtype != "AddGames") {

    $insertquery .= "('$gamertag', '$passtype', '$firstname', '$lastname', '$email', '$state', 
                      $usf4, $ttt2, $mkx, $kof, $mvc,
                      '$paymentId', 'Pending'), ";
    
    $insertcount = $insertcount + 1;
    
  } else {
    
    $status = "-Pending-Add-";
    
    if ($registrants[$i]->usf4) {
      $updatequery .= "update `ohn13_registrants` set `USF4` = true where `Gamertag` = '$gamertag'; ";
      $status .= "USF4";
    }
     if ($registrants[$i]->ttt2) {
      $updatequery .= "update `ohn13_registrants` set `TTT2` = true where `Gamertag` = '$gamertag'; ";
      $status .= "TTT2";
    }
     if ($registrants[$i]->mkx) {
      $updatequery .= "update `ohn13_registrants` set `MKX` = true where `Gamertag` = '$gamertag'; ";
      $status .= "MKX";
    }
     if ($registrants[$i]->kof) {
      $updatequery .= "update `ohn13_registrants` set `KOF` = true where `Gamertag` = '$gamertag'; ";
      $status .= "KOF";
    }
     if ($registrants[$i]->mvc) {
      $updatequery .= "update `ohn13_registrants` set `MARVEL` = true where `Gamertag` = '$gamertag'; ";
      $status .= "MARVEL";
    }
    
    $updatequery .= "update `ohn13_registrants` set `Payment_status` = CONCAT(`Payment_status`, '$status'), `Payment_ID` = '$paymentId' where `Gamertag` = '$gamertag'; ";
  }
 
}

$query = "";

if ($insertcount > 0) {
  $insertquery = substr($insertquery, 0, -2);
  $query = $insertquery.";";
}

$query .= $updatequery;

error_log("Register:".$query);

$result = $query;

if ($mysqli->multi_query($query)) {
  $result .= $mysqli->affected_rows;
} else {
  $result .= $mysqli->error;
  
  error_log($result, 0);
}

$mysqli->close();

error_log("Register:".$result);
echo $result;

?>