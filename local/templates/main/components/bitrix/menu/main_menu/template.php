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
$menuBlockId = "menu_".$this->randString();
?>

<nav class="mu-header__nav" id="<?=$menuBlockId?>">
		<?
		foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns)
		{
		    //--first level--
            $class = "";
			if($arResult["ALL_ITEMS"][$itemID]["SELECTED"])
			{
				$class = "active";
			}
		?>
				<?
				if (is_array($arColumns) && count($arColumns) > 0)
				{
				?>
                    <div class="mu-header__nav-item js-header-nav">
                        <span><?=htmlspecialcharsbx($arResult["ALL_ITEMS"][$itemID]["TEXT"], ENT_COMPAT, false)?></span>
                        <svg class="">
                            <use xlink:href="#arrow-down"></use>
                        </svg>
						<?
						foreach($arColumns as $key=>$arRow)
						{
						?>
                            <ul class="mu-header__nav-list">
							<?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>
                                <li class="mu-header__nav-list-item">
                                    <a class="mu-header__nav-list-link <?if($arResult["ALL_ITEMS"][$itemIdLevel_2]["SELECTED"]):?>active<?endif?>"
										href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>">
										    <?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>
									</a>
								</li>
							<?endforeach;?>
							</ul>
						<?
						}
						?>
					</div>
				<?
				}else{?>
                    <a
                            class="mu-header__nav-item <?=$class?>"
                            href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>"
                    >
                        <?=htmlspecialcharsbx($arResult["ALL_ITEMS"][$itemID]["TEXT"], ENT_COMPAT, false)?>
                    </a>

                <?}
				?>
			</a>
		<?
		}
		?>
		</ul>
</nav>

<script>
	BX.ready(function () {
		window.obj_<?=$menuBlockId?> = new BX.Main.MenuComponent.CatalogHorizontal('<?=CUtil::JSEscape($menuBlockId)?>', <?=CUtil::PhpToJSObject($arResult["ITEMS_IMG_DESC"])?>);
	});
</script>