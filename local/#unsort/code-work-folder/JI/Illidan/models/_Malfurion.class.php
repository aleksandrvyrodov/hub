<?php
class Malfurion
{
    const DATE_m = '12';
    const DATE_d = '28';
    const DATE_H = '12';
    const DATE_i = '59';
    const DATE_s = '59';

    const M_SINGLE        = 2;
    const M_SINGLE_YEAR   = 3;
    const M_SINGLE_TO     = 5;
    const M_MULTIPLE      = 7;
    const M_MULTIPLE_FROM = 11;

    private $CAT_FROM;
    private $CAT_TO;
    private $DATE;
    private $DATE_MOD;
    private $DATE_EX;
    private $PREVIEW;

    private $method;
    static public $send_lock;

    private $CAT_FROM_CURRENT = 0;
    private $CAT_TO_CURRENT   = 0;
    private $LOOP_GET_ALL     = 0;
    private $LOOP_SEND_ALL    = 0;
    private $LOOP_GET         = 0;
    private $LOOP_SEND        = 0;


    public function __construct($illidan)
    {
        $this->CAT_FROM  = explode('.', $_POST['cat_from']);
        $this->CAT_TO    = explode('.', $_POST['cat_to']);
        $this->DATE_EX   = isset($_POST['date_ex']) ? $_POST['date_ex'] : array();
        $this->DATE_MOD  = explode('.', $_POST['date']);
        $this->PREVIEW   = isset($_POST['preview']) ? 1 : 0;
        $this->illidan   = $illidan;

        // $this->detectMethod();
    }

    public function detectMethod()
    {

        $count_cf = count($this->CAT_FROM);
        $count_ct = count($this->CAT_TO);
        $count_dy = count($this->DATE_MOD);

        if ((!(bool)$count_cf && (bool)$count_ct && (bool)$count_dy)) {
            throw new Exception('E-METHOD:1');
        }

        if ($count_cf == $count_ct && $count_ct == $count_dy) {
            if ($count_cf == 1) {
                $this->method = self::M_SINGLE;
            } else {
                $this->method = self::M_MULTIPLE;
            }
        } else {
            if ($count_cf == $count_ct && $count_dy == 1) {
                $this->method = self::M_SINGLE_YEAR;
            } elseif ($count_cf == $count_dy && $count_ct == 1) {
                $this->method = self::M_SINGLE_TO;
            } elseif ($count_ct == $count_dy && $count_dy == 1 && $count_cf > 1) {
                $this->method = self::M_MULTIPLE_FROM;
            } else {
                throw new Exception('E-METHOD:2');
            }
        }
        return $this->getMethod();
    }

    public function getMethod()
    {
        return $this->method;
    }
    public function printMethod()
    {
        switch ($this->method) {
            case self::M_SINGLE:
                echo 'M_SINGLE', PHP_EOL;
                break;
            case self::M_SINGLE_YEAR:
                echo 'M_SINGLE_YEAR', PHP_EOL;
                break;
            case self::M_SINGLE_TO:
                echo 'M_SINGLE_TO', PHP_EOL;
                break;
            case self::M_MULTIPLE_FROM:
                echo 'M_MULTIPLE_FROM', PHP_EOL;
                break;
            case self::M_MULTIPLE:
                echo 'M_MULTIPLE', PHP_EOL;
                break;
            default:
                echo 'M-ERROR', PHP_EOL;
                break;
        }

        return $this->getMethod();
    }

