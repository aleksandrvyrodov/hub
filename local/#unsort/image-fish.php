<?php

namespace MIT {
  const URL_SELF = 'https://www.napitkimira.com';
}

namespace {
  $image_URI = MIT\URL_SELF . $_SERVER['REQUEST_URI'];

  stream_context_set_default(['http' => ['method' => 'HEAD']]);
  $headers = get_headers($image_URI, true);

  stream_context_set_default(['http' => ['method' => 'GET']]);
  $code = (int) explode(' ', $headers[count(array_filter($headers, fn ($_) => is_int($_),  ARRAY_FILTER_USE_KEY)) - 1])[1];

  if ($code === 200) {
    // php://temp/maxmemory:4M
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, file_get_contents(MIT\URL_SELF . $_SERVER['REQUEST_URI']));
    rewind($stream);

    header('Content-type: ' . mime_content_type($stream));
    echo stream_get_contents($stream);
  } else {
    header('Content-type: image/webp');
    echo base64_decode('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');
  }

  exit();
}
