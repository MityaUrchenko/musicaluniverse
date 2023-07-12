<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

foreach ($arResult["ITEMS"] as $item) {
    $users[] = $item["CREATED_BY"];
}
$users = array_unique($users);

$rsUsers = CUser::GetList(array('id'), array('asc'), array("ID"=>$users));
while($arUser = $rsUsers->fetch()) {
    $arUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
    $arResult["USERS"][$arUser["ID"]] = $arUser;
}
