<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult["ALL_ITEMS"]))
    return;

CUtil::InitJSCore();

$menuBlockId = "catalog_menu_".$this->randString();
?>

<div  class="main-menu d-flex my-3" id="<?=$menuBlockId?>">
    <ul class="d-flex w-100 justify-content-between flex-column flex-lg-row">
        <?
        foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns)
        {
            $class = "";
            if($arResult["ALL_ITEMS"][$itemID]["SELECTED"])
            {
                $class = "active";
            }?>

            <li class="<?=$class?>">
                <a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>"><?=htmlspecialcharsbx($arResult["ALL_ITEMS"][$itemID]["TEXT"], ENT_COMPAT, false)?></a>
            </li>

        <?}?>

    </ul>
</div>


<script>
  BX.ready(function () {
    window.obj_<?=$menuBlockId?> = new BX.Main.MenuComponent.CatalogHorizontal('<?=CUtil::JSEscape($menuBlockId)?>', <?=CUtil::PhpToJSObject($arResult["ITEMS_IMG_DESC"])?>);
  });
</script>