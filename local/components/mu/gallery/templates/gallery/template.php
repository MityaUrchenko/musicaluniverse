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
<div id="gallery_<?=$arResult["ID"]?>">
    <?/*<div class="header line"><?=$arResult["NAME"]?></div>*/?>
    <div class="grid">
        <?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty) {?>
            <?foreach($arProperty["FILE_VALUE"] as $fid=>$arFile) {?>
                <a href="<?=$arFile["SRC"]?>"
                   data-fancybox="gallery"
                   data-caption="<?=$arFile["DESCRIPTION"]?>"
                   class="grid-item example-image-link"
                   data-ratio="<?=$arFile["WIDTH"] / $arFile["HEIGHT"]?>"
                   style="background-image: url('<?=$arFile["SRC_MINI"]?>')"
                >
                        <div><i class="fa-solid fa-magnifying-glass fa-2x"></i></div>
                </a>
            <?}?>
        <?}?>
    </div>

    <script src="https://kit.fontawesome.com/71ec36ff95.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <script src="https://unpkg.com/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
</div>