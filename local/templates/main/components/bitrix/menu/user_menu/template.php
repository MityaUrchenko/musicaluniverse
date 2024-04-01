<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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

if(empty($arResult["ALL_ITEMS"])) {
    return;
}

CUtil::InitJSCore();
$menuBlockId = "menu_" . $this->randString();
?>

<ul class="mu-header-menu__list" id="<?=$menuBlockId?>">
    <?
    foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns) {
        $class = "";
        if($arResult["ALL_ITEMS"][$itemID]["SELECTED"]) {
            $class = "icon-active";
        }
        ?>
            <li class="mu-header-menu__item">
                <a class="mu-header-menu__link" href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
                    <svg class="<?=$class?>">
                        <use xlink:href="#<?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["icon"]?>"></use>
                    </svg>
                    <span class="mu-header-menu__link-name"><?=htmlspecialcharsbx($arResult["ALL_ITEMS"][$itemID]["TEXT"], ENT_COMPAT, false)?></span>
                </a>
            </li>
        <?
    }
    ?>
</ul>
