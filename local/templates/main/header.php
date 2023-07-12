<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");
CJSCore::Init(array("fx"));

\Bitrix\Main\UI\Extension::load("ui.bootstrap4");
\Bitrix\Main\UI\Extension::load("jquery");
use \Bitrix\Main\Page\Asset;

$curPage = $APPLICATION->GetCurPage(true);
$curDir = $APPLICATION->GetCurDir();
define("IBLOCK_ID_ARTICLES", 5);




if($_GET["country"] == "all") {
    $_SESSION["countryFilter"] = "";
}else if($_GET["country"]) {
    $_SESSION["countryFilter"] = $_GET["country"];
}
$countryFilter = ["=PROPERTY_COUNTRY"=>$_SESSION["countryFilter"]];

global $getCountryFlagPath;
$getCountryFlagPath = fn($countryCode) => SITE_TEMPLATE_PATH . "/vendors/flags/" . strtolower($countryCode) . ".webp";

global $countries;
$countries = file_get_contents($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/vendors/countries.json");
$countries = (array) json_decode($countries);

?><!DOCTYPE html>
<html xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
	<title><?$APPLICATION->ShowTitle()?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR?>favicon.ico" />
	<? $APPLICATION->ShowHead(); ?>

    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH . "/vendors/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css"?>">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH . "/vendors/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css"?>">
    <script src="<?= SITE_TEMPLATE_PATH . "/vendors/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"?>"></script>

    <script src="<?= SITE_TEMPLATE_PATH . "/script.js"?>"></script>
    <script src="<?= SITE_TEMPLATE_PATH?>/vendors/bootstrap/popper.min.js"></script>
    <script src="<?= SITE_TEMPLATE_PATH?>/vendors/bootstrap/bootstrap.min.js"></script>

    <?if(explode("/",$curDir)[1] != "personal" && count(explode("/",$curDir))>3){?>
        <link href="<?= SITE_TEMPLATE_PATH?>/assets/css/app.css" rel="stylesheet">
    <?}?>
</head>
<body class="d-flex flex-column">
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="wrapper d-flex flex-column" id="bx_eshop_wrap">
    <header class="">
        <div class="container">
            <!--region bx-header-->
            <div class="d-flex align-items-stretch flex-column flex-lg-row position-relative">
                <div class="d-flex align-items-center justify-content-lg-center justify-content-between">
                    <a class="company-logo" href="<?=SITE_DIR?>">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR."include/company_logo.php"),
                            false
                        );?>
                    </a>
                    <div class="d-lg-none">
                        <a id="main-menu-btn" href="#">
                            <img src="<?= SITE_TEMPLATE_PATH?>/images/triline.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="d-none d-lg-flex pl-lg-5 mt-4 mb-3 w-100 align-items-stretch flex-column flex-row position-relative">
                    <div class="pl-lg-3 w-100 align-items-start flex-column">
                        <div class="d-flex w-100 align-items-center justify-content-lg-between flex-column flex-lg-row">

                            <div class="header-phone mr-3 mb-3 mb-lg-0 d-none d-lg-flex">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/phone.php"
                                    ),
                                    false
                                );?>
                            </div>

                            <div class="search-container ml-lg-auto mr-lg-3 mb-3 mb-lg-0">
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:search.title",
                                    "main_search",
                                    array(
                                        "NUM_CATEGORIES" => "1",
                                        "TOP_COUNT" => "5",
                                        "CHECK_DATES" => "N",
                                        "SHOW_OTHERS" => "N",
                                        "PAGE" => SITE_DIR."search/",
                                        "CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
                                        "CATEGORY_0" => array(
                                            0 => "no",
                                        ),
                                        "CATEGORY_0_iblock_catalog" => array(
                                            0 => "all",
                                        ),
                                        "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
                                        "SHOW_INPUT" => "Y",
                                        "INPUT_ID" => "title-search-input",
                                        "CONTAINER_ID" => "search",
                                        "PRICE_CODE" => array(
                                        ),
                                        "SHOW_PREVIEW" => "Y",
                                        "PREVIEW_WIDTH" => "75",
                                        "PREVIEW_HEIGHT" => "75",
                                        "CONVERT_CURRENCY" => "Y",
                                        "COMPONENT_TEMPLATE" => "main_search",
                                        "ORDER" => "rank",
                                        "USE_LANGUAGE_GUESS" => "Y",
                                        "TEMPLATE_THEME" => "blue",
                                        "PRICE_VAT_INCLUDE" => "Y",
                                        "PREVIEW_TRUNCATE_LEN" => "",
                                        "CURRENCY_ID" => "RUB"
                                    ),
                                    false
                                );?>
                            </div>
                            <div class="dropdown ml-lg-3 mb-3 mb-lg-0">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?=$countries[$_SESSION["countryFilter"]]?:"Все страны и города";?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <?
                                    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                                    $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
                                    $uri->addParams(["country"=>"all"]);
                                    ?>
                                    <a class="dropdown-item" href="<?=$uri?>">Все</a>
                                    <?
                                    foreach ($countries as $key => $country) {
                                        $uri->addParams(["country"=>$key]);
                                        ?>
                                        <a class="dropdown-item" href="<?=$uri?>"><?=$country?></a>
                                    <?}?>
                                </div>
                            </div>
                            <a href="/login" class="btn btn-primary ml-5 header-btn d-none">Войти</a>
                        </div>
                        <div class="w-100 mt-2 d-flex align-items-center">
                            <div class="menu-container w-100">
                                <?$APPLICATION->IncludeComponent("bitrix:menu", "main_menu", Array(
                                    "ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
                                    "MENU_CACHE_TYPE" => "A",	// Тип кеширования
                                    "MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
                                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                                    "MENU_THEME" => "site",	// Тема меню
                                    "CACHE_SELECTED_ITEMS" => "N",
                                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                                    "MAX_LEVEL" => "3",	// Уровень вложенности меню
                                    "USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                                    "COMPONENT_TEMPLATE" => "bootstrap_v4"
                                ),
                                                                 false
                                );?>
                            </div>
                            <!-- <a href="#" class="btn btn-secondary ml-5 ml-lg-auto header-btn d-none d-lg-block">Rus</a> -->
                        </div>
                    </div>
                </div>
            </div>
            <!--endregion-->
        </div>
    </header>

    <div class="workarea <?=$curDir=="/"?"main-page":"";?> pt-4 pb-5">

        <?if(explode("/",$curDir)[1] == "personal" || ($curDir != "/" && count(explode("/",$curDir))<4)){?>
            <div class="container">
                <h1 class="header"><?$APPLICATION->ShowTitle(false)?></h1>
        <?}?>
