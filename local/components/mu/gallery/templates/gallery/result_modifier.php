<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>&$arProperty) {
    foreach($arProperty["FILE_VALUE"] as $fid=>&$arFile) {
        if(empty($arFile)) continue;
        $arFile["SRC_MINI"] = CFile::ResizeImageGet($arFile, Array("width" => 600, "height" => 600))["src"];
    }
}