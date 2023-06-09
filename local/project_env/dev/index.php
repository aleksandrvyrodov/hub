<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle(".dev");
?>
<!-- ---- -->
<?

echo "<pre>";
var_dump("Hello, my Dear!
  \n\rThis is develepment section.
  \n\rPlease stand by...");
echo "</pre>";

?>
<!-- ---- -->
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>