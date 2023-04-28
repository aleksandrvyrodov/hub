<?php

class StorageLinks
{
    private $PDO;
    private $PDOPrepare = array();

    private $db_file;

    public function __construct($wakeup = false)
    {
        $this->db_file = realpath(__DIR__ . '/../') . '/tmp/link_db.sq3';
        // if (!$wakeup && file_exists($this->db_file)) unlink($this->db_file);

        $this->buffLinks();
    }

    public function buffLinks()
    {
        $PDO = new \PDO(
            'sqlite:' . $this->db_file,
            null,
            null,
            array(
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            )
        );

        // if (!$this->HIBERNATION) {
            $PDO->exec(
                "CREATE TABLE IF NOT EXISTS `links` (
                `link` TEXT,
                `level` INTEGER,
                `status` INTEGER)"
            );
        // }

        $this->PDO = $PDO;
        return $this;
    }

    public function buffLinksDrop()
    {
        foreach ($this->PDOPrepare as $stmt) {
            if ($stmt instanceof \PDOStatement) $stmt->closeCursor();
        }
        // ! $this->PDO->exec("DROP TABLE `links`");
        $this->PDO = null;
        unset($this->PDO);
    }

    public function insertLinks($link, $level, $status)
    {
        if (!(array_key_exists('insert', $this->PDOPrepare) && $this->PDOPrepare['insert'] instanceof \PDOStatement)) {
            $this->PDOPrepare['insert'] = $this->PDO->prepare("INSERT INTO `links` (`link`, `level`, `status`) VALUES (?,?,?)");
        }
        $this->PDOPrepare['insert']->execute([$link, $level, $status]);
    }

    public function checkLinks($link, $level){
        if (!(array_key_exists('check', $this->PDOPrepare) && $this->PDOPrepare['check'] instanceof \PDOStatement)) {
            $this->PDOPrepare['check'] = $this->PDO->prepare("SELECT `status` FROM `links` WHERE `level` = ? AND `link` = ? LIMIT 1");
        }
        $this->PDOPrepare['check']->execute([$level, $link]);
        return $this->PDOPrepare['check']->fetchColumn();
    }

    public function querySelect()
    {
        if (!isset($this->query_select))
            $this->query_select = $this->PDO->query("SELECT * FROM `links` WHERE `status` = 1 ORDER BY `level` ASC, `link` ASC");
        return $this->query_select->fetch(\PDO::FETCH_ASSOC);
    }

    public function getCountLink()
    {
        $res = $this->PDO->query("SELECT count(*) FROM `links` WHERE `status` = 1 ORDER BY `level` ASC, `link` ASC");
        return $res->fetchColumn();
    }
}
