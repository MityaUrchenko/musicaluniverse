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

<div class="mt-4">
    <div class="row">
        <?

        foreach ($arResult["ITEMS"] as $key => $arItem): ?>
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
                    "ELEMENT_DELETE"
                ),
                array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
            );
            ?>



            <div class="calendar-item d-flex mb-4 col-12 col-md-6 col-lg-3" id="<?= $this->GetEditAreaId(
                $arItem['ID']
            ); ?>" >
                <div class="card">

                    <button class="favor <?= in_array(
                        $arItem['ID'],
                        unserialize($_COOKIE['favorites'])
                    ) ? "active" : "" ?>" data-item="<?= $arItem['ID'] ?>"></button>

                    <?php
                    $type = $arItem["PROPERTIES"]["TYPE"]["VALUE_XML_ID"];
                    ?>
                    <span class="event_type <?= $type ?>"></span>

                    <div class="card-img-container">
                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"> <img
                                    class=""
                                    src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                    alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
                                    title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
                            /> </a> <a class="btn btn-primary" href="<? echo $arItem["DETAIL_PAGE_URL"] ?>">Купить
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/ticket.svg" alt=""></a>
                    </div>


                    <div class="card-body d-flex flex-column">
                        <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                            <div class="card-title">
                                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                                    <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>"><? echo $arItem["NAME"] ?></a>
                                <? else: ?>
                                    <? echo $arItem["NAME"] ?>
                                <? endif; ?>
                            </div>
                        <? endif; ?>

                        <? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
                            <p class="card-text"><? echo $arItem["PREVIEW_TEXT"]; ?></p>
                        <? endif; ?>

                        <div class="date-published mt-auto"><?= $arItem["ACTIVE_FROM"]?:$arItem["TIMESTAMP_X"] ?></div>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>
</div>
