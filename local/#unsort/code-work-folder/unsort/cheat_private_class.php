<?php

final class Foo
{
    private $DeceiveMeIfYouCan = false;

    public function __construct()
    {
        throw new Exception("Noup", 1);
    }

    public function CouldYouCheat()
    {
        if ($this->DeceiveMeIfYouCan)
            return $this->DeceiveMeIfYouCan;
        else
            throw new Exception("Noup", 2);
    }
}


try {
    # -\/----------------------------

    $ReflectionClass = new ReflectionClass('Foo');
    $Foo = $ReflectionClass->newInstanceWithoutConstructor();
    $ReflectionProperty = $ReflectionClass->getProperty('DeceiveMeIfYouCan');
    $ReflectionProperty->setAccessible(true);
    $ReflectionProperty->setValue($Foo, true);

    # -/\----------------------------
    if (!isset($Foo) || !($Foo instanceof Foo))
        $Foo = new Foo;

    if ($Foo->CouldYouCheat())
        echo 'Yep';
    else
        throw new Exception("Noup", 3);
} catch (\Throwable $th) {
    echo $th->getMessage() . ' (' . $th->getCode() . ')';
}
