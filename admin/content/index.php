<?

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("Контент");

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Web\Uri,
    Bitrix\Main\GroupTable,
    Bitrix\Main\UI\PageNavigation,
    Bitrix\Main\UserGroupTable,
    Bitrix\Main\UserTable;

use Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\ElementTable;

?>

<?php
$arResult = [];

/* список пользователей */
$users = UserTable::getList([
    'select' => ['*'],
    'order' => ['ID' => 'ASC'],
    "count_total" => true,
]);
while($user = $users->fetch()) {
    $arResult['USERS'][$user['ID']] = $user;
}



/* опции для поиска элементов по умолчанию */
$options = array(
    'select' => ['*','DETAIL_PAGE_URL' => 'IBLOCK.DETAIL_PAGE_URL'],
    'order' => ['ID' => 'DESC'],
    "count_total" => true,
);


$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
$uri = new Uri($uriString);


/* навигация */
$pageLimit = $request->get("limit") ?: 10;
/* Навигация */
$nav = new PageNavigation("items");
$nav->allowAllRecords(true);
$nav->setPageSize($pageLimit);
$nav->initFromUri();


$options['offset'] = $nav->getOffset();
$options['limit'] = $nav->getLimit();

/* список Инфоблоков */
$iblocks = IblockTable::getList([
    'filter' => [],
    'order' => [],
    'select' => ['*'],
]);
while($iblock = $iblocks->fetch()) {
    $arResult['IBLOCKS'][$iblock['ID']] = $iblock;
}


/* вывод элементов из одного инфоблока */
$curCategory = $request->get("category");
if($curCategory) {
    $options['filter']['IBLOCK_ID'] = $curCategory;
}


/* вывод блокированных элементов */
$blocked = $request->get("blocked");
if($blocked) {
    $options['filter']['ACTIVE'] = $blocked ? "N" : "Y";
}

/* поиск */
if($request->get("search")) {
    $options['filter'] = [
        'LOGIC' => 'OR',
        "%NAME" => $request->get("search"),
        "%PREVIEW_TEXT" => $request->get("search"),
        "%DETAIL_TEXT" => $request->get("search")
    ];
}
$elements = ElementTable::getList($options);
$arResult['ELEMENTS_COUNT'] = $elements->GetCount();
$nav->setRecordCount($arResult['ELEMENTS_COUNT']);

while($arElement = $elements->fetch()) {
    $arResult['ITEMS'][] = $arElement;
}

$uriCategory = new Uri($uriString);

$uriLimit = new Uri($uriString);
$limit = $request->get("limit");

