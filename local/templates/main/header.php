<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . SITE_TEMPLATE_ID . "/header.php");
CJSCore::Init(array("fx"));

\Bitrix\Main\UI\Extension::load("ui.bootstrap4");
// \Bitrix\Main\UI\Extension::load("jquery");
use \Bitrix\Main\Page\Asset;

use Bitrix\Main\UserGroupTable,
    Bitrix\Main\UserTable;


$curPage = $APPLICATION->GetCurPage(true);
$curDir = $APPLICATION->GetCurDir();
define("IBLOCK_ID_ARTICLES", 5);
define("IBLOCK_ID_SPECIAL", 4);
define("IBLOCK_ID_PERSONALITIES", 3);
define("IBLOCK_ID_NEWS", 2);


global $USER;
$curUser = $USER->GetById($USER->GetId())->fetch();
$userFav = json_decode($curUser['UF_FAVORITES']);
$cookieFav = json_decode($_COOKIE['favorites']);

if(!is_array($_SESSION['favorites'])){
    $_SESSION['favorites'] = [];
}
if($userFav){
    $_SESSION['favorites'] = $userFav;
}
if($cookieFav){
    $_SESSION['favorites'] = array_merge($cookieFav,$_SESSION['favorites']);
    $_SESSION['favorites'] = array_unique($_SESSION['favorites']);
    setcookie("favorites", json_encode($_SESSION['favorites']), time() + 60*60*24*365*10, "/", $_SERVER['SERVER_NAME'], false);
    $USER->Update($USER->GetId(), Array("UF_FAVORITES" => json_encode($_SESSION['favorites'])));
}

/* получаем название основной группы пользователя */
$params = [
    'filter' => ['USER.ID' => $curUser['ID'], '<=GROUP.C_SORT' => 100],
    'select' => ['GROUP.NAME', '*'],
    'order' => ['GROUP.C_SORT' => 'ASC'],
    'limit' => 1
];
$groupsOfUser = UserGroupTable::getList($params);
if($arGroupOfUser = $groupsOfUser->fetch()) {
    $curUser["ROLE"] = $arGroupOfUser["MAIN_USER_GROUP_GROUP_NAME"];
}


/* фильтрация новостей по стране */
if($_GET["country"] == "all") {
    $_SESSION["countryFilter"] = "";
} else {
    if($_GET["country"]) {
        $_SESSION["countryFilter"] = $_GET["country"];
    }
}
$countryFilter = ["=PROPERTY_COUNTRY" => $_SESSION["countryFilter"]];

global $getCountryFlagPath;
$getCountryFlagPath = fn($countryCode) => SITE_TEMPLATE_PATH . "/vendors/flags/" . strtolower($countryCode) . ".webp";

