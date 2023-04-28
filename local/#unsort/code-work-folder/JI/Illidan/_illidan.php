<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', '86400');
set_time_limit(0);

// exit();
if (isset(getallheaders()['X-Malfurion'])) {
    ob_implicit_flush(true);
    ob_end_flush();

    require 'models/_Malfurion.class.php';
    if (getallheaders()['X-Malfurion'] == 'pre') echo '<pre>';
    if (!isset($_REQUEST['send'])) Malfurion::SendLock();

    $illidan = 'https://new.mo-strelna.ru/documents/import/';
    include 'devsettings.php';


       $Malfurion = new Malfurion($illidan);

    try {
        $Malfurion->
        detectMethod();
        $Malfurion->printMethod();
        echo '--- START ---', PHP_EOL;
        $Malfurion->runWork();
        echo '--- END ---';
    } catch (Exception $e) {
        $Malfurion::vdE('--- ERR ---');
        echo $e->getMessage(), PHP_EOL;
        echo $Malfurion->getRaportError();
        $Malfurion::vdE('--- END ---');
    }
    if (getallheaders()['X-Malfurion'] == 'pre') echo '<pre>';
    exit();
}

require_once "maincore.php";
require_once THEMES . "templates/header.php";
require_once VIEWS . 'classes/Page.class.php';
require_once INCLUDES . 'breadcrumbs.php';


$breadcrumbs = new Breadcrumbs();
$breadcrumbs->addLink('Главная', '/');
$breadcrumbs->addLink('Illidan', '', true);
set_title('Illidan');

echo $twig->render('_illidan.twig', array(
    'breadcrumbs' => $breadcrumbs->render()
));

require_once THEMES . "templates/footer.php";



/*#
$path = DOC_FILE_UPLOAD . "municipzakaz";
$file = '47d08ayin0j7b2b7.pdf';
$file_ext = trim(strstr($file, '.'), '.');
$file_name = trim(strstr($file, '.', true), '.');

$from = $path . '/' . $file;
$from_root = $_SERVER['DOCUMENT_ROOT'] . '/' . $from;
$to = DOC_FILE_PREVIEW . $file_name . '.jpeg';
$to_root = $_SERVER['DOCUMENT_ROOT'] . '/' . $to;
#*/




/* #
$im = new Imagick();
$im->setBackgroundColor('white');
$im->setResolution(300, 300);
$im->readImage($from_root . '[0]');
$im->setImageFormat('jpeg');
$im->mergeImageLayers($im::LAYERMETHOD_FLATTEN);

if ($im->getImageAlphaChannel()) {
    $im->setImageAlphaChannel($im::VIRTUALPIXELMETHOD_WHITE);
}
$im->setCompression($im::COMPRESSION_JPEG);
$im->setCompressionQuality(60);

$img_width = $im->getImageWidth();
$img_height = $im->getImageHeight();

$size = 380;
if ($img_height >= $img_width) {
    $cof = $img_height / $img_width;
    $img_new_size = array(
        'w' => $size,
        'h' => $size * $cof
    );
} else {
    $cof = $img_width / $img_height;
    $img_new_size = array(
        'w' => $size * $cof,
        'h' => $size
    );
}

$im->resizeImage($img_new_size['w'], $img_new_size['h'], $im::DISPOSE_UNDEFINED, 1);

$im->writeImage($to_root);
$im->clear();
$im->destroy();
# */




/* #
$content_type = mime_content_type($to);
header('Content-Type: ' . $content_type);
// header('Content-Disposition: attachment; filename=' . 'xx');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($to));
readfile($to);
# */
