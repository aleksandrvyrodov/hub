<?define('ENCODING','windows-1251');?>
<?
define('VER','0.6');
define("SCRIPT_NAME",$_SERVER['SCRIPT_NAME']);
$NC_ENCODING=(ENCODING=='windows-1251')?'UTF-8':'windows-1251';
//if ((isset($_SERVER["HTTP_USER_AGENT"]))&&(strpos($_SERVER["HTTP_USER_AGENT"],'MSIE')))
//die('Этот скрипт не работает в Internet Explorer по причине личной неприязни автора скрипта к данному браузеру. Спасибо за понимание:)');

if (@$_GET['delete']=="Y")
{
header("Content-type:text/html; charset=".ENCODING);
echo "<div style='background-color:#B9D3EE;
   border:1px solid black;
   text-align:center;
   color:red;
   height:30;
   z-index:10000;'> Файл удалён!</div>";
 unlink(__File__);
die();
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('sale');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
header("Content-type:text/html; charset=".ENCODING);
$ToUpperFunction = function_exists('mb_strtoupper') ? 'mb_strtoupper' : 'ToUpper';



$MESS['HAVE_LOCATION']='Присутствует свойство типа LOCATION';
$MESS['NO_HAVE_LOCATION']='Отсутствует свойство типа LOCATION';
$MESS['TAX_LOCATION']='Свойство LOCATION используется для расчёта налогов';
$MESS['NO_TAX_LOCATION']='Свойство LOCATION не используется для расчёта налогов';
$MESS['DELIVERY_LOCATION']='Свойство LOCATION используется для расчёта стоимости доставки';
$MESS['NO_DELIVERY_LOCATION']='Свойство LOCATION не используется для расчёта стоимости доставки';
$MESS['PAY_SYSTEMS']='Платёжные системы присутствуют';
$MESS['NO_PAY_SYSTEMS']='Отсутствуют активные платёжные системы';
$MESS['EMPTY_ENCODUNG']='Не указана кодировка';
$MESS['NO_PAYERS']='Не найдено типов плательщика';
$MESS['WRONG_CURRENCY']='Валюта не соответствует валюте сайта';
$MESS['NO_PROPS']='Отсутствуют свойства заказа';
$MESS['NO_DELIVERY']='Нет активных служб доставки';
$MESS['DELIVERY_COUNT']='Количество активных служб доставки: ';
$MESS['NOT_IN_MOSCOW']='Магазин находится не в Москве';
$MESS['NOT_IN_USA']='Магазин находится не в США';
$MESS['WHERE_IS_YOUR_SHOP_PLACED']='Не указан адрес магазина';
$MESS['PAYERS_COUNT']='Количество типов плательщика:';
$MESS['STRTOUPPER_DSTW']='Функция strtoupper() не работает с кириллицей';
$MESS['NO_LOCATIONS_FOR_DELIVERY']='Не указаны местоположения';
$MESS['NO_LOCATIONS']='Нет ни одного местоположения';
$MESS['NO_CURRENCY']='Валюты не найдены';
$MESS['BAD_ID']='Идентификатор валюты некорректный (должен состоять только из латинских символов)';
$MESS['WRONG_AMOUNT']='Это базовая валюта, курс по умолчанию должен быть равен "1"';
$MESS['NO_BASE_CURR']='Нет базовой валюты (валюты с курсом по умолчанию "1")';
//$MESS['']='';


function ShowMsg($text='---no message---',$class='message',$link='',$blank=true)
	{
		global $MESS;
		if ($MESS[$text]) $text_message=$MESS[$text]; else $text_message=$text;
	    $message="";
		$message='<div class='.$class.'>'.$text_message.' ';
		if ($link<>'') 
		{
		$message.='<a href="javascript:OpenWin(\''.$link.'\')" OnMouseOver="LightOn(this);" OnMouseOut="LightOff();"';
		if ($blank) $message.='target="_blank"';
		$message.='>исправить</a>';
		}
		$message.='</div>';
		echo $message;
		
	}
	
function ShowWindow($ID,$NAME='Untitled',$ShowHideSectionID,$content='',$RefreshStepID='',$needrefresh=true)
	{
			echo '<div id="'.$ID.'">';
			echo '<table align=center class=main_table cellpadding=0 sellspacing=0>';
			echo '<tr><td onclick=ShowHideSection("'.$ShowHideSectionID.'_ps") class="section">'.$NAME.'</td></tr>';
			echo '<tr><td class=main_table_td>';
			echo '<div style="width:100%;text-align:right;">';
			if ($needrefresh)
			echo '<a align=left href="javascript:StartDiag('.$RefreshStepID.',\''.$ID.'\')">обновить</a>';
			echo '</div>';
			echo '<div id='.$ShowHideSectionID.'_ps align=left>';
			echo $content;
			//echo '<pre>';print_r($arPersonType['PAY_SYSTEMS']);echo '</pre>';
			echo '</div>';	
			echo '</td></tr></table>';	
			echo '</div>';
		
	}
	
function ShowWhatIsIt()
			{
				ob_start();
				echo '<div style="margin:10">
				Это скрипт, который может помочь обнаружить и исправить некоторые ошибки настройки модуля интернет-магазина на CMS Bitrix.
				Всё, что нужно "нажимать" находится на панели слева, результаты теста отображаются в этом же окне.
				Блок справа содержит ссылки на документацию, а в некоторых случаях даже выдержки из документации и FAQ. 
				Текст справочной информации подгружается из текстового файла на удалённом сервере, поэтому информация может исправляться и обновляться.
				<div><hr>';
				echo '<h3>Что это за светофоры?</h3>';
				echo '<div>В процессе тестирования будут выявлены ошибки, которые пользователи наиболее часто допускают при настройке магазина.
				Тест будет выдавать сообщения разного уровня критичности:
				</div><p><b>';
				ShowMsg('Хорошо','subaccept');
				ShowMsg('Не очень хорошо','subwarning');
				ShowMsg('Плохо','suberror','javascript:alert(\'Будет открыта страница административной части, на которой можно исправить ошибку!\');',false);
				echo '<div style="padding-left:38">';
				ShowMsg('Очень плохо','error');
				echo '</div></b></p>';
				
				echo 'В зависимости от кричиности ошибки функционал магазина может работать некорректно либо вообще не работать.<hr>';
				echo '<h3>Что ещё полезного?</h3>';
				echo 'В дополнение, сделаны разделы "Доставки" и "Полезности". В разделе "Доставки" можно подсчитать стоимость автоматизированной доставки(пока только для "Почта России"), используя местоположения, которые у вас есть на сайте. В отличие от стандартного комопнента оформления заказа данный скрипт будет полностью возвращать форму с сервера почты, так можно определить причину проблемы неработоспособности доставки и написать в ТП со знанием дела.';
				echo ' Раздел "Полезности" включает несколько полезных функций, которые решают некоторые актуальные мини-задачи для магазина на Bitrix или позволяют автоматически исправить ошибки, возникшие в результате некорректных действий клиента/пользователя/администратора.';
				$content=ob_get_contents();
				ob_end_clean();
				ShowWindow('whatisit','Что это?','O_o',$content,'',false);
			}
function ShowMenuWindow($ID,$NAME,$ShowHideSectionID,$content='')
	{
			echo '<table class=menu cellspacing=0 cellpadding=0>';
									echo '<tr><td>';
						echo '<b class="rtopwin">
  <b class="r1win"></b> <b class="r2win"></b> <b class="r3win"></b> <b class="r4win"></b>
</b>';
			echo '</td></tr>';
			echo '<tr><td class=msection>';
			echo '<div style="background:#B9D3EE;position:relative;left:10;width:180;color:black">'.$NAME.'</div></td></tr>';
			echo '<tr><td class=menu_td>';
				echo '<div  id='.$ShowHideSectionID.'_ps style="background:white;padding:10" align=center>';
			echo $content;
			echo '</div>';
			echo '</td></tr>';
			echo '<tr><td>';
						echo '<b class="rbottomwin">
  <b class="r4win"></b> <b class="r3win"></b> <b class="r2win"></b> <b class="r1win"></b>
</b>';
			echo '</td></tr>';
		echo '</table>';	
		
	}
	function OneMoreTab($title)
	{
			echo '<b class="rtop" width=50>
		  <b class="r1"></b> <b class="r2"></b> <b class="r3"></b> <b class="r4"></b>
		</b>
		<div class=\'hsection myborder\'><span class=header>'.$title.'</div>';
	}
	
	function ShowHelp($ID,$NAME,$ShowHideSectionID,$content='')
	{
			echo '<table class=htable  cellspacing=0 cellpadding=0>';
									echo '<tr><td>';
						echo '<b class="rtop">
  <b class="r1"></b> <b class="r2"></b> <b class="r3"></b> <b class="r4"></b>
</b>';
			echo '</td></tr>';
			echo '<tr><td onclick=ShowHideSection("'.$ShowHideSectionID.'_ps") class=hsection >';
			echo '<div style="background:;position:relative;left:10;width:400;padding:0;">'.$NAME.'</div></td></tr>';

			echo '<tr><td class=htable_td>';
			
			echo '<div  id='.$ShowHideSectionID.'_ps style="display:none;padding:10 0 10 5;background:white;margin:2;">';
			echo $content;
			echo '</div>';
			echo '</td></tr>';
			echo '<tr><td class=hsection>';
						echo '<b class="rbottom">
  <b class="r4"></b> <b class="r3"></b> <b class="r2"></b> <b class="r1"></b>
</b>';
			echo '</td></tr>';
		echo '</table>';	
	}
	
	
function GetReportsList($strPath2Export,$Profile=false)
		{
	$arReports = array();
		
	CheckDirPath($_SERVER["DOCUMENT_ROOT"].$strPath2Export);
	if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].$strPath2Export))
	{
		while (($file = readdir($handle)) !== false)
		{
			$file_prefix=str_replace("_run.php","",$file);
				if (($file_prefix!=$Profile)&&($Profile))
							continue;
			if ($file == "." || $file == "..") continue;
			if (is_file($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$file) && substr($file, strlen($file)-8)=="_run.php")
			{
				$export_name = substr($file, 0, strlen($file)-8);
				
				$rep_title = $export_name;
				$file_handle = fopen($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$file, "rb");
				$file_contents = fread($file_handle, 1500);
				fclose($file_handle);

				$arMatches = array();
				if (preg_match("#<title[\s]*>([^<]*)</title[\s]*>#i", $file_contents, $arMatches))
				{
					$arMatches[1] = Trim($arMatches[1]);
					if (strlen($arMatches[1])>0) $rep_title = $arMatches[1];
				}

				
				$arReports[$export_name] = array(
					"PATH" => $strPath2Export,
					"FILE_RUN" => $strPath2Export.$file,
					"TITLE" => $rep_title,
					"PREFIX"=>$file_prefix
					);
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$strPath2Export.$export_name."_setup.php"))
				{
					$arReports[$export_name]["FILE_SETUP"] = $strPath2Export.$export_name."_setup.php";
				}
			}
			
		}
	}
	closedir($handle);

	return $arReports;
}