global $countries;
$countries = file_get_contents($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/vendors/countries.json");
$countries = (array)json_decode($countries);
?><!DOCTYPE html>
<html xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR?>favicon.ico"/>
    <? $APPLICATION->ShowHead(); ?>

    <link rel="stylesheet"
          href="<?=SITE_TEMPLATE_PATH . "/vendors/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css"?>">
    <link rel="stylesheet"
          href="<?=SITE_TEMPLATE_PATH . "/vendors/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css"?>">
    <script src="<?=SITE_TEMPLATE_PATH . "/vendors/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"?>"></script>

    <script src="<?=SITE_TEMPLATE_PATH . "/script.js"?>"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/vendors/bootstrap/popper.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/vendors/bootstrap/bootstrap.min.js"></script>

    <? if(explode("/", $curDir)[1] != "personal" && count(explode("/", $curDir)) > 3) { ?>
    <? } ?>
    <link href="<?=SITE_TEMPLATE_PATH?>/assets/css/app.css" rel="stylesheet">
</head>
<body class="d-flex flex-column">
<?php
include(__DIR__ . '/svg.php');
?>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="wrapper d-flex flex-column" id="bx_eshop_wrap">
    <header class="mu-header">
        <div class="mu-container">
            <div class="mu-header__wrap"><a class="mu-header__logo"
                                            href="/"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/logo.png"></a>
                <div class="mu-header__inner">
                    <div class="mu-header__row">
                        <a class="mu-header__phone" href="tel:8-123-456-78-99">8-123-456-78-99</a>
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:search.title",
                            "bootstrap_v4",
                            array(
                                "NUM_CATEGORIES" => "1",
                                "TOP_COUNT" => "5",
                                "CHECK_DATES" => "N",
                                "SHOW_OTHERS" => "N",
                                "PAGE" => SITE_DIR . "search/",
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
                                "CONTAINER_ID" => "title-search-container",
                                "PRICE_CODE" => array(),
                                "SHOW_PREVIEW" => "Y",
                                "PREVIEW_WIDTH" => "75",
                                "PREVIEW_HEIGHT" => "75",
                                "CONVERT_CURRENCY" => "Y",
                                "COMPONENT_TEMPLATE" => "bootstrap_v4",
                                "ORDER" => "rank",
                                "USE_LANGUAGE_GUESS" => "Y",
                                "TEMPLATE_THEME" => "blue",
                                "PRICE_VAT_INCLUDE" => "Y",
                                "PREVIEW_TRUNCATE_LEN" => "",
                                "CURRENCY_ID" => "RUB"
                            ),
                            false
                        ); ?>
                        <div class="mu-header__personal js-header-personal">
                            <span class="mu-header__personal-name"><?=$curUser["NAME"]?></span>
                            <span class="mu-header__personal-status"><?=$curUser["ROLE"]?></span>
                            <div class="mu-header__personal-avatar">
                                <?
                                $userPhoto = CFile::GetPath($curUser["PERSONAL_PHOTO"]);
                                if($curUser["PERSONAL_PHOTO"]) {
                                    ?>
                                    <img src="<?=$userPhoto?>">
                                <? } ?>
                            </div>
                            <div class="mu-header-menu js-header-menu">
                                <div class="mu-header-menu__personal js-header-menu-personal">
                                    <span class="mu-header-menu__personal-name"><?=$curUser["NAME"]?></span>
                                    <span class="mu-header-menu__personal-status"><?=$curUser["ROLE"]?></span>
                                    <div class="mu-header__personal-avatar"><img src="<?=$userPhoto?>"></div>
                                </div>

                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "user_menu",
                                    array(
                                        "ROOT_MENU_TYPE" => "user_menu",
                                        "MAX_LEVEL" => "1",
                                        "CHILD_MENU_TYPE" => "",
                                        "USE_EXT" => "Y",
                                        "DELAY" => "N",
                                        "ALLOW_MULTI_SELECT" => "Y",
                                        "MENU_CACHE_TYPE" => "N",
                                        "MENU_CACHE_TIME" => "3600",
                                        "MENU_CACHE_USE_GROUPS" => "Y",
                                        "MENU_CACHE_GET_VARS" => array(),
                                        "COMPONENT_TEMPLATE" => "user_menu",
                                        "MENU_THEME" => "site"
                                    ),
                                    false
                                ); ?>
                            </div>
                        </div>
                        <?/*
                        <div class="mu-header__controls-item">
                            <button class="mu-header__theme-toggle">
                                <svg class="night">
                                    <use xlink:href="#night"></use>
                                </svg>
                            </button>
                        </div>
                        */?>
                    </div>
                    <div class="mu-header__row">
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "main_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "left",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "2",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "ROOT_MENU_TYPE" => "top",
                                "USE_EXT" => "N",
                                "COMPONENT_TEMPLATE" => "main_menu",
                                "MENU_THEME" => "site"
                            ),
                            false
                        ); ?>

                        <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_round mu-select_header">
                            <?
                            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                            $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
                            $uri->addParams(["country" => "all"]);
                            ?>
                            <div class="mu-select__toggle-wrap" data-type="toggle">
                                <svg class="">
                                    <use xlink:href="#location"></use>
                                </svg>
                                <div class="mu-select__toggle"
                                     data-default-text="<?=$countries[$_SESSION["countryFilter"]] ?: "Все страны и города";?>"><?=$countries[$_SESSION["countryFilter"]] ?: "Все страны и города";?></div>
                            </div>
                            <div class="mu-select__drop">
                                <a href="<?=$uri?>" class="mu-select__item" data-type="item" data-id="country-0>">
                                    <span class="mu-select__item-text">Все</span>
                                </a>
                                <?
                                foreach($countries as $key => $country) {
                                    $uri->addParams(["country" => $key]);
                                    ?>
                                    <a href="<?=$uri?>"
                                       class="mu-select__item"
                                       data-type="item"
                                       data-id="country-<?=$key?>>">
                                        <span class="mu-select__item-text"><?=$country?></span>
                                    </a>
                                <? } ?>
                            </div>
                        </div>
                        <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_round">
                            <div class="mu-select__toggle-wrap">
                                <div class="mu-select__toggle" data-type="toggle" data-default-text="Rus">Rus</div>
                            </div>
                            <div class="mu-select__drop">
                                <label class="mu-select__item" data-type="item" data-id="role-1" for="role-1">
                                    <input class="mu-select__item-input js-select-input"
                                           type="radio"
                                           id="role-1"
                                           name="role"
                                           data-text="Rus">
                                    <span class="mu-select__item-text">Rus</span></label>
                                <label class="mu-select__item" data-type="item" data-id="role-2" for="role-2">
                                    <input class="mu-select__item-input js-select-input"
                                           type="radio"
                                           id="role-2"
                                           name="role"
                                           data-text="Eng">
                                    <span class="mu-select__item-text">Eng</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <header class="d-none">
        <div class="container">
            <!--region bx-header-->
            <div class="d-flex align-items-stretch flex-column flex-lg-row position-relative">
                <div class="d-flex align-items-center justify-content-lg-center justify-content-between">
                    <a class="company-logo" href="<?=SITE_DIR?>">
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR . "include/company_logo.php"
                            ),
                            false
                        ); ?>
                    </a>
                    <div class="d-lg-none">
                        <a id="main-menu-btn" href="#">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/triline.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="d-none d-lg-flex pl-lg-5 mt-4 mb-3 w-100 align-items-stretch flex-column flex-row position-relative">
                    <div class="pl-lg-3 w-100 align-items-start flex-column">
                        <div class="d-flex w-100 align-items-center justify-content-lg-between flex-column flex-lg-row">

                            <a href="tel: 8-123-456-78-99" class="header-phone mr-3 mb-3 mb-lg-0 d-lg-flex">
                                8-123-456-78-99
                                <? //$APPLICATION->IncludeComponent(
                                //                                    "bitrix:main.include",
                                //                                    "",
                                //                                    array(
                                //                                        "AREA_FILE_SHOW" => "file",
                                //                                        "PATH" => SITE_DIR."include/phone.php"
                                //                                    ),
                                //                                    false
                                //                                );?>
                            </a>

                            <div class="search-container mr-3">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:search.title",
                                    "main_search",
                                    array(
                                        "NUM_CATEGORIES" => "1",
                                        "TOP_COUNT" => "5",
                                        "CHECK_DATES" => "N",
                                        "SHOW_OTHERS" => "N",
                                        "PAGE" => SITE_DIR . "search/",
                                        "CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
                                        "CATEGORY_0" => array(
                                            0 => "no",
                                        ),
                                        "CATEGORY_0_iblock_catalog" => array(
                                            0 => "all",
                                        ),
                                        "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
                                        "SHOW_INPUT" => "Y",
                                        "INPUT_ID" => "mobile-search-input",
                                        "CONTAINER_ID" => "mobile-search-container",
                                        "PRICE_CODE" => array(),
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
                                ); ?>
                            </div>

                            <div class="dropdown ml-lg-3 mb-3 mb-lg-0">
                                <button class="btn btn-secondary dropdown-toggle"
                                        type="button"
                                        id="dropdownMenuButton"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                    <?=$countries[$_SESSION["countryFilter"]] ?: "Все страны и города";?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <?
                                    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                                    $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
                                    $uri->addParams(["country" => "all"]);
                                    ?>
                                    <a class="dropdown-item" href="<?=$uri?>">Все</a>
                                    <?
                                    foreach($countries as $key => $country) {
                                        $uri->addParams(["country" => $key]);
                                        ?>
                                        <a class="dropdown-item" href="<?=$uri?>"><?=$country?></a>
                                    <? } ?>
                                </div>
                            </div>
                            <a href="/login" class="btn btn-primary ml-5 header-btn d-none">Войти</a>

                            <a href="/login"
                               class="mu-btn mu-btn--round mu-btn--login mu-btn--filled"><span>Войти</span></a>
                        </div>
                        <div class="w-100 mt-2 d-flex align-items-center">
                            <div class="menu-container w-100">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "main_menu",
                                    array(
                                        "ROOT_MENU_TYPE" => "top",
                                        // Тип меню для первого уровня
                                        "MENU_CACHE_TYPE" => "A",
                                        // Тип кеширования
                                        "MENU_CACHE_TIME" => "36000000",
                                        // Время кеширования (сек.)
                                        "MENU_CACHE_USE_GROUPS" => "Y",
                                        // Учитывать права доступа
                                        "MENU_THEME" => "site",
                                        // Тема меню
                                        "CACHE_SELECTED_ITEMS" => "N",
                                        "MENU_CACHE_GET_VARS" => "",
                                        // Значимые переменные запроса
                                        "MAX_LEVEL" => "3",
                                        // Уровень вложенности меню
                                        "USE_EXT" => "Y",
                                        // Подключать файлы с именами вида .тип_меню.menu_ext.php
                                        "DELAY" => "N",
                                        // Откладывать выполнение шаблона меню
                                        "ALLOW_MULTI_SELECT" => "N",
                                        // Разрешить несколько активных пунктов одновременно
                                        "COMPONENT_TEMPLATE" => "bootstrap_v4"
                                    ),
                                    false
                                ); ?>
                            </div>
                            <div class="header-select mu-select js-mu-select js-select-single mu-select_single mu-select_round ml-3">
                                <div class="mu-select__toggle-wrap">
                                    <div class="mu-select__toggle" data-type="toggle" data-default-text="Rus">Rus</div>
                                </div>
                                <div class="mu-select__drop">
                                    <label class="mu-select__item" data-type="item"
                                           data-id="role-1" for="role-1">
                                        <input
                                                class="mu-select__item-input js-select-input" type="radio" id="role-1"
                                                name="role" data-text="Rus">
                                        <span
                                                class="mu-select__item-text">Rus</span></label>
                                    <label
                                            class="mu-select__item"
                                            data-type="item"
                                            data-id="role-2"
                                            for="role-2">
                                        <input
                                                class="mu-select__item-input js-select-input" type="radio" id="role-2"
                                                name="role" data-text="Eng">
                                        <span
                                                class="mu-select__item-text">Eng</span></label>
                                </div>
                            </div>
                            <!-- <a href="#" class="btn btn-secondary ml-5 ml-lg-auto header-btn d-none d-lg-block">Rus</a> -->
                        </div>
                    </div>
                </div>
            </div>
            <!--endregion-->
        </div>
    </header>

    <div class="page__main mu-content-page <?=$curDir == "/"?"main-page":""?>">
        <?if($curDir != "/"){ ?>
            <div class="mu-container">
                <h1 class="page__title"><? $APPLICATION->ShowTitle(false) ?></h1>
        <? } ?>
