<?php

class StorageWorks
{
    static public function reset()
    {

        $memcache = new \Memcache;
        $memcache->connect('localhost', 11211);
        $memcache->delete(GSMX);
        unset($memcache);
    }

    static public function wakeup()
    {

        $memcache = new \Memcache;
        $memcache->connect('localhost', 11211);
        $class = $memcache->get(GSMX);
        if (!$class) {
            throw new Exception('Memcache get');
        }
        $memcache->delete(GSMX);
        $class->wakeup();
        return $class;
    }
    static public function sleep(&$class)
    {
        $class->sleep();
        $memcache = new \Memcache;
        $memcache->connect('localhost', 11211);
        $result = $memcache->set(GSMX, $class, false, 0);

        if (!$result) {
            throw new Exception('Memcache set');
        }

        $class = null;
        unset($class);
    }
}
