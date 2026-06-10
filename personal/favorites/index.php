<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");

$arrFilter = [];
$arrFilter['ID'] = $_SESSION['favorites'];
if($_SESSION['favorites']) {
    $APPLICATION->IncludeComponent(
        "mu:news_list_from_every_iblock",
        "main",
        array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "FIELD_CODE" => array(
                0 => "ID",
                1 => "NAME",
                2 => "PREVIEW_TEXT",
                3 => "PREVIEW_PICTURE",
                4 => "DATE_ACTIVE_FROM",
                5 => "ACTIVE_FROM",
                6 => "DATE_ACTIVE_TO",
                7 => "ACTIVE_TO",
                8 => "DATE_CREATE",
                9 => "TIMESTAMP_X",
                10 => "",
            ),
            "FILTER_NAME" => "arrFilter",
            "FILTER_PROPERTY_CODE" => "",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "IBLOCK_TYPE" => "-",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MEDIA_PROPERTY" => "",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "9999",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Новости",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "PROPERTY_CODE" => array(
                0 => "",
                1 => "",
            ),
            "SEARCH_PAGE" => "/search/",
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SLIDER_PROPERTY" => "",
            "SORT_BY1" => "",
            "SORT_BY2" => "",
            "SORT_ORDER1" => "DESC",
            "SORT_ORDER2" => "DESC",
            "SORT_PROPERTY_CODE" => "",
            "STRICT_SECTION_CHECK" => "N",
            "TEMPLATE_THEME" => "blue",
            "USE_RATING" => "N",
            "USE_SHARE" => "N",
            "COMPONENT_TEMPLATE" => "news",
            "SORT_BY_IDS" => $_SESSION['favorites']
        ),
        false
    );
} ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>