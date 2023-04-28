<?php

require_once "maincore.php";
require_once THEMES . "templates/header.php";
require_once VIEWS . 'classes/Page.class.php';
require_once CLASSES . "PageController.class.php";


?>

<?php
/* require_once "maincore.php";
require_once THEMES."templates/header.php";
set_title($settings['sitename']);
require_once THEMES."templates/footer.php"; */
?>
<? /*
<style>
    html {}

    body {
        padding: 0;
        margin: 0;
        background-color: #1e1e1e;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    html,
    body {
        height: 100%;
    }

    .cont {
        display: grid;
        grid-template: repeat(4, 150px) / repeat(4, 150px);
        gap: 25px;
        padding: 25px;
        background-color: #ffeb95;
        border-radius: 15px;
    }

    .el {
        background-color: #c5e478;
        border-radius: 10px;
    }

    .el-ect {
        height: 140px;
        width: 140px;
        place-self: center;
    }

    .core {
        height: 100%;
        width: 100%;
        border-radius: 10px;
    }

    .el-ect .core {
        background-color: #80cbc4;
        cursor: pointer;
    }
</style>

<div class="cont" id="game">
    <div class="el el-ect">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
    <div class="el">
        <div class="core"></div>
    </div>
</div>

<script>
    const place = [
        [2, 0, 0, 0],
        [0, 0, 0, 0],
        [0, 0, 0, 0],
        [0, 0, 0, 0]
    ];


    [].map.call(document.querySelectorAll('.el-ect'), el => {

        el.addEventListener('click', detectDirection);
    });

    function detectDirection(e) {
        let {
            x,
            y,
            height,
            width
        } = this.getClientRects()[0];

        let yCoord = Math.round(e.clientY - y - height / 2) * -1,
            xCoord = Math.round(e.clientX - x - width / 2);

        let way = 0;

        if (xCoord != yCoord) {
            if (yCoord > 0 && Math.abs(yCoord) > Math.abs(xCoord)) way = 12;
            if (xCoord > 0 && Math.abs(yCoord) < Math.abs(xCoord)) way = 3;
            if (yCoord < 0 && Math.abs(yCoord) > Math.abs(xCoord)) way = 6;
            if (xCoord < 0 && Math.abs(yCoord) < Math.abs(xCoord)) way = 9;
        }

        console.log(way);
    }

    function switchDirection(way){
        switch (way) {
            case 12:

                break;
            case 3:

                break;
            case 6:

                break;
            case 9:

                break;

            default:
                break;
        }
    }
</script>