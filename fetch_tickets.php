<?php
  $email="vrundak@bsf.io";
  $password="vrunda1071991";
  
  $url = 'https://bsfv.freshdesk.com/helpdesk/tickets.json';
  $ch = curl_init ($url);
  
  curl_setopt($ch, CURLOPT_USERPWD, "$email:$password");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $server_output = curl_exec ($ch);
  
  $tickets = json_decode($server_output);
  echo '<xmp>'; print_r($tickets); echo '</xmp>';
  foreach ($tickets as $key => $ticket) {
      //echo $ticket->subject . "\n";
  }
  curl_close ($ch);
?>

