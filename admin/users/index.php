<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Пользователи");

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Web\Uri,
    Bitrix\Main\GroupTable,
    Bitrix\Main\UI\PageNavigation,
    Bitrix\Main\UserGroupTable,
    Bitrix\Main\UserTable,
    Bitrix\Main\UserPhoneAuthTable;

?>

<?php /* опции для поиска пользователей по умолчанию */
$options = array(
    'select' => ['*'],
    'order' => ['ID' => 'DESC'],
    "count_total" => true,
);

$arResult = [];

$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
$uri = new Uri($uriString);


/* навигация */
$pageLimit = $request->get("limit") ?: 10;
/* Навигация */
$nav = new PageNavigation("users");
$nav->allowAllRecords(true);
$nav->setPageSize($pageLimit);
$nav->initFromUri();


$options['offset'] = $nav->getOffset();
$options['limit'] = $nav->getLimit();

/* список групп */
$groups = GroupTable::getList(
    [
        'filter' => ["<=C_SORT" => 100],
        'order' => ['C_SORT' => 'ASC'],
        'select' => array('*'),
    ]
);
while($group = $groups->fetch()) {
    $arResult["GROUPS"][$group["ID"]] = $group;
}

/* вывод пользователей из групп */
$curGroup = $request->get("group");
if($curGroup) {
    $params = [
        'select' => [
            'USER_ID',
            'NAME' => 'USER.NAME',
            'LAST_NAME' => 'USER.LAST_NAME'
        ],
        'order' => ['USER.ID' => 'DESC'],
    ];
    if($curGroup == 2) {
        $params['filter']['<GROUP.C_SORT'] = 100;
    }else{
        $params['filter']['GROUP.ID'] = $curGroup;
    }

    $groupsOfUsers = UserGroupTable::getList($params);
    while($arGroup = $groupsOfUsers->fetch()) {
        $userIDs[] = $arGroup["USER_ID"];
    }

    if($curGroup == 2) {
        $options['filter'] = [
            '!ID' => $userIDs
        ];
    }else{
        $options['filter'] = [
            'ID' => $userIDs
        ];
    }

}

/* вывод блокированных пользователей */
$blocked = $request->get("blocked");
if($blocked) {
    $options['filter']['BLOCKED'] = $blocked ? "Y" : "N";
}

/* поиск */
if($request->get("search")) {
    $options['filter'] = [
        'LOGIC' => 'OR',
        "%NAME" => $request->get("search"),
        "%SECOND_NAME" => $request->get("search"),
        "%LAST_NAME" => $request->get("search"),
        "%EMAIL" => $request->get("search")
    ];
}
$users = UserTable::getList($options);
$arResult["USERS_COUNT"] = $users->GetCount();
$nav->setRecordCount($arResult["USERS_COUNT"]);

while($arUser = $users->fetch()) {
    $params = [
        'filter' => ['USER.ID' => $arUser['ID']],
        'select' => ['*'],
        'order' => ['GROUP.C_SORT' => 'ASC'],
        'limit' => 1
    ];
    $groupsOfUser = UserGroupTable::getList($params);
    if($arGroupOfUser = $groupsOfUser->fetch()) {
        $arUser["ROLE"] = $arResult["GROUPS"][$arGroupOfUser["GROUP_ID"]]["NAME"] ?: "Пользователь";
        if($arGroupOfUser["GROUP_ID"] == 1) {
            $arUser["UNEDITABLE"] = true;
        }
    }
    $arUser["FULL_NAME"] = str_replace(
        "  ",
        " ",
        implode(" ", [$arUser["NAME"], $arUser["SECOND_NAME"], $arUser["LAST_NAME"]])
    );
    $arResult["USERS"][] = $arUser;
}

$uriGroup = new Uri($uriString);

$uriLimit = new Uri($uriString);
$limit = $request->get("limit");

