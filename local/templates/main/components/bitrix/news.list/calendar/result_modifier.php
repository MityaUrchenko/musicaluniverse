<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach ($arResult["ITEMS"] as $item){
    if(!$item["ACTIVE_FROM"]) continue;
    $arResult["CALENDAR_DATES"][] = implode(".", array_reverse(explode(".", $item["ACTIVE_FROM"])));
}
$arResult["CALENDAR_DATES"] = array_unique($arResult["CALENDAR_DATES"]);
sort($arResult["CALENDAR_DATES"]);
