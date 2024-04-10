<?

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контент");

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Web\Uri,
    Bitrix\Main\GroupTable,
    Bitrix\Main\UI\PageNavigation;

use Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\PropertyTable,
    Bitrix\Iblock\ElementTable,
    Bitrix\Iblock\ElementPropertyTable,
    Bitrix\Iblock\PropertyEnumerationTable,
    Bitrix\Main\UserTable;

?>

<?php

$arResult = [];

$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
$uri = new Uri($uriString);

$elementId = $request->get("id");
$isNew = $request->get("add");
$iblockId = $request->get("iblock");


//========================================================
if($request->get("update") && !empty($_POST)) {

    $id = $request->get("ID");
    $_POST['ACTIVE'] = $_POST['ACTIVE'] == "on" ? "Y" : "N";
    unset($_POST['ID']);



    $arPREVIEW_PICTURE = CIBlock::makeFileArray(
        array_key_exists("PREVIEW_PICTURE", $_FILES)? $_FILES["PREVIEW_PICTURE"]: $_REQUEST["PREVIEW_PICTURE"],
        ${"PREVIEW_PICTURE_del"} === "Y",
        ${"PREVIEW_PICTURE_descr"}
    );
    if ($arPREVIEW_PICTURE["error"] == 0)
        $arPREVIEW_PICTURE["COPY_FILE"] = "Y";

    $arDETAIL_PICTURE = CIBlock::makeFileArray(
        array_key_exists("DETAIL_PICTURE", $_FILES)? $_FILES["DETAIL_PICTURE"]: $_REQUEST["DETAIL_PICTURE"],
        ${"DETAIL_PICTURE_del"} === "Y",
        ${"DETAIL_PICTURE_descr"}
    );
    if ($arDETAIL_PICTURE["error"] == 0)
        $arDETAIL_PICTURE["COPY_FILE"] = "Y";

    $_POST["PREVIEW_PICTURE"] = $arPREVIEW_PICTURE;
    $_POST["DETAIL_PICTURE"] = $arDETAIL_PICTURE;


    $el = new CIBlockElement;
    if($id) {
        if($res = $el->Update($id, $_POST, "N", true, true)) {
            header("Location: /admin/content/" . $id);
        }
    } else {
        $_POST['CODE'] = $el->generateMnemonicCode($_POST['NAME'], $_POST['IBLOCK_ID']);
        if($id = $el->Add($_POST, "N", true, true)) {
            header("Location: /admin/content/" . $id);
        }
    }

    $strError = "";
    $strError .= $el->LAST_ERROR;
}
//========================================================


$obUsers = UserTable::getList(['select' => ['*']]);
while($user = $obUsers->fetch()) {
    $user['FULL_NAME'] = str_replace(
        "  ",
        " ",
        implode(" ", [$user['NAME'], $user['SECOND_NAME'], $user['LAST_NAME']])
    );
    $arUsers[$user['ID']] = $user;
}


/* список Инфоблоков */
$obIblocks = IblockTable::getList(
    [
        'filter' => [],
        'order' => [],
        'select' => ['*'],
    ]
);
while($iblock = $obIblocks->fetch()) {
    $arIblocks[$iblock['ID']] = $iblock;
}


/* опции для поиска элементов по умолчанию */
$options = [
    'filter' => ['ID' => $elementId],
    'select' => ['*', 'DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL'],
    'order' => ['ID' => 'DESC']
];

