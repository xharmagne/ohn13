<?php session_start();
require_once('./rconfig.php');

$result = "";

$postData[] = "USER=".PP_USERNAME;
$postData[] = "PWD=".PP_PASSWORD;            
$postData[] = "SIGNATURE=".PP_SIGNATURE;
$postData[] = "METHOD=GetExpressCheckoutDetails";
$postData[] = "VERSION=123";
$postData[] = "TOKEN=".$_SESSION["access_token"];
           
$postData_str = implode('&',$postData);
error_log("GetExpressCheckoutDetails:".$postData_str);

$url = PP_ENDPOINT;
$ch = curl_init($url);                                                                      
curl_setopt($ch, CURLOPT_POST, true);      
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData_str); 

$getResult = curl_exec($ch);

if (curl_errno($ch) || empty($getResult)) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}
else {

  error_log("GetExpressCheckoutDetails:".$getResult);
  parse_str($getResult, $result_array);
  $payer_id = $result_array['PAYERID'];
  $amt = $result_array['PAYMENTREQUEST_0_AMT'];
  error_log("GetExpressCheckoutDetails:".$payer_id);
  
  $execPostData[] = "USER=".PP_USERNAME;
  $execPostData[] = "PWD=".PP_PASSWORD;            
  $execPostData[] = "SIGNATURE=".PP_SIGNATURE;
  $execPostData[] = "METHOD=DoExpressCheckoutPayment";
  $execPostData[] = "VERSION=123";
  $execPostData[] = "PAYERID=".$payer_id;
  $execPostData[] = "PAYMENTREQUEST_0_PAYMENTACTION=SALE";
  $execPostData[] = "PAYMENTREQUEST_0_AMT=".$amt;
  $execPostData[] = "PAYMENTREQUEST_0_ITEMAMT=".$amt;
  $execPostData[] = "PAYMENTREQUEST_0_CURRENCYCODE=AUD";
  $execPostData[] = "TOKEN=".$_SESSION["access_token"];
           
  $execPostData_str = implode('&',$execPostData);
  error_log("DoExpressCheckoutPayment:".$execPostData_str);

  curl_setopt($ch, CURLOPT_URL, PP_ENDPOINT);
  curl_setopt($ch, CURLOPT_POST, true); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, $execPostData_str); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  
  $execResult = curl_exec($ch);
             
  error_log("DoExpressCheckoutPayment:".$execResult);
             
  if (curl_errno($ch) || empty($execResult)) {
    header('HTTP/1.1 500 Internal Server Error');
    exit();
  }
  else {
    parse_str($execResult, $exresult_array);
    $execStatus = $exresult_array['ACK'];
    
    if ($execStatus != "Success") {
      header('HTTP/1.1 500 Internal Server Error');
      exit();
    }
    
    $result = "ok";
  }
}

curl_close($ch);

echo $result;

?>