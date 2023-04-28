<?php
class Sindy
{
    public $i = 0;
    public $count_status_1 = 0;
    public $count_links_insert = 0;

    private $_clear_links = array();
    private $_host = array();
    private $_link = array();
    private $_base = false;
    private $_dirt_links = false;

    private StorageLinks $StorageLinks;

    public static $HIBERNATION = false;

    private $_param = array(
        'extlink_black' => array('jpg', 'jpeg', 'png', 'webp', 'svg', 'bmp', 'gif', 'tiff', 'heic', 'heif', 'mkv', 'mp4', 'webm', '3gp', 'ts', 'm4a', 'flac', 'ogg', 'wmv', 'wav'),
        'mime_white' => array('application/pdf', 'application/xhtml+xml', 'application/xml', 'text/csv', 'text/html', 'text/plain', 'text/xml', 'text/markdown'),
        'mime_white_ultra' => array('application/pdf')
    );

    function __construct($site)
    {
        $this->StorageLinks = new StorageLinks();

        $chunk = $this->_getChunk($site);

        $this->_clear_links[0] = $this->_buildLink($chunk, true);

        $chunk["base"] = $chunk["deep"]["chunks"][0];
        $chunk["deep"] = array(
            "chunks" => $chunk["base"],
            "level" => 1
        );
        $chunk["close"] = '/';
        $chunk["file"] = $chunk["get"] = NULL;

        $this->_host = $chunk;

        libxml_use_internal_errors(true);

        self::out('create host - ' . print_r($chunk, true) . PHP_EOL);
    }

    function __destruct()
    {
        libxml_use_internal_errors(false);
        if (!self::$HIBERNATION) {
            $this->StorageLinks->buffLinksDrop();
        }
    }

    private function _insertLinks($link, $status = 0)
    {
        $level = 0;
        if ($this->_checkLinks($link, $level) === 0) {
            $this->StorageLinks->insertLinks($link, $level, $status);
            if ($status === 1) $this->count_status_1++;
            $this->count_links_insert++;
        }
        return $this;
    }

    private function _checkLinks($link, &$level = 0)
    {
        $level = $this->_detectLevel($link);
        $column = $this->StorageLinks->checkLinks($link, $level);
        return (int)!($column === false);
    }

    private function _processingResponseCode($opt)
    {
        switch ($opt["code"]) {
            case 301:
            case 302:
            case 307:
                $this->_insertLinks($opt['url']);
                $mes = '<span style="color:red;">skip [' . $opt['code'] . '] - ';
                if (!in_array($opt["r_url"], $this->_clear_links)) {
                    if ($this->_checkLinks($opt["r_url"]) === 0) {
                        array_unshift($this->_clear_links, $opt["r_url"]);
                    }
                }
                $result = false;
                break;

            case 304:
            case 200:
                $result = true;
                break;

            default:
                $this->_insertLinks($opt['url']);
                $mes = '<span style="color:red;">skip [' . $opt['code'] . '] - ';
                $result = false;
                break;
        }
        if (isset($mes)) self::out($mes . $opt['url'] . '</span>' . PHP_EOL);
        return $result;
    }

    private function _checkBlockByExtension($link)
    {
        $lvl = $this->_detectLevel($link);
        $link_cr = explode('//', $link)[1];

        $chunk = explode('/', $link_cr);
        if (count($chunk) === 0 || $lvl === 0) return false;

        $chunk = explode('.', $chunk);
        $ext = array_pop($chunk);
        $pattern = '/(' . implode('|', $this->_param['extlink_black']) . ')/i';

        if (preg_match($pattern, $ext)) {
            $this->_insertLinks($link);
            self::out('<span style="color:red;">skip [' . $ext . '] - ' . $link . '</>' . PHP_EOL);
            return true;
        } else return false;
    }

