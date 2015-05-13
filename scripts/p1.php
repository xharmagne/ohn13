<?php session_start();
require_once('./rconfig.php');

$data = json_decode(file_get_contents("php://input"));
$paymentData = json_encode($data);
$result = "";

$ch = curl_init('https://api.sandbox.paypal.com/v1/oauth2/token');                                                                      
curl_setopt($ch, CURLOPT_POST, true);      
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PP_CLIENTID.":".PP_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");                                                                  

$authResult = curl_exec($ch);

if (curl_errno($ch) || empty($authResult)) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}
else {

  $authJson = json_decode($authResult);
  $accessToken = $authJson->access_token;
  
  curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
  curl_setopt($ch, CURLOPT_POST, true); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, $paymentData); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                      "Content-Type: application/json",
                      "Authorization: Bearer " . $accessToken, 
                      "Content-length: " . strlen($paymentData))
                      );
                  
  $paymentResult = curl_exec($ch);
             
  if (curl_errno($ch) || empty($paymentResult)) {
    header('HTTP/1.1 500 Internal Server Error');
    exit();
  }
  else {
      $paymentResultJson = json_decode($paymentResult);
      $_SESSION["payment_id"] = $paymentResultJson->id;
      $_SESSION["access_token"] = $accessToken;
      
      $result = $paymentResult;
      
  }               
}

curl_close($ch);

echo $result;

?>