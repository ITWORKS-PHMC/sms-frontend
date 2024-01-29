<?php
session_start();

if (isset($_POST['toDecrypt'])) {
  // Storingthe cipher method
  $ciphering = "AES-128-CTR";

  // Using OpenSSl Encryption method
  $iv_length = openssl_cipher_iv_length($ciphering);
  $options = 0;

  // Non-NULL Initialization Vector for decryption
  $decryption_iv = '1234567891011121';

  // Storing the decryption key
  $decryption_key = "chasey";

  // Using openssl_decrypt() function to decrypt the data
  $decryption = openssl_decrypt($_POST['toDecrypt'], $ciphering, $decryption_key, $options, $decryption_iv);

  // Displaying the decrypted string
  echo $decryption;
} else {
  // TODO: Change URL to Production URL if available
  // header('Location: http://localhost/sms-frontend/sms.php');
  header('Location: http://phmc-sms/sms-frontend/sms.php'); // server phmc-sms01
  die();
}