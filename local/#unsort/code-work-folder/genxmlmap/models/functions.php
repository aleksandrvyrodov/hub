<?php
function append_in_urlset($obj, $dom, $elem_urlset)
{
    $link = $obj['link'];

    $elem_url = $dom->createElement('url');

    $elem_url->appendChild($dom->createElement('loc', $link));
    $elem_url->appendChild($dom->createElement('lastmod', date('Y-m-d')));
    $elem_url->appendChild($dom->createElement('priority', priority($obj['level'])));

    $elem_urlset->appendChild($elem_url);
};

function priority($level)
{
    $level = ((int)$level) > 9 ? 10 : $level;
    $level = 10 - $level;

    $level = $level == 10 ? '1.0' : '0.' . $level;
    return $level;
};
