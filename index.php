<?php
session_start();
//echo "your mobilenumber is".$_POST["mob"];
//echo "Your username is".$_SESSION["vuser"];
$mob=$_POST["mob"];
$opid=$_POST["opid"];
$amount=$_POST["amount"];





$cuser=$_SESSION["vuser"];
//echo "current user is".$cuser;

$servername="localhost";
$username="";
$password="";
$dbname="";
$link=mysql_connect($servername,$username,$password);
$db=mysql_selectdb($dbname);
if(!$link)
{
	echo "unable to connect to database server";
}





$sql="SELECT *FROM data1 where username='$cuser'";

$result=mysql_query($sql);
while($row=mysql_fetch_array($result))
{
$currentbal=$row["bal"];

}

if($currentbal>=$amount)
{

$tid=sha1(md5(time()));

$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"http://api.rechapi.com/recharge.php?format=text&token=YOU_CODE&mobile=$mob&amount=$amount&opid=$opid&urid=$tid");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$output=curl_exec($ch);



$orderid = substr($output, 0, 8);

















$url="http://api.rechapi.com/api_status.php?format=xml&token=YOUR_CODE&orderId=$orderid";
$request_timeout = 60; // 60 seconds timeout
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, $request_timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $request_timeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
$curl_error = curl_errno($ch);
$getserver= curl_getinfo($ch);
curl_close($ch);      
  if($getserver["http_code"]==200)
{

$xml = simplexml_load_string($output);
foreach($xml as $nxml){
$orderid=$nxml[id];
$status=$nxml->status;
$optid=$nxml->transactionId;
$error_code=$nxml->error_code;


 if($status=="PENDING")
  
{

$sql="INSERT INTO lrecharge (username,orderid,transactionid,mob,operator,amount,status) VALUES('$cuser','$orderid','$tid','$mob','','$amount','$finalstatus')";

$result=mysql_query($sql);


$newbal=$currentbal-$amount;



$sql="UPDATE data1 set bal=$newbal where username='$cuser'";

$result=mysql_query($sql);

$finalstatus="PENDING";


$msg="Sorry, Your Recharge status is PENDING but its done in 2Hrs. If not Success please contact us contact@kitopay.com";

$finalmsg=urlencode($msg);

$ch=curl_init("http://api.rechapi.com/sms/send_sms.php?format=text&token=&mobile=$mob&type=txt&smsType=promo&senderid=RPROMO&msg=$finalmsg");

}


elseif($status=="SUCCESS"){

$sql="INSERT INTO lrecharge (username,orderid,transactionid,mob,operator,amount,status) VALUES('$cuser','$orderid','$tid','$mob','$op','$amount','$status')";

$result=mysql_query($sql);




$newbal=$currentbal-$amount;



$sql="UPDATE data1 set bal=$newbal where username='$cuser'";

$result=mysql_query($sql);



$finalstatus="SUCCESS";


$msg="Thanks for Recharge Mobile With Us.";

$finalmsg=urlencode($msg);

$ch=curl_init("http://api.rechapi.com/sms/send_sms.php?format=text&token=YOUR_CODE&mobile=$mob&type=txt&smsType=promo&senderid=RPROMO&msg=$finalmsg");
   
    
}
else {
$finalstatus="FAILED";
$sql="INSERT INTO lrecharge (username,orderid,transactionid,mob,operator,amount,status) VALUES('$cuser','$orderid','$tid','$mob','$op','$amount','$finalstatus')";

$result=mysql_query($sql);




$sql="UPDATE data1 set bal=$newbal where username='$cuser'";

$result=mysql_query($sql);


$msg="Thats Not! Your Recharge is Failed Due to some reasons Contact us for this problem contact@kitopay.com";

$finalmsg=urlencode($msg);

$ch=curl_init("http://api.rechapi.com/sms/send_sms.php?format=text&token=YOUR_CODE&mobile=$mob&type=txt&smsType=promo&senderid=RPROMO&msg=$finalmsg");

}


}

  

}




//echo $finalstatus;








}
else
{



echo '
<!-- IPad Login - START -->
<div class="container">
    <div class="row colored">
        <div class="contcustom">
            <span class="fa fa-calendar-o bigicon"></span>
            <h2>AMOUNT GREATER</h2>
            <div>
              <hr/> Amount for Recharge is greater than Balance in Wallet.
            </div>
        </div>
    </div>
</div>

<style>
    .colored {
        background-color: #F0EEEE;
    }

    .row {
        padding: 20px 0px;
    }

    .bigicon {
        font-size: 97px;
        color: #f96145;
    }

    .contcustom {
        text-align: center;
        width: 300px;
        border-radius: 0.5rem;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: 10px auto;
        background-color: white;
        padding: 20px;
    }

    input {
        width: 100%;
        margin-bottom: 17px;
        padding: 15px;
        background-color: #ECF4F4;
        border-radius: 2px;
        border: none;
    }

    h2 {
        margin-bottom: 20px;
        font-weight: bold;
        color: #ABABAB;
    }

    .btn {
        border-radius: 2px;
        padding: 10px;
    }

    .med {
        font-size: 27px;
        color: white;
    }

    .wide {
        background-color: #8EB7E4;
        width: 100%;
        -webkit-border-top-right-radius: 0;
        -webkit-border-bottom-right-radius: 0;
        -moz-border-radius-topright: 0;
        -moz-border-radius-bottomright: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>

';
die();
}


?>

<div class="container">
          
  <table class="table">
    <thead>
      <tr>
        <th>Mobile Number</th>
          <th>Oder ID</th>
        <th>Amount</th>
        <th>Status</th>
          <th>More</th>
      
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo $mob; ?></td>
         <td><?php echo $orderid; ?></td>
        <td><?php echo $amount; ?></td>
        <td><?php echo $finalstatus; ?></td>
     <td><a href="rhistory">Click Here</a></td>
      </tr>
  
    
    </tbody>
  </table>

</div>


<?php

curl_exec($ch);
?>
