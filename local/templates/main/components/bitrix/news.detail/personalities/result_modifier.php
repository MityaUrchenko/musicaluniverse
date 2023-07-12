<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult["PROPERTIES"]["AUTHOR"]["VALUE"]) {
    $arSelect = array("ID", "NAME", "PREVIEW_PICTURE", "IBLOCK_ID");
    $arFilter = array("=ID" => $arResult["PROPERTIES"]["AUTHOR"]["VALUE"]);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();

        $arFields["PERSONAL_PHOTO"] = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]);
        $arFields["FULL_NAME"] = $arFields["NAME"];
        $arResult["AUTHOR"] = $arFields;
    }
}

if(empty($arResult["AUTHOR"])) {
    $rsUsers = CUser::GetList(array('id'), array('asc'), array("ID" => $arResult["CREATED_BY"]));
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
}

foreach ($arResult["PROPERTIES"]["PHOTO"]["VALUE"] as &$picture){
    $picture = CFile::GetFileArray($picture);
}