<?php 
session_start();

if (isset($_POST['toEncrypt'])) {
  // Storing a string into the variable which
  // needs to be Encrypted

  // Displaying the original string
  // echo "Original String: " . $simple_string . "<br>";

  // Storingthe cipher method
  // echo $_POST['toEncrypt'];
  // die();
  $ciphering = "AES-128-CTR";

  // Using OpenSSl Encryption method
  $iv_length = openssl_cipher_iv_length($ciphering);
  $options = 0;

  // Non-NULL Initialization Vector for encryption
  $encryption_iv = '1234567891011121';

  // Storing the encryption key
  $encryption_key = "chasey";

  // Using openssl_encrypt() function to encrypt the data
  $encryption = openssl_encrypt($_POST['toEncrypt'], $ciphering, $encryption_key, $options, $encryption_iv);
  // return $encryption;

  echo $encryption;

} else {
  // TODO: Change URL to Production URL if available
  // header('Location: http://localhost/sms-frontend/sms.php');
  header('Location: http://phmc-sms/sms-frontend/sms.php'); // server phmc-sms01
  die();
}