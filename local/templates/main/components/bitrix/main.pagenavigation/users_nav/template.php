<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

/** @var PageNavigationComponent $component */
$component = $this->getComponent();

$this->setFrameMode(true);
?>

<div class="mu-pagination">
    <ul class="mu-pagination__list">

            <? if ($arResult["CURRENT_PAGE"] > 1) { ?>
                <li class="mu-pagination__item mu-pagination__item-control">
                    <a href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"] - 1)) ?>">
                        <svg class="arrow-prev">
                            <use xlink:href="#arrow-prev"></use>
                        </svg>
                    </a>
                </li>
                <? if ($arResult["CURRENT_PAGE"] > 2) { ?>
                    <li class="mu-pagination__item">
                        <a href="<?= htmlspecialcharsbx($arResult["URL"]) ?>"> 1 </a>
                    </li>
                <? } else { ?>
                    <li class="mu-pagination__item"><a href="<?= htmlspecialcharsbx($arResult["URL"]) ?>">1</a></li>
                <? } ?>
            <? } else { ?>
                <li class="mu-pagination__item active">
                    <a href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"])) ?>">
                        <?= $arResult["CURRENT_PAGE"] ?>
                    </a>
                </li>
            <? } ?>

            <?
            $page = $arResult["START_PAGE"] + 1;
            while ($page <= $arResult["END_PAGE"] - 1) {
                ?><? if ($page == $arResult["CURRENT_PAGE"]) { ?>
                    <li class="mu-pagination__item active"><a href="javascript: void(0);"><?= $page ?></a></li>
                <? } else { ?>
                    <li class="mu-pagination__item"><a href="<?= htmlspecialcharsbx(
                            $component->replaceUrlTemplate($page)
                        ) ?>"><?= $page ?></a></li>
                <? } ?>
                <? $page++ ?>
            <? } ?>

            <? if ($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]) { ?>

                <? if ($arResult["PAGE_COUNT"] > 1) { ?>
                <li class="mu-pagination__item"><a href="<?= htmlspecialcharsbx(
                        $component->replaceUrlTemplate($arResult["PAGE_COUNT"])
                    ) ?>"><?= $arResult["PAGE_COUNT"] ?></a></li>
                <? } ?>
                <li class="mu-pagination__item mu-pagination__item-control">
                    <a href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"] + 1)) ?>">
                        <svg class="arrow-next">
                            <use xlink:href="#arrow-next"></use>
                        </svg>
                    </a>
                </li>
            <? } else { ?>
                <? if ($arResult["PAGE_COUNT"] > 1) { ?>
                    <li class="mu-pagination__item active"><a href="#"><?= $arResult["PAGE_COUNT"] ?></a></li>
                <? } ?>
            <? } ?>

        <?/* if ($arResult["SHOW_ALL"]) { ?>
            <? if ($arResult["ALL_RECORDS"]) { ?>
                <li class="mu-pagination__item"><a href="<?= htmlspecialcharsbx(
                        $arResult["URL"]
                    ) ?>" rel="nofollow"><? echo GetMessage("round_nav_pages") ?></a></li>
            <? } else { ?>
                <li class="mu-pagination__item"><a href="<?= htmlspecialcharsbx(
                        $component->replaceUrlTemplate("all")
                    ) ?>" rel="nofollow"><? echo GetMessage("round_nav_all") ?></a></li>
            <? } ?>
        <? } */?>

    </ul>
</div>
