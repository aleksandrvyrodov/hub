
<?php
/* ================================================================ */
class RenameMe
{
    public function import()
    {
        $Docx = new Docx(false);

        $data = array(
            'datetime'    => $_POST['date'],
            'description' => null,
            'category'    => $_POST['in_cat'],
            'title'       => $_POST['title'],
            'url'         => $Docx->translit($_POST['title']),
            'visibility'  => 1,
            'access'      => 0,
        );


        $status = $Docx->importDataWithFile($_POST['link'], $data);

        if (isset($status['status'])) {
            if ((int)$_POST['preview']) $Docx->priviewByImagick($status);
            echo json_encode($status);
        } else {
            echo json_encode(array('status' => 'err_GEN'));
        }
    }
}
