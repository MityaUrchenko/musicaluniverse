<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
/* Избранное */
global $APPLICATION;
global $USER;
if($_GET['id'])
{
    $arElements = [];
    if($USER->IsAuthorized()) {
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        if(!empty($arUser['UF_FAVORITES'])) {
            $arElements = unserialize($arUser['UF_FAVORITES']);
        }
    }


    if(empty($arElements) && !empty(unserialize($_COOKIE['favorites']))) {
        $arElements = unserialize($_COOKIE['favorites']);
    }


    if(!in_array($_GET['id'], $arElements))
    {
        $arElements[] = $_GET['id'];
        $result = 1;
    } else {
        $key = array_search($_GET['id'], $arElements);
        unset($arElements[$key]);
        $result = -1;
    }

    if(empty($arElements)){
        setcookie("favorites", '', time() - 1, "/", $_SERVER['SERVER_NAME'], false);
    } else {
        setcookie("favorites", serialize($arElements), time() + 60*60*24*365*10, "/", $_SERVER['SERVER_NAME'], false);
    }

    if($USER->IsAuthorized()) {
        $USER->Update($idUser, Array("UF_FAVORITES" => serialize($arElements)));
    }
}
echo json_encode($result);
die();
?>