?>


    <div>
        <div class="mu-filters">
            <div class="mu-filters__row">
                <a href="/admin/content/add" class="mu-btn mu-btn--add"><span>+</span> Новая страница</a>
                <a href="/admin/content/?blocked=true"
                   class="mu-btn <?=$_REQUEST['blocked'] == true ? "active" : ""?>"><span>Премодерация</span></a>
                <a href="/admin/content/"
                   class="mu-btn <?=empty($_GET) ? "active" : ""?>"><span>Список страниц</span></a>
            </div>
        </div>
        <div class="mu-filters">
            <div class="mu-filters__row">
                <div class="mu-filters__input">
                    <div class="mu-input">
                        <div class="mu-input__wrap">
                            <div class="mu-input__inner">
                                <svg class="search mu-input__icon">
                                    <use xlink:href="#search"></use>
                                </svg>
                                <form action="" style="display: flex; gap: 10px;">
                                    <input class="mu-input__input"
                                           name="search"
                                           id="search"
                                           placeholder="Искать"
                                           value="<?=$_REQUEST['search'] ?: ""?>">
                                    <button class="mu-btn mu-btn--round mu-btn--filled" type="submit">
                                        <span>Искать</span></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mu-select js-mu-select js-select-single mu-select_single">
                    <div class="mu-select__toggle-wrap">
                        <div class="mu-select__toggle"
                             data-type="toggle"
                             data-default-text="Все инфоблоки"><?=$curCategory ? $arResult['IBLOCKS'][$_REQUEST['category']]['NAME'] : "Все инфоблоки"?></div>
                    </div>
                    <div class="mu-select__drop">
                        <? foreach($arResult['IBLOCKS'] as $arIblock) {
                            $uriCategory->addParams(array("category" => $arIblock['ID']));
                            $link = $uriCategory->getUri();
                            ?>
                            <label class="mu-select__item"><a href="<?=$link?>">
                                    <span class="mu-select__item-text"><?=$arIblock['NAME']?></span> </a></label>
                        <? } ?>
                    </div>
                </div>

                <div class="mu-select js-mu-select js-select-single mu-select_single">
                    <div class="mu-select__toggle-wrap">
                        <div class="mu-select__toggle"
                             data-type="toggle"
                             data-default-text="Выдача"><?=$limit ? "По " . $limit : "Выдача";?></div>
                    </div>
                    <div class="mu-select__drop">
                        <? foreach([5, 10, 20, 50, 100] as $arLimit) {
                            $uriLimit->addParams(array("limit" => $arLimit));
                            $uriLimit->deleteParams(array("users"));
                            $link = $uriLimit->getUri();
                            ?>
                            <label class="mu-select__item"><a href="<?=$link?>">
                                    <span class="mu-select__item-text">По <?=$arLimit?></span> </a>
                            </label> <? } ?>
                    </div>
                </div>
            </div>
        </div>

        <? if($arResult['ITEMS']) {?>

            <div class="mu-users__table">
                <div class="mu-content-table">
                    <div class="mu-content-table__head">
                        <span>Название</span><span>ID</span><span>Язык</span><span>Инфоблок</span><span>Автор</span><span>Дата</span>
                    </div>
                    <? foreach($arResult['ITEMS'] as $arItem) { ?>
                        <div class="mu-content-table__row"><a href="<?=$arItem['ID']?>" class="mu-content-table__row-title"><?=$arItem['NAME']?></a>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <div class="mu-input__input"><?=$arItem['ID']?></div>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <input class="mu-input__input" name="lang" id="lang" placeholder="" value="Рус." required>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <div class="mu-input__input"><?=$arResult['IBLOCKS'][$arItem['IBLOCK_ID']]['NAME']?></div>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <div class="mu-input__input"><?=$arResult['USERS'][$arItem['CREATED_BY']]['EMAIL']?></div>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <div class="mu-input__inner-date">
                                    <div class="mu-input__input"><?=explode(" ",$arItem['DATE_CREATE'])[0]?></div>
                                    <span class="mu-input__date-icon"></span>
                                </div>
                            </div>
                        </div>
                        <button class="mu-btn mu-btn--round <?=$arItem['ACTIVE']=="Y"?"":"mu-btn--filled"?> mu-btn--medium  js-block-mu-btn"
                                data-blocked="<?=$arItem['ACTIVE']=="Y"?"N":"Y"?>"
                                data-elementId="<?=$arItem['ID']?>">
                            <span><?=$arItem['ACTIVE']=="Y"?"Заблокировать":"Утвердить"?></span>
                        </button>
                    </div>
                    <?}?>
                </div>
            </div>

            <?
            $APPLICATION->IncludeComponent(
                "bitrix:main.pagenavigation",
                "users_nav",
                [
                    "NAV_OBJECT" => $nav,
                    "SEF_MODE" => "N",
                ],
                false
            );
            ?>
        <? } else { ?>
            <div>Контента с такими параметрами не найдено!</div>
        <? } ?>
    </div>

    <script>
      
      $('.js-block-mu-btn').on('click', (e) => {
        let el = e.currentTarget
        let elementId = el.dataset.elementid
        let blocked = el.dataset.blocked

        if (elementBlock(elementId, blocked)) {
          el.dataset.blocked = blocked === 'Y' ? 'N' : 'Y'
          if (el.dataset.blocked == 'Y') {
            $(el).text('Утвердить')
            $(el).addClass("mu-btn--filled")
          }
          else {
            $(el).text('Заблокировать')
            $(el).removeClass("mu-btn--filled")
          }
        }
      })

      let changeRequest = async params => {
        let response = await fetch('update.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json;charset=utf-8',
          },
          body: JSON.stringify(params),
        })
        let result = await response.json()
        return result.success
      }

      let elementBlock = (elementId, blocked) => {
        let params = {
          'elementId': elementId,
          'blocked': blocked,
        }
        return changeRequest(params)
      }
    </script>


<? require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php"); ?>