<?php
require_once '../../../../maincore.php';
require_once INFUSIONS . "panel_editor/infusion_db.php";
require_once PANEL_EDITOR_MODELS . 'panelEditorModel.php';
$test = $_FILES['img']['type'];
if ($_FILES['img']['type'] == "image/jpeg" || $_FILES['img']['type'] == "image/svg+xml" || $_FILES['img']['type'] == "image/png") {
    preg_match('/([\S]+?).([^\.]+?)$/', $_FILES['img']['name'], $matc);

    $name = \PanelEditor\savePhoto::generate_name($matc[2]);

    switch ($_FILES['img']['type']) {
        case "image/jpeg":
            $size = getimagesize($_FILES['img']['tmp_name']);
            if ($settings['photo_max_w'] < $size[0] || $settings['photo_max_h'] < $size[1]) {
                $sizeOut = \PanelEditor\savePhoto::num_size(['h' => $settings['photo_max_h'], 'w' => $settings['photo_max_w']], ['h' => $size[1], 'w' => $size[0]]);
                \PanelEditor\savePhoto::imageresize(IMAGES . 'timeDir/' . $name, $_FILES['img']['tmp_name'], $sizeOut['w'], $sizeOut['h']) ? IMAGES . 'timeDir/' . $name : 'false';
                echo '/images/timeDir/' . $name;
            } else {
                \PanelEditor\savePhoto::imageresize(IMAGES . 'timeDir/' . $name, $_FILES['img']['tmp_name'], $size[0], $size[1]) ? IMAGES . 'timeDir/' . $name : 'false';
                echo '/images/timeDir/' . $name;
            }
            break;
        case "image/png":
            rename($_FILES['img']['tmp_name'], IMAGES . 'timeDir/' . $name);
            echo '/images/timeDir/' . $name;
            break;
        case "image/svg+xml":
            $svg = file_get_contents($_FILES['img']['tmp_name']);
            //производим удаление ненужных стилей для смены цвета и тому подобное
            file_put_contents(IMAGES . 'timeDir/' . $name, $svg);
            echo '/images/timeDir/' . $name;
            break;
    }


    chmod(realpath(IMAGES . 'timeDir/' . $name), 0644);
}