    private function createDate($mod)
    {
        $mod_sw = false;
        switch ($mod) {
            case 'now':
                $this->DATE = new DateTime('NOW');
                break;
            case 'cl-x':
                $mod_sw = '0001';
            case 'cl':
                $mod_sw = $mod_sw === false ? '0000' : $mod_sw;
            default:
                $mod_sw = $mod_sw === false ? $mod : $mod_sw;
                if (preg_match('/(\d{4})(-(\d{2})(-(\d{2})(T(\d{2})(:(\d{2}))?)?)?)?/', $mod_sw, $chunk)) {
                    $md = isset($this->DATE_EX['month_day']) ? explode('-', $this->DATE_EX['month_day']) : array();
                    $t = isset($this->DATE_EX['time']) ? explode(':', $this->DATE_EX['time']) : array();
                    $date_ex = array(
                        'm' => isset($md[0]) ? $md[0] : NULL,
                        'd' => isset($md[1]) ? $md[1] : NULL,
                        'H' => isset($t[0]) ? $t[0] : NULL,
                        'i' => isset($t[1]) ? $t[1] : NULL,
                    );

                    $Y = $chunk[1];
                    if ((int)$Y < 1971 && (int)$Y > 10) throw new Exception('E-DATE:Y');

                    $m = isset($chunk[3]) ? $chunk[3] : (!empty($date_ex['m']) ? $date_ex['m'] : self::DATE_m);
                    if ((int)$m > 12 || (int)$m < 1) throw new Exception('E-DATE:m');

                    $d = isset($chunk[5]) ? $chunk[5] : (!empty($date_ex['d']) ? $date_ex['d'] : self::DATE_d);
                    if ((int)$d > 31 || (int)$d < 1) throw new Exception('E-DATE:d');

                    $H = isset($chunk[7]) ? $chunk[7] : (!empty($date_ex['H']) ? $date_ex['H'] : self::DATE_H);
                    if ((int)$H > 23 || (int)$H < 0) throw new Exception('E-DATE:H');

                    $i = isset($chunk[9]) ? $chunk[9] : (!empty($date_ex['i']) ? $date_ex['i'] : self::DATE_i);
                    if ((int)$i > 59 || (int)$i < 0) throw new Exception('E-DATE:i');
                    $s = self::DATE_s;

                    $YmdHis = "{$Y}-{$m}-{$d}T{$H}:{$i}:{$s}";

                    $this->DATE = strtotime($YmdHis) > time() ? new DateTime('NOW') : new DateTime($YmdHis);
                } else {
                    throw new Exception('E-DATE:0');
                }
                break;
        }

        return $this->DATE;
    }

    private function getDateFormat($a)
    {
        $case = (int)$this->DATE->format('Y');
        if ($case >= 1971) {
            $date_format = $this->DATE->format('Y-m-d H:i:s');
            $this->DATE->modify('-1 seconds');
        } elseif ($case == 0) {
            $date = 'NOW';
            preg_match_all('/\d{4}/', $a->nodeValue, $all);
            $all = array_reverse($all[0]);

            for ($i = 0; $i < count($all); $i++) {
                if ((int)$all[$i] <= 2020 && (int)$all[$i] >= 1971) {
                    $date = $all[$i] . '-' . $this->DATE->format('m-d') . 'T' . $this->DATE->format("H:i:s");
                    break;
                }
            }
            $date = new DateTime($date);
            $date_format = $date->format('Y-m-d H:i:s');
            $this->DATE->modify('-1 seconds');
        } elseif ($case == 1) {
            $date = 'NOW';
            preg_match('/((\d{1}|\d{2})\s*?(янв|фев|март|апр|ма|июн|июл|авг|сен|окт|ноя|дек).*?\s*?(\d{4}|\d{2}))|(((\d{1}|\d{2})\.(0?1|0?2|0?3|0?4|0?5|0?6|0?7|0?8|0?9|10|11|12)\.(\d{4}|\d{2})))/i', $a->nodeValue, $all);

            if ((int)$all[2] && $all[3] && (int)$all[4]) {
                $train = array(
                    'дек' => '12', 'янв' => '1', 'фев' => '2',
                    'март' => '3', 'апр' => '4', 'ма' => '5',
                    'июн' => '6', 'июл' => '7', 'авг' => '8',
                    'сен' => '9', 'окт' => '10', 'ноя' => '11',
                );
                $m_tmp = strtr($all[3], $train);
                $m = empty($m_tmp) ? $all[3] : $m_tmp;
                if (((int)$all[4] >= 1971) && ((int)$m <= 12 && (int)$m >= 1) && ((int)$all[2] >= 1 && (int)$all[2] <= 31)) {
                    $Y = $all[4];
                    $m = (int)$m < 10 ? "0$m" : $m;
                    $d = (int)$all[2];
                    $d = (int)$d < 10 ? "0{$d}" : $d;
                    $date = "{$Y}-{$m}-{$d}T" . self::DATE_H . ":" . self::DATE_i . ":" . self::DATE_s;
                } else {
                    $date = date('Y') . '-' . $this->DATE->format('m-d') . 'T' . $this->DATE->format("H:i:s");
                }
            } else {
                $date = date('Y') . '-' . $this->DATE->format('m-d') . 'T' . $this->DATE->format("H:i:s");
            }

            $date = new DateTime($date);
            $date_format = $date->format('Y-m-d H:i:s');
            $this->DATE->modify('-1 seconds');
        }
        return $date_format;
    }