function CreateFile($prefix='test',$name='unknown',$type='run',$CopyFromProfile=false)
		{			
			//if ($type!='run')&&($type!='setup') return "Неверный тип";
			
			$ErrorText="";
								
			$file1=$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/load/".$prefix.'_'.$type.'.php';
			$file2=$_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2EXPORTS.$prefix.'_'.$type.'.php';
		
			$content_file1='<?'."\n".'//<title>'.$name.'</title>'."\n".'?>';		
			$content_file2='<?//<title>'.$name.'</title>'."\n".'require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/load/'.$prefix.'_'.$type.'.php");?>';
						
	
			if ($CopyFromProfile&&$CopyFromProfile!='null')
				{
					$content_file1=preg_replace("#<title[\s]*>([^<]*)</title[\s]*>#i",'<title>'.$name.'</title>',file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/load/".$CopyFromProfile.'_'.$type.'.php'));
					$content_file2=preg_replace("#<title[\s]*>([^<]*)</title[\s]*>#i", '<title>'.$name.'</title>',file_get_contents($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2EXPORTS.$CopyFromProfile.'_'.$type.'.php'));
				}

			if($file_handle = fopen($file1, "w+"))
				{
					$file_contents = fwrite($file_handle, $content_file1);
					fclose($file_handle);
				}
			else 
					$ErrorText.='Не удалось записать файл '.$prefix.'_'.$type.'.php'."<br>";
			
			if($file_handle = fopen($file2, "w+"))
				{
					$file_contents = fwrite($file_handle, $content_file2);
					fclose($file_handle);
				} 
			else 
					$ErrorText.='Не удалось записать файл '.$prefix.'_'.$type.'.php'."<br>";
		
			return $ErrorText;
		}
	
		function IsRussian($arLocation,$ToUpperFunction)
	{
		return 
			($ToUpperFunction($arLocation["COUNTRY_NAME_ORIG"]) == "РОССИЯ"
			|| $ToUpperFunction($arLocation["COUNTRY_SHORT_NAME"]) == "РОССИЯ" 
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_LANG"]) == "РОССИЯ"
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_ORIG"]) == "RUSSIA" 
			|| $ToUpperFunction($arLocation["COUNTRY_SHORT_NAME"]) == "RUSSIA" 
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_LANG"]) == "RUSSIA"
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_ORIG"]) == "РОССИЙСКАЯ ФЕДЕРАЦИЯ" 
			|| $ToUpperFunction($arLocation["COUNTRY_SHORT_NAME"]) == "РОССИЙСКАЯ ФЕДЕРАЦИЯ"
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_LANG"]) == "РОССИЙСКАЯ ФЕДЕРАЦИЯ"
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_ORIG"]) == "RUSSIAN FEDERATION" 
			|| $ToUpperFunction($arLocation["COUNTRY_SHORT_NAME"]) == "RUSSIAN FEDERATION"
			|| $ToUpperFunction($arLocation["COUNTRY_NAME_LANG"]) == "RUSSIAN FEDERATION");
	}
		function IsMoscow($arLocation,$ToUpperFunction)
	{
		if (!IsRussian($arLocation,$ToUpperFunction)) return false;
        return ($ToUpperFunction($arLocation["CITY_NAME_ORIG"]) == "МОСКВА" 
			|| $ToUpperFunction($arLocation["CITY_SHORT_NAME"]) == "МОСКВА" 
			|| $ToUpperFunction($arLocation["CITY_NAME_LANG"]) == "МОСКВА" 
			|| $ToUpperFunction($arLocation["CITY_NAME_ORIG"]) == "MOSCOW" 
			|| $ToUpperFunction($arLocation["CITY_SHORT_NAME"]) == "MOSCOW" 
			|| $ToUpperFunction($arLocation["CITY_NAME_LANG"]) == "MOSCOW");

	}
	
	
if (@$_GET['change_encode']=="Y")
{
$encoding='<?define(\'ENCODING\',\''.$NC_ENCODING.'\');?>';
$encoding2='<?define(\'ENCODING\',\''.ENCODING.'\');?>';
$f = fopen($_SERVER['DOCUMENT_ROOT'].SCRIPT_NAME, 'r');
$textfile=fread($f,filesize($_SERVER['DOCUMENT_ROOT'].SCRIPT_NAME));
fclose($f);
//echo $_SERVER['DOCUMENT_ROOT'].SCRIPT_NAME;
$t = fopen ($_SERVER['DOCUMENT_ROOT'].SCRIPT_NAME, 'w+');
$textfile=str_replace($encoding2,$encoding,$textfile);
$textfile=$GLOBALS['APPLICATION']->ConvertCharset($textfile, ENCODING, $NC_ENCODING);
fwrite($t,$textfile);
fclose($t);
echo ShowMsg('<a href=\'\'>Обновите страницу</a>','info_vers');
die();
}
	
if (@$_GET['whatisit']=='Y')
{
ShowWhatIsIt();
die();
}

if (@$_GET['set']=='Y')
{
$i=0;
$Field=$_GET['field'];
$arFields=Array($Field=>$_GET['value']);
CModule::IncludeModule('catalog');
CModule::IncludeModule('iblock');
$dbCatalog=CIBlockElement::GetList(Array(),Array('IBLOCK_ID'=>$_GET['iblock_id']));
while($cat=$dbCatalog->Fetch())
	{
		if (CCatalogProduct::Update($cat['ID'],$arFields))
			$i++;
	}
	ShowMsg('Обновлено товаров: '.$i,'result');
die();
}

if (@$_GET['check']=='cat')
{
ob_start();

CModule::IncludeModule('catalog');
echo '<table cellspacing=0 cellpadding=0 class=cat style="padding:2;border:none;font-size:12;text-align:center;">';
$dbCatalog=CCatalog::GetList(Array(),Array());
echo '<tr><td colspan=3 align=left>Выберите каталог<select id=catalog_list style="width:500;">';
while($cat=$dbCatalog->Fetch())
{
//echo '<pre>';
//print_r($vat);
echo '<option value="'.$cat['ID'].'">'.$cat['NAME'].' ['.$cat['IBLOCK_TYPE_ID'].']</option>';
}
echo '</select><hr></td></tr>';

$dbvat=CCatalogVat::GetList(array(), array());
echo '<tr><td align=right>Назначить НДС для всех товаров</td><td align=left><select id=vat>';
while($vat=$dbvat->Fetch())
{
//echo '<pre>';
//print_r($vat);
echo '<option value="'.$vat['ID'].'">'.$vat['NAME'].' ['.$vat['RATE'].']</option>';
}
echo '</select></td><td><div onclick="SetField(\'VAT_ID\',\'vat\');" class=but>сделать это</div></td></tr>';
echo '<tr><td align=right>Включать или не включать НДС в цену?</td><td align=left><select id=incvat>';
echo '<option value="Y">включать</option>';
echo '<option value="N">не включать</option>';
echo '</select></td><td><div onclick="SetField(\'VAT_INCLUDED\',\'incvat\');" class=but>сделать это</div></td></tr>';
echo '<tr><td align=right>Учитывать или не учитывать количество товара при заказе?</td><td align=left><select id=trace>';
echo '<option value="Y">учитывать</option>';
echo '<option value="N">не учитывать</option>';
echo '</select></td><td><div onclick="SetField(\'QUANTITY_TRACE\',\'trace\');" class=but>сделать это</div></td></tr>';
echo '<tr><td colspan=3 id=count_el align=left></td></tr>';
echo '</table>';
echo '</div>';

$content=ob_get_contents();
ob_end_clean();
ShowWindow('cat','Каталог','cat_id',$content,'',false);
die();
}
//
if (@$_GET['check']=='create')
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");
	if (@$_GET['mode']=='new')
	{ 
		$_REQUEST['name']=$GLOBALS['APPLICATION']->ConvertCharset($_REQUEST['name'], 'utf-8','windows-1251');
		if (GetReportsList(CATALOG_PATH2EXPORTS,$prefix))
				$result.='Такой профиль уже существует. Используйте другой префикс для имени файлов.';
		else 
			{
				$result.=CreateFile($_REQUEST['prefix'],$_REQUEST['name'],'run',$_REQUEST['original']);
				$result.=CreateFile($_REQUEST['prefix'],$_REQUEST['name'],'setup',$_REQUEST['original']);
			}
				//$result.=CreateFile($_REQUEST['prefix'],$_REQUEST['name'],'run',$_REQUEST['original']);
				//$result.=CreateFile($_REQUEST['prefix'],$_REQUEST['name'],'setup',$_REQUEST['original']);
	if ($result)		
		ShowMsg($result,"error");
	else
		{
			$result.="Профиль <b>\"".$_REQUEST['name']."\"</b> успешно создан."."<br>";
			$result.='<a href="/bitrix/admin/fileman_file_edit.php?path=/bitrix/modules/catalog/load/'.$_REQUEST['prefix'].'_run.php&full_src=Y" target="_blank" OnMouseOver="LightOn(this);" OnMouseOut="LightOff();">Перейти к редактированию скрипта экспорта</a>'."<br>";
			$result.='<a href="/bitrix/admin/fileman_file_edit.php?path=/bitrix/modules/catalog/load/'.$_REQUEST['prefix'].'_setup.php&full_src=Y" target="_blank" OnMouseOver="LightOn(this);" OnMouseOut="LightOff();">Перейти к редактированию скрипта настроек экспорта</a>'."\n";
			ShowMsg($result,'result2');
		}
		
		

		die();
	}

$arReportsList = GetReportsList(CATALOG_PATH2EXPORTS);

ob_start();?>
<table width=100% class=cat>
	<tr>
		<td align=right>
	Создать профиль на основе:
	</td>
	<td align=left>
		<select id=original  id=catalog_list style="width:200;">
		<option value="null">новый профиль...</option>
				<?foreach($arReportsList as $Profile):?>
						<option value="<?=$Profile['PREFIX']?>"><?=$Profile['TITLE']?></option>
				<?endforeach;?>
		</select>
		</td>
	</tr>
	<tr>
		<td align=right>
			Введите название профиля:
		</td>
		<td align=left>
			<input id='name' name='name' value='Мой профиль'>
		</td>
	</tr>
	
	<tr>
		<td align=right>
			Введите префикс имён файлов профиля:
		</td>
		<td align=left>
			<input id='prefix' name='prefix' value='my_profile'>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=right>
			<div id='create_profile' class=button align=center onclick="CreateProfile();">Создать</div>
		</td>
	</tr>
	<tr><td align=left colspan=2 id=exp_result ></td></tr>
</table>
<?
$content=ob_get_contents();
ob_end_clean();
ShowWindow('new_profile','Настройки нового профиля экспорта','O_o',$content,5,true);
die();
}