    private function _checkBlockByMime($link)
    {
        $headers = get_headers($link, true);

        if (is_array($headers) && array_key_exists('Content-Type', $headers)) {
            $type = $headers['Content-Type'];
            if (is_array($type)) $type = array_pop($type);

            $type = explode(';', $type)[0];
            $pattern = '~(' . implode('|', $this->_param['mime_white']) . ')~i';

            if (!preg_match($pattern, $type)) {
                $this->_insertLinks($link);
                self::out('<span style="color:red;">skip [' . $type . '] - ' . $link . '</span>' . PHP_EOL);
                return true;
            } else {
                $pattern = '~(' . implode('|', $this->_param['mime_white_ultra']) . ')~i';
                if (preg_match($pattern, $type)) {
                    self::out('insert ' . $type . ' - <a style="text-decoration:none; color:white;" href=' . $link . '>' . $link . '</a>' . PHP_EOL);
                    $this->_insertLinks($link, 1);
                    return true;
                } else return false;
            }
        }
    }

    private static function _prefixRender($link_chunk_l)
    {
        preg_match_all("/^(\.\/|\/|(\.\.\/)+)?(.*?)$/", $link_chunk_l[0], $out);
        $result = array("base" => trim($out[3][0], "./ \n\r\t\v\0"));

        if (isset($link_chunk_l[1])) {
            $result["prfix"] = array(
                "type" => "//",
                "count" => 1,
                "orig" => empty($link_chunk_l[1]) ? NULL : trim($link_chunk_l[1], ':')
            );
        } else {
            if ($out[1][0] === $out[2][0] && empty($out[2][0])) {
                $result["prfix"] = array(
                    "type" => '',
                    "count" => 1
                );
            } else {
                if (empty($out[2][0])) {
                    $result["prfix"] = array(
                        "type" => $out[1][0],
                        "count" => 1
                    );
                } else {
                    $result["prfix"] = array(
                        "type" => $out[2][0],
                        "count" => count(explode($out[2][0], $out[1][0])) - 1
                    );
                }
            }
            $result["prfix"]["orig"] = NULL;
        }

        return $result;
    }

    private static function _postfixRender($link_chunk_l)
    {
        $base = trim($link_chunk_l[0], "/. \n\r\t\v\0");
        $close = (substr(strrev($link_chunk_l[0]), 0, 1) == '/' && !empty($base)) ? '/' : NULL;

        $result = array(
            "deep" => NULL,
            "file" => NULL,
            "close" => $close
        );

        $chunks = explode('/', $base);
        $chunks_count = count($chunks);

        if (!isset($close)) {
            $file = explode('.', $chunks[$chunks_count - 1]);
            $key = isset($link_chunk_l[1]) ? ($chunks_count > 1) : true;
            if ($key && isset($file[1])) {
                if (isset($file[1])) {
                    if (!empty($file[1])) {
                        $result["file"] = array(
                            "name" => $file[0],
                            "ext" => $file[1]
                        );
                    }
                }
            }
            if (isset($result["file"]) && isset($link_chunk_l[1])) {
                array_pop($chunks);
                $chunks_count = count($chunks);
            }
        }
        if (isset($link_chunk_l[1])) {
            $result["deep"] = array(
                "chunks" => $chunks,
                "level" => $chunks_count
            );
        }
        return $result;
    }

    private static function _removeHash($link)
    {
        $link_chunk = explode('#', $link);
        if (preg_match("/\?/", $link_chunk[0])) {
            return $link;
        } else {
            return $link_chunk[0];
        }
    }

    private static function _getChunk($link)
    {
        $chunk = array();
        $link = self::_removeHash($link);

        $link_chunk_r = explode('?', $link);
        $link_chunk_l = array_reverse(
            explode("//", $link_chunk_r[0])
        );

        $base = self::_prefixRender($link_chunk_l);
        $tip = self::_postfixRender($link_chunk_l);

        $chunk["base"] = $base["base"];
        $chunk["prfix"] = $base["prfix"];
        $chunk["get"] = empty($link_chunk_r[1]) ? NULL :  $link_chunk_r[1];
        $chunk["close"] = $tip["close"];

        $chunk["file"] = $tip["file"];
        $chunk["deep"] = $tip["deep"];
        return $chunk;
    }

    private static function _riseAbove($chunk, $level = 1)
    {
        $level = $level === true ? $chunk["deep"]["level"] : $level;

        for ($i = 0; $i < $level && $chunk["deep"]["level"] > 1; $i++) {
            array_pop($chunk["deep"]["chunks"]);
            $chunk["base"] = implode('/', $chunk["deep"]["chunks"]) . isset($chunk['file']) ? '/' . $chunk["file"]["name"] . $chunk["file"]["ext"] : '';
            $chunk["deep"]["level"] = count($chunk["deep"]["chunks"]);
        }
        return $chunk;
    }

