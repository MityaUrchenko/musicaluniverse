<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$rsUsers = CUser::GetList(array('id'), array('asc'), array("ID" => $arResult["PROPERTIES"]["AUTHOR"]["VALUE"]?:$arResult["CREATED_BY"]));
while ($arUser = $rsUsers->fetch()) {
    $arUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
    $arUser["FULL_NAME"] = implode(
        " ",
        [
            $arUser["NAME"],
            $arUser["LAST_NAME"]
        ]
    );
    $arResult["AUTHOR"] = $arUser;
}

foreach ($arResult["PROPERTIES"]["PHOTO"]["VALUE"] as &$picture){
    $picture = CFile::GetFileArray($picture);
}