//-------------------test почты россии
if (@$_GET['check']=='russianpost')
{
if ($_GET['mode']=='calc')
{
define('DELIVERY_RUSSIANPOST_SERVER_POST_CATEGORY', 'viewPost');
define('DELIVERY_RUSSIANPOST_SERVER_POST_CATEGORY_NAME', 'viewPostName');
define('DELIVERY_RUSSIANPOST_SERVER_POST_PROFILE', 'typePost');
define('DELIVERY_RUSSIANPOST_SERVER_POST_PROFILE_NAME', 'typePostName');
define('DELIVERY_RUSSIANPOST_SERVER_POST_ZIP', 'postOfficeId');
define('DELIVERY_RUSSIANPOST_SERVER_POST_WEIGHT', 'weight');
define('DELIVERY_RUSSIANPOST_SERVER_POST_PRICE', 'value1');

define('DELIVERY_RUSSIANPOST_SERVER_POST_COUNTRY', 'countryCode');
define('DELIVERY_RUSSIANPOST_SERVER_POST_COUNTRY_NAME', 'countryCodeName');

define('DELIVERY_RUSSIANPOST_SERVER', 'www.russianpost.ru');
define('DELIVERY_RUSSIANPOST_SERVER_PORT', 80);
define('DELIVERY_RUSSIANPOST_SERVER_PAGE', '/autotarif/Autotarif.aspx');
define('DELIVERY_RUSSIANPOST_SERVER_METHOD', 'GET');

//http://www.russianpost.ru/autotarif/Autotarif.aspx?viewPost=12&countryCode=643&typePost=1&viewPostName=%D0%97%D0%B0%D0%BA%D0%B0%D0%B7%D0%BD%D0%B0%D1%8F%20%D0%BA%D0%B0%D1%80%D1%82%D0%BE%D1%87%D0%BA%D0%B0&countryCodeName=%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D0%B9%D1%81%D0%BA%D0%B0%D1%8F%20%D0%A4%D0%B5%D0%B4%D0%B5%D1%80%D0%B0%D1%86%D0%B8%D1%8F&typePostName=%D0%9D%D0%90%D0%97%D0%95%D0%9C%D0%9D.&weight=1000&value1=0&postOfficeId=101000
$viewPostName=str_replace('+','%20',urlencode($_REQUEST['viewPostName']));
$typePost=$_REQUEST['typePost'];
$viewPost=$_REQUEST['viewPost'];
$typePostName=$_REQUEST['typePostName'];
$weight=$_REQUEST['weight'];
$postOfficeId=$_REQUEST['postOfficeId'];
$value1=$_REQUEST['value1'];


$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_CATEGORY."=".$viewPost;
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_PROFILE."=".urlencode($typePost);
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_CATEGORY_NAME."=".$viewPostName;
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_PROFILE_NAME.'='.urlencode($typePostName);
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_COUNTRY."=643";
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_COUNTRY_NAME.'='.urlencode($GLOBALS['APPLICATION']->ConvertCharset('Российская Федерация', LANG_CHARSET, 'utf-8'));
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_WEIGHT."=".urlencode($weight);
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_PRICE."=".$value1;
			$arQuery[] = DELIVERY_RUSSIANPOST_SERVER_POST_ZIP."=".urlencode($postOfficeId);
			$data = QueryGetData(
			DELIVERY_RUSSIANPOST_SERVER, 
			DELIVERY_RUSSIANPOST_SERVER_PORT,
			DELIVERY_RUSSIANPOST_SERVER_PAGE,
			implode("&", $arQuery),
			$error_number = 0,
			$error_text = "",
			DELIVERY_RUSSIANPOST_SERVER_METHOD
		);
if (ENCODING<>'UTF-8') $data=$GLOBALS['APPLICATION']->ConvertCharset($data, 'utf-8','windows-1251');		
echo $data;
die();
} else
{
	CModule::IncludeModule('sale');
	$rsDeliveryHandler = CSaleDeliveryHandler::GetBySID('russianpost');
		if ($arHandler = $rsDeliveryHandler->Fetch())
		{
		    $viewPostNames=$arHandler['CONFIG']['CONFIG']['category']['VALUES'];
			$typePostNames=$arHandler['PROFILES'];
		}

	$i=1;
	ob_start();
	
	echo '<br><table class=russianpost>';
	echo '<tr><td>Вид отправления:</td><td>';
	echo '<select id=viewPostName>';
	foreach($viewPostNames as $key=>$value)
	echo '<option value="'.$key.'">'.$value.'</option>';
	echo '</select></td></tr>';
	echo '<tr><td>Способ пересылки:</td><td>';
	
	echo '<select id=typePostName>';
	foreach($typePostNames as $value)
	echo '<option value="'.$i++.'">'.$value['TITLE'].'</option>';
	echo '</select></td></tr>';
	$db_vars = CSaleLocation::GetList(
        array(
                "SORT" => "ASC",
                "COUNTRY_NAME_LANG" => "ASC",
                "CITY_NAME_LANG" => "ASC"
            ),
        array("LID" =>'ru'),
        false,
        false,
        array()
    );
	while ($vars = $db_vars->Fetch()):
     $dbZip=CSaleLocation::GetLocationZIP($vars['ID']);
	 if(($arZip=$dbZip->Fetch())&&(IsRussian($vars,$ToUpperFunction)))
	 {
				$vars['ZIP_CODE']=$arZip['ZIP'];
				$Locations[]=$vars;
	 }
	 endwhile;
	
	echo '<tr><td>Местоположение:</td><td>';
	echo '<select id="postOfficeId">';
	foreach($Locations as $vars)
	echo '<option value="'.$vars["ZIP_CODE"].'">'.$vars["COUNTRY_NAME"]." - ".$vars["CITY_NAME"].'</option>';
	echo '</select>';
	echo '</td></tr>';
	echo '<tr><td>Вес:</td><td><input id=weight value=\'0\'>г.</td></tr>';
	echo '<tr><td>Ценность:</td><td><input id=value1 value=\'0\'>руб.</td></tr>';
	echo '<tr><td colspan=2 align=center><div class=small_but onclick=\'javascript:CheckRussianPost();\'>Проверить</div></td></tr>';
	echo '</table><br>';
	echo '<div id=rp></div>';
	
	$content=ob_get_contents();
				ob_end_clean();		
	ShowWindow('rp_test','Проверка Почты России',$_GET['check'],$content,'',false);
	die();
}
}
	
	
if ((@$_GET['check']=='payer')||(@$_GET['check']=='pay_system'))
	{
	   	$arPersonType=Array();
		$arPersonType['count']=0;
		$LangCur=CSaleLang::GetByID($_GET['siteid']);
		$CURRENCY=$LangCur['CURRENCY'];
		$db_ptype = CSalePersonType::GetList(($by="SORT"), ($order="DESC"), Array("LID"=>$_GET['siteid']));

		while ($ptype = $db_ptype->Fetch()):
		$arPersonType['count']++;
		$arPersonType['PAYERS'][]=$ptype;
		endwhile;
		if ($arPersonType['count']>0)
			{
				$PaySystemID=Array();		
				foreach ($arPersonType['PAYERS'] as $key=>$payertype):
				$arPersonType['PAYERS'][$key]['TAX_LOCATION']='N';
				$arPersonType['PAYERS'][$key]['HAVE_LOCATION']='N';
				$arPersonType['PAYERS'][$key]['DELIVERY_LOCATION']='N';
				$db_props = CSaleOrderProps::GetList(
						array("SORT" => "ASC"),
						array(
								"PERSON_TYPE_ID" => $payertype['ID'],
							 )

					);
				while($props = $db_props->Fetch())
						{
							$arPersonType['PAYERS'][$key]['PROPS'][]=$props;
							if ($props['TYPE']=='LOCATION')
									{
										$arPersonType['PAYERS'][$key]['HAVE_LOCATION']='Y';
										if ($props['IS_LOCATION']=='Y')
											$arPersonType['PAYERS'][$key]['DELIVERY_LOCATION']='Y';
										if ($props['IS_LOCATION4TAX']=='Y')
											$arPersonType['PAYERS'][$key]['TAX_LOCATION']='Y';
										$arPersonType['PAYERS'][$key]["PROP_LOCATION_ID"]=$props['ID'];
									}
						}

				$db_ptype = CSalePaySystem::GetList($arOrder = Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("LID"=>$_GET['siteid'], "PERSON_TYPE_ID"=>$payertype['ID'],"ACTIVE"=>'Y'));
				while($ptype = $db_ptype->Fetch())
					{
						$arPersonType['PAYERS'][$key]['PAY_SYSTEMS']='Y';
						if (in_array($ptype['ID'],$PaySystemID)==false)
								{
									$arPersonType['PAY_SYSTEMS'][$ptype['ID']]=$ptype;
									$PaySystemID[]=$ptype['ID'];
								}
						if ($ptype['PSA_ENCODING']=="") $arPersonType['PAY_SYSTEMS'][$ptype['ID']]['EMPTY_ENCODUNG']='Y';
					}
				endforeach;
			}
	if ($_GET['check']<>'pay_system')
		{
			ob_start();
			if ($arPersonType['count']==0) 
				{ 
					ShowMsg("NO_PAYERS","error","/bitrix/admin/sale_person_type_edit.php");
					
				}
			else
				{	
					ShowMsg("Количество типов плательщика: ".$arPersonType['count'],"accept");
					echo '<br>';
					foreach ($arPersonType['PAYERS'] as $key=>$payertype):
					ShowMsg('<b>'.$payertype['NAME'].'</b>',"subsection");
					if (!isset($payertype['PROPS']))
						{
							ShowMsg('NO_PROPS',"suberror","bitrix/admin/sale_order_props_edit.php?site=".$_GET['siteid']."&PERSON_TYPE_ID=".$payertype['ID']);
							echo '<br>';
							continue;
						}
					ShowMsg('Свойства заказа присутствуют',"subaccept");
					if ($payertype['HAVE_LOCATION']=='Y') 
					{
						ShowMsg("HAVE_LOCATION","subaccept");
						if ($payertype['TAX_LOCATION']=='Y') ShowMsg('TAX_LOCATION',"subaccept"); 
						else ShowMsg('NO_TAX_LOCATION',"suberror","/bitrix/admin/sale_order_props_edit.php?ID=".$payertype['PROP_LOCATION_ID']);
						if ($payertype['DELIVERY_LOCATION']=='Y') ShowMsg('DELIVERY_LOCATION',"subaccept"); 
						else ShowMsg('NO_DELIVERY_LOCATION',"suberror","/bitrix/admin/sale_order_props_edit.php?ID=".$payertype['PROP_LOCATION_ID']);
					}
					else  
					{					
					ShowMsg("NO_HAVE_LOCATION","suberror","/bitrix/admin/sale_order_props_edit.php?ID=".$payertype['PROP_LOCATION_ID']);
					}
					if (isset($payertype['PAY_SYSTEMS'])) ShowMsg('PAY_SYSTEMS',"subaccept");
					 else ShowMsg('NO_PAY_SYSTEMS',"suberror","/bitrix/admin/sale_pay_system.php");
					echo '<br>';
					endforeach;
				
				}
				$content=ob_get_contents();
				ob_end_clean();			
				ShowWindow('payer_list','Типы плательщиков',$_GET['check'],$content,0);	  
				die();
		}
	else
		{
		    ob_start();
			if (!isset($arPersonType['PAY_SYSTEMS'])) 
				{ 
					ShowMsg("NO_PAY_SYSTEMS","error","/bitrix/admin/sale_pay_system.php");
				} 
			else
				{
					ShowMsg('Количество активных платёжных систем: '.count($arPersonType['PAY_SYSTEMS']),"accept");
					echo '<br>';
					foreach($arPersonType['PAY_SYSTEMS'] as $key=>$value):
					ShowMsg('<b>'.$value['NAME'].'</b>',"subsection");
						if ($CURRENCY!=$value['CURRENCY']) ShowMsg('WRONG_CURRENCY',"suberror","/bitrix/admin/sale_pay_system_edit.php?ID=".$value['ID']);
						if (isset($value['EMPTY_ENCODUNG'])) ShowMsg('EMPTY_ENCODUNG','subwarning',"/bitrix/admin/sale_pay_system_edit.php?ID=".$value['ID']);
					endforeach;	
					echo '<br>';
				}
			$content=ob_get_contents();
			ob_end_clean();
			
			ShowWindow('pay_sys','Платёжные системы',$_GET['check'],$content,1);	   
			die();
		}
	}

