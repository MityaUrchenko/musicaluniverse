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
    $arUser = UserTable::getList(
        [
            'filter' => ['ID' => $_POST['elementId']],
            'select' => ['UF_FILES']
        ]
    )->fetch();

    if(empty($_POST['PASSWORD'])) {
        unset($_POST['PASSWORD']);
    }
    $strError = "";
    if($_FILES['photo']) {
        $_POST['PERSONAL_PHOTO'] = $_FILES['photo'];
    }

    if($_FILES['docs']) {
        $_POST['UF_FILES'] = $arUser['UF_FILES'];
        $_POST['UF_FILES'][] = CFile::MakeFileArray($_FILES['docs']['tmp_name']);
    }

    $user = new CUser;
    $user->Update($_POST['elementId'], $_POST);

    $strError .= $user->LAST_ERROR;
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

        <form class="mu-content-add__panel">
            <input type="hidden" name="elementId" value="<?=$arResult['ID']?>">
            <div class="mu-content-add__panel-top">
                <span class="mu-content-add__panel-title"><?=$isNew ? "Добавление" : "Редактирование"?></span>

                <? if(!$isNew) { ?>
                    <span class="mu-content-add__panel-id">ID - <?=$arResult['ID']?></span>
                    <a href="<?=$arResult['DETAIL_PAGE_URL']?>" class="mu-content-add__panel-id">Предпросмотр</a>
                <? } ?>
            </div>

            <? if($isNew || $arResult) { ?>
                <div class="mu-content-add__columns">
                    <div class="mu-content__col">
                        <div class="mu-content__row">
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <label class="mu-input__label" for="name">Название</label>
                                    <input class="mu-input__input"
                                           name="name"
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
                                                           name="date"
                                                           id="date"
                                                           placeholder=""
                                                           value="<?=$arResult['DATE_CREATE']?>"
                                                           required>
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
                                            <input class="checkbox__input"
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
                                                <input class="mu-input__input" name="date"
                                                       id="date" placeholder="" value="<?=$arResult['ACTIVE_FROM']?>"
                                                       required>
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
                                                <input class="mu-input__input" name="date"
                                                       id="date" placeholder="" value="<?=$arResult['ACTIVE_TO']?>"
                                                       required>
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
                                    <div class="mu-file-input">
                                        <div class="mu-file-input__result js-file-input-result"></div>
                                        <div class="mu-file-input__wrap">
                                            <svg class="file mu-file-input__icon">
                                                <use xlink:href="#file"></use>
                                            </svg>
                                            <label class="mu-file-input__label">Картинка анонса</label>
                                            <input
                                                    class="mu-file-input__input js-file-input" type="file">
                                        </div>
                                    </div>
                                    <? if($arResult['PREVIEW_PICTURE']) { ?>
                                        <br>
                                        <img class="w-100"
                                             src="<?=CFile::getpath($arResult['PREVIEW_PICTURE'])?>"
                                             alt="">
                                    <? } ?>
                                </div>
                                <div class="mu-content__col">
                                    <div class="mu-file-input">
                                        <div class="mu-file-input__result js-file-input-result"></div>
                                        <div class="mu-file-input__wrap">
                                            <svg class="file mu-file-input__icon">
                                                <use xlink:href="#file"></use>
                                            </svg>
                                            <label class="mu-file-input__label">Картинка детальная</label>
                                            <input
                                                    class="mu-file-input__input js-file-input" type="file">
                                        </div>
                                    </div>
                                    <? if($arResult['DETAIL_PICTURE']) { ?>
                                        <br>
                                        <img class="w-100"
                                             src="<?=CFile::getpath($arResult['DETAIL_PICTURE'])?>"
                                             alt="">
                                    <? } ?>
                                </div>
                            </div>
                        </div>

                        <div class="mu-content__row">
                            <div class="mu-textarea">
                                <label class="mu-textarea__label" for="text">Описание анонса</label>
                                <textarea name="text"
                                          placeholder="Введите текст или перетащите изображение в поле"><?=$arResult['PREVIEW_TEXT']?></textarea>
                            </div>
                        </div>
                        <div class="mu-content__row">
                            <div class="mu-textarea">
                                <label class="mu-textarea__label" for="text">Описание детальное</label>
                                <textarea name="text"
                                          placeholder="Введите текст или перетащите изображение в поле"><?=$arResult['DETAIL_TEXT']?></textarea>
                            </div>
                        </div>
                    </div>
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
                                                   data-type="item"
                                                   data-id="section-1"
                                                   for="iblock-<?=$iblock['ID']?>">
                                                <input class="mu-select__item-input js-select-input"
                                                       type="radio"
                                                       id="iblock-<?=$iblock['ID']?>"
                                                       name="iblockId"
                                                       data-text="<?=$iblock['NAME']?>">
                                                <span class="mu-select__item-text"><?=$iblock['NAME']?></span>
                                            </label>
                                        <? } ?>
                                    </div>
                                </div>
                            <? } ?>
                        </div>

                        <? foreach($arResult['PROPERTIES'] as $property) { ?>
                            <div class="mu-content__row">
                                <div class="mu-input">
                                    <div class="mu-input__wrap">
                                        <label class="mu-input__label"
                                               for="<?=$property['CODE']?>"><?=$property['NAME']?></label>
                                        <input class="mu-input__input" name="time" id="<?=$property['CODE']?>"
                                               value="<?=implode(',', $property['VALUE'])?>">
                                    </div>
                                </div>
                            </div>
                        <? } ?>
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


<? require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php"); ?>