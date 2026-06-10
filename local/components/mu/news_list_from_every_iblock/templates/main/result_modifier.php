<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

if($arParams["SORT_BY_IDS"]){
    $itemsOrder = array_reverse($arParams["SORT_BY_IDS"]);
    $itemsOrder = array_flip($itemsOrder);
    foreach ($arResult["ITEMS"] as $item) {
        $itemsOrder[$item["ID"]] = $item;
    }
    $arResult["ITEMS"] = $itemsOrder;
}