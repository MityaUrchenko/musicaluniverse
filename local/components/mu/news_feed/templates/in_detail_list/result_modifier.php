<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

/** @var CBitrixComponentTemplate $this */


use Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\ElementTable;


foreach($arResult["ITEMS"] as $arItem) {
    $arResult['ITEMS_BY_IBLOCKS'][$arItem['IBLOCK_ID']][] = $arItem;
}

/* список Инфоблоков */
$iblocks = IblockTable::getList(
    [
        'filter' => ['ID' => array_keys($arResult['ITEMS_BY_IBLOCKS'])],
        'order' => [],
        'select' => ['ID','NAME'],
    ]
);
while($iblock = $iblocks->fetch()) {
    $arResult['IBLOCKS'][$iblock['ID']] = $iblock;
}