?>


    <div>
        <div class="mu-filters">
            <div class="mu-filters__row">
                <a href="/admin/users/add" class="mu-btn mu-btn--add"><span>+</span></a>
                <a href="/admin/users/?blocked=true"
                   class="mu-btn <?=$_REQUEST["blocked"] == true ? "active" : ""?>"><span>На рассмотрении</span></a>
                <a href="/admin/users/"
                   class="mu-btn <?=empty($_GET) ? "active" : ""?>"><span>Все пользователи</span></a>
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
                                           value="<?=$_REQUEST["search"] ?: ""?>">
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
                             data-default-text="Все роли"><?=$curGroup ? $arResult["GROUPS"][$_REQUEST["group"]]["NAME"] : "Все роли"?></div>
                    </div>
                    <div class="mu-select__drop">
                        <? foreach($arResult["GROUPS"] as $arGroup) {
                            $uriGroup->addParams(array("group" => $arGroup["ID"]));
                            $link = $uriGroup->getUri();
                            ?>
                            <label class="mu-select__item"><a href="<?=$link?>">
                                    <span class="mu-select__item-text"><?=$arGroup["NAME"]?></span> </a></label>
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

        <? if($arResult["USERS"]) { ?>
            <div class="mu-users__table">
                <div class="mu-users-table">
                    <div class="mu-users-table__head">
                        <span>ID</span><span>e-mail</span><span>Последний вход</span><span>ФИО</span><span>Роль</span>
                    </div>
                    <? foreach($arResult["USERS"] as $arUser) { ?>
                        <div class="mu-users-table__row" data-id="<?=$arUser["ID"]?>">
                            <div class="mu-input">
                                <? if($arUser["UNEDITABLE"]) { ?>
                                    <div class="mu-input__wrap">
                                        <div class="mu-input__input"><?=$arUser["ID"]?></div>
                                    </div>
                                <? } else { ?>
                                    <a href="<?=$arUser["ID"]?>" class="mu-input__wrap">
                                        <div class="mu-input__input"><?=$arUser["ID"]?></div>
                                    </a>
                                <? } ?>
                            </div>
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <div class="mu-input__input"><?=$arUser["EMAIL"]?></div>
                                </div>
                            </div>
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <div class="mu-input__input"><?=$arUser["LAST_LOGIN"] ? $arUser["LAST_LOGIN"]->format(
                                            "Y-m-d H:i:s"
                                        ) : "-"?></div>
                                </div>
                            </div>
                            <? /*
                                <div class="mu-input">
                                    <div class="mu-input__wrap">
                                        <div class="mu-input__inner-password js-input-password">
                                            <input class="mu-input__input"
                                                   type="password"
                                                   name="PASSWORD"
                                                   placeholder=""
                                                   value="123456"
                                                   >
                                            <span class="mu-input__password-icon"></span>
                                        </div>
                                    </div>
                                </div>
                                */ ?>
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <div class="mu-input__input"><?=$arUser["FULL_NAME"]?></div>
                                </div>
                            </div>
                            <div class="mu-input">
                                <div class="mu-input__wrap">
                                    <div class="mu-input__input" data-type="role"><?=$arUser["ROLE"]?></div>
                                </div>
                            </div>
                            <? if(!$arUser["UNEDITABLE"]) { ?>
                                <button class="mu-btn mu-btn--round mu-btn--filled js-change-role-mu-btn"
                                        data-userid="<?=$arUser["ID"]?>">
                                    <span>Права</span></button>
                                <button class="mu-btn mu-btn--round <?=$arUser["BLOCKED"] == "Y" ? "mu-btn--filled" : ""?> js-block-mu-btn"
                                        data-blocked="<?=$arUser["BLOCKED"]?>"
                                        data-userid="<?=$arUser["ID"]?>"><?=$arUser["BLOCKED"] == "Y" ? "Разблокировать" : "Заблокировать"?></button>
                            <? } ?>
                        </div>
                    <? } ?>
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
            <div>Пользователей с такими параметрами не найдено!</div>
        <? } ?>
    </div>


    <div class="mu-modal js-modal js-change-role-modal">
        <div class="mu-modal__wrap">
            <div class="mu-modal__close js-modal-close"></div>
            <div class="mu-change-role"><span class="mu-change-role__title">Изменить роль</span>
                <div class="mu-change-role__form">
                    <? foreach($arResult["GROUPS"] as $arGroup) {
                        if($arGroup["ID"] == 1) {
                            continue;
                        } ?>
                        <button class="mu-btn mu-btn--large" data-groupid="<?=$arGroup["ID"]?>">
                            <span><?=$arGroup["NAME"]?></span></button>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>


    <script>
      var groups = <?=CUtil::PHPToJSObject($arResult["GROUPS"]);?>;

      $('.js-change-role-mu-btn').on('click', (e) => {
        let userId = e.currentTarget.dataset.userid
        $('.js-change-role-modal').css({ 'display': 'flex' })
        $('.js-change-role-modal .mu-change-role__form').attr('data-userid', userId)
      })
      $('.mu-modal__close').on('click', (e) => {
        $('.js-change-role-modal').css({ 'display': 'none' })
      })
      $('.mu-change-role__form .mu-btn').on('click', (e) => {
        console.log(e)
        $('.js-change-role-modal').css({ 'display': 'none' })
        let userId = $('.mu-change-role__form').attr('data-userid')
        let groupId = e.currentTarget.dataset.groupid
        if (changeRole(userId, groupId)) {
          let groupName = $('.mu-change-role__form [data-groupid="' + groupId + '"] span').text()
          $('.mu-users-table__row[data-id="' + userId + '"] [data-type="role"]').text(groupName)
        }
      })
      $('.js-block-mu-btn').on('click', (e) => {
        let el = e.currentTarget
        let userId = el.dataset.userid
        let userBlocked = el.dataset.blocked

        if (userBlock(userId, userBlocked)) {
          el.dataset.blocked = userBlocked === 'Y' ? 'N' : 'Y'
          if (el.dataset.blocked == 'Y') {
            $(el).text('Разблокировать')
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

      let changeRole = (userId, groupId) => {
        let params = {
          'userId': userId,
          'groupId': groupId,
        }
        return changeRequest(params)
      }
      let userBlock = (userId, userBlocked) => {
        let params = {
          'userId': userId,
          'userBlocked': userBlocked,
        }
        return changeRequest(params)
      }
    </script>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>