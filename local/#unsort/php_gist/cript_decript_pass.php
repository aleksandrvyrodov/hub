<?php

function encrypt_password($password, $key)
{
  $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
  $encrypted = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
  return base64_encode($encrypted . '::' . $iv);
}

function decrypt_password($encrypted_password, $key)
{
  list($encrypted_password, $iv) = explode('::', base64_decode($encrypted_password), 2);
  return openssl_decrypt($encrypted_password, 'aes-256-cbc', $key, 0, $iv);
}
