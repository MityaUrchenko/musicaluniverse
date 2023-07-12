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
CUtil::InitJSCore(array('fx'));

$arLikedElements = unserialize($_COOKIE['likes']);

$liked = "";
if (in_array($arResult["ID"], $arLikedElements)) {
    $liked = "liked";
}

?>

<div class="page__main mu-person-page">
    <div class="mu-container">
        <div class="mu-person-page__inner" id="<?echo $this->GetEditAreaId($arResult['ID'])?>">
            <div class="mu-person-page__gallery">
                <div class="mu-person-page__gallery-slide">
                    <img
                        src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
                        alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
                        title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
                    />
                </div>

                <?
                if($arResult["PROPERTIES"]["PHOTO"]["VALUE"]){
                    foreach ($arResult["PROPERTIES"]["PHOTO"]["VALUE"] as $picture) {?>
                        <div class="mu-person-page__gallery-slide"><img src="<?=$picture["SRC"]?>"></div>
                    <?}
                }?>
            </div>
            <div class="mu-person-page__main person-page-card">
                <button class="mu-save-mark favor <?=in_array($arResult['ID'], unserialize($_COOKIE['favorites']))?"active":""?>" data-item="<?=$arResult['ID']?>"></button>
                <div class="person-page-card__name"><?=$arResult["NAME"]?>
                    <svg class="success person-page-card__name-icon">
                        <use xlink:href="#success"></use>
                    </svg>
                </div>
                <div class="person-page-card__post">Актер, Продюсер, Режиссер, Сценарист</div>
                <div class="person-page-card__controls"><a class="btn btn--filled btn--round btn--icon"
                                                           href="#"><span>Купить</span>
                        <svg class="ticket">
                            <use xlink:href="#ticket"></use>
                        </svg>
                    </a><a class="btn btn--round" href="#"><span>Контактные данные и резюме</span></a></div>

                <?
                foreach ($arResult["DISPLAY_PROPERTIES"] as $property) {?>
                    <div class="person-page-card__row">
                        <div class="person-page-card__row-value person-page-card__row-value--bold"><?=$property["NAME"]?></div>
                        <div class="person-page-card__row-value"><?=$property["VALUE"]?></div>
                    </div>
                <?}?>
                <div class="person-page-card__row">
                    <div class="person-page-card__row-value person-page-card__row-value--bold">Биография / О
                        персоне
                    </div>
                    <div class="person-page-card__row-value"><?echo $arResult["DETAIL_TEXT"];?></div>
                </div>
            </div>
        </div>
    </div>
</div>

