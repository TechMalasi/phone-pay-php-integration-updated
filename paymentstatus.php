<?php
require_once "./utils/config.php";
require_once "./utils/common.php";
require_once "./utils/SendMail.php";

if(isset($_POST['merchantId']) && isset($_POST['transactionId']) && isset($_POST['amount']) )
    {

        $merchantId=$_POST['merchantId'];
        $transactionId=$_POST['transactionId'];
        $amount=$_POST['amount'];


        session_start();

     $name = $_SESSION['name'] ;
     $email = $_SESSION['email'] ;
     $mobile = $_SESSION['mobile'] ;




if (API_STATUS == "LIVE") {
    $url = LIVESTATUSCHECKURL . $merchantId . "/" . $transactionId;
    $saltkey = SALTKEYLIVE;
    $saltindex = SALTINDEX;
} else {
    $url = STATUSCHECKURL . $merchantId . "/" . $transactionId;
    $saltkey = SALTKEYUAT;
    $saltindex = SALTINDEX;
}



$st = "/pg/v1/status/" . $merchantId . "/" . $transactionId . $saltkey;

$dataSha256 = hash("sha256", $st);

$checksum = $dataSha256 . "###" . $saltindex;


//GET API CALLING
$headers = array(
    "Content-Type: application/json",
    "accept: application/json",
    "X-VERIFY: " . $checksum,
    "X-MERCHANT-ID:" . $merchantId
);



$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$resp = curl_exec($curl);

curl_close($curl);

$responsePayment = json_decode($resp, true);

echo "<pre>";
print_r($responsePayment);
echo "</pre>";


$tran_id = $responsePayment['data']['transactionId'];
$amount = $responsePayment['data']['amount'];



    if ($responsePayment['success'] && $responsePayment['code'] == "PAYMENT_SUCCESS")
    {
        //Send Email and redirect to success page

        $now = new DateTime();
        $timestring = $now->format('d-M-Y h:i:s');

        $msg = 'Dear ' . $name . ",<br/>";
        $msg .= '<br/>We have received your payment and Below is your payment Details<br/> ';
        $msg .= '<table>';
        $msg .= '<tr><td>Name:</td><td>' . $name . '</td></tr>';
        $msg .= '<tr><td>Email:</td><td>' . $email . '</td></tr>';
        $msg .= '<tr><td>Mobile:</td><td>' . $mobile . '</td></tr>';
        $msg .= '<tr><td>Amount:</td><td>Rs.' . $amount/100 . '</td></tr>';
        $msg .= '<tr><td>Transaction id:</td><td>' . $tran_id . '</td></tr>';
        $msg .= '<tr><td>Date:</td><td>' . $timestring . '</td></tr>';
        $msg .= '</table><br/>';

        $msg .= '<p>From,</p>';
        $msg .= '<p>Techmalasi Team</p>';

        $ob = new Mail();
       $r =  $ob->sendMail($email, $msg);
       echo "response>>".$r;
        sleep(3);

        if($r)
        header('Location:' . BASE_URL . "success.php?tid=" . $tran_id . "&amount=" . $amount);
        else
        header('Location:' . BASE_URL . "success.php?tid=" . $tran_id . "&amount=" . $amount);

}
else {

    header('Location:' . BASE_URL . "failure.php?tid=" . $tran_id . "&amount=" . $amount);

    }







    }


?>