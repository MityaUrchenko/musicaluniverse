<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Администрирование"); ?>

<div class="pt-4">
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
            "MENU_CACHE_GET_VARS" => array(
            ),
            "COMPONENT_TEMPLATE" => "user_menu",
            "MENU_THEME" => "site"
        ),
        false
    ); ?>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>