if (@$_GET['check']=='delivery')
	{
	$location = intval(COption::GetOptionString('sale', 'location', '', $_GET['siteid']));
	$arLocs = CSaleLocation::GetByID($location);
	//print_r($arLocs);
	$arDeliveries=Array();
		$dbResult = CSaleDeliveryHandler::GetList(
		  array(
			'SORT' => 'ASC', 
			'NAME' => 'ASC'
		  ), 
		  array(
			'ACTIVE' => 'Y'
		  )
		);
		while ($arResult = $dbResult->GetNext())
				$arDeliveries['AUTO'][]=$arResult;
	$dbResult = CSaleDelivery::GetList(
    array(
            "SORT" => "ASC",
            "NAME" => "ASC"
        ),
    array(
            "LID" => $_GET['siteid'],
            "ACTIVE" => "Y",
        )
	);
	
	while ($arResult = $dbResult->GetNext())
			{
				$dbLocation=CSaleDelivery::GetLocationList(Array('DELIVERY_ID'=>$arResult['ID']));
				if($arLocations=$dbLocation->Fetch())
				$arResult['LOCATIONS']=$arLocations;
				$arDeliveries['MANUAL'][]=$arResult;
			}

		
		ob_start();
		if (!count($arDeliveries)) 
			{ 
				ShowMsg("NO_DELIVERY","error","/bitrix/admin/sale_delivery_index.php");
			}	
		else
			{
				ShowMsg('Количество активных служб доставки: '.(count($arDeliveries['AUTO'])+count($arDeliveries['MANUAL'])).'<br>' ,"accept");
				echo '<br>';
				require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/ru/delivery/delivery_russianpost.php');
				require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/delivery/dhl_usa/country.php');
				if ($arDeliveries['AUTO'])
				//echo 'ок';
				foreach($arDeliveries['AUTO'] as $key=>$value):
						ShowMsg('<b>'.$value['NAME'].'</b>',"subsection");
						//echo $value['SID'];
						if (!$location) ShowMsg('WHERE_IS_YOUR_SHOP_PLACED',"suberror","/bitrix/admin/settings.php?mid=sale");
						switch($value['SID'])
						{
							case 'russianpost':
										if(!IsMoscow($arLocs,$ToUpperFunction))
										ShowMsg('NOT_IN_MOSCOW',"suberror","/bitrix/admin/settings.php?mid=sale");
										break;
							case 'dhlusa':
										$arLocation = CSaleLocation::GetByID($location, 'en');
										if($arDHLUSACountryList[ToUpper($arLocation['COUNTRY_NAME'])]!= 'US')
										ShowMsg('NOT_IN_USA',"suberror","/bitrix/admin/settings.php?mid=sale");
										break;
							case 'cpcr':
										
							break;
						}
				endforeach;	

				if ($arDeliveries['MANUAL'])
				{
					//echo '<hr>';
					foreach($arDeliveries['MANUAL'] as $key=>$value):
							ShowMsg('<b>'.$value['NAME'].'</b>',"subsection");
							if (!$value['LOCATIONS'])
								ShowMsg('NO_LOCATIONS_FOR_DELIVERY',"suberror","/bitrix/admin/sale_delivery_edit.php?ID=".$value['ID']);
					endforeach;
				}
						echo '<br>';
						
			}
		$content=ob_get_contents();
		ob_end_clean();
		
	ShowWindow('delivery_list','Службы доставки',$_GET['check'],$content,2);	
die();
}

