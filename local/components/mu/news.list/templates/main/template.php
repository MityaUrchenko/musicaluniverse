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
$curDir = $APPLICATION->GetCurDir();
?>

<div class="mb-4">
        <div class="row">
            <?foreach($arResult["ITEMS"] as $key => $arItem){?>
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

                <?
                $mb = "mb-4";
                $width = "col-md-6 col-lg-3";
                ?>

                <div class="<?=$mb?> col-12 <?=$width?> d-flex" id="<?=$this->GetEditAreaId($arItem['ID']);?>" data-date="<?=$arItem["ACTIVE_FROM"]?>">
                    <div class="card <?=$dark_card?>">

                        <button class="favor <?=in_array($arItem['ID'], unserialize($_COOKIE['favorites']))?"active":""?>" data-item="<?=$arItem['ID']?>"></button>

                        <div class="card-img-container">
                            <div style="background-image: url(<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>)"></div>
                            <a class="btn btn-primary" href="<?echo $arItem["DETAIL_PAGE_URL"]?>">Подробнее</a>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="card-title">
                                <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                                    <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?> </a>
                                <?else:?>
                                    <?echo $arItem["NAME"]?>
                                <?endif;?>
                            </div>

                            <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                                <p class="card-text"><?echo $arItem["PREVIEW_TEXT"];?></p>
                            <?endif;?>

                            <div class="d-flex mt-auto">
                                <div class="date-published"><?=$arItem["DATE_ACTIVE_FROM"]?:$arItem["DATE_CREATE"]?></div>
                                <?/*<div class="category"><?=$arItem["PROPERTIES"]["CATEGORY"]["VALUE"]?></div>*/?>
                            </div>
                        </div>
                    </div>
                </div>

            <?}?>
            <?/*
            <div class="<?=$mb?> col d-flex align-items-stretch">
                <div class="w-100 d-flex align-items-center justify-content-center rounded" style="background: #eee">
                    БАННЕР
                </div>
            </div>
            */?>
        </div>


        <?/*if($curDir == "/"){?>
            <div class="text-center mt-4">
                <a href="<?=$arResult["LIST_PAGE_URL"]?>" class="btn btn-third">Показать больше</a>
            </div>
        <?}*/?>
</div>
