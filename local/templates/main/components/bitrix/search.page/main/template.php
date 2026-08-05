<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @var CBitrixComponentTemplate $this */
$this->setFrameMode(true);



if($arParams["SHOW_TAGS_CLOUD"] == "Y") {
    $arCloudParams = [
            "SEARCH" => $arResult["REQUEST"]["~QUERY"],
            "TAGS" => $arResult["REQUEST"]["~TAGS"],
            "CHECK_DATES" => $arParams["CHECK_DATES"],
            "arrFILTER" => $arParams["arrFILTER"],
            "SORT" => $arParams["TAGS_SORT"],
            "PAGE_ELEMENTS" => $arParams["TAGS_PAGE_ELEMENTS"],
            "PERIOD" => $arParams["TAGS_PERIOD"],
            "URL_SEARCH" => $arParams["TAGS_URL_SEARCH"],
            "TAGS_INHERIT" => $arParams["TAGS_INHERIT"],
            "FONT_MAX" => $arParams["FONT_MAX"],
            "FONT_MIN" => $arParams["FONT_MIN"],
            "COLOR_NEW" => $arParams["COLOR_NEW"],
            "COLOR_OLD" => $arParams["COLOR_OLD"],
            "PERIOD_NEW_TAGS" => $arParams["PERIOD_NEW_TAGS"],
            "SHOW_CHAIN" => $arParams["SHOW_CHAIN"],
            "COLOR_TYPE" => $arParams["COLOR_TYPE"],
            "WIDTH" => $arParams["WIDTH"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "RESTART" => $arParams["RESTART"],
    ];
    if (is_array($arCloudParams["arrFILTER"])) {
        foreach ($arCloudParams["arrFILTER"] as $strFILTER) {
            if ($strFILTER == "main") {
                $arCloudParams["arrFILTER_main"] = $arParams["arrFILTER_main"];
            } else if ($strFILTER == "forum" && IsModuleInstalled("forum")) {
                $arCloudParams["arrFILTER_forum"] = $arParams["arrFILTER_forum"];
            } else if (mb_strpos($strFILTER, "iblock_") === 0) {
                if (isset($arParams["arrFILTER_" . $strFILTER]) && is_array($arParams["arrFILTER_" . $strFILTER])) {
                    foreach ($arParams["arrFILTER_" . $strFILTER] as $strIBlock)
                        $arCloudParams["arrFILTER_" . $strFILTER] = $arParams["arrFILTER_" . $strFILTER];
                }
            } else if ($strFILTER == "blog") {
                $arCloudParams["arrFILTER_blog"] = $arParams["arrFILTER_blog"];
            } else if ($strFILTER == "socialnetwork") {
                $arCloudParams["arrFILTER_socialnetwork"] = $arParams["arrFILTER_socialnetwork"];
            }
        }
    }
    $APPLICATION->IncludeComponent("bitrix:search.tags.cloud", ".default", $arCloudParams, $component);
}
?>

<form action="" method="get" class="search-page-form mb-4 d-flex align-items-center gap-2">
    <input type="text"
           class="form-control"
           name="q"
           value="<?= htmlspecialcharsbx($arResult["REQUEST"]["QUERY"]) ?>"
           placeholder="Поиск..."
           autocomplete="off">
    <button type="submit" class="btn btn-primary">Найти</button>
    <input type="hidden" name="how" value="<?= $arResult["REQUEST"]["HOW"] == "d" ? "d" : "r" ?>">
</form>

<? if (isset($arResult["REQUEST"]["ORIGINAL_QUERY"])): ?>
    <div class="search-language-guess mb-3">
        <?= GetMessage("CT_BSP_KEYBOARD_WARNING", ["#query#" => '<a href="' . $arResult["ORIGINAL_QUERY_URL"] . '">' . $arResult["REQUEST"]["ORIGINAL_QUERY"] . '</a>']) ?>
    </div>
<? endif; ?>

<div class="left-column-container">
    <!-- Левая колонка — Фильтры -->
    <div class="mb-5">
        <div class="sticky-top" style="top: 40px;">
            <form action="" method="get" id="search-filters-form">
                <?php
                if (!empty($arResult["REQUEST"]["~QUERY"])) {
                    echo '<input type="hidden" name="q" value="' . htmlspecialcharsbx($arResult["REQUEST"]["~QUERY"]) . '">';
                }
                if (!empty($arResult["REQUEST"]["HOW"])) {
                    echo '<input type="hidden" name="how" value="' . htmlspecialcharsbx($arResult["REQUEST"]["HOW"]) . '">';
                }
                if (!empty($arResult["REQUEST"]["TAGS"])) {
                    echo '<input type="hidden" name="tags" value="' . htmlspecialcharsbx($arResult["REQUEST"]["TAGS"]) . '">';
                }
                if (!empty($arResult["REQUEST"]["~FROM"])) {
                    echo '<input type="hidden" name="from" value="' . htmlspecialcharsbx($arResult["REQUEST"]["~FROM"]) . '">';
                }
                if (!empty($arResult["REQUEST"]["~TO"])) {
                    echo '<input type="hidden" name="to" value="' . htmlspecialcharsbx($arResult["REQUEST"]["~TO"]) . '">';
                }
                ?>

                <!-- Блок 1: Выбор инфоблоков (всегда раскрыт) -->
                <div class="card mb-4">
                    <div class="card-header">Искать в:</div>
                    <div class="card-body">
                        <?php if (!empty($arResult["FILTER_IBLOCKS"])): ?>
                            <?php
                            $currentIblocks = $arResult["CURRENT_FILTER"]["IBLOCKS"] ?? [];
                            $allSelected = empty($currentIblocks);
                            ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="filter_iblock_all"
                                        <?= $allSelected ? 'checked' : '' ?>
                                       onchange="toggleAllIblocks(this)">
                                <label class="form-check-label" for="filter_iblock_all"><strong>Все</strong></label>
                            </div>
                            <hr class="my-2">
                            <?php foreach ($arResult["FILTER_IBLOCKS"] as $iblock): ?>
                                <?php
                                $checked = $allSelected || in_array($iblock["ID"], $currentIblocks, true);
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input filter-iblock-cb" type="checkbox"
                                           name="filter_iblock[]"
                                           value="<?= $iblock["ID"] ?>"
                                           id="filter_iblock_<?= $iblock["ID"] ?>"
                                            <?= $checked ? 'checked' : '' ?>
                                           onchange="onIblockChange()">
                                    <label class="form-check-label" for="filter_iblock_<?= $iblock["ID"] ?>">
                                        <?= htmlspecialcharsbx($iblock["NAME"]) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Инфоблоки не найдены</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Блок 2: Свойства по каждому инфоблоку (свёрнуты по умолчанию) -->
                <?php if (!empty($arResult["FILTER_IBLOCKS"])): ?>
                    <?php foreach ($arResult["FILTER_IBLOCKS"] as $iblock): ?>
                        <?php if (empty($iblock["PROPERTIES"])) continue; ?>
                        <div class="card mb-4 filter-iblock-props" data-iblock-id="<?= $iblock["ID"] ?>">
                            <div class="card-header filter-accordion-header collapsed"
                                 onclick="toggleAccordion(this)">
                                <span>Поиск по <?= htmlspecialcharsbx($iblock["NAME"]) ?></span>
                                <span class="accordion-arrow">▼</span>
                            </div>
                            <div class="card-body filter-accordion-body collapsed">
                                <?php foreach ($iblock["PROPERTIES"] as $prop): ?>
                                    <?php
                                    $currentVal = $arResult["CURRENT_FILTER"]["PROPS"][$iblock["ID"]][$prop["CODE"]] ?? null;
                                    ?>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold d-block">
                                            <?= htmlspecialcharsbx($prop["NAME"]) ?>
                                        </label>

                                        <?php if ($prop["PROPERTY_TYPE"] === "L" && !empty($prop["VALUES"])): ?>
                                            <?php foreach ($prop["VALUES"] as $enum): ?>
                                                <?php
                                                $isChecked = false;
                                                if (is_array($currentVal)) {
                                                    $isChecked = in_array($enum["ID"], $currentVal) || in_array($enum["VALUE"], $currentVal);
                                                } elseif ($currentVal !== null && $currentVal !== "") {
                                                    $isChecked = ((string)$currentVal === (string)$enum["ID"] || (string)$currentVal === (string)$enum["VALUE"]);
                                                }
                                                ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="filter_prop[<?= $iblock["ID"] ?>][<?= htmlspecialcharsbx($prop["CODE"]) ?>][]"
                                                           value="<?= $enum["ID"] ?>"
                                                           id="prop_<?= $iblock["ID"] ?>_<?= $prop["ID"] ?>_<?= $enum["ID"] ?>"
                                                            <?= $isChecked ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="prop_<?= $iblock["ID"] ?>_<?= $prop["ID"] ?>_<?= $enum["ID"] ?>">
                                                        <?= htmlspecialcharsbx($enum["VALUE"]) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>

                                        <?php elseif ($prop["PROPERTY_TYPE"] === "N"): ?>
                                            <?php
                                            $from = $arResult["CURRENT_FILTER"]["PROPS"][$iblock["ID"]][$prop["CODE"] . "_from"] ?? "";
                                            $to   = $arResult["CURRENT_FILTER"]["PROPS"][$iblock["ID"]][$prop["CODE"] . "_to"] ?? "";
                                            ?>
                                            <div class="row g-2">
                                                <div class="col">
                                                    <input type="number" class="form-control form-control-sm"
                                                           name="filter_prop[<?= $iblock["ID"] ?>][<?= htmlspecialcharsbx($prop["CODE"]) ?>_from]"
                                                           placeholder="от"
                                                           value="<?= htmlspecialcharsbx($from) ?>">
                                                </div>
                                                <div class="col">
                                                    <input type="number" class="form-control form-control-sm"
                                                           name="filter_prop[<?= $iblock["ID"] ?>][<?= htmlspecialcharsbx($prop["CODE"]) ?>_to]"
                                                           placeholder="до"
                                                           value="<?= htmlspecialcharsbx($to) ?>">
                                                </div>
                                            </div>

                                        <?php else: ?>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="filter_prop[<?= $iblock["ID"] ?>][<?= htmlspecialcharsbx($prop["CODE"]) ?>]"
                                                   value="<?= htmlspecialcharsbx(is_array($currentVal) ? implode(", ", $currentVal) : (string)$currentVal) ?>"
                                                   placeholder="Введите значение">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Блок 3: Период публикации (свёрнут по умолчанию) -->
                <div class="card mb-4">
                    <div class="card-header filter-accordion-header collapsed"
                         onclick="toggleAccordion(this)">
                        <span>Период публикации контента</span>
                        <span class="accordion-arrow">▼</span>
                    </div>
                    <div class="card-body filter-accordion-body collapsed">
                        <div class="filter-date-row">
                            <label class="filter-date-label">с</label>
                            <input type="date"
                                   class="form-control filter-date-input"
                                   name="filter_date_from"
                                   value="<?= htmlspecialcharsbx($arResult["CURRENT_FILTER"]["DATE_FROM"] ?? "") ?>">
                            <label class="filter-date-label">по</label>
                            <input type="date"
                                   class="form-control filter-date-input"
                                   name="filter_date_to"
                                   value="<?= htmlspecialcharsbx($arResult["CURRENT_FILTER"]["DATE_TO"] ?? "") ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">Применить фильтры</button>
                <a href="?<?= !empty($arResult["REQUEST"]["~QUERY"]) ? "q=" . urlencode($arResult["REQUEST"]["~QUERY"]) : "" ?>"
                   class="btn btn-outline-secondary w-100">Сбросить</a>
            </form>
        </div>
    </div>


    <!-- Правая колонка — Результаты -->
    <div class="">
        <?php if ($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false): ?>
        <?php elseif ($arResult["ERROR_CODE"] != 0): ?>
            <p><?= GetMessage("SEARCH_ERROR") ?></p>
            <?php ShowError($arResult["ERROR_TEXT"]); ?>
            <p><?= GetMessage("SEARCH_CORRECT_AND_CONTINUE") ?></p>
            <br/><br/>
            <p><?= GetMessage("SEARCH_SINTAX") ?><br/><b><?= GetMessage("SEARCH_LOGIC") ?></b></p>
            <table border="0" cellpadding="5">
                <tr>
                    <td align="center" valign="top"><?= GetMessage("SEARCH_OPERATOR") ?></td>
                    <td valign="top"><?= GetMessage("SEARCH_SYNONIM") ?></td>
                    <td><?= GetMessage("SEARCH_DESCRIPTION") ?></td>
                </tr>
                <tr>
                    <td align="center" valign="top"><?= GetMessage("SEARCH_AND") ?></td>
                    <td valign="top">and, &amp;, +</td>
                    <td><?= GetMessage("SEARCH_AND_ALT") ?></td>
                </tr>
                <tr>
                    <td align="center" valign="top"><?= GetMessage("SEARCH_OR") ?></td>
                    <td valign="top">or, |</td>
                    <td><?= GetMessage("SEARCH_OR_ALT") ?></td>
                </tr>
                <tr>
                    <td align="center" valign="top"><?= GetMessage("SEARCH_NOT") ?></td>
                    <td valign="top">not, ~</td>
                    <td><?= GetMessage("SEARCH_NOT_ALT") ?></td>
                </tr>
                <tr>
                    <td align="center" valign="top">( )</td>
                    <td valign="top">&nbsp;</td>
                    <td><?= GetMessage("SEARCH_BRACKETS_ALT") ?></td>
                </tr>
            </table>
        <?php elseif (!empty($arResult["SEARCH"])): ?>
            <?php if ($arParams["DISPLAY_TOP_PAGER"] != "N") echo $arResult["NAV_STRING"]; ?>

            <?php
            $grouped = [];
            foreach ($arResult["SEARCH"] as $arItem) {
                $iblockName = $arItem["IBLOCK_NAME"] ?? "Результаты поиска";
                $grouped[$iblockName][] = $arItem;
            }
            ?>

            <?php foreach ($grouped as $groupName => $items): ?>
                <h2 class="mb-4"><?= htmlspecialcharsbx($groupName) ?></h2>

                <div id="items-container" class="news-list news-list__3-columns view-grid mb-5">
                    <?php foreach ($items as $arItem): ?>
                        <div class="card" id="search-item-<?= (int)$arItem["ITEM_ID"] ?>">

                            <button class="favor" data-item="<?= (int)$arItem["ITEM_ID"] ?>"></button>

                            <div class="card-img-container">
                                <?php if (!empty($arItem["PREVIEW_PICTURE"]["SRC"])): ?>
                                    <a href="<?= $arItem["URL"] ?>">
                                        <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="">
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-primary" href="<?= $arItem["URL"] ?>">Подробнее</a>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div class="card-title">
                                    <a href="<?= $arItem["URL"] ?>">
                                        <?= $arItem["TITLE_FORMATED"] ?: htmlspecialcharsbx($arItem["TITLE"]) ?>
                                    </a>
                                </div>

                                <?php if (!empty($arItem["BODY_FORMATED"])): ?>
                                    <p class="card-text"><?= $arItem["BODY_FORMATED"] ?></p>
                                <?php elseif (!empty($arItem["BODY"])): ?>
                                    <p class="card-text"><?= strip_tags($arItem["BODY"]) ?></p>
                                <?php endif; ?>

                                <div class="date-published mt-auto">
                                    <?= htmlspecialcharsbx($arItem["DATE_CREATE"] ?? $arItem["DATE_CREATE"] ?? "") ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <?php if ($arParams["DISPLAY_BOTTOM_PAGER"] != "N") echo $arResult["NAV_STRING"]; ?>

            <br/>
            <p>
                <?php if ($arResult["REQUEST"]["HOW"] == "d"): ?>
                    <a href="<?= $arResult["URL"] ?>&amp;how=r"><?= GetMessage("SEARCH_SORT_BY_RANK") ?></a>&nbsp;|&nbsp;<b><?= GetMessage("SEARCH_SORTED_BY_DATE") ?></b>
                <?php else: ?>
                    <b><?= GetMessage("SEARCH_SORTED_BY_RANK") ?></b>&nbsp;|&nbsp;<a href="<?= $arResult["URL"] ?>&amp;how=d"><?= GetMessage("SEARCH_SORT_BY_DATE") ?></a>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <div class="text-center py-5">
                <h4>Ничего не найдено</h4>
                <p class="text-muted">Попробуйте изменить параметры поиска</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleAccordion(header) {
        header.classList.toggle('collapsed');
        var body = header.nextElementSibling;
        if (body) {
            body.classList.toggle('collapsed');
        }
    }

    function toggleAllIblocks(allCb) {
        var cbs = document.querySelectorAll('.filter-iblock-cb');
        cbs.forEach(function(cb) {
            cb.checked = allCb.checked;
        });
        onIblockChange();
    }

    function onIblockChange() {
        var allCb = document.getElementById('filter_iblock_all');
        var cbs = document.querySelectorAll('.filter-iblock-cb');
        var allChecked = true;
        var anyChecked = false;
        cbs.forEach(function(cb) {
            if (!cb.checked) allChecked = false;
            if (cb.checked) anyChecked = true;
        });
        if (allCb) {
            allCb.checked = allChecked || !anyChecked;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        onIblockChange();
    });
</script>