    public function getRaportError()
    {
        return $this->CAT_FROM_CURRENT . '->' . $this->CAT_TO_CURRENT . PHP_EOL . 'ALL GET - ' . $this->LOOP_GET_ALL . ' | ALL SEND - '  . $this->LOOP_SEND_ALL . PHP_EOL . 'GET - ' . $this->LOOP_GET . ' | SEND - '  . $this->LOOP_SEND . PHP_EOL;
    }

    public function getRaportEnd()
    {
        return '--- < GET-ALL - ' . $this->LOOP_GET_ALL . ' | SEND-ALL - ' . $this->LOOP_SEND_ALL . ' --- ---' . PHP_EOL;
    }

    public function getRaportChunkStart()
    {
        return '--- --- start > FROM - ' . $this->CAT_FROM_CURRENT . ' | TO - ' . $this->CAT_TO_CURRENT . ' --- ---' . PHP_EOL;
    }

    public function getRaportChunkEnd()
    {
        $mes =  '--- --- end < FROM - ' . $this->CAT_FROM_CURRENT . ' | TO - ' . $this->CAT_TO_CURRENT . ' || GET - ' . $this->LOOP_GET . ' | SEND - ' . $this->LOOP_SEND . ' --- ---' . PHP_EOL;
        $this->clearLoopInfo();
        return $mes;
    }

    public function getDate()
    {
        return $this->DATE;
    }

    public function printDate()
    {
        echo $this->getDate()->format('Y-m-d H:i:s'), PHP_EOL;
        return $this->getDate();
    }

    private function zipRunWork()
    {
        echo $this->getRaportChunkStart(), PHP_EOL;
        $data = $this->getData();
        if (self::$send_lock) {
            vd($data);
        } else {
            $this->sendData($data);
        }
        echo $this->getRaportChunkEnd(), PHP_EOL;
    }

    public function runWork()
    {
        switch ($this->method) {
            case self::M_SINGLE:
                $this->CAT_TO_CURRENT = $this->CAT_TO[0];
                $this->CAT_FROM_CURRENT = $this->CAT_FROM[0];
                $this->createDate($this->DATE_MOD[0]);
                $this->zipRunWork();
                break;
            case self::M_SINGLE_YEAR:
                for ($i = 0; $i < count($this->CAT_FROM); $i++) {
                    $this->CAT_TO_CURRENT = $this->CAT_TO[$i];
                    $this->CAT_FROM_CURRENT = $this->CAT_FROM[$i];
                    $this->createDate($this->DATE_MOD[0]);
                    $this->zipRunWork();
                }
                break;
            case self::M_SINGLE_TO:
                for ($i = 0; $i < count($this->CAT_FROM); $i++) {
                    $this->CAT_TO_CURRENT = $this->CAT_TO[0];
                    $this->CAT_FROM_CURRENT = $this->CAT_FROM[$i];
                    $this->createDate($this->DATE_MOD[$i]);
                    $this->zipRunWork();
                }
                break;
            case self::M_MULTIPLE_FROM:
                for ($i = 0; $i < count($this->CAT_FROM); $i++) {
                    $this->CAT_TO_CURRENT = $this->CAT_TO[0];
                    $this->CAT_FROM_CURRENT = $this->CAT_FROM[$i];
                    $this->createDate($this->DATE_MOD[0]);
                    $this->zipRunWork();
                }
                break;
            case self::M_MULTIPLE:
                for ($i = 0; $i < count($this->CAT_FROM); $i++) {
                    $this->CAT_TO_CURRENT = $this->CAT_TO[$i];
                    $this->CAT_FROM_CURRENT = $this->CAT_FROM[$i];
                    $this->createDate($this->DATE_MOD[$i]);
                    $this->zipRunWork();
                }
                break;

            default:
                throw new Exception('E-GEN:0');
                break;
        }
        echo $this->getRaportEnd();
    }