    private static function _goingDown($acceptor, $donor)
    {
        if (empty($acceptor["base"])) {
            return $acceptor;
        }

        $chunks = explode('/', $donor["base"]);
        $over = array();
        if (isset($donor["file"])) {
            $over[0] = array_pop($chunks);
        }

        $acceptor["deep"] = array(
            "chunks" => array_merge($acceptor["deep"]["chunks"], $chunks),
            "level" => count($acceptor["deep"]["chunks"]) + count($chunks)
        );

        $acceptor["base"] = implode('/', array_merge($acceptor["deep"]["chunks"], $over));

        return $acceptor;
    }

    private static function _transfusionChunk($acceptor, $donor, array $keys)
    {
        foreach ($keys as $key) {
            $acceptor[$key] = $donor["$key"];
        }
        return $acceptor;
    }

    private static function _damnBase($page, $url)
    {
        $base = $url;
        if (preg_match("/\<base.+?href=[\'\"](.*?)[\'\"].*?\>/s", $page, $out)) {
            if (!empty($out[1])) {
                $base_dirt = self::_getChunk($out[1]);
                if ($base_dirt["prfix"]["type"] === "//" && !empty($base_dirt["base"])) {
                    $base = $base_dirt;
                    if (!isset($base["prfix"]["origin"])) {
                        $base["prfix"]["orig"] = $url["prfix"]["orig"];
                    }
                }
            }
        }
        $base["close"] = '/';
        $base["file"] = NULL;
        $base["get"] = NULL;

        return $base;
    }

    private function _relativityLink($link, $base)
    {
        switch ($link["prfix"]["type"]) {
            case '/':
                $result = self::_riseAbove($base, true);
                $result = self::_goingDown($result, $link);
                $result = self::_transfusionChunk($result, $link, ["close", "file", "get"]);
                break;

            case '../':
                $result = self::_riseAbove($base, $link["prfix"]["count"]);
                $result = self::_goingDown($result, $link);
                $result = self::_transfusionChunk($result, $link, ["close", "file", "get"]);
                break;

            case './':
                if (!isset($this->_link["close"])) {
                    $result = self::_riseAbove($base, 1);
                }
                $result = self::_goingDown($base, $link);
                $result = self::_transfusionChunk($result, $link, ["close", "file", "get"]);
                break;
            case '':
                $result = self::_goingDown($base, $link);
                $result = self::_transfusionChunk($result, $link, ["close", "file", "get"]);
                break;
            case '//':
                $result = $link;
                if (isset($result["prfix"]["orig"])) {
                    $result = self::_transfusionChunk($result, $this->_link, ["prfix"]);
                }
                break;

            default:
                $result = false;
                break;
        }

        return $result;
    }

    private function _buildLink($chunk, $forse = false)
    {
        if ($forse) {
            $close = $chunk["deep"]["level"] > 1 ? $chunk["close"] : '/';
            $link = $chunk["prfix"]["orig"] . "://" . $chunk["base"] . (isset($chunk["get"]) || isset($chunk["file"]) ? $chunk["close"] : $close) . (isset($chunk["get"]) ? '?' . $chunk["get"] : '');
        } else {
            if (isset($chunk["prfix"]["orig"]) && isset($chunk["base"])) {
                $link = $chunk["prfix"]["orig"] . "://" . $chunk["base"] . $chunk["close"]  . (isset($chunk["get"]) ? '?' . $chunk["get"] : '');
            } else {
                $link = false;
            }
        }
        return $link;
    }

    private function _prepareLink($link)
    {
        $result = false;
        if (!preg_match('/\/\//', $link)) {
            $match = preg_match_all("/^((https?:)?\/\/)?(" . quotemeta($this->_host["base"]) . "([\/\#\?]|$))?(.*)$/", $link, $shard);
            if ($match && !(!empty($shard[2][0]) && empty($shard[3][0]))) {
                $chunk = self::_getChunk($link);
                $chunk = $this->_relativityLink($chunk, $this->_base);
                $result = $this->_buildLink($chunk);
            }
        }
        return $result;
    }

