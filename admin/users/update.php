<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\UserGroupTable,
    Bitrix\Main\UserTable;


$json = file_get_contents('php://input');
$arData = json_decode($json);


if($arData->userId){
    $arFields = [];

    $params = [
        'filter' => ['USER.ID' => $arData->userId,"<GROUP.C_SORT" => 100],
        'select' => ['GROUP_ID'],
        'order' => ['GROUP.ID' => 'ASC'],
        'limit' => 100
    ];
    $groupsOfUser = UserGroupTable::getList($params);

    //если пользователь админ
    $arUserGroups = [];
    while($arGroupOfUser = $groupsOfUser->fetch()) {
        if($arGroupOfUser['GROUP_ID'] == 1) {
            echo json_encode(['success' => false, 'errors' => ['Невозможно изменить права']]);
            die();
        }
        $arUserGroups[] = $arGroupOfUser;
    }


    if($arData->groupId){
        foreach($arUserGroups as $group) {
            Bitrix\Main\UserGroupTable::delete([
                                                   "USER_ID" => $arData->userId,
                                                   "GROUP_ID" => $group['GROUP_ID'],
                                               ]);
        }
        if($arData->groupId != 2) {
            Bitrix\Main\UserGroupTable::add([
                                                "USER_ID" => $arData->userId,
                                                "GROUP_ID" => $arData->groupId,
                                            ]);
        }
        echo json_encode(['success' => true]);
        die();
    }


    if($arData->userBlocked){
        $block = $arData->userBlocked=="Y"?"N":"Y";
        $arFields["BLOCKED"] = $block;

        $user = new CUser;
        if($user->Update($arData->userId, $arFields)){
            echo json_encode(['success' => true]);
        }else{
            echo json_encode(['success' => false, 'errors' => $GLOBALS["strError"]]);
        }
        die();
    }

}



require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';