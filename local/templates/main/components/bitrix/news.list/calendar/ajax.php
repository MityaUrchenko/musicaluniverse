<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

Loader::includeModule('iblock');

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$dateStr = trim($request->get('calendar_date')); // приходит в формате yyyy.mm.dd

if (empty($dateStr)) {
    echo '<div class="col-12"><p class="text-center">Дата не указана.</p></div>';
    die();
}

// Правильное преобразование даты
try {
    $date = new DateTime($dateStr . " 00:00:00", 'Y.m.d H:i:s');
    $date = $date->format('d.m.Y H:i:s');

} catch (Exception $e) {
    echo '<div class="col-12"><p class="text-center">Ошибка формата даты.</p></div>';
    die();
}

$arFilter = [
        "IBLOCK_ID"     => 1,
        "ACTIVE"        => "Y",
        "ACTIVE_DATE"   => "Y",
        "=DATE_ACTIVE_FROM" => $date,
];

$rs = CIBlockElement::GetList(
        ["ACTIVE_FROM" => "ASC"],
        $arFilter,
        false,
        false,
        ["ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "ACTIVE_FROM", "DETAIL_PAGE_URL", "PROPERTY_*"]
);
?>

<?php if ($rs->SelectedRowsCount() > 0): ?>
    <?php while ($ob = $rs->GetNextElement()):
        $arItem = $ob->GetFields();
        $arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
        $arItem["PROPERTIES"] = $ob->GetProperties();
        ?>
        <div class="calendar-item d-flex mb-4 col-12 col-md-6 col-lg-3" id="bx_<?= $arItem['ID'] ?>">
            <div class="card">
                <button class="favor <?= in_array($arItem['ID'], $_SESSION['favorites'] ?? []) ? "active" : "" ?>"
                        data-item="<?= $arItem['ID'] ?>"></button>

                <?php $type = $arItem["PROPERTIES"]["TYPE"]["VALUE_XML_ID"] ?? ''; ?>
                <span class="event_type <?= $type ?>"></span>

                <div class="card-img-container">
                    <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                        <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?? '' ?>" alt="">
                    </a>
                    <a class="btn btn-primary" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">Купить <img src="<?= SITE_TEMPLATE_PATH ?>/images/ticket.svg" alt=""></a>
                </div>

                <div class="card-body d-flex flex-column">
                    <div class="card-title">
                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a>
                    </div>
                    <? if ($arItem["PREVIEW_TEXT"]): ?>
                        <p class="card-text"><?= $arItem["PREVIEW_TEXT"] ?></p>
                    <? endif; ?>
                    <div class="date-published mt-auto"><?= $arItem["ACTIVE_FROM"] ?></div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-12">
        <p class="text-center">Новостей на выбранную дату нет.</p>
    </div>
<?php endif; ?>

<?php die(); ?>