if (@$_GET['check']=='locations')
	{
	$Locations['LOCATIONS']['NO_ZIP']=0;;
   $db_vars = CSaleLocation::GetList(
        array(
                "SORT" => "ASC",
                "COUNTRY_NAME_LANG" => "ASC",
                "CITY_NAME_LANG" => "ASC"
            ),
        array("LID" =>'ru'),
        false,
        false,
        array()
    );
	$arSite=CSite::GetByID(SITE_ID);
	while ($vars = $db_vars->Fetch()):
     $dbZip=CSaleLocation::GetLocationZIP($vars['ID']);
	 if(!$arZip=$dbZip->Fetch())
	 {
				$Locations['LOCATIONS']['NO_ZIP']++;
				$Locations['LOCATIONS'][]=$vars;
	 }
	 endwhile;
ob_start();
		if(!$db_vars) 
			{
				ShowMsg("NO_LOCATIONS","error","/bitrix/admin/sale_location_admin.php"); 	
			}
		else
			{
ShowMsg("Местоположения присутствуют","accept"); 	
if ($Locations['LOCATIONS']['NO_ZIP']>0)
{
			 ShowMsg('Местоположений без индекса: '.$Locations['LOCATIONS']['NO_ZIP'],'subwarning');	
?>
				<select style="margin-left:60;" name="LOCATION" onchange='EditLocation(this)' style="width:300px;">
				<?foreach($Locations['LOCATIONS'] as $vars):?>
							<option value="<?= $vars["ID"]?>"><?=$vars["COUNTRY_NAME"]." - ".$vars["CITY_NAME"]?></option>
				  <?endforeach;?>
				</select>
				<a id='loc' href="javascript:alert('Выберите местоположение');" target='_blank'>исправить</a>
			<?}
}
			 echo '<br>';
			 echo '<br>';
		$content=ob_get_contents();
		ob_end_clean();
		
	ShowWindow('location_list','Местоположения',$_GET['check'],$content,3);	
die();
}

if (@$_GET['check']=='currency')
{
$alph='QWERTYUIOPASDFGHJKLZXCVBNM';
$base=CCurrency::GetBaseCurrency();
$db_curr=CCurrency::GetList(($b="name"), ($order1="asc"));
while($lcur_res = $db_curr->Fetch())
{
$i=0;
while($i<=strlen($lcur_res['CURRENCY']-1))
{
	if($base==$lcur_res['CURRENCY']) 
		{
			$lcur_res['BASE']='Y';
			if ($lcur_res['AMOUNT']<=0) $lcur_res['WRONG_AMOUNT']='Y';
		}
	$res=strpos($alph,ToUpper($lcur_res['CURRENCY'][$i]));
	if ($res===false)
	{
		$lcur_res['BAD_ID']='Y';
		break;
	}
	$i++;
}
$arCurrency[]=$lcur_res;
}

ob_start();
		if(!$db_curr) 
			{
				ShowMsg("NO_CURRENCY","error","/bitrix/admin/currencies.php"); 	
			}
		else
			{
ShowMsg("Валюты присутствуют","accept"); 	
if (!$base) ShowMsg("NO_BASE_CURR","error","/bitrix/admin/currencies.php");
echo '<br>'; 	
foreach($arCurrency as $cur):
	ShowMsg('<b>'.$cur['CURRENCY'].'('.$cur['FULL_NAME'].')</b>',"subsection");
	if ($cur['BAD_ID']) ShowMsg("BAD_ID","suberror","/bitrix/admin/currency_edit.php");
	if ($cur['WRONG_AMOUNT']) ShowMsg("WRONG_AMOUNT","suberror","/bitrix/admin/currency_edit.php?ID=".$cur['CURRENCY']);
	
			
endforeach;
		echo '<br>';
		$content=ob_get_contents();
		ob_end_clean();
		
	ShowWindow('currency_list','Валюты',$_GET['check'],$content,4);	
die();
}
}

if (@$_REQUEST['action']=='productfix')
{
      $count=0;
	CModule::IncludeModule('catalog');
	CModule::IncludeModule('iblock');
      $arFilter=array("IBLOCK_ID" =>$_REQUEST['catalog_id']);
		$dbProduct=CIBlockElement::GetList(Array("ID"=>'ASC'),
        $arFilter,
        false,
        false,Array());
		while ($arProduct = $dbProduct->GetNext())
		{
		$IsProduct=CCatalogProduct::GetByID($arProduct['ID']);
				if (!$IsProduct)
				{
					$ID=CCatalogProduct::Add(Array('ID'=>$arProduct['ID']));
					if ($ID) $count++;
				}
		}
echo 'Создано продуктов: '. $count.'. Перезапустите тест, пожалуйста!';
die();
}
	
