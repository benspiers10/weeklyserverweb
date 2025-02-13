<?php 

$reciever = "benspiers4@gmail.com";
$subject = "Test Email via php using localhost";
$body = "Hi there, This is a test email sent from localhost";
$headers = "From: benjidev01@gmail.com";

if(mail($reciever, $subject, $body, $headers)){
    echo "Email sent successfully to $reciever";
}else{
    echo "Email sending failed...";
}
?>