<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

/** @var CBitrixComponentTemplate $this */

use Bitrix\Iblock\ElementTable;

foreach ($arResult["IBLOCKS"] as $arIblock) {
    foreach ($arIblock["ITEMS"] as $arItem) {
        $arItemIDs[] = $arItem["ID"];
    }
}
/* список Инфоблоков */
$arRes = ElementTable::getList(
    [
        'filter' => ['ID' => $arItemIDs],
        'order' => [],
        'select' => ['*'],
    ]
);
while ($item = $arRes->fetch()) {
    $item["PREVIEW_PICTURE"] = CFile::GetFileArray($item["PREVIEW_PICTURE"]);
    $items[$item["ID"]] = $item;
}

foreach ($arResult["IBLOCKS"] as &$arIblock) {
    foreach ($arIblock["ITEMS"] as &$arItem) {
        $arItem = $items[$arItem["ID"]];
    }
}