if($arResult = ElementTable::getList($options)->fetch()) {
    $arResult['DETAIL_PAGE_URL'] = CIBlock::ReplaceDetailUrl($arResult['DETAIL_PAGE_URL'], $arResult, false, 'E');

    $obProperties = PropertyTable::getList(
        [
            'filter' => ['IBLOCK_ID' => $arResult['IBLOCK_ID'] ?: $iblockId],
            'order' => [],
            'select' => ['*']
        ]
    );
    while($property = $obProperties->fetch()) {
        $arResult['PROPERTIES'][$property['ID']] = $property;
    }


    $obElementPropertiesEnum = PropertyEnumerationTable::getList(
        [
            'filter' => ['PROPERTY_ID' => array_keys($arResult['PROPERTIES'])],
            'select' => ['*']
        ]
    );
    while($property = $obElementPropertiesEnum->fetch()) {
        $propEnum[$property['ID']] = $property['VALUE'];
    }


    $obElementProperties = ElementPropertyTable::getList(
        [
            'filter' => ['IBLOCK_ELEMENT_ID' => $elementId],
            'select' => ['*']
        ]
    );
    while($property = $obElementProperties->fetch()) {
        if($property['VALUE_ENUM']) {
            $arResult['PROPERTIES'][$property['IBLOCK_PROPERTY_ID']]['VALUE_ENUM'][] = $property['VALUE_ENUM'];
            $arResult['PROPERTIES'][$property['IBLOCK_PROPERTY_ID']]['VALUE'][] = $propEnum[$property['VALUE']];
        } else {
            $arResult['PROPERTIES'][$property['IBLOCK_PROPERTY_ID']]['VALUE'][] = $property['VALUE'];
        }
    }
}

