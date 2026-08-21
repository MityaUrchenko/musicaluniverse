<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>


<div class="mb-5">
    <? foreach ($arResult["IBLOCKS"] as $arIBlock) { ?>
        <?
        $this->AddEditAction('iblock_' . $arIBlock['ID'], $arIBlock['ADD_ELEMENT_LINK'], CIBlock::GetArrayByID($arIBlock["ID"], "ELEMENT_ADD"));
        ?>
        <div class="page__title"><?= $arIBlock["NAME"] ?></div>
        <div>
            <?php
            $APPLICATION->IncludeComponent(
                    "bitrix:news.list",
                    "news",
                    [
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
                            "FIELD_CODE" => [
                                    0 => "ID",
                                    1 => "CODE",
                                    2 => "NAME",
                                    3 => "PREVIEW_TEXT",
                                    4 => "PREVIEW_PICTURE",
                                    5 => "DATE_ACTIVE_FROM",
                                    6 => "ACTIVE_FROM",
                                    7 => "DATE_ACTIVE_TO",
                                    8 => "ACTIVE_TO",
                                    9 => "DATE_CREATE",
                                    10 => "TIMESTAMP_X",
                                    11 => "",
                            ],
                            "FILTER_NAME" => "arrFilter",
                            "FILTER_PROPERTY_CODE" => "",
                            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                            "IBLOCK_ID" => $arIBlock["ID"],
                            "IBLOCK_TYPE" => "content",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "INCLUDE_SUBSECTIONS" => "N",
                            "MEDIA_PROPERTY" => "",
                            "MESSAGE_404" => "",
                            "NEWS_COUNT" => "8",
                            "PAGER_BASE_LINK_ENABLE" => "N",
                            "PAGER_DESC_NUMBERING" => "N",
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                            "PAGER_SHOW_ALL" => "N",
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => ".default",
                            "PAGER_TITLE" => "",
                            "PARENT_SECTION" => "",
                            "PARENT_SECTION_CODE" => "",
                            "PREVIEW_TRUNCATE_LEN" => "",
                            "PROPERTY_CODE" => [
                                    0 => "",
                                    1 => "",
                            ],
                            "SEARCH_PAGE" => "/search/",
                            "SET_BROWSER_TITLE" => "N",
                            "SET_LAST_MODIFIED" => "N",
                            "SET_META_DESCRIPTION" => "N",
                            "SET_META_KEYWORDS" => "N",
                            "SET_STATUS_404" => "N",
                            "SET_TITLE" => "N",
                            "SHOW_404" => "N",
                            "SLIDER_PROPERTY" => "",
                            "SORT_BY1" => "ID",
                            "SORT_BY2" => "SORT",
                            "SORT_ORDER1" => "DESC",
                            "SORT_ORDER2" => "ASC",
                            "SORT_PROPERTY_CODE" => "",
                            "STRICT_SECTION_CHECK" => "N",
                            "TEMPLATE_THEME" => "blue",
                            "USE_RATING" => "N",
                            "USE_SHARE" => "N",
                            "COMPONENT_TEMPLATE" => "news"
                    ],
                    false
            );
            ?>
        </div>
    <? } ?>
</div>