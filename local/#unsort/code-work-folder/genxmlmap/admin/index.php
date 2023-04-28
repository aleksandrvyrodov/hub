<?php
require_once '../../../maincore.php';
if (!checkrights('GSMX') || !defined('iAUTH') || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) {
    redirect('../../../index.php');
}
require_once THEMES . 'templates/admin_header_mce.php';
require_once INFUSIONS . "genxmlmap/infusion_db.php";

$last_mod = dbarray(dbquery("SELECT * FROM `" . GSMX_DB_SETTINGS . "` WHERE `property` = 'lastmod'"));

add_to_head('<link rel="stylesheet" href="' . GSMX_STYLES . 'Vikki.css">');
add_to_footer('<script src="' . GSMX_SCRIPTS . 'Loura.js"></script>');

opentable('Sitemap.xml', 'illidan');
?>

<div class="card card-top-bordered card-border-info">
    <div class="card-header card-header-divider">Генератор sitemap.xml</div>
    <div class="card-body ">
        <div>
            <p>Дата последней генерации: <?= empty($last_mod['value']) ? 'Не сгенерирован' : $last_mod['value'] ?></p>
        </div>
        <div class="position-relative mb-3" id="illidan">
            <div class="counter counter-hide" onclick="this.classList.toggle('counter-hide');">0s</div>
            <pre class="malfurion d-none" id="malfurion">
╔--------------------------------------------------------------------------------------------╗
 ***===░██████╗░███████╗███╗░░██╗██╗░░██╗███╗░░░███╗██╗░░░░░███╗░░░███╗░█████╗░██████╗░===***
 **====██╔════╝░██╔════╝████╗░██║╚██╗██╔╝████╗░████║██║░░░░░████╗░████║██╔══██╗██╔══██╗====**
 *=====██║░░██╗░█████╗░░██╔██╗██║░╚███╔╝░██╔████╔██║██║░░░░░██╔████╔██║███████║██████╔╝======
 ======██║░░╚██╗██╔══╝░░██║╚████║░██╔██╗░██║╚██╔╝██║██║░░░░░██║╚██╔╝██║██╔══██║██╔═══╝░=====*
 **====╚██████╔╝███████╗██║░╚███║██╔╝╚██╗██║░╚═╝░██║███████╗██║░╚═╝░██║██║░░██║██║░░░░░====**
 ***===░╚═════╝░╚══════╝╚═╝░░╚══╝╚═╝░░╚═╝╚═╝░░░░░╚═╝╚══════╝╚═╝░░░░░╚═╝╚═╝░░╚═╝╚═╝░░░░░===***
╚--------------------------------------------------------------------------------------------╝
            </pre>
        </div>

        <div>
            <button id="gen" class="btn btn-primary btn-sm">Генерировать</button>
            <button id="reconnect" class="btn btn-warning btn-sm <? if (!isset($_GET['r'])): ?>d-none<? endif; ?>">~ RECONNECT ~</button>
            <? if (!empty($last_mod['value'])) : ?>
                <button class="btn btn-success btn-sm" onclick="window.open(location.origin+'/sitemap.xml', '_blank' );">sitemap.xml</button>
            <? endif; ?>
        </div>

    </div>
</div>

<?php

closetable();
require_once THEMES . "templates/footer.php";
?>