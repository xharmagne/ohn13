<?php
require_once('./rconfig.php');

$data = json_decode(file_get_contents("php://input"));
$gamertag = $data->gamertag;
$result = null;

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$query = "select * from `ohn13_registrants` 
          where `Gamertag` = '$gamertag' 
          and `Pass_type` = 'Competitor'
          and `Payment_status` = 'Paid'; ";

if ($qryresult = $mysqli->query($query)) {
  while ($row = $qryresult->fetch_assoc()) {
    
      $usf4Registered = 0;
      $ttt2Registered = 0;
      $mkxRegistered = 0;
      $kofRegistered = 0;
      $mvcRegistered = 0;
       
      if ($row["USF4"]) {
        $usf4Registered = 1;
      }
        if ($row["TTT2"]) {
        $ttt2Registered = 1;
      }
        if ($row["MKX"]) {
        $mkxRegistered = 1;
      }
      if ($row["KOF"]) {
        $kofRegistered = 1;
      }
      if ($row["MARVEL"]) {
        $mvcRegistered = 1;
      }
                
      $arr = array('usf4Registered' => $usf4Registered, 'ttt2Registered' => $ttt2Registered, 'mkxRegistered' => $mkxRegistered, 'kofRegistered' => $kofRegistered, 'mvcRegistered' => $mvcRegistered);
      $result = json_encode($arr);

  }
  
}
  


$mysqli->close();

echo $result;

?>