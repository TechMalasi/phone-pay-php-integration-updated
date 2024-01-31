<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require './vendor/autoload.php';

class Mail
{
function sendMail($to='',$msg='')
{
	try {

	$mail = new PHPMailer(true);
	$mail->SMTPDebug = 2;
	$mail->isSMTP();
	$mail->Host	 = 'smtp.hostinger.com';
	$mail->SMTPAuth = true;
	$mail->Username = '';
	$mail->Password = '';
	$mail->SMTPSecure = 'ssl';
	$mail->Port	 = 465;
	$mail->setFrom('', 'Techmalasi');
	$mail->addAddress($to);
	$mail->isHTML(true);
	$mail->Subject = 'Payment Successfully Completed';
	$mail->Body = $msg;
	// $mail->AltBody = 'Body in plain text for non-HTML mail clients';
	return $mail->send();

} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}

}
?>