<?php
/*
  # /etc/sysconfig/memcached
  PORT="0"
  USER="carolina"
  GROUP="carolina"
  MAXCONN="10240"
  CACHESIZE="2048"
  OPTIONS="-t 8 -s /home/bitrix/ext_www/carolinashop.ru/.mit/souls/socket/memcached.sock -a 0600"
*/

/*
  # bitrix/php_interface/dbconn.php
  define("BX_CACHE_TYPE", "memcache");
  define("BX_CACHE_SID", "s1");
  define("BX_MEMCACHE_HOST", "unix:///var/www/napitkimira/data/souls/socket/memcached.sock");
  define("BX_MEMCACHE_PORT", "0");
*/
?>
<?php
return array(
  'cache' => array(
    'value' => array(
      'type' => 'memcache',
      'memcache' => array(
        'host' => 'unix:///var/www/napitkimira/data/souls/socket/memcached.sock',
        'port' => '0',
      ),
      'sid' => 's1'
    ),
  ),
);
?>
