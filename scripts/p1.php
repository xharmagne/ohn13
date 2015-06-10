<?php session_start();
require_once('./rconfig.php');

$data = json_decode(file_get_contents("php://input"));
$result = "";

$postData[] = "USER=".PP_USERNAME;
$postData[] = "PWD=".PP_PASSWORD;            
$postData[] = "SIGNATURE=".PP_SIGNATURE;
$postData[] = "METHOD=SetExpressCheckout";
$postData[] = "VERSION=123";
$postData[] = "PAYMENTREQUEST_0_PAYMENTACTION=SALE";
$postData[] = "PAYMENTREQUEST_0_AMT=".$data->transactions[0]->amount->total;
$postData[] = "PAYMENTREQUEST_0_ITEMAMT=".$data->transactions[0]->amount->total;
$postData[] = "PAYMENTREQUEST_0_CURRENCYCODE=AUD";
$postData[] = "RETURNURL=http://dev.ozhadou.net/confirmed.html";
$postData[] = "CANCELURL=http://dev.ozhadou.net/cancelled.html";
          
$items = $data->transactions[0]->item_list->items;
$count = count($items);

for ($i=0; $i < $count; $i++) {
    $postData[] = "L_PAYMENTREQUEST_0_NAME".$i."=".urlencode($items[$i]->name);
    $postData[] = "L_PAYMENTREQUEST_0_AMT".$i."=".$items[$i]->price;
    $postData[] = "L_PAYMENTREQUEST_0_DESC".$i."=".urlencode($items[$i]->description);
}

$postData_str = implode('&',$postData);
error_log("SetExpressCheckout:".$postData_str);

$ch = curl_init(PP_ENDPOINT);                                                                      
curl_setopt($ch, CURLOPT_POST, true);      
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PP_CLIENTID.":".PP_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData_str);                                                                  

$setExpressCheckoutResult = curl_exec($ch);

if (curl_errno($ch) || empty($setExpressCheckoutResult)) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}
else {
    error_log("SetExpressCheckout:".$setExpressCheckoutResult);
    parse_str($setExpressCheckoutResult, $result_array);
    $result = urldecode($result_array['TOKEN']);
    
    error_log("SetExpressCheckout:".$result);
    $_SESSION["access_token"] = $result;
}

curl_close($ch);

echo $result;

?>