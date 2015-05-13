<?php session_start();

$data = json_decode(file_get_contents("php://input"));
$payerData = json_encode($data);
$result = "";

$url = "https://api.sandbox.paypal.com/v1/payments/payment/".$_SESSION["payment_id"]."/execute";
$ch = curl_init($url);                                                                      
curl_setopt($ch, CURLOPT_POST, true);      
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payerData); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $_SESSION["access_token"], 
                    "Content-length: " . strlen($payerData))
                    );
                    
$execResult = curl_exec($ch);

if (curl_errno($ch) || empty($execResult)) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}
else {
  $result = "ok";            
}

curl_close($ch);

echo $result;

?>