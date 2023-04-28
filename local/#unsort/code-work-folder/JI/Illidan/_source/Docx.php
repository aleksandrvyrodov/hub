<?php
/* ================================================================ */
class RenameMe
{
    public function importDataWithFile($file, &$doc)
    {
        $res = array();

        $onlyfilename = array_pop(explode('/', $file));
        $dotchunk = array_reverse(explode('.', $onlyfilename));
        $path = $this->getCurrentPath($doc['category']);

        if (count($dotchunk) === 1 || empty($dotchunk[0])) {
            $ext = '';
        } else {
            $ext = $dotchunk[0];
        }

        \_DB::connectKillDB();

        $name = '';
        $arraySymbol = ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        do {
            while (strlen($name) < 16)
                $name .= $arraySymbol[rand(0, count($arraySymbol) - 1)];
            $name .= '.' . $ext;
        } while (file_exists($path . $name));

        $fp = fopen($path . $name, 'w+');

        $res['new_file'] = $path . $name;
        $res['old_file'] = $file;

        $defaults = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $file,
            CURLOPT_HEADER => 0,
            CURLOPT_TIMEOUT => -1,
            CURLOPT_FILE => $fp,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $file_blob = curl_exec($ch);
        $st_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        $res['res_code'] = $st_code;

        if ($st_code == 200) {
            $res['file_len'] = fwrite($fp, $file_blob);
            fclose($fp);

            if ($res['file_len'] > 1) {
                \_DB::connectDB();
                \_DB::$linkes->begin_transaction();

                if ($this->importData($doc)) {
                    $data['id'] = \_DB::$linkes->insert_id;
                    if ($this->writeFilename($doc['id'], $name)) {
                        $res['status'] = 'ok';
                        \_DB::$linkes->commit();
                        return $res;
                    } else {
                        $res['status'] = 'err_write-filename';
                        \_DB::$linkes->rollback();
                        \unlink($path . $name);
                        return  $res;
                    }
                } else {
                    $res['status'] = 'err_write-data';
                    \_DB::$linkes->rollback();
                    \unlink($path . $name);
                    return $res;
                }
            } else {
                $res['status'] = 'err_empty-byte';
            }
        } else {
            $res['status'] = 'err_connect';
            return $res;
        }
    }

    public function priviewByImagick(&$res = array())
    {
        $chunks = explode('/', $res['new_file']);

        $file = array_pop($chunks);
        $path = implode('/', $chunks);

        $file_ext = trim(strstr($file, '.'), '.');
        $file_name = trim(strstr($file, '.', true), '.');

        $from = $path . '/' . $file;
        $from_root = $_SERVER['DOCUMENT_ROOT'] . '/' . $from;
        $to = DOC_FILE_PREVIEW . $file_name . '.jpeg';
        $to_root = $_SERVER['DOCUMENT_ROOT'] . '/' . $to;

        if (strtoupper($file_ext) != 'PDF') {
            $res['preview'] = '<span style="color:red">[fail_format-file] - ' . strtoupper($file_ext) . '</span>';
            return false;
        }
        if (!file_exists($from_root)) {
            $res['preview'] = '<span style="color:red">[fail_open] - ' . $from_root . '</span>';
            return false;
        }

        try {
            $im = new \Imagick();
            $im->setBackgroundColor('white');
            $im->setResolution(300, 300);

            $im->setColorSpace($im::COLORSPACE_SRGB);
            $im->readImage($from_root . '[0]');

            $im->setImageAlphaChannel($im::ALPHACHANNEL_REMOVE);
            $im->setImageFormat('jpeg');

            $im->mergeImageLayers($im::LAYERMETHOD_FLATTEN);

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

            if ($im->writeImage($to_root) === true) $res['preview'] = '<span style="color:green">' . $to_root . '</span>';
            else $res['preview'] = '<span style="color:red">[fail_write] - ' . $to_root . '</span>';

            $im->clear();
            $im->destroy();
        } catch (\ImagickException $ImE) {
            $res['preview'] = '<span style="color:red">[fail] - ' . $ImE->getMessage() . '</span>';
            $res['exeption'] = array(
                'line' => $ImE->getLine(),
                'trace' => $ImE->getTrace()
            );
        }
    }
}
