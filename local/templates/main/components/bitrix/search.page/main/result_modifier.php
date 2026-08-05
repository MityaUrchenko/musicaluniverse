<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

if (!Loader::includeModule('iblock')) {
    return;
}

// === Подгрузка PREVIEW_PICTURE и доп. данных элементов ===
$itemsIds = [];
$iblockIdsFromResults = [];

foreach ($arResult["SEARCH"] as $k => $v) {
    if (!empty($v["ITEM_ID"]) && (!isset($v["MODULE_ID"]) || $v["MODULE_ID"] === "iblock")) {
        $itemsIds[$k] = $v["ITEM_ID"];
        if (!empty($v["PARAM2"])) {
            $iblockIdsFromResults[(int)$v["PARAM2"]] = true;
        }
    }
}

$itemsData = [];
if (!empty($itemsIds)) {
    $arRes = \CIBlockElement::GetList(
        ['ACTIVE_FROM' => 'ASC'],
        [
            'ID' => array_values($itemsIds),
            'ACTIVE' => 'Y'
        ],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE', 'IBLOCK_NAME', 'DATE_ACTIVE_FROM', 'DATE_CREATE']
    );

    while ($item = $arRes->Fetch()) {
        $itemsData[$item["ID"]] = $item;
        $iblockIdsFromResults[(int)$item["IBLOCK_ID"]] = true;
    }

    foreach ($arResult["SEARCH"] as $k => &$v) {
        if (isset($itemsData[$v["ITEM_ID"]])) {
            $data = $itemsData[$v["ITEM_ID"]];
            $v["PREVIEW_PICTURE"] = CFile::GetFileArray($data["PREVIEW_PICTURE"]);
            $v["IBLOCK_ID"] = $data["IBLOCK_ID"];
            $v["IBLOCK_NAME"] = $data["IBLOCK_NAME"] ?: ($v["PARAM1"] ?? "Результаты поиска");
            if (empty($v["DATE_CHANGE"]) && !empty($data["DATE_ACTIVE_FROM"])) {
                $v["DATE_CHANGE"] = $data["DATE_ACTIVE_FROM"];
            }
            if (empty($v["DATE_CREATE"]) && !empty($data["DATE_CREATE"])) {
                $v["DATE_CREATE"] = $data["DATE_CREATE"];
            }
        }
    }
    unset($v);
}

// === Список инфоблоков и их свойств для фильтра ===
$arResult["FILTER_IBLOCKS"] = [];

$iblockIds = [];
if (!empty($arParams["arrFILTER_iblock_content"]) && is_array($arParams["arrFILTER_iblock_content"])) {
    foreach ($arParams["arrFILTER_iblock_content"] as $id) {
        if ($id !== "all" && (int)$id > 0) {
            $iblockIds[] = (int)$id;
        }
    }
}

if (empty($iblockIds) && !empty($iblockIdsFromResults)) {
    $iblockIds = array_keys($iblockIdsFromResults);
}

if (empty($iblockIds)) {
    $rsIBlocks = CIBlock::GetList(
        ["SORT" => "ASC", "NAME" => "ASC"],
        ["ACTIVE" => "Y", "TYPE" => "content", "CHECK_PERMISSIONS" => "N"]
    );
    while ($ib = $rsIBlocks->Fetch()) {
        $iblockIds[] = (int)$ib["ID"];
    }
}

$iblockIds = array_unique($iblockIds);

if (!empty($iblockIds)) {
    $rsIBlocks = CIBlock::GetList(
        ["SORT" => "ASC", "NAME" => "ASC"],
        ["ID" => $iblockIds, "ACTIVE" => "Y", "CHECK_PERMISSIONS" => "N"]
    );

    $iblocks = [];
    while ($ib = $rsIBlocks->Fetch()) {
        $iblocks[(int)$ib["ID"]] = [
            "ID"   => (int)$ib["ID"],
            "NAME" => $ib["NAME"],
            "CODE" => $ib["CODE"],
            "PROPERTIES" => [],
        ];
    }

    foreach ($iblocks as $iblockId => &$iblock) {
        $rsProps = CIBlockProperty::GetList(
            ["SORT" => "ASC", "NAME" => "ASC"],
            [
                "ACTIVE"    => "Y",
                "IBLOCK_ID" => $iblockId,
                "FILTRABLE" => "Y"
            ]
        );

        while ($prop = $rsProps->Fetch()) {
            if (in_array($prop["CODE"], ["", "CML2_LINK", "CML2_ATTRIBUTES"], true)) {
                continue;
            }

            $property = [
                "ID"            => (int)$prop["ID"],
                "CODE"          => $prop["CODE"],
                "NAME"          => $prop["NAME"],
                "PROPERTY_TYPE" => $prop["PROPERTY_TYPE"],
                "USER_TYPE"     => $prop["USER_TYPE"],
                "MULTIPLE"      => $prop["MULTIPLE"],
                "LIST_TYPE"     => $prop["LIST_TYPE"],
                "VALUES"        => [],
            ];

            if ($prop["PROPERTY_TYPE"] === "L") {
                $rsEnum = CIBlockPropertyEnum::GetList(
                    ["SORT" => "ASC", "VALUE" => "ASC"],
                    ["PROPERTY_ID" => $prop["ID"]]
                );
                while ($enum = $rsEnum->Fetch()) {
                    $property["VALUES"][] = [
                        "ID"     => (int)$enum["ID"],
                        "VALUE"  => $enum["VALUE"],
                        "XML_ID" => $enum["XML_ID"],
                    ];
                }
            }

            $iblock["PROPERTIES"][] = $property;
        }
    }
    unset($iblock);

    $arResult["FILTER_IBLOCKS"] = array_values($iblocks);
}

// Текущие значения фильтров
$arResult["CURRENT_FILTER"] = [
    "IBLOCKS"   => [],
    "PROPS"     => [],
    "DATE_FROM" => "",
    "DATE_TO"   => "",
];

if (!empty($_REQUEST["filter_iblock"]) && is_array($_REQUEST["filter_iblock"])) {
    $arResult["CURRENT_FILTER"]["IBLOCKS"] = array_map("intval", $_REQUEST["filter_iblock"]);
}

if (!empty($_REQUEST["filter_prop"]) && is_array($_REQUEST["filter_prop"])) {
    $arResult["CURRENT_FILTER"]["PROPS"] = $_REQUEST["filter_prop"];
}

if (!empty($_REQUEST["filter_date_from"])) {
    $arResult["CURRENT_FILTER"]["DATE_FROM"] = htmlspecialcharsbx($_REQUEST["filter_date_from"]);
}
if (!empty($_REQUEST["filter_date_to"])) {
    $arResult["CURRENT_FILTER"]["DATE_TO"] = htmlspecialcharsbx($_REQUEST["filter_date_to"]);
}
