<?php
require_once 'swift/lib/swift_required.php';

$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername('kapitainyannick@gmail.com')
  ->setPassword('avenir de 2013 pilote');

$mailer = Swift_Mailer::newInstance($transport);

$message = Swift_Message::newInstance('Test Subject')
  ->setFrom(array('kapitainyannick@gmail.com' => 'kapitainyannick'))
  ->setTo(array('monbookemail@gmail.com'))
  ->setBody('This is a test mail.');

$result = $mailer->send($message);
?>