    public function getDoc($url)
    {

        $this->_link = self::_getChunk($url);

        if ($this->_checkBlockByMime($url)) return false;

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => false,
                CURLOPT_FRESH_CONNECT => true,
                CURLOPT_RETURNTRANSFER => true,
            )
        );

        $page = curl_exec($curl);

        try {
            if (curl_errno($curl)) {
                throw new \Exception('curl_error');
            }

            $curl_inf = array(
                "url" => curl_getinfo($curl)["url"],
                "code" => (int)curl_getinfo($curl)["http_code"],
                "r_url" => curl_getinfo($curl)["redirect_url"],
                "content_type" => explode("/", explode("; ", curl_getinfo($curl)["content_type"])[0])[0],
            );

            if ($this->_processingResponseCode($curl_inf)) {
                self::out('open - <a style="text-decoration:none; color:white;" href=' . $url . '>' . $url . '</a>' . PHP_EOL);
                $this->_insertLinks($url, 1);
                $this->_base = self::_damnBase($page, $this->_link);
                preg_match_all("/\<a.+?\>/is", $page, $dirtout);
                $dirt_links = $dirtout[0];
            } else {
                $dirt_links = false;
            }
        } catch (\Exception $e) {
            self::out('<span style="color:orange">open fail [' . $e->getMessage() . '] - <a style="text-decoration:none;" href=' . $url . '>' . $url . '</a></span>' . PHP_EOL);
            $dirt_links = false;
        } finally {
            curl_close($curl);
        }

        $this->_dirt_links = $dirt_links;
        return $dirt_links;
    }

    function _detectLevel($link)
    {
        preg_match('/\/\/(.+)\??/i', $link, $match);
        $chunks = explode('/', $match[1]);
        return count($chunks) - 1;
    }

    function getLinks($dirt_links)
    {
        $domdoc = new \DOMDocument();

        foreach ($dirt_links as $v) {
            if (preg_match("/href/i", $v)) {
                if (!(bool)preg_match("/href\=.([\w\d]+?(?<!http|https)\:|\"|\')/i", $v)) {
                    $key = $domdoc->loadHTML($v);

                    if (count(libxml_get_errors()) != 0) {
                        libxml_clear_errors();
                        if ($key === false) continue;
                    }

                    if ($domdoc instanceof \DOMDocument) {
                        $_a = $domdoc->getElementsByTagName("a");
                        if ($_a->length > 0) {
                            if ($_a[0]) {
                                $_link = $_a[0]->getAttribute("href");
                                if ($_link) {
                                    $link =  $this->_prepareLink($_link);
                                } else {
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else {
                        Sindy::out('<span style="color:red;">[err #504] - ' . htmlspecialchars($v) . '</span>');
                        continue;
                    }

                    if ($link === false) continue;
                    if ($this->_checkBlockByExtension($link)) continue;
                    if (in_array($link, $this->_clear_links)) continue;
                    if ($this->_checkLinks($link) !== 0) continue;

                    $this->_clear_links[] = $link;
                }
            }
        }
        unset($domdoc);
    }

    public function &getClearLinks()
    {
        return $this->_clear_links;
    }

    public function getClearLinksLvl()
    {
        return $this->StorageLinks->querySelect();
    }

    public function getCountLink()
    {
        return $this->StorageLinks->getCountLink();
    }

    public static function out($mes, $status = 'process')
    {
        echo '|' . json_encode(['status' => $status, 'mes' => $mes]) . '|';
    }

    /* =========================================== */


    function sleep()
    {
        self::$HIBERNATION = true;
        unset($this->StorageLinks);
    }
    function wakeup()
    {
        $this->StorageLinks = new StorageLinks(true);
        self::$HIBERNATION = false;
    }


    /* public static function hello()
    {
        $memcache = new \Memcache;
        $memcache->connect('localhost', 11211);
        $ln = $memcache->get(GSMX);

        $ln->StorageLinks = new StorageLinks(true);
        $ln->HIBERNATION = false;

        $memcache->delete(GSMX);
        return $ln;
    }

    public static function bye(&$ln)
    {
        $ln->HIBERNATION = true;
        unset($ln->StorageLinks);

        $memcache = new \Memcache;
        $memcache->connect('localhost', 11211);
        $result = $memcache->set(GSMX, $ln, false, 0);

        unset($ln);
    } */
}
