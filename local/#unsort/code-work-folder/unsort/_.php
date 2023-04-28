<?php

class Foo_usual{
    protected $register_key = 'FOO'; # !important

    public function getKey(){
        echo PHP_EOL . $this->register_key . PHP_EOL;
    }
}

class Bar_usual extends Foo_usual{
    protected $register_key = 'BAR';

    public function getKey(){
       parent::getKey();
    }
}

$Bar_usual = new Bar_usual();
$Bar_usual->getKey();

/* ======================================================= */

class Foo_static{
    protected static $register_key = 'FOO'; # !important

    public function getKey(){
        echo PHP_EOL . self::$register_key . PHP_EOL;
    }
}

class Bar_static extends Foo_static{
    protected static $register_key = 'BAR';

    public function getKey(){
       parent::getKey();
    }
}

$Bar_static = new Bar_static();
$Bar_static->getKey();