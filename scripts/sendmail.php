<?php
require_once('./rconfig.php');
require './PHPMailerAutoload.php';

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');
  
function send_mail($toName, $toAddress, $subject, $body, $plainBody) {

  //Create a new PHPMailer instance
  $mail = new PHPMailer;

  //Tell PHPMailer to use SMTP
  $mail->isSMTP();

  //Enable SMTP debugging
  // 0 = off (for production use)
  // 1 = client messages
  // 2 = client and server messages
  $mail->SMTPDebug = 2;

  //Ask for HTML-friendly debug output
  $mail->Debugoutput = 'html';

  //Set the SMTP config
  $mail->Host = SMTP_HOST;
  $mail->Port = 465;
  $mail->SMTPSecure = 'ssl';
  $mail->SMTPAuth = true;
  $mail->Username = SMTP_USER;
  $mail->Password = SMTP_PASSWORD;

  //Set who the message is to be sent from
  $mail->setFrom('ohn@ozhadou.net', 'OHN 13 Registration');

  //Set who the message is to be sent to
  $mail->addAddress($toAddress, $toName);

  //Send copy to Ozhadou
  $mail->addBCC('ohn@ozhadou.net', 'Ozhadou team');

  $mail->isHTML(true); 

  //Mail content
  $mail->Subject = $subject." [TEST]";
  $mail->Body = $body;
  $mail->AltBody = $plainBody;

  //send the message, check for errors
  $success = $mail->send();
  if (!$success) {
      echo "Mailer Error: " . $mail->ErrorInfo;
  } else {
      echo "Message sent!";
  }
  
  return $success;
  
}

?>