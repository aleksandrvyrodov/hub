<?php
require_once '../../../maincore.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', '86400');

ini_set('output_buffering', 0);
ini_set('zlib.output_compression', 0);
ini_set('implicit_flush', 1);

set_time_limit(0);

header('X-Accel-Buffering: no');

ob_implicit_flush(true);
ob_end_clean();

require_once INFUSIONS . 'genxmlmap/infusion_db.php';
require_once GSMX_MODELS . 'functions.php';
require_once GSMX_MODELS . 'StorageWorks.php';
require_once GSMX_MODELS . 'StorageLinks.php';
require_once GSMX_MODELS . 'Sindy.php';

global $settings;


$ln = new Sindy($settings['siteurl']);

Sindy::out(PHP_EOL . '=== <span style="color:Cyan">end parse, total links - ' . $ln->getCountLink() . '</span> ================' . PHP_EOL);


$dom = new \DOMDocument('1.0', 'UTF-8');

$elem_urlset = $dom->createElement('urlset');

$atrib_xmlns = $dom->createAttribute('xmlns');
$atrib_xmlns->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
$elem_urlset->appendChild($atrib_xmlns);


$obj = $ln->getClearLinksLvl();
$obj['level'] = 0;
append_in_urlset($obj, $dom, $elem_urlset);

while ($obj = $ln->getClearLinksLvl()) {
    echo "<pre>";
    var_dump($obj);
    echo "</pre>";
    append_in_urlset($obj, $dom, $elem_urlset);
}

$dom->appendChild($elem_urlset);

$bytes = $dom->save('sitemap-x.xml');
Sindy::out('<span style="color:blue;">' . $bytes . '</span>' . PHP_EOL);
