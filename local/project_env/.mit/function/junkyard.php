<?php

namespace MIT\Function\Junkyard;

function getDataByLink(string $url, array $options = []): string|false
{
  $curl = curl_init();
  $options += [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
  ];

  curl_setopt_array($curl, $options);
  $data = curl_exec($curl);

  try {
    if (curl_errno($curl))
      throw new \Exception('curl_error');

    if (empty($data))
      throw new \Exception('curl_empty');
  } catch (\Throwable $th) {
    $data = false;
  } finally {
    curl_close($curl);
  }

  return $data;
}

function GUIDv4(): string {
  if (function_exists('com_create_guid') === true)
      return trim(com_create_guid(), '{}');

  $data = openssl_random_pseudo_bytes(16);

  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}