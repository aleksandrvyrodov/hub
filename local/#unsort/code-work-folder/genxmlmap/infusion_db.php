<?php

if (!defined("IN_FUSION")) {
    die("Access Denied");
}


if (!defined("GSMX")) {
    define("GSMX", "genxmlmap");
}

if (!defined("GSMX_ROOT")) {
    define("GSMX_ROOT", INFUSIONS . GSMX . "/");
}

if (!defined("GSMX_SCRIPTS")) {
    define("GSMX_SCRIPTS", GSMX_ROOT . "admin/scripts/");
}

if (!defined("GSMX_STYLES")) {
    define("GSMX_STYLES", GSMX_ROOT . "admin/styles/");
}

if (!defined("GSMX_MODELS")) {
    define("GSMX_MODELS", GSMX_ROOT . "models/");
}

if (!defined("GSMX_AJAX")) {
    define("GSMX_AJAX", GSMX_ROOT . "admin/ajax/");
}

if (!defined("GSMX_DB_SETTINGS")) {
    define("GSMX_DB_SETTINGS", DB_PREFIX . GSMX . "_settings");
}
