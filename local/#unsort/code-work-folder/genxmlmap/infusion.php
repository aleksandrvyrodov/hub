<?php
if (!defined("IN_FUSION") || !checkrights("I")) { header("Location:index.php"); exit; }

require_once INFUSIONS."genxmlmap/infusion_db.php";

$inf_title = 'Genxmlmap';
$inf_description = 'Генератор sitemap.xml';
$inf_version = '1.0';
$inf_developer = 'Bender Rodriges';
$inf_email = '2-10101100110@1000010.1110';
$inf_weburl = 'https://ji-touch.ru/';

$inf_folder = 'genxmlmap';


$inf_adminpanel[1] = array(
    'title'=> 'sitemap.xml',
    'image'=>'',
    'panel'=>'admin/index.php',
    'rights'=>'GSMX'
);

$inf_newtable = array(
    1 => GSMX_DB_SETTINGS . " (
        `property` varchar(300) NOT NULL,
        `value` text,
            PRIMARY KEY (`property`)
    ) ENGINE=InnoDB;",
);

$inf_insertdbrow = array(
    1 => GSMX_DB_SETTINGS . " (`property`, `value`)
    VALUES
        ('lastmod', '')"
);

$inf_droptable = array(
    1 => GSMX_DB_SETTINGS
);
