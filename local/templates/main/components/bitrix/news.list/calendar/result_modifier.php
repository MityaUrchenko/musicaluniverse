<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */


foreach ($arResult["ITEMS"] as $item){
    $arResult["CALENDAR_DATES"][] = implode(".", array_reverse( explode(".", $item["ACTIVE_FROM"])));
}
$arResult["CALENDAR_DATES"] = array_unique($arResult["CALENDAR_DATES"]);
sort($arResult["CALENDAR_DATES"]);