    private function clearLoopInfo()
    {
        $this->LOOP_GET  =  $this->LOOP_SEND = 0;
    }

    private function connectDB()
    {
        $db_host = "sinfoji.beget.tech";
        $db_user = "sinfoji_mostrel";
        $db_pass = "Ee&FdZ8M";
        $db_name = $db_user;

        return new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, array(
            PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC
        ));
    }

    private function getDataDB($PDO)
    {
        $db_prefix = "fusion5vn4q_";
        $PDOS = $PDO->query(
            "SELECT *
            FROM {$db_prefix}custom_pages
            WHERE `page_id` = '{$this->CAT_FROM_CURRENT}'"
        );
        return $PDOS->fetch()['page_content'];
    }

    private function getData()
    {
        try {
            $PDO = $this->connectDB();
            $data_raw = $this->getDataDB($PDO);
            if (empty($data_raw)) {
                echo 'DATA-DB:EMPTY', PHP_EOL;
                return array();
            }
            unset($PDO);
        } catch (PDOException $PDOE) {
            throw new Exception('E-DATABASE');
        }

        try {
            $DOM = new \DOMDocument();
            $DOM->loadHTML('<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"></head><body>' . str_replace('\\', '', $data_raw) . '</body></html>');
            $a_list = $DOM->getElementsByTagName('a');
            if (empty($a_list)) {
                echo 'DATA-DOM:EMPTY', PHP_EOL;
                return array();
            }
        } catch (DOMException $DOM) {
            throw new Exception('E-DOM');
        }

        $data = array();
        for ($ii = 0, $ik = 0; $ii < count($a_list); $ii++) {
            $a = $a_list[$ii];
            if (preg_match('/file/', $a->getAttribute('class'))) {
                $this->LOOP_GET++;
                $this->LOOP_GET_ALL++;

                $date_format = $this->getDateFormat($a);

                $data[] = array(
                    'title' => $a->nodeValue,
                    'link' => "https://mo-strelna.ru" . '/' . trim($a->getAttribute('href'), '/'),
                    'date' => $date_format,
                    'preview' => $this->PREVIEW
                );
                // $ik++;
            }
        }

        if (empty($data)) echo 'DATA:EMPTY', PHP_EOL;
        return $data;
    }
    private function sendData($data)
    {
        $defaults = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->illidan,
            CURLOPT_POST => true,
        );
        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        foreach ($data as $send) {
            $send['in_cat'] = $this->CAT_TO_CURRENT;
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($send));
            $res_curl = curl_exec($ch);
            $st_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

            if ($st_code == 200) {
                if ($res_curl !== false) {
                    if (empty($res_curl)) {
                        $this->vdE(array('ERROR' => 'SEND:FAIL-466', 'data' => $send));
                    } else {
                        $mes = json_decode($res_curl, true);
                        if ($mes) {
                            if ($mes['status'] != 'ok') {
                                $this->vdE(array('ERROR' => 'SEND:FAIL-IMPORT-2', 'request' => $mes, 'data' => $send));
                            } else {
                                $this->vd($mes);
                                $this->LOOP_SEND++;
                                $this->LOOP_SEND_ALL++;
                            }
                        } else {
                            $this->vdE(array('ERROR' => 'SEND:FAIL-IMPORT-1', 'request' => $res_curl, 'data' => $send));
                        }
                    }
                } else {
                    $this->vdE(array('ERROR' => 'SEND:FAIL-467', 'data' => $send));
                }
            } else {
                $this->vdE(array('ERROR' => 'SEND:FAIL-470', 'data' => $send));
            }
        }
        curl_close($ch);
    }

    static public function vdE($data)
    {
        echo '<span style="color:red;">', print_r($data, true), '</span>', PHP_EOL;
    }
    private function vd($data)
    {
        echo '<span style="">', print_r($data, true), '</span>', PHP_EOL;
    }
    static public function SendLock()
    {
        self::$send_lock = true;
    }
}
