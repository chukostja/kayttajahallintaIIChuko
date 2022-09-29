<?php

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$to = $admin_mail;
define("PALVELU","mailtrap");

function posti($emailTo,$msg,$subject){
if (PALVELU == 'sendgrid'){
/* SendGrid */      
$host = "smtp.sendgrid.net";
$port = 587;
$username = $username_sendgrid;
$password = $password_sendgrid;
}

elseif (PALVELU == 'mailtrap'){
/* Mailtrap */
$host ='smtp.mailtrap.io';
$port = 2525;
$username = $username_mailtrap;
$password = $password_mailtrap;
}

$emailFrom = "wohjelmointi@gmail.com";
$emailFromName = "Ohjelmointikurssi";
$emailToName = "";
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Port = $port;
$mail->Username = $username;
$mail->Password = $password;

$mail->CharSet = 'UTF-8';
$mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
$mail->Host = $host; 
$mail->Port = $port;
$mail->SMTPSecure = 'tls'; 
$mail->setFrom($emailFrom, $emailFromName);
$mail->addAddress($emailTo, $emailToName);
$mail->Subject = $subject;
$mail->msgHTML($msg); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
$mail->AltBody = 'HTML messaging not supported';
// $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
if(!$tulos = $mail->send()){
    //$tulos = false;
    debuggeri("Mailer Error: " . $mail->ErrorInfo);
} else {
    //$tulos = true;
    debuggeri("Viesti lähetetty: $emailTo!");
}
return $tulos;
}