?>

    <div class="mu-content__filters">
        <div class="mu-filters">
            <div class="mu-filters__row">
                <a href="/admin/content/add" class="mu-btn mu-btn--add active"><span>+</span> Новая страница</a>
                <a href="/admin/content/?blocked=true"
                   class="mu-btn <?=$_REQUEST['blocked'] == true ? "active" : ""?>"><span>Премодерация</span></a>
                <a href="/admin/content/"
                   class="mu-btn <?=empty($_GET) ? "active" : ""?>"><span>Список страниц</span></a>
            </div>
        </div>

        <form class="mu-content-add__panel" action="?update=y" method="post" enctype="multipart/form-data">
            <input type="hidden" name="ID" value="<?=$arResult['ID']?>">
            <div class="mu-content-add__panel-top">
                <span class="mu-content-add__panel-title"><?=$isNew ? "Добавление" : "Редактирование"?></span>

                <? if(!$isNew) { ?>
                    <span class="mu-content-add__panel-id">ID - <?=$arResult['ID']?></span>
                    <a href="<?=$arResult['DETAIL_PAGE_URL']?>" class="mu-content-add__panel-id">Предпросмотр</a>
                <? } ?>
            </div>
            <? if($strError) { ?>
                <div style="color: red">
                    <?=$strError?>
                    <br>
                </div>
            <? } ?>

            <? if($isNew || $arResult) { ?>
                <div class="mu-content-add__columns">
                    <div class="mu-content__col">
                        <div class="mu-content__row">
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <label class="mu-input__label" for="name">Название</label>
                                    <input class="mu-input__input"
                                           name="NAME"
                                           id="name"
                                           placeholder="Введите название"
                                           value="<?=$arResult['NAME']?>"
                                           required>
                                </div>
                            </div>
                        </div>

                        <? if($arResult['DATE_CREATE']) { ?>
                            <div class="mu-content__row">
                                <div class="mu-content-add__columns">
                                    <div class="mu-content__col">
                                        <div class="mu-input">
                                            <div class="mu-input__wrap">
                                                <label class="mu-input__label" for="date">Дата создания</label>
                                                <div class="mu-input__inner-date">
                                                    <input class="mu-input__input"
                                                           name="DATE_CREATE"
                                                           id="date"
                                                           placeholder=""
                                                           value="<?=$arResult['DATE_CREATE']?>"
                                                           disabled
                                                    >
                                                    <span
                                                            class="mu-input__date-icon"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mu-content__col">
                                        <div class="mu-input">
                                            <div class="mu-input__wrap">
                                                <label class="mu-input__label" for="author">Автор</label>
                                                <a href="/admin/users/<?=$arResult['CREATED_BY']?>"
                                                   class="mu-input__input"
                                                   id="author"><?=$arUsers[$arResult['CREATED_BY']]['FULL_NAME']?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mu-content__row">
                                <div class="mu-content__col">
                                    <div class="">
                                        <p class="mu-content-add__row-label">Активность</p>
                                        <div class="checkbox checkbox--large">
                                            <input class="checkbox__input" name="ACTIVE"
                                                   type="checkbox" <?=$arResult['ACTIVE'] == 'Y' ? "checked" : ""?>>
                                            <label class="checkbox__label">
                                                <p class="checkbox__text"></p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>

                        <div class="mu-content__row">
                            <div class="mu-content-add__columns">
                                <div class="mu-content__col">
                                    <div class="mu-input">
                                        <div class="mu-input__wrap">
                                            <label class="mu-input__label" for="date">Начало активности</label>
                                            <div class="mu-input__inner-date">
                                                <input class="mu-input__input" name="ACTIVE_FROM"
                                                       id="date" placeholder="" value="<?=$arResult['ACTIVE_FROM']?>"
                                                >
                                                <span
                                                        class="mu-input__date-icon"></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mu-content__col">
                                    <div class="mu-input">
                                        <div class="mu-input__wrap">
                                            <label class="mu-input__label" for="date">Окончание активности</label>
                                            <div class="mu-input__inner-date">
                                                <input class="mu-input__input" name="ACTIVE_TO"
                                                       id="date" placeholder="" value="<?=$arResult['ACTIVE_TO']?>"
                                                >
                                                <span
                                                        class="mu-input__date-icon"></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mu-content__row">
                            <div class="mu-content-add__columns">
                                <div class="mu-content__col">
                                    <div class="adm-detail-file-row">
                                        <label class="mu-input__label" for="date">Картинка для анонса</label>
                                        <?
                                        echo \Bitrix\Main\UI\FileInput::createInstance(array(
                                                                                           "name" => "PREVIEW_PICTURE",
                                                                                           "description" => true,
                                                                                           "upload" => true,
                                                                                           "allowUpload" => "I",
                                                                                           "medialib" => true,
                                                                                           "fileDialog" => true,
                                                                                           "cloud" => true,
                                                                                           "delete" => true,
                                                                                           "maxCount" => 1
                                                                                       ))->show(
                                            $arResult['PREVIEW_PICTURE'],
                                            $arResult['PREVIEW_PICTURE'] ? true : false
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="mu-content__col">
                                    <label class="mu-input__label" for="date">Картинка детальная</label>
                                    <?
                                    echo \Bitrix\Main\UI\FileInput::createInstance(array(
                                                                                       "name" => "DETAIL_PICTURE",
                                                                                       "description" => true,
                                                                                       "upload" => true,
                                                                                       "allowUpload" => "I",
                                                                                       "medialib" => true,
                                                                                       "fileDialog" => true,
                                                                                       "cloud" => true,
                                                                                       "delete" => true,
                                                                                       "maxCount" => 1
                                                                                   ))->show(
                                        $arResult['DETAIL_PICTURE'],
                                        $arResult['DETAIL_PICTURE'] ? true : false
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="mu-content__row">
                            <div class="mu-textarea">
                                <label class="mu-textarea__label" for="text">Описание анонса</label>
                                <textarea name="PREVIEW_TEXT"
                                          placeholder="Введите текст для анонса"><?=$arResult['PREVIEW_TEXT']?></textarea>
                            </div>
                        </div>
                        <div class="mu-content__row">
                            <div class="mu-textarea">
                                <label class="mu-textarea__label" for="text">Описание детальное</label>
                                <textarea name="DETAIL_TEXT"
                                          placeholder="Введите основной текст"><?=$arResult['DETAIL_TEXT']?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mu-content__col">
                        <div class="mu-content-add__columns">
                            <div class="mu-content__row span-2">
                                <? if($arResult['IBLOCK_ID']) { ?>
                                    <div class="mu-input">
                                        <div class="mu-input__wrap">
                                            <div class="mu-input__label">Инфоблок</div>
                                            <input class="mu-input__input"
                                                   value="<?=$arIblocks[$arResult['IBLOCK_ID']]['NAME']?>"
                                                   disabled
                                            >
                                        </div>
                                    </div>
                                <? } else { ?>
                                    <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_filled">
                                        <div class="mu-input__label">Инфоблок</div>
                                        <div class="mu-select__toggle-wrap">
                                            <div class="mu-select__toggle"
                                                 data-type="toggle"
                                                 data-default-text="Выбрать инфоблок">
                                                Выбрать инфоблок
                                            </div>
                                        </div>
                                        <div class="mu-select__drop">
                                            <? foreach($arIblocks as $iblock) { ?>
                                                <label class="mu-select__item"
                                                       data-type="iblock-item"
                                                       for="iblock-<?=$iblock['ID']?>">
                                                    <input class="mu-select__item-input js-select-input"
                                                           type="radio"
                                                           id="iblock-<?=$iblock['ID']?>"
                                                           name="IBLOCK_ID"
                                                           data-text="<?=$iblock['NAME']?>"
                                                           value="<?=$iblock['ID']?>">
                                                    <span class="mu-select__item-text"><?=$iblock['NAME']?></span>
                                                </label>
                                            <? } ?>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        <div class="mu-content-add__columns" data-properties_block>

                            <? foreach($arResult['PROPERTIES'] as $property) { ?>
                                <div class="mu-content__row">
                                    <div class="mu-input">
                                        <div class="mu-input__wrap">
                                            <label class="mu-input__label"
                                                   for="<?=$property['CODE']?>"><?=$property['NAME']?></label>
                                            <input class="mu-input__input"
                                                   name="PROPERTY_VALUES[<?=$property['CODE']?>]"
                                                   id="property_<?=$property['CODE']?>"
                                                   value="<?=implode(',', $property['VALUE'])?>"
                                                <?=$property['IS_REQUIRED'] ? "requaired" : "";?>
                                            >
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            <? } else { ?>
                Нет элемента с таким ID
            <? } ?>
            <div class="mu-content-add__panel-button">
                <button class="mu-btn mu-btn--round mu-btn--filled mu-btn--medium"><span>Сохранить</span></button>
            </div>
        </form>

        <script>
          async function getPropertiesByIblockId (e) {
            let response = await fetch('/admin/content/properties.php', {
              method: 'POST',
              body: JSON.stringify({ 'iblockId': e.target.getAttribute('value') }),
            }).then(function (response) {
              return response.text()
            }).then(function (html) {
              document.querySelector('[data-properties_block]').innerHTML = html
            })
          }

          for (let iblockSelect of document.querySelectorAll('[name="IBLOCK_ID"]')) {
            iblockSelect.onclick = getPropertiesByIblockId
          }
          /*
          userForm.onsubmit = async (e) => {
            return
            e.preventDefault()

            let data = new FormData(userForm)
            let response = await fetch('userUpdate.php', {
              method: 'POST',
              body: data,
            })
            let result = await response.json()
            console.log(result)
          }
          */
        </script>
    </div>

    <style>
        .adm-fileinput-wrapper.adm-fileinput-wrapper-single,
        .adm-fileinput-area-container,
        .adm-fileinput-drag-area .bx-bxu-thumb-thumb,
        .adm-fileinput-area-container div.adm-fileinput-item-wrapper {
            width: 100%;
        }
        .adm-fileinput-wrapper-single {
            line-height: 0;
        }
        .adm-fileinput-wrapper-single * {
            line-height: initial;
        }
        .adm-fileinput-wrapper-single .adm-fileinput-area {
            width: 100%;
            min-height: 217px;
            height: 217px;
            padding: 0;
            border-radius: 15px;
        }
        div.adm-fileinput-item {
            width: 100%;
            height: 100%;
            background: #444343;
            margin: 0;
            border-radius: 15px;
        }
        div.adm-fileinput-item-saved {
            box-shadow: inset 0 0 0 2px #f9cb74;
            box-shadow: none;
        }
        div.adm-fileinput-item div.adm-fileinput-item-preview {
            width: initial;
            height: initial;
            background: none;
            box-shadow: none;
            margin: 0 auto;
        }
        .adm-fileinput-item-panel-btn:before,
        .adm-fileinput-item-panel-btn.adm-btn-del:before {
            filter: brightness(2);
        }
        div.adm-fileinput-wrapper-single input.adm-fileinput-drag-area-input {
            top: 0;
            bottom: 0;
            height: 100%;
        }
        .adm-fileinput-drag-area {
            border: 2px dashed #a8a8a8;
        }
        .adm-fileinput-drag-area:hover{
            border: 2px dashed #fff;
        }
        .adm-fileinput-area .adm-fileinput-drag-area-hint {
            color: #a8a8a8;
        }
        .adm-fileinput-area:hover .adm-fileinput-drag-area-hint {
            color: #fff;
        }
        .adm-fileinput-btn-panel {
            display: none;
        }
    </style>


<? require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php"); ?>