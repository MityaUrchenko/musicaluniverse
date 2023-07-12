<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IBLOCK_ELEMENT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("IBLOCK_ELEMENT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/photo_detail.gif",
	"CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "gtu",
        "NAME" => GetMessage("SECTION_NAME"),
        "SORT" => 9999,
	),
);

?>