<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
/* Избранное */
global $APPLICATION;
global $USER;
if($_GET['id'])
{
    $arElements = [];
    if($USER->IsAuthorized()) {
        $arUser = $USER->GetById($USER->GetId())->fetch();
        if(!empty($arUser['UF_FAVORITES'])) {
            $arElements = json_decode($arUser['UF_FAVORITES']);
        }
    }


    if(empty($arElements) && !empty(json_decode($_COOKIE['favorites']))) {
        $arElements = json_decode($_COOKIE['favorites']);
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

    $arElements = array_unique($arElements);
    $_SESSION['favorites'] = $arElements;
    $serializedElements = json_encode($arElements);

    if(empty($arElements)){
        setcookie("favorites", '', time() - 1, "/", $_SERVER['SERVER_NAME'], false);
    } else {
        setcookie("favorites", $serializedElements, time() + 60*60*24*365*10, "/", $_SERVER['SERVER_NAME'], false);
    }

    if($USER->IsAuthorized()) {
        $USER->Update($USER->GetId(), Array("UF_FAVORITES" => $serializedElements));
    }
}
echo json_encode($result);
die();
?>