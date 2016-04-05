<?php
  /**
   * Sets error header and json error message response.
   *
   * @param  String $messsage error message of response
   * @return void
   */
  function errorResponse ($messsage) {
    header('HTTP/1.1 500 Internal Server Error');
    die(json_encode(array('message' => $messsage)));
  }
  /**
   * Pulls posted values for all fields in $fields_req array.
   * If a required field does not have a value, an error response is given.
   */
  function constructMessageBody () {
  $fields_req =  array("firstname" => true, "lastname" => true, "email" ==> true, "conemail" ==> true, "phone" => true, "occupants" => true, "date" => true, "bedrooms" => true, "bathrooms" => true, "message" => false);
  $message_body = "";
    foreach ($fields_req as $name => $required) {
      $postedValue = $_POST[$name];
      if ($required && empty($postedValue)) {
        errorResponse("$name is empty.");
      } else {
        $message_body .= ucfirst($name) . ":  " . $postedValue . "\n";
      }
    }
    return $message_body;
  }
  header('Content-type: application/json');
  //attempt to send email
  $messageBody = constructMessageBody();
  require 'library/vender/php_mailer/PHPMailerAutoload.php';
  $mail = new PHPMailer;
  $mail->CharSet = 'UTF-8';
  $mail->isSMTP();
  $mail->Host = 'relay-hosting.secureserver.net';
  $mail->SMTPAuth = true;
  $mail->Username = 'username';
  $mail->Password = 'password';
  
  $mail->SMTPSecure = 'tls';
  $mail->Port = 25;
  
  $mail->setFrom($_POST['email'], $_POST['firstname']);
  $mail->addAddress('crossingsofwexford@zoominternet.net');
  $mail->Subject = $_POST['reason'];
  $mail->Body  = $messageBody;

  //try to send the message
  if($mail->send()) {
    echo json_encode(array('message' => 'Your message was successfully submitted.'));
  } else {
    errorResponse('An expected error occured while attempting to send the email: ' . $mail->ErrorInfo);
  }
?>