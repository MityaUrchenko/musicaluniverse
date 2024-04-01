<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Пользователь");

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

<?php
$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
$uri = new Uri($uriString);


//========================================================
if($request->get("update") && !empty($_POST)) {

    $arUser = UserTable::getList([
         'filter' => ['ID' => $_POST["userId"]],
         'select' => ['*','UF_FILES']
     ])->fetch();

    if(empty($_POST["PASSWORD"])) {
        unset($_POST["PASSWORD"]);
    }
    $strError = "";
    if($_FILES["photo"]) {
        $_POST["PERSONAL_PHOTO"] = $_FILES["photo"];
    }

    if($_FILES["docs"]) {
        $_POST["UF_FILES"] = $arUser['UF_FILES'];
        $_POST["UF_FILES"][] = CFile::MakeFileArray($_FILES["docs"]["tmp_name"]);
    }

    $user = new CUser;
    if($arUser){
        $user->Update($_POST["userId"], $_POST);
    }else{
        if($userId = $user->Add($_POST)) {
            header("Location: /admin/users/");
        }
    }

    $strError .= $user->LAST_ERROR;
}
//========================================================


$arResult = [];

/* список групп */
$groups = GroupTable::getList(
    [
        'filter' => ["<=C_SORT" => 100],
        'order' => ['C_SORT' => 'ASC'],
        'select' => array('*'),
    ]
);
while($group = $groups->fetch()) {
    if(empty($group["STRING_ID"])) {
        continue;
    }
    $arResult["GROUPS"][$group["ID"]] = $group;
}

