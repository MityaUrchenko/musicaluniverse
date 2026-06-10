<?

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контент");

use Bitrix\Main\Application,
    Bitrix\Main\Page\Asset,
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


Asset::getInstance()->addCss("/admin/content/style.css");
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
        array_key_exists("PREVIEW_PICTURE", $_FILES) ? $_FILES["PREVIEW_PICTURE"] : $_REQUEST["PREVIEW_PICTURE"],
        ${"PREVIEW_PICTURE_del"} === "Y",
        ${"PREVIEW_PICTURE_descr"}
    );
    if($arPREVIEW_PICTURE["error"] == 0) {
        $arPREVIEW_PICTURE["COPY_FILE"] = "Y";
    }

    $arDETAIL_PICTURE = CIBlock::makeFileArray(
        array_key_exists("DETAIL_PICTURE", $_FILES) ? $_FILES["DETAIL_PICTURE"] : $_REQUEST["DETAIL_PICTURE"],
        ${"DETAIL_PICTURE_del"} === "Y",
        ${"DETAIL_PICTURE_descr"}
    );
    if($arDETAIL_PICTURE["error"] == 0) {
        $arDETAIL_PICTURE["COPY_FILE"] = "Y";
    }

    $_POST["PREVIEW_PICTURE"] = $arPREVIEW_PICTURE;
    $_POST["DETAIL_PICTURE"] = $arDETAIL_PICTURE;


    $el = new CIBlockElement;
    if($id) {
        if($res = $el->Update($id, $_POST, "N", true, true)) {
            header("Location: /admin/content/" . $id);
        }
    } else {
        if(!$_POST['CODE']){
            $_POST['CODE'] = $el->generateMnemonicCode($_POST['NAME'], $_POST['IBLOCK_ID']);
        }
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

$bLinked = false;
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
                                           id="NAME"
                                           placeholder="Введите название"
                                           value="<?=$arResult['NAME']?>"
                                           required>
                                </div>
                            </div>
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <label class="mu-input__label" for="code">Символьный код</label>
                                    <img id="code_link" title="<?echo GetMessage("IBEL_E_LINK_TIP")?>" class="linked" src="/bitrix/themes/.default/icons/iblock/<?if($bLinked) echo 'link.gif'; else echo 'unlink.gif';?>" onclick="set_linked()" />

                                    <input class="mu-input__input"
                                           name="CODE"
                                           id="CODE"
                                           placeholder="Введите символьный код"
                                           value="<?=$arResult['CODE']?>"
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
                                                    <span class="mu-input__date-icon"></span>
                                                </div>
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
                                                <span class="mu-input__date-icon"></span>
                                            </div>
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
                                                <span class="mu-input__date-icon"></span>
                                            </div>
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
                                        echo \Bitrix\Main\UI\FileInput::createInstance(
                                            array(
                                                "name" => "PREVIEW_PICTURE",
                                                "description" => true,
                                                "upload" => true,
                                                "allowUpload" => "I",
                                                "medialib" => true,
                                                "fileDialog" => true,
                                                "cloud" => true,
                                                "delete" => true,
                                                "maxCount" => 1
                                            )
                                        )->show(
                                            $arResult['PREVIEW_PICTURE'],
                                            $arResult['PREVIEW_PICTURE'] ? true : false
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="mu-content__col">
                                    <label class="mu-input__label" for="date">Картинка детальная</label>
                                    <?
                                    echo \Bitrix\Main\UI\FileInput::createInstance(
                                        array(
                                            "name" => "DETAIL_PICTURE",
                                            "description" => true,
                                            "upload" => true,
                                            "allowUpload" => "I",
                                            "medialib" => true,
                                            "fileDialog" => true,
                                            "cloud" => true,
                                            "delete" => true,
                                            "maxCount" => 1
                                        )
                                    )->show(
                                        $arResult['DETAIL_PICTURE'],
                                        $arResult['DETAIL_PICTURE'] ? true : false
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="mu-content__row">
                            <div class="mu-textarea">
                                <label class="mu-textarea__label" for="bxed_PREVIEW_TEXT">Описание анонса</label>
                                <?
                                CFileMan::AddHTMLEditorFrame(
                                    "PREVIEW_TEXT",
                                    $arResult['PREVIEW_TEXT'],
                                    "PREVIEW_TEXT_TYPE",
                                    mb_strtolower($arResult['PREVIEW_TEXT_TYPE']),
                                    array(
                                        'height' => "auto",
                                        'width' => '100%',
                                    ),
                                    "N",
                                    0,
                                    "",
                                    "",
                                    "s1",
                                    true,
                                    false,
                                    array(
                                        'toolbarConfig' => CFileMan::GetEditorToolbarConfig(
                                            "iblock_" . (defined(
                                                'BX_PUBLIC_MODE'
                                            ) && BX_PUBLIC_MODE == 1 ? 'public' : 'admin')
                                        ),
                                        'saveEditorKey' => $arResult['IBLOCK_ID'],
                                        'hideTypeSelector' => false,
                                    )
                                ); ?>
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
                                                   data-current_iblock="<?=$arResult['IBLOCK_ID']?>"
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

                        </div>
                    </div>

                    <div class="mu-content__col span-2">
                            <div class="mu-textarea">
                                <label class="mu-textarea__label" for="bxed_DETAIL_TEXT">Описание детальное</label>
                                <?
                                CFileMan::AddHTMLEditorFrame(
                                    "DETAIL_TEXT",
                                    $arResult['DETAIL_TEXT'],
                                    "DETAIL_TEXT_TYPE",
                                    mb_strtolower($arResult['DETAIL_TEXT_TYPE']),
                                    array(
                                        'height' => "auto",
                                        'width' => '100%',
                                    ),
                                    "N",
                                    0,
                                    "",
                                    "",
                                    "s1",
                                    true,
                                    false,
                                    array(
                                        'toolbarConfig' => true,
                                        'saveEditorKey' => $arResult['IBLOCK_ID'],
                                        'hideTypeSelector' => false,
                                    )
                                ); ?>
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
          async function getPropertiesByIblockId (iblockId) {
            await fetch('/admin/content/properties.php', {
              method: 'POST',
              body: JSON.stringify({ 'iblockId': iblockId }),
            }).then(function (response) {
              return response.text()
            }).then(function (html) {
              document.querySelector('[data-properties_block]').innerHTML = html
            })
          }
          function requestPropertiesOnClick (e) {
            getPropertiesByIblockId (e.target.getAttribute('value'))
          }



          for (let iblockSelect of document.querySelectorAll('[name="IBLOCK_ID"]')) {
            iblockSelect.onclick = requestPropertiesOnClick
          }
          getPropertiesByIblockId (document.querySelector('[data-current_iblock]').getAttribute('data-current_iblock'))
        </script>
    </div>

<?
$arTranslit = array
(
    'UNIQUE' => 'Y',
    'TRANSLITERATION' => 'Y',
    'TRANS_LEN' => 100,
    'TRANS_CASE' => 'L',
    'TRANS_SPACE' => '-',
    'TRANS_OTHER' => '-',
    'TRANS_EAT' => 'Y',
    'USE_GOOGLE' => 'N'
);

if($arTranslit["TRANSLITERATION"] == "Y")
{
    CJSCore::Init(array('translit'));
    ?>
    <script type="text/javascript">
        var linked=<?if($bLinked) echo 'true'; else echo 'false';?>;
        function set_linked()
        {
          linked=!linked;
          var code_link = document.getElementById('code_link');
          if(code_link)
          {
            if(linked)
              code_link.src='/bitrix/themes/.default/icons/iblock/link.gif';
            else
              code_link.src='/bitrix/themes/.default/icons/iblock/unlink.gif';
          }
        }
        var oldValue = '';
        function transliterate()
        {
          if(linked)
          {
            var from = document.getElementById('NAME');
            var to = document.getElementById('CODE');
            if(from && to && oldValue != from.value)
            {
              BX.translit(from.value, {
                'max_len' : <?echo intval($arTranslit['TRANS_LEN'])?>,
                'change_case' : '<?echo $arTranslit['TRANS_CASE']?>',
                'replace_space' : '<?echo $arTranslit['TRANS_SPACE']?>',
                'replace_other' : '<?echo $arTranslit['TRANS_OTHER']?>',
                'delete_repeat_replace' : <?echo $arTranslit['TRANS_EAT'] == 'Y'? 'true': 'false'?>,
                'use_google' : <?echo $arTranslit['USE_GOOGLE'] == 'Y'? 'true': 'false'?>,
                'callback' : function(result){to.value = result; setTimeout('transliterate()', 250); }
              });
              oldValue = from.value;
            }
            else
            {
              setTimeout('transliterate()', 250);
            }
          }
          else
          {
            setTimeout('transliterate()', 250);
          }
        }
        transliterate();
    </script>
    <?
}?>

<? require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php"); ?>