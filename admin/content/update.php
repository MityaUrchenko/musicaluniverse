<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

CModule::IncludeModule("iblock");

$json = file_get_contents('php://input');
$arData = json_decode($json);

if($arData->elementId){
    $arFields = [];

    if($arData->blocked){
        $arFields["ACTIVE"] = $arData->blocked;

        $el = new CIBlockElement;
        if($el->Update($arData->elementId, $arFields)){
            echo json_encode(['success' => true]);
        }else{
            echo json_encode(['success' => false, 'errors' => $GLOBALS["strError"]]);
        }
        die();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';