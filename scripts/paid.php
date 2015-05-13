<?php
require_once('./rconfig.php');
require_once('./sendmail.php');

$data = json_decode(file_get_contents("php://input"));
$paymentId = $data->paymentId;
$payerId = $data->payerId;
$status = $data->status;

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$query = "update `ohn13_registrants` set `Payment_status` = '$status', `Payer_ID` = '$payerId' where `Payment_ID` = '$paymentId'; ";

$result = $query;

if ($mysqli->multi_query($query)) {
  $result .= $mysqli->affected_rows;
  
  $updatedRegistrantsQuery = "select * from `ohn13_registrants` 
                              where `Payment_ID` = '$paymentId'
                              and `Payment_status` = 'Paid'; ";
  
  $result.="run mail query\n";
  
  $result.=$updatedRegistrantsQuery;
  
  if ($updatedRegistrantsResult = $mysqli->query($updatedRegistrantsQuery)) {
    while ($row = $updatedRegistrantsResult->fetch_assoc()) {
      $result.="result";
    
        $name = $row["First_name"]." ".$row["Last_name"];
        $gamertag = $row["Gamertag"];
        $pass = $row["Pass_type"];
        $email = $row["Email"];
        $games = "";
        
        $result.=$gamertag;
        
        if ($row["USF4"]) {
          $games .= "USF4 ";
        }
         if ($row["TTT2"]) {
          $games .= "TTT2 ";
        }
         if ($row["MKX"]) {
          $games .= "MKX ";
        }
         if ($row["KOF"]) {
          $games .= "KOF ";
        }
         if ($row["MARVEL"]) {
          $games .= "MARVEL ";
        }
        
        if ($games == "") {
          $games = "N/A";
        }
        
        $body = sprintf("<p>Thanks for registering for OHN13! Here are the details of your registration:</p>
                         <table>
	                        <tr><td>Gamertag</td><td>%s</td></tr>
	                        <tr><td>Pass type</td><td>%s</td></tr>
	                        <tr><td>Games</td><td>%s</td></tr>
                        </table>", $gamertag, $pass, $games);
                        
        $plainBody = sprintf("Thanks for registering for OHN13! Here are the details of your registration:\n\n
                              Gamertag: %s\n
	                            Pass type: %s\n
	                            Games: %s\n", $gamertag, $pass, $games);
        
        send_mail($name, $email, "OHN 13 Registration Confirmed", $body, $plainBody);
    }
  
  }
  
  
} else {
  $result .= $mysqli->error;
  
  error_log($result, 0);
}

$mysqli->close();

echo $result;

?>