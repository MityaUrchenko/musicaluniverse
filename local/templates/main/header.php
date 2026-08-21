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


/* Город пользователя: cookie + session + GeoIP (Sypex через Битрикс) */
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/mu_city.php';
MuCity::init();

// Отладка: ?city_debug=1 — жёлтый блок вверху страницы
//$muCityDebugHtml = MuCity::renderDebug();

global $cities, $cityFilter, $countryFilter;
$cities = MuCity::getCities();
// Фильтр по городу для разделов с PROPERTY_CITY (афиша, события, сцены…)
$cityFilter = MuCity::getFilter();


// Старый фильтр по стране отключён (новости/статьи/персоналии без привязки к городу)
$countryFilter = [];

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
    <link href="<?=SITE_TEMPLATE_PATH?>/assets/css/app.css?v6" rel="stylesheet">
</head>
<body class="d-flex flex-column">
<?php
include(__DIR__ . '/svg.php');
?>
<?= $muCityDebugHtml ?? '' ?>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="wrapper d-flex flex-column" id="bx_eshop_wrap">
    <header class="mu-header" id="mu-header">
        <div class="container">
            <div class="mu-header__wrap">
                <a class="mu-header__logo" href="/">
                    <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/logo.png" alt="">
                </a>

                <div class="mu-header__inner">
                    <div class="mu-header__row mu-header__row--top">
                        <a class="mu-header__phone" href="tel:8-123-456-78-99">8-123-456-78-99</a>

                        <div class="mu-header__search-wrap">
                            <? $APPLICATION->IncludeComponent(
                                    "bitrix:search.title",
                                    "bootstrap_v4",
                                    array(
                                            "NUM_CATEGORIES" => "1",
                                            "TOP_COUNT" => "5",
                                            "CHECK_DATES" => "Y",
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
                        </div>

                        <?php
                        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                        $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
                        $uri->deleteParams(['city', 'country']);
                        $uriAll = clone $uri;
                        $uriAll->addParams(['city' => 'all']);
                        $cityLabel = MuCity::getName() ?: 'Все города';
                        ?>

                        <div class="mu-header__city mu-header__desktop-only">
                            <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_round mu-select_header">
                                <div class="mu-select__toggle-wrap" data-type="toggle">
                                    <svg class="">
                                        <use xlink:href="#location"></use>
                                    </svg>
                                    <div class="mu-select__toggle"
                                         data-default-text="<?= htmlspecialcharsbx($cityLabel) ?>"><?= htmlspecialcharsbx($cityLabel) ?></div>
                                </div>
                                <div class="mu-select__drop">
                                    <a href="<?= htmlspecialcharsbx($uriAll->getUri()) ?>" class="mu-select__item" data-type="item" data-id="city-all">
                                        <span class="mu-select__item-text">Все города</span>
                                    </a>
                                    <?php foreach ($cities as $key => $cityName):
                                        $uriCity = clone $uri;
                                        $uriCity->addParams(['city' => $key]);
                                        ?>
                                        <a href="<?= htmlspecialcharsbx($uriCity->getUri()) ?>"
                                           class="mu-select__item"
                                           data-type="item"
                                           data-id="city-<?= htmlspecialcharsbx($key) ?>">
                                            <span class="mu-select__item-text"><?= htmlspecialcharsbx($cityName) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mu-header__auth mu-header__desktop-only">
                            <?if($USER->isAuthorized()){?>
                                <div class="mu-header__personal js-header-personal">
                                    <span class="mu-header__personal-name"><?=$curUser["NAME"]?></span>
                                    <span class="mu-header__personal-status"><?=$curUser["ROLE"]?></span>
                                    <div class="mu-header__personal-avatar">
                                        <?
                                        $userPhoto = CFile::GetPath($curUser["PERSONAL_PHOTO"]);
                                        if($curUser["PERSONAL_PHOTO"]) {
                                            ?>
                                            <img src="<?=$userPhoto?>" alt="">
                                        <? } ?>
                                    </div>
                                    <div class="mu-header-menu js-header-menu">
                                        <div class="mu-header-menu__personal js-header-menu-personal">
                                            <span class="mu-header-menu__personal-name"><?=$curUser["NAME"]?></span>
                                            <span class="mu-header-menu__personal-status"><?=$curUser["ROLE"]?></span>
                                            <div class="mu-header__personal-avatar"><img src="<?=$userPhoto?>" alt=""></div>
                                        </div>

                                        <? $APPLICATION->IncludeComponent(
                                                "bitrix:menu",
                                                "user_menu",
                                                array(
                                                        "ROOT_MENU_TYPE" => "user_menu",
                                                        "MAX_LEVEL" => "1",
                                                        "CHILD_MENU_TYPE" => "",
                                                        "USE_EXT" => "N",
                                                        "DELAY" => "N",
                                                        "ALLOW_MULTI_SELECT" => "N",
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
                            <?}else{?>
                                <button type="button" class="btn btn-primary js-login-btn w-100">Войти</button>
                            <?}?>
                        </div>
                    </div>

                    <div class="mu-header__row mu-header__row--nav mu-header__desktop-only">
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
                                    <span class="mu-select__item-text">Rus</span>
                                </label>
                                <label class="mu-select__item" data-type="item" data-id="role-2" for="role-2">
                                    <input class="mu-select__item-input js-select-input"
                                           type="radio"
                                           id="role-2"
                                           name="role"
                                           data-text="Eng">
                                    <span class="mu-select__item-text">Eng</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button"
                        class="mu-header__burger"
                        id="mu-header-burger"
                        aria-label="Меню"
                        aria-expanded="false"
                        aria-controls="mu-header-drawer">
                    <span class="mu-header__burger-line"></span>
                    <span class="mu-header__burger-line"></span>
                    <span class="mu-header__burger-line"></span>
                </button>
            </div>
        </div>

        <!-- Мобильное меню (≤1023px) -->
        <div class="mu-header__drawer" id="mu-header-drawer" aria-hidden="true">
            <div class="mu-header__drawer-inner">
                <div class="mu-header__drawer-menu">
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
                </div>

                <div class="mu-header__drawer-footer">
                    <div class="mu-header__city">
                        <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_round mu-select_header">
                            <div class="mu-select__toggle-wrap" data-type="toggle">
                                <svg class="">
                                    <use xlink:href="#location"></use>
                                </svg>
                                <div class="mu-select__toggle"
                                     data-default-text="<?= htmlspecialcharsbx($cityLabel) ?>"><?= htmlspecialcharsbx($cityLabel) ?></div>
                            </div>
                            <div class="mu-select__drop">
                                <a href="<?= htmlspecialcharsbx($uriAll->getUri()) ?>" class="mu-select__item" data-type="item" data-id="city-all-m">
                                    <span class="mu-select__item-text">Все города</span>
                                </a>
                                <?php foreach ($cities as $key => $cityName):
                                    $uriCity = clone $uri;
                                    $uriCity->addParams(['city' => $key]);
                                    ?>
                                    <a href="<?= htmlspecialcharsbx($uriCity->getUri()) ?>"
                                       class="mu-select__item"
                                       data-type="item"
                                       data-id="city-m-<?= htmlspecialcharsbx($key) ?>">
                                        <span class="mu-select__item-text"><?= htmlspecialcharsbx($cityName) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mu-header__auth">
                        <?if($USER->isAuthorized()){?>
                            <a href="/personal/" class="mu-header__personal-link">
                                <span class="mu-header__personal-name"><?=htmlspecialcharsbx($curUser["NAME"])?></span>
                                <span class="mu-header__personal-status"><?=htmlspecialcharsbx($curUser["ROLE"])?></span>
                                <div class="mu-header__personal-avatar"><img src="<?=$userPhoto?>" alt=""></div>
                            </a>
                        <?}else{?>
                            <button type="button" class="btn btn-primary js-login-btn w-100">Войти</button>
                        <?}?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mu-header__overlay" id="mu-header-overlay" aria-hidden="true"></div>
    </header>

    <script>
        (function () {
            var header = document.getElementById('mu-header');
            var burger = document.getElementById('mu-header-burger');
            var drawer = document.getElementById('mu-header-drawer');
            var overlay = document.getElementById('mu-header-overlay');
            if (!header || !burger || !drawer) return;

            function openMenu() {
                header.classList.add('is-menu-open');
                burger.setAttribute('aria-expanded', 'true');
                drawer.setAttribute('aria-hidden', 'false');
                if (overlay) overlay.setAttribute('aria-hidden', 'false');
                document.body.classList.add('mu-header-menu-open');
            }

            function closeMenu() {
                header.classList.remove('is-menu-open');
                burger.setAttribute('aria-expanded', 'false');
                drawer.setAttribute('aria-hidden', 'true');
                if (overlay) overlay.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('mu-header-menu-open');
            }

            function toggleMenu() {
                if (header.classList.contains('is-menu-open')) closeMenu();
                else openMenu();
            }

            burger.addEventListener('click', function (e) {
                e.preventDefault();
                toggleMenu();
            });
            if (overlay) {
                overlay.addEventListener('click', closeMenu);
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeMenu();
            });
            window.addEventListener('resize', function () {
                if (window.innerWidth > 1023) closeMenu();
            });
        })();
    </script>

    <div class="mu-content-page<?=$curDir == "/"?"main-page":""?>">
        <div class="container my-5">

            <?if(count(explode("/",$curDir)) == 3){ ?>
            <h1 class="header"><? $APPLICATION->ShowTitle(false) ?></h1>
<? } ?>