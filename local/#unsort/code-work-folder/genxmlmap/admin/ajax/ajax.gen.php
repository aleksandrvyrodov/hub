<?php

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

switch (AJAX_METHOD) {
    case 'reconnect':
        $ln = StorageWorks::wakeup();

    case 'gen':
        $time = time();

        if (!isset($ln)) {
            StorageWorks::reset();
            $ln = new Sindy($settings['siteurl']);
            // $ln = new Sindy('https://pizzanizza.ru/');
        }

        try {

            $clear_links = &$ln->getClearLinks();
            while ($link = array_shift($clear_links)) {
                Sindy::out('--- ' . count($clear_links) . ' - ' . $ln->count_status_1 . ' -------------' . PHP_EOL);
                $dirt_links = $ln->getDoc($link);
                if ($dirt_links !== false) {
                    $ln->getLinks($dirt_links);
                }

                if (time() - $time > 30) {
                    StorageWorks::sleep($ln);
                    Sindy::out(PHP_EOL . '=== <span style="color:Violet">RECCONECTED</span> ================' . PHP_EOL, 'reconnect');
                    exit();
                }
                $ln->$i++;
            }

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
                if (empty($obj['link'])) Sindy::out('<span style="color:red;">[empty]</span>' . PHP_EOL);
                append_in_urlset($obj, $dom, $elem_urlset);
            }

            $dom->appendChild($elem_urlset);

            $bytes = $dom->save(BASEDIR . 'sitemap-x.xml');

            if ($bytes) {
                Sindy::out(PHP_EOL . '--- END GEN --------- <span style="font-weight; font-size:1.2em; color:LimeGreen;">' . $bytes . ' bytes</span> ----------------------' . PHP_EOL);
                dbquery("UPDATE `" . GSMX_DB_SETTINGS . "` SET `value`='" . date('d.m.Y H:i') . "' WHERE `property` = 'lastmod'");
            } else  throw new Exception('zero bytes');
        } catch (\Throwable $th) {
            $mes = '--- END -------------------------------' . PHP_EOL;
            $mes .= '<span style="color:red;">[FAIL] - ' . $th->getMessage() . '</span><br>';
            Sindy::out($mes, 'fail');
            // Sindy::bye($ln);
        } finally {
            StorageWorks::reset();
        }

        break;
}