$userId = $request->get("id");
$isNew = $request->get("add");
$arUser = [];
if(!$isNew) {
    $arUser = UserTable::getList([
        'filter' => ['ID' => $userId],
        'select' => ['*', 'UF_FILES']
    ])->fetch();

    if($arUser) {
        $params = [
            'filter' => ['USER.ID' => $arUser['ID']],
            'select' => ['GROUP_ID'],
            'order' => ['GROUP.C_SORT' => 'ASC'],
            'limit' => 1
        ];
        $groupsOfUser = UserGroupTable::getList($params);
        if($arGroupOfUser = $groupsOfUser->fetch()) {
            $arUser["GROUP"] = $arGroupOfUser["GROUP_ID"];
            $arUser["ROLE"] = $arResult["GROUPS"][$arGroupOfUser["GROUP_ID"]]["NAME"] ?: "Пользователь";
        }

        $options = [
            'filter' => ['USER_ID' => $userId],
            'select' => ['PHONE_NUMBER']
        ];
        $arUser['PHONE_NUMBER'] = UserPhoneAuthTable::getList($options)->fetch()['PHONE_NUMBER'];
    }
}
if($arUser["GROUP"] == 1) {
    header("Location: /admin/users/");
}
?>

    <div class="mu-users__filters">
        <div class="mu-filters">
            <div class="mu-filters__row">
                <a href="/admin/users/add" class="mu-btn mu-btn--add active"><span>+</span></a>
                <a href="/admin/users/?consideration=true"
                   class="mu-btn <?=$_REQUEST["consideration"] == true ? "active" : ""?>"><span>На рассмотрении</span></a>
                <a href="/admin/users/"
                   class="mu-btn <?=empty($_GET) ? "active" : ""?>"><span>Все пользователи</span></a>
            </div>
        </div>

        <? if($isNew || $arUser) { ?>
            <div class="mu-users__panel">
                <div class="mu-users__panel-tabs">
                    <span class="mu-users__panel-tab active">русский</span>
                    <span class="mu-users__panel-tab">english</span>
                    <span class="mu-users__panel-tab">deutsche</span>
                </div>
                <form id="userForm"
                      class="mu-users__panel-inner"
                      enctype="multipart/form-data"
                      method="post"
                      action="?update=y">
                    <input type="hidden" name="userId" value="<?=$arUser["ID"]?>">
                    <div class="mu-users__panel-top">
                        <span class="mu-users__panel-title"><?=$isNew ? "Добавление" : "Редактирование"?></span>
                        <? if(!$isNew) { ?>
                            <span class="mu-users__panel-id">ID - <?=$arUser["ID"]?></span>
                        <? } ?>
                    </div>
                    <? if($strError) { ?>
                        <div style="color: red">
                            <?=$strError?>
                            <br>
                        </div>
                    <? } ?>
                    <div class="mu-users__panel-row">
                        <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_filled">
                            <div class="mu-select__title">Выберите роль</div>
                            <div class="mu-select__toggle-wrap">
                                <div class="mu-select__toggle"
                                     data-type="toggle"
                                     data-default-text="<?=$arUser["ROLE"] ?: "Выберите роль";?>">
                                    Выберите роль
                                </div>
                            </div>
                            <div class="mu-select__drop">

                                <? foreach($arResult["GROUPS"] as $arGroup) {
                                    if($arGroup["ID"] == 1) {
                                        continue;
                                    }
                                    ?>
                                    <label class="mu-select__item"
                                           data-type="item"
                                           for="role_id-<?=$arGroup["ID"]?>"
                                           data-groupid="<?=$arGroup["ID"]?>">
                                        <input class="mu-select__item-input js-select-input"
                                               type="radio"
                                               id="role_id-<?=$arGroup["ID"]?>"
                                               value="<?=$arGroup["ID"]?>"
                                               name="role"
                                               data-text="<?=$arGroup["NAME"]?>">
                                        <span class="mu-select__item-text"><?=$arGroup["NAME"]?></span>
                                    </label>
                                <? } ?>

                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="email">Почта</label>
                                <input class="mu-input__input"
                                       type="email"
                                       name="EMAIL"
                                       id="email"
                                       value="<?=$arUser["EMAIL"]?>"
                                       placeholder="Введите e-mail"
                                >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="login">Логин</label>
                                <input class="mu-input__input"
                                       name="LOGIN"
                                       id="login"
                                       placeholder="Введите логин"
                                       value="<?=$arUser["LOGIN"]?>"
                                >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="password">Пароль</label>
                                <div class="mu-input__inner-password js-input-password">
                                    <input class="mu-input__input"
                                           type="password"
                                           name="PASSWORD"
                                           id="password"
                                           placeholder="Введите пароль"
                                    >
                                    <span class="mu-input__password-icon"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mu-users__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="LAST_NAME">Фамилия</label>
                                <input class="mu-input__input"
                                       name="LAST_NAME"
                                       id="LAST_NAME"
                                       placeholder="Введите фамилию"
                                       value="<?=$arUser["LAST_NAME"]?>"
                                >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="name">Имя</label>
                                <input class="mu-input__input"
                                       name="NAME"
                                       id="name"
                                       placeholder="Введите имя"
                                       value="<?=$arUser["NAME"]?>"
                                >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="SECOND_NAME">Отчество</label>
                                <input class="mu-input__input"
                                       name="SECOND_NAME"
                                       id="SECOND_NAME"
                                       placeholder="Введите отчество"
                                       value="<?=$arUser["SECOND_NAME"]?>"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="mu-users__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="phone">Телефон</label>
                                <input class="mu-input__input"
                                       type="phone"
                                       name="PHONE_NUMBER"
                                       id="phone"
                                       placeholder="Введите телефон"
                                       value="<?=$arUser["PHONE_NUMBER"]?>"
                                >
                            </div>
                        </div>
                        <div class="mu-file-input">
                            <div class="mu-file-input__result js-file-input-result"></div>
                            <div class="mu-file-input__wrap">
                                <svg class="file mu-file-input__icon">
                                    <use xlink:href="#file"></use>
                                </svg>
                                <label class="mu-file-input__label">Фото профиля</label>
                                <input class="mu-file-input__input js-file-input" name="photo" type="file">
                            </div>
                        </div>
                        <div class="mu-header-menu__personal-avatar">
                            <?
                            $photo = CFile::GetPath($arUser["PERSONAL_PHOTO"]);
                            ?>
                            <? if($photo) { ?>
                                <img src="<?=$photo?>" alt="">
                            <? } ?>
                        </div>
                    </div>
                    <div class="mu-users__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="">Серия и номер паспорта</label>
                                <input class="mu-input__input"
                                       name=""
                                       id=""
                                       placeholder="Введите серию и номер"
                                       >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="">Кем выдан</label>
                                <input class="mu-input__input" name="" id="" placeholder="Укажите" >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="">Код подразделения</label>
                                <input class="mu-input__input" name="" id="" placeholder="Укажите" >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="">Когда выдан паспорт</label>
                                <input class="mu-input__input" name="" id="" placeholder="Укажите" >
                            </div>
                        </div>
                    </div>
                    <div class="mu-users__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label"
                                       for="LAST_NAME">Прописка
                                </label>
                                <input class="mu-input__input"
                                       name="LAST_NAME"
                                       id="LAST_NAME"
                                       placeholder="Укажите"
                                       >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="name">Место рождения</label>
                                <input class="mu-input__input" name="NAME" id="name" placeholder="Укажите" >
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <label class="mu-input__label" for="SECOND_NAME">Дата рождения</label>
                                <input class="mu-input__input"
                                       name="SECOND_NAME"
                                       id="SECOND_NAME"
                                       placeholder="Укажите"
                                       >
                            </div>
                        </div>
                    </div>
                    <?/*
                    <div class="mu-users__panel-row">
                        <div class="mu-file-input">
                            <div class="mu-file-input__result js-file-input-result">
                                <div class="mu-file">
                                    <div class="mu-file__icon">
                                        <svg class="doc">
                                            <use xlink:href="#doc"></use>
                                        </svg>
                                    </div>
                                    <span class="mu-file__name">Подтверждающий документ номер один.doc</span>
                                    <button class="mu-file__delete" type="button">
                                        <svg class="close">
                                            <use xlink:href="#close"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mu-file">
                                    <div class="mu-file__icon">
                                        <svg class="pdf">
                                            <use xlink:href="#pdf"></use>
                                        </svg>
                                    </div>
                                    <span class="mu-file__name">Подтверждающий документ номер два с д....pdf</span>
                                    <button class="mu-file__delete" type="button">
                                        <svg class="close">
                                            <use xlink:href="#close"></use>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mu-file">
                                    <div class="mu-file__icon">
                                        <svg class="pdf">
                                            <use xlink:href="#pdf"></use>
                                        </svg>
                                    </div>
                                    <span class="mu-file__name">Подтверждающий документ номер два с д....pdf</span>
                                    <button class="mu-file__delete" type="button">
                                        <svg class="close">
                                            <use xlink:href="#close"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mu-file-input__wrap">
                                <svg class="file mu-file-input__icon">
                                    <use xlink:href="#file"></use>
                                </svg>
                                <label class="mu-file-input__label">Загрузить документы</label>
                                <input class="mu-file-input__input js-file-input" name="docs" multiple type="file">
                            </div>
                        </div>
                    </div>
 */?>
                    <br>
                    <div class="mu-users__panel-textarea">
                        <div class="mu-textarea">
                            <label class="mu-textarea__label" for="text">Информация о пользователе</label>
                            <textarea name="PERSONAL_NOTES"
                                      placeholder="Введите текст"><?=$arUser["PERSONAL_NOTES"]?></textarea>
                        </div>
                    </div>
                    <div class="mu-users__panel-button">
                        <button class="mu-btn mu-btn--round mu-btn--filled">
                            <span><?=$isNew ? "Создать" : "Сохранить"?></span></button>
                    </div>
                </form>
            </div>
        <? } else { ?>
            Нет пользователя с таким ID
        <? } ?>


        <script>
          /*
          userForm.onsubmit = async (e) => {
            return
            e.preventDefault()

            let data = new FormData(userForm)
            let response = await fetch('update.php', {
              method: 'POST',
              body: data,
            })
            let result = await response.json()
            console.log(result)
          }
          */
        </script>
    </div>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>