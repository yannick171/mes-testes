<?php $config = array(
                    'ssl'=>'ssl',
                    'port'=>465,
                    'auth' => 'login',
                    'username' => 'kapitainyannick@gmail.com',
                    'password' => 'avenir de 2013 pilote');
                $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
                    $mail = new Zend_Mail('ISO-8859-1');
                    $mail->setBodyHtml(utf8_decode($body));
//                    $mail->setFrom("contact.sea1@sorties-entre-amis.com");
                    $mail->setFrom("votre-adresse-mail@gmail.com");
                    $mail->setReplyTo("votre-adresse-mail@gmail.com");
                    $mail->addTo("monbookemail@gmail.com");
                    $mail->setSubject(stripslashes(utf8_decode($subject)));
                    $error = false;
                    try {
                         $mail->send($transport);
                    } catch(Exception $e) {
                        echo "Exception: {$e->getMessage()}";
                        $error = true;
                    }
?>