if (@$_REQUEST['data'])
{
	
		$found=false;
		$count=0;
		
	//$cdata=json_decode($_REQUEST['data']);
	$cdata=CUtil::JsObjectToPhp($_REQUEST['data']);
	
	if (!$cdata['is_not_product']) 
		$cdata['is_not_product']=0;
	if (!$cdata['without_price']) 
		$cdata['without_price']=0;
	if (!$cdata['no_product_weight']) 
		$cdata['no_product_weight']=0;
	if (!$cdata['no_active_product']) 
			$cdata['no_active_product']=0;
	CModule::IncludeModule('catalog');
	CModule::IncludeModule('iblock');
	$start=time();
	
	if (!$cdata['iblock_type_id'])
	{
		$IBLOCK=CIBlock::GetByID($cdata['catalog_id'])->Fetch();
		$cdata['iblock_type_id']=$IBLOCK['IBLOCK_TYPE_ID'];
	}
		$arFilter=array("IBLOCK_ID" =>$cdata['catalog_id']);
		if ($cdata['last_element'])	
				$arFilter[">ID"]=$cdata['last_element'];
		$dbProduct=CIBlockElement::GetList(Array("ID"=>'ASC'),
        $arFilter,
        false,
        array("nTopCount" =>200),Array());
		while ($arProduct = $dbProduct->GetNext())
		{
			if (ToUpper(LANG_CHARSET)!='UTF-8')
				$name=$GLOBALS['APPLICATION']->ConvertCharset($arProduct['NAME'], LANG_CHARSET, 'utf-8');
			$edit_path="<a style='font-size:11px;' href='javascript:OpenWin(\"/bitrix/admin/iblock_element_edit.php?ID=".$arProduct['ID']."&type=".$arProduct['IBLOCK_TYPE_ID']."&IBLOCK_ID=".$arProduct['IBLOCK_ID']."\")' target=_blank >".$name."</a>";
			
			//echo $arProduct["NAME"]."<br>";
				$found=true;
				if ($arProduct['ACTIVE']!='Y')
				{
					$cdata['no_active_product']++;	
					$cdata['no_active_product_item'][]=$edit_path;
				}				
				if (!CPrice::GetList(array(),array('PRODUCT_ID'=>$arProduct['ID']))->Fetch())
							{								
								$cdata['without_price']++;
								$cdata['without_price_product'][]=$edit_path;
							}
							$IsProduct=CCatalogProduct::GetByID($arProduct['ID']);
				if (!$IsProduct)
				{
					$cdata['is_not_product']++;
					$cdata['not_product_item'][]=$edit_path;
				}
				else
				if ($IsProduct['WEIGHT']==false)
					{
						$cdata['no_product_weight']++;	
						$cdata['no_product_weight_item'][]=$edit_path;
						//$cdata->no_product_weight_item[]='<a href="#">Test</a>';
					}
				
							//	
			//$_SESSION['bx_catalog_test']['catalog_id']['current_product_id']=$arProduct['ID'];
			$cdata['last_element']=$arProduct['ID'];
			$count++;
		    if ((time()-$start)>=15)  
		      {
			      $cdata['count']+=$count;
			      echo CUtil::PhpToJSObject($cdata);
			      //echo json_encode($cdata);
			      die();
		      }
		}
	   $cdata['count']+=$count;
	if ($found==false||$count<200) 
			unset($cdata['last_element']);
			echo CUtil::PhpToJSObject($cdata);
			//echo json_encode($cdata);
	
	die();
}

if ($_REQUEST['check']=='catalog')
{
        CModule::IncludeModule('catalog');
        $arCatalogList=CCatalog::GetList(Array(),Array());?>
        <div id=msg></div>
<?ob_start();?>
<table width=100% class=cat>
	<tr>
		<td align=right>
	Выберите каталог для теста:
	</td>
	<td align=left>
		<select  id=catalog_list style="width:200;">
				<?while($arCatalog=$arCatalogList->Fetch()):?>
						<option value="<?=$arCatalog['ID']?>"><?=$arCatalog['NAME']?></option>
				<?endwhile;?>
		</select>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=right>
			<div class=button align=center onclick="CheckCatalog();">поехали</div>
		</td>
	</tr>
	<tr><td align=left class=result colspan=2 id=result_cat > Нажмите кнопку "поехали"!</td></tr>
	<tr><td align=left class=detail_result colspan=2 id=detail_result ></td></tr>
</table>
<?
$content=ob_get_contents();
ob_end_clean();
ShowWindow('catalog','Тестирование каталога','O_o',$content,'6',true);
die();
}
?>

<html>
<title>Тест интернет-магазина <?='v'.VER?></title>
<head>
<style type="text/css">

body
{
	font-family:tahoma,verdana,arial,sans-serif,Lucida Sans;
	font-size:14;
	background:#CCCCFF;
}

.cat select
{
border:1px dotted #B0E2FF;
width:115;
background:#DCDCDC;
}

.cat td
{
padding:2 2 5 5;
font-size:12;
}

.header
{
font-size:15;
z-index:10;
}

.myborder
{
border-bottom:10px solid #6699CC;
}

h2
{
font-size:20;
font-weight:bold;
padding:5;
}

.gtable
{
border:none;
border-bottom:5px solid #6699CC;
border-right:none;
font-size:12;
height:100%;
width:60%;
background:white;
}

.gtable_td
{
padding-left:10;
padding-right:10;
text-align:center;
margin:0;
width:600;
border:1px solid #6699CC;

}

.gtable_td_right
{
padding-left:10;
padding-right:10;
#padding-bottom:10;
#border:none;
margin:0;
width:400;
border-right:2px solid #6699CC;

}

.main_table
{
border:1px solid #B9D3EE;
width:100%;
font-size:12;
margin:0;
align:center;
}

.main_table_td
{
padding:0 10 0 10;
border:none;
width:100%;
border-bottom:1px solid white;
}

.section
{
background:#B9D3EE;
font-size:14;
padding:2px 2px 2px 15px;
}



.russianpost
{
width:100%;
font-size:12;
align:center;
}

.russianpost td
{
font-size:12;
align:center;
padding-left:10;
border:1px dotted #66CCCC;
}

#rp table
{
padding:0;
font-size:12;
}

#rp td
{
padding:0;
font-size:12;
margin-left:0;
padding:3;
}

.menu
{
border:none;
width:170;
font-size:12;
z-index:100;
}

.menu_td
{
border-left:3px solid #B9D3EE;
border-right:3px solid #B9D3EE;
z-index:1000;
background:#B9D3EE;
}

.htable
{
width:400;
font-size:12;
align:center;
padding-top:1px;
}

.htable_td
{
padding:0px;
border-left:1px solid #6699CC;
border-right:1px solid #6699CC;
background:#6699CC;
}

.hsection
{
width:100%;
font-size:12;
color:white;
cursor:pointer;
border-top:none;
font-weight:bold;
background:#6699CC;
}

.rtop,.rbottom{display:block;width:100%;}
.rtop *,.rbottom *{display: block; height: 1px; overflow: hidden;background:#6699CC;}
.r1{margin: 0 5px;}
.r2{margin: 0 3px;}
.r3{margin: 0 2px;}
.r4{margin: 0 1px; height: 2px;}

.msection
{
width:180;
font-size:14;
color:white;
border-top:none;
background:#B9D3EE;
border-bottom:6px solid #B9D3EE;
}


.rtopwin, .rbottomwin{display:block;width:200;}
.rtopwin *,.rbottomwin *{display: block; height: 1px; overflow: hidden;background:#B9D3EE;}
.r1win{margin: 0 5px;}
.r2win{margin: 0 3px;}
.r3win{margin: 0 2px;}
.r4win{margin: 0 1px; height: 2px;}


.htable a
{
font-size:11;
}


.panel
{

padding:10;
width:100;
background:#6699CC;
text-align:center;
border-left:3px solid #4F94CD;

}

a
{
 text-decoration: none;
 color:#36648B;
 font-size:12;
}


a.section
{
	font-size:11;
	margin:0 0 10 0;
	padding-left:20;
	border:none;
	width:100%;
}

.area
{
border-right:1px dashed #B9D3EE;
border-left:1px dashed #B9D3EE;
width:500;padding:10 0 10 25;
}

.subsection
{
background-image: url(/bitrix/images/arr_right.gif);
background-repeat: no-repeat;
background-position:left center;
font-size:13;
padding:0 10 0 15;
margin-left:20;
width:100%;
text-align:left;

}

.error
{
background-image: url(/bitrix/images/install/error.png);
background-size:16 16;
background-repeat: no-repeat;
background-position:left top;
font-size:13;
padding:0 0 10 22;
color:red;
}

.result
{
font-size:13;
padding:10 0 10 22;
color:green;
background:#FFE4B5;
font-weight:bold;
}

.result2
{
font-size:13;
padding:10 0 10 5;
color:green;

font-weight:bold;
width:100%;
}

.suberror
{
background-image: url(/bitrix/images/sale/red.gif);
background-size:12 12;
background-repeat: no-repeat;
background-position:left center;
font-size:12;
position:relative;
padding:0 0 0 18;
margin:2 2 2 40;
color:red;
}

.accept
{
background-image: url(/bitrix/images/sale/green.gif);
background-repeat: no-repeat;
background-position:left top;
position:relative;
font-size:12;
font-weight:bold;
padding:0 0 10 22;
color:green;
}

.subaccept
{
background-image: url(/bitrix/images/sale/green.gif);
background-size:12px 12px;
background-repeat: no-repeat;
background-position:left center;
position:relative;
font-size:12;
padding:0 0 0 17;
margin:2 2 2 40;
color:green;
}

.subwarning
{
background-image: url(/bitrix/images/sale/yellow.gif);
background-size:12 12;
background-repeat: no-repeat;
background-position:left center;
font-size:12;
padding:0 0 0 17;
margin:2 2 2 40;
position:relative;
color:red;
}



.message
{
padding:5 5 0 22;
color:black;
}

.button
   {
 background-color:#B9D3EE;
   border:1px solid #ADC3D5;
   width:170;
   height:20px;
   font-size:13;
   color:#2B587A;
   cursor:pointer;
   margin-top:5;
   margin-bottom:5;
   
   }  
 .small_but {
	border: 1px solid #E6E6FA;
	background-color: #F0F8FF;
	padding: 4px 1px 4px 1px;
	margin:5;
	font-size:11;
	width:190;
	cursor:pointer;
	color:black;
	#align:center;
}

.but {
	border: 1px solid Gray;
	background-color: #F0F8FF;
	padding: 3px 1px 2px 3px;
	margin:1px;
	font-size:11;
	cursor:pointer;
	width:148;
	color:black;
	align:center;
}
.load
{
background-image: url(/bitrix/images/install/wait.gif);
background-repeat: no-repeat;
background-position:12px center;
font-size:14;
background-color:#FFE4B5;
position:absolute;
left:85%;
top:2%;
display:none;
width:110;
padding:10 10 10 40;
margin:5;
z-index:10000;
}

.inf
{
background-position:12px center;
font-size:11;
background-color:#FFE4B5;
width:100%;
padding:2 2 2 2;
}

.info_vers
{
background-color:#FFE4B5;
font-size:14;
width:100%;
padding:2 2 2 2;
}

.intable
{
padding:2;
font-size:12;
text-align:center;
}

</style>
</head>
<body>

<table class=gtable align=center cellspacing=0>
<tr>
<td height=20 valign=bottom align=center style='background:#CCCCFF'><?OneMoreTab('Поехали');?></td>
<td height=20 valign=bottom align=center style='background:#CCCCFF'><?OneMoreTab('Тест магазина на CMS Bitrix');?></td>
</tr>
<tr>
<td class=panel align=center valign=top>
<div onclick="delete_file();" class=small_but>Удалить скрипт</div>
<div onclick="change_encode('<?=$NC_ENCODING?>');" class=small_but id=conv>А у меня <?=$NC_ENCODING;?></div>
<div onclick="WhatIsIt();" class=small_but>Открыть "Что это?"</div><br>
<?ob_start();

echo 'Выберите сайт:<br>
<select onchange=\'javascript:document.getElementById("result").innerHTML="";\' id=\'siteid\'  style="width:150px;">';
$rsSites=CSite::GetList();
while ($arSite = $rsSites->Fetch())
echo  '<option value="'.$arSite['ID'].'">'.$arSite['NAME'].' ['.$arSite['ID'].']</option>';
echo '</select><br>
Что тестируем:<br>
<select onDblClick="Start();" id=list size=6 style="width:150px;">
  <option value="all">Всё</option>
  <option selected value=0>Типы плательщика</option>
  <option value=1>Платёжные системы</option>
  <option value=2>Доставки</option>
  <option value=3>Местоположения</option>
  <option value=4>Валюты</option>
</select>
<div class=but onclick=\'javascript:Start();\'>СТАРТ</div>';

$content=ob_get_contents();
ob_end_clean();
$DeleveryMenu='<div class=but onclick="javascript:AjaxRequest(\''.SCRIPT_NAME.'?check=russianpost\',\'result\',false);">Проверить Почту России</div>';
$Cool='<div class=but onclick="javascript:AjaxRequest(\''.SCRIPT_NAME.'?check=cat\',\'result\',false);">Каталог</div>
<div class=but onclick="javascript:AjaxRequest(\''.SCRIPT_NAME.'?check=create\',\'result\',false);">Профили экспорта</div>
<div class=but onclick="javascript:AjaxRequest(\''.SCRIPT_NAME.'?check=catalog\',\'result\',false);">Проверить каталог</div>
';


ShowMenuWindow('win_menu','Общая проверка','general',$content);
echo '<br>';
ShowMenuWindow('cool','Полезности','cool_id',$Cool);
echo '<br>';
ShowMenuWindow('del_menu','Доставки','delmenu',$DeleveryMenu);
?>
</td>
<td valign=top align=center class=gtable_td>
<div width=800 id=result style='padding:5;' align=center>
<?ShowWhatIsIt();?>
</div>
</td>
</table>
<div id=load class=load>Зачекайте...</div>
</body>
</html>


<script type="text/javascript">
var params= new Array('payer','pay_system','delivery','locations','currency','create','catalog');
var i=50;
var oldelem;
var borderold;
var IdS=new Array();
var encode_proccess=false;

function AjaxRequestCallBack(url,id,AddResult,callback)
				{
				//alert(url);
				var ajaxreq=createHttpRequest();
				document.getElementById('load').style.display="block";
				   ajaxreq.open("GET", url, true);
				 // ajaxreq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				  ajaxreq.onreadystatechange=function() {callback(ajaxreq,url,id,AddResult);};
 				ajaxreq.send(null);
					
				}
function AjaxCallBack(ajaxreq,url,id,AddResult)
	{
		//alert(ajaxreq.responseText);
		//alert(ajaxreq.readyState);

				if (ajaxreq.readyState == 4)
									{	
										json_data=eval("(" +ajaxreq.responseText+")");
									      json_data_global=json_data;
										if (json_data.no_product_weight_item)
											no_weight=no_weight.concat(json_data.no_product_weight_item);
										if (json_data.without_price_product)
											no_price=json_data.without_price_product.concat(no_price);
										if (json_data.not_product_item)
											no_product=json_data.not_product_item.concat(no_product);
										if (json_data.no_active_product_item)
											no_active=json_data.no_active_product_item.concat(no_active);
										if (AddResult==false)
											{
												document.getElementById(id).style.display='block';
												document.getElementById(id).innerHTML="Проверено товаров: "+json_data.count;
												document.getElementById(id).innerHTML+="<br>Товаров без цен: <a href=javascript:DetailCheck('price',"+json_data.catalog_id+") title='Показать товары без цен'>"+json_data.without_price+"</a>";
												document.getElementById(id).innerHTML+="<br>Не товар: <a href=javascript:DetailCheck('product',"+json_data.catalog_id+") title='Показать элементы, которые не являются товарами'>"+json_data.is_not_product+"</a>";
												document.getElementById(id).innerHTML+="<br>Не указан вес: <a href=javascript:DetailCheck('weight',"+json_data.catalog_id+") title='Показать товары у которых не указан вес'>"+json_data.no_product_weight+"</a>";
												document.getElementById(id).innerHTML+="<br>Неактиных товаров: <a href=javascript:DetailCheck('active',"+json_data.catalog_id+") title='Показать элементы, которые не являются товарами'>"+json_data.no_active_product+"</a>";
											}	
										else 
											document.getElementById(id).innerHTML+="Проверено товаров: "+json_data.count+'<br>';
		
											document.getElementById('load').style.display="none";
										if (json_data.last_element) 
												{
												
												urldata='{"catalog_id":'+json_data.catalog_id+',"count":'+json_data.count+',"is_not_product":'+json_data.is_not_product+',"without_price":'+json_data.without_price+',"no_product_weight":'+json_data.no_product_weight+',"no_active_product":'+json_data.no_active_product+',"iblock_type_id":"'+json_data.iblock_type_id+'","last_element":'+json_data.last_element+'}';
												//alert("?data="+ajaxreq.responseText);
												AjaxRequestCallBack("?data="+urldata,id,AddResult,AjaxCallBack);
												}
											
									}
											
	}


function AjaxCallBackDetail(ajaxreq,url,id,AddResult)
		{
			if (ajaxreq.readyState == 4)
									{	
										document.getElementById(id).style.display='block';
										//json_data=eval("(" +ajaxreq.responseText+")");
										document.getElementById(id).innerHTML=ajaxreq.responseText;
										document.getElementById('load').style.display="none";
									}
		}

function AjaxCallBackFix(ajaxreq,url,id,AddResult)
		{
			if (ajaxreq.readyState == 4)
									{	
//alert(ajaxreq.responseText);
										/*document.getElementById(id).style.display='block';
										//json_data=eval("(" +ajaxreq.responseText+")");
										document.getElementById(id).innerHTML=ajaxreq.responseText;*/
										document.getElementById('load').style.display="none";
									}
		}
		
function CheckCatalog()
		{
			 no_price=new Array();
			 no_product=new Array();
			 no_weight=new Array();
			 no_active=new Array();

			var catalog=document.getElementById('catalog_list').value;
			document.getElementById('result_cat').style.display='none';
			document.getElementById('detail_result').innerHTML='';
			//var urldata='?data={"action":"check_catalog","catalog_id":'+catalog+'"is_not_product":0,"without_price":0,"no_product_weight":0,"no_active_product":0}';
		      var urldata='?data={"action":"check_catalog","catalog_id":'+catalog+'}';

			count=0;
			
			AjaxRequestCallBack(urldata,"result_cat",false,AjaxCallBack);
		}
function DetailCheck(mode,catalog_id)
		{
			var i=0;
			var list=0;
 
			//alert(json_data_global.without_price_product);
			
			document.getElementById('detail_result').style.display='block';
			document.getElementById('detail_result').innerHTML='Списки отображают только первые 100 элементов!<br><br>'
			if (mode=='price')
			{
				list=no_price;
				document.getElementById('detail_result').innerHTML+='<span class=section >Ссылки на редактирование товаров без цен:</span><hr>';
			} else
			if (mode=='product')
			{
				document.getElementById('detail_result').innerHTML+='<span class=section >Ссылки на редактирование элементов, которые не являются товарами:</span><hr>';				
				if(json_data_global.is_not_product>0)
				document.getElementById('detail_result').innerHTML+='<a href=javascript:AjaxRequestCallBack("?action=productfix&catalog_id='+json_data_global.catalog_id+'","result",false,AjaxCallBackFix) OnMouseOver="LightOn(this);" OnMouseOut="LightOff();">Исправить</a><hr>';
	                        list=no_product;
			} else	
			
			if (mode=='weight')
			{
				document.getElementById('detail_result').innerHTML+='<span class=section >Товары без веса:</span><hr>';				
				list=no_weight;
			} else			
			if (mode=='active')
			{
				document.getElementById('detail_result').innerHTML+='<span class=section >Неактивные товары:</span><hr>';				
				list=no_active;
			}
			if (list.length>0) 
			{
				while(i<list.length && i<=99)
					{
						document.getElementById('detail_result').innerHTML+=i+1+'. '+list[i]+'<br>';
						i++;
					}
			}
			else 
			{
				document.getElementById('detail_result').innerHTML+='Нет таких элементов';
			}
			//document.getElementById('detail_result').innerHTML=;
//			AjaxRequest('?data={"action":"'+mode+'"}','detail_result',false, AjaxCallBackDetail);
		}

function EditLocation(el)
		{
		document.getElementById('loc').href='/bitrix/admin/sale_location_edit.php?ID='+el.value;
		document.getElementById('loc').innerHTML='исправить '+'"'+el.options[el.selectedIndex].innerHTML+'"';
	   }
function createHttpRequest() 

   {
	var httpRequest;
		if (window.XMLHttpRequest) 
		httpRequest = new XMLHttpRequest();  
		else if (window.ActiveXObject) {    
		try {
		httpRequest = new ActiveXObject('Msxml2.XMLHTTP');  
		} catch (e){}                                   
		try {                                           
		httpRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e){}
		}
	return httpRequest;

}

function Start()
	{
		document.getElementById('result').innerHTML="";
		if (document.getElementById("list").value=="all")
		{
			i=0;
			StartDiag(i,'result');
		}
		else
		{
		StartDiag(document.getElementById("list").value,'result');	
		}
		
	}

function StartDiag(step,id)
				{
			  
              var siteid=(document.getElementById('siteid').value);
			  var ajaxreq=createHttpRequest();
			  if (id!='result')
			  {
				setElementOpacity(document.getElementById(id),.5);
			  }
			  
			  document.getElementById('siteid').disabled=true;
			  document.getElementById('load').style.display='block';
			  
				url='<?=SCRIPT_NAME;?>?siteid='+siteid+'&check='+params[step]+'&show=sale';
				   ajaxreq.open("GET", url, true);
					ajaxreq.onreadystatechange=function()
								{
								if (ajaxreq.readyState == 4)
									{		
									if (id!='result')
									{
										//fadeOpacity(id,'oR2');
										setElementOpacity(document.getElementById(id),1);
										document.getElementById(id).innerHTML=ajaxreq.responseText;
																		
									} else {document.getElementById(id).innerHTML+=ajaxreq.responseText;document.getElementById("result").style.display="block";}
									document.getElementById('load').style.display='none';
									document.getElementById('siteid').disabled=false;
									i++;
									
									document.getElementById(id).style.opacity="";
									if (i<=4) 
										{
											StartDiag(i,id);
										}
									}
								}
					
					ajaxreq.send(null);
				}

				function SetField(field,id)
					{
			
					var value=document.getElementById(id).value;
					var iblock_id=document.getElementById('catalog_list').value;
								//alert(iblock_id);
						AjaxRequest('<?=SCRIPT_NAME;?>?set=Y&field='+field+'&value='+value+'&iblock_id='+iblock_id,'count_el',false);
					}
function change_encode(enc)
		{
		//alert(enc);
		if (encode_proccess==false)
		{
			document.getElementById('conv').style.display='none';
			encode_proccess=true;
			var ajaxreq=createHttpRequest();
			load.style.display="block";
				   ajaxreq.open("GET", '<?=SCRIPT_NAME;?>?change_encode=Y', true);
					ajaxreq.onreadystatechange=function()
								{
							//alert(ajaxreq.readyState);
								if (ajaxreq.readyState == 4)
								{		
									document.getElementById('result').innerHTML=ajaxreq.responseText;
									load.style.display="none";
								}
					
									
								}
		ajaxreq.send(null);
		
		}
}
					
function AjaxRequest(url,id,AddResult)
				{
				//alert(url);
				var ajaxreq=createHttpRequest();
				load.style.display="block";
				setElementOpacity(document.getElementById(id),.3);
				   ajaxreq.open("GET", url+'&show=sale', true);

					ajaxreq.onreadystatechange=function()
								{
							//alert(ajaxreq.readyState);
								if (ajaxreq.readyState == 4)
								{		
								if (AddResult==false)
								{			
								
									document.getElementById(id).innerHTML=ajaxreq.responseText;
									setElementOpacity(document.getElementById(id),1); 
								}
								else 
								{
									document.getElementById(id).innerHTML+=ajaxreq.responseText;
									setElementOpacity(document.getElementById(id),1);
								}
									load.style.display="none";
									
								}
								}
					
					ajaxreq.send(null);
					
				}
function CheckRussianPost()
				{
				var viewPostName=document.getElementById('viewPostName').options[document.getElementById('viewPostName').selectedIndex].innerHTML;
				var viewPost=document.getElementById('viewPostName').value;
				var typePostName=document.getElementById('typePostName').options[document.getElementById('typePostName').selectedIndex].innerHTML;
				var typePost=document.getElementById('typePostName').value;
				var postOfficeId=document.getElementById('postOfficeId').value;
				var weight=document.getElementById('weight').value;
				var value1=document.getElementById('value1').value;
				
AjaxRequest('<?=SCRIPT_NAME;?>?viewPost='+viewPost+'&typePost='+typePost+'&viewPostName='+encodeURIComponent(viewPostName)+'&typePostName='+encodeURIComponent(typePostName)+'&postOfficeId='+postOfficeId+'&weight='+weight+'&value1='+value1+'&mode=calc&check=russianpost','rp',false);				
			
				}
				function WhatIsIt()
				{
					AjaxRequest('<?=SCRIPT_NAME;?>?whatisit=Y','result',false);				
				}
function ShowHideSection(id)
		{
			var t='none';
			if(document.getElementById(id).style.display=='none')
			{t='block';}
			document.getElementById(id).style.display=t;
		}
function LightOn(el)
{
oldelem=el;
borderold=el.style.background;
el.style.background="#C6E2FF";
el.style.padding=2;

}


function LightOff()
{
var el= oldelem;
el.style.background=borderold;
el.style.padding=0;
}	

function CreateProfile()
		{
			var name=document.getElementById('name').value;
			var prefix=document.getElementById('prefix').value;
			var original=document.getElementById('original').value;
			AjaxRequest("?check=create&mode=new&original="+original+"&prefix="+prefix+"&name="+encodeURIComponent(name),"exp_result",false);
		}
function setElementOpacity(oElem, nOpacity)
{
	var p = getOpacityProperty();
	(setElementOpacity = p=="filter"?new Function('oElem', 'nOpacity', 'nOpacity *= 100;	var oAlpha = oElem.filters["DXImageTransform.Microsoft.alpha"] || oElem.filters.alpha;	if (oAlpha) oAlpha.opacity = nOpacity; else oElem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")";'):p?new Function('oElem', 'nOpacity', 'oElem.style.'+p+' = nOpacity;'):new Function)(oElem, nOpacity);
}

function OpenWin(path)
{
	window.open(path,'new','width=1000,height=700, top=100, left=200,toolbar=1 scrollbars=yes');
}

function getOpacityProperty()
{
	var p;
	if (typeof document.body.style.opacity == 'string') p = 'opacity';
	else if (typeof document.body.style.MozOpacity == 'string') p =  'MozOpacity';
	else if (typeof document.body.style.KhtmlOpacity == 'string') p =  'KhtmlOpacity';
	else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) p =  'filter';
	
	return (getOpacityProperty = new Function("return '"+p+"';"))();
}

function delete_file()
	{
		if (confirm('Удалить файл?'))
			document.location = "<?=SCRIPT_NAME;?>?delete=Y";
	}
	

</script>
