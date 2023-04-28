\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('sale');

$obBasket = \Bitrix\Sale\Basket::getList(
    array(
        'select'  => array(
            'FUSER_ID'
        )
    )
);
while($bItem = $obBasket->Fetch()){
    CSaleBasket::DeleteAll(
        $bItem['FUSER_ID'],
        False
    );
}