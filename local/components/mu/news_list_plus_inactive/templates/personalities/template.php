<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

<div class="personalities-list mb-4">
    <?if($arParams["DISPLAY_TOP_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?><br />
    <?endif;?>

    <div class="row">
        <?foreach($arResult["ITEMS"] as $key => $arItem):?>
            <?
                $this->AddEditAction(
                    $arItem['ID'],
                    $arItem['EDIT_LINK'],
                    CIBlock::GetArrayByID(
                        $arItem["IBLOCK_ID"],
                        "ELEMENT_EDIT"
                    )
                );
                $this->AddDeleteAction(
                    $arItem['ID'],
                    $arItem['DELETE_LINK'],
                    CIBlock::GetArrayByID(
                        $arItem["IBLOCK_ID"],
                        "ELEMENT_DELETE"),
                    array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
                );
            ?>
            <div class="d-flex mb-4 col-12 col-md-6 col-lg-3" id="<?=$this->GetEditAreaId($arItem['ID']);?>" data-date="<?=$arItem["ACTIVE_FROM"]?>">
                <div class="card">

                    <button class="favor <?=in_array($arItem['ID'], $_SESSION['favorites'])?"active":""?>" data-item="<?=$arItem['ID']?>"></button>

                    <div class="card-img-container">
                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                            <img
                                    class=""
                                    src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                    alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
                                    title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
                            />
                        </a>
                        <a class="btn btn-primary" href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее</a>
                    </div>


                    <div class="card-body d-flex flex-column">
                        <?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                            <div class="card-title">
                                <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                                <?else:?>
                                    <?=$arItem["NAME"]?>
                                <?endif;?>
                            </div>
                        <?endif;?>

                        <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                            <p class="card-text"><?=$arItem["PREVIEW_TEXT"];?></p>
                        <?endif;?>

                        <div class="date-published mt-auto"><?=$arItem["ACTIVE_FROM"]?:$arItem["DATE_CREATE"]?></div>
                    </div>
                </div>
            </div>
        <?endforeach;?>
    </div>

    <?if(count(explode("/",$APPLICATION->GetCurDir())) <= 3 && $APPLICATION->GetCurDir() != $arResult["LIST_PAGE_URL"]){?>
        <div class="text-center mt-4">
            <a href="<?=$arResult["LIST_PAGE_URL"]?>" class="btn btn-secondary py-2 px-4">Показать больше</a>
        </div>
    <?}?>

    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>
