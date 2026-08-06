<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}

$arUser = $arResult["arUser"];
?>

<?php ShowError($arResult["strProfileError"]); ?>
<?php if ($arResult['DATA_SAVED'] == 'Y'): ?>
    <?php ShowNote(GetMessage('PROFILE_DATA_SAVED')); ?>
<?php endif; ?>

<?php if ($arResult["SHOW_SMS_FIELD"] == true): ?>

    <form method="post" action="<?= $arResult["FORM_TARGET"] ?>" class="mu-personal__form">
        <?= $arResult["BX_SESSION_CHECK"] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>"/>
        <input type="hidden" name="ID" value="<?= $arResult["ID"] ?>"/>
        <input type="hidden" name="SIGNED_DATA" value="<?= htmlspecialcharsbx($arResult["SIGNED_DATA"]) ?>"/>

        <div class="mu-personal__blocks">
            <div class="mu-personal__block">
                <span class="mu-personal__block-title"><?= GetMessage("main_profile_code") ?></span>
                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="SMS_CODE"
                               value="<?= htmlspecialcharsbx($arResult["SMS_CODE"]) ?>"
                               placeholder="<?= GetMessage("main_profile_code") ?>" autocomplete="off" required>
                    </div>
                </div>
                <button type="submit" name="code_submit_button" class="mu-btn mu-btn--round mu-btn--filled">
                    <span><?= GetMessage("main_profile_send") ?></span>
                </button>
            </div>
        </div>
    </form>

    <script>
        new BX.PhoneAuth({
            containerId: 'bx_profile_resend',
            errorContainerId: 'bx_profile_error',
            interval: <?= (int)$arResult["PHONE_CODE_RESEND_INTERVAL"] ?>,
            data: <?= CUtil::PhpToJSObject(['signedData' => $arResult["SIGNED_DATA"]]) ?>,
            onError: function (response) {
                var errorDiv = BX('bx_profile_error');
                var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                errorNode.innerHTML = '';
                for (var i = 0; i < response.errors.length; i++) {
                    errorNode.innerHTML += BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                }
                errorDiv.style.display = '';
            }
        });
    </script>
    <div id="bx_profile_error" style="display:none"><?php ShowError("error") ?></div>
    <div id="bx_profile_resend"></div>

<?php else: ?>

    <form method="post" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data" class="mu-personal__form">
        <?= $arResult["BX_SESSION_CHECK"] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>"/>
        <input type="hidden" name="ID" value="<?= (int)$arResult["ID"] ?>"/>

        <div class="mu-personal__blocks">

            <!-- Кабинет: email, логин -->
            <div class="mu-personal__block">
                <span class="mu-personal__block-title">Кабинет</span>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="email" name="EMAIL" id="EMAIL"
                               placeholder="E-mail"
                               value="<?= htmlspecialcharsbx($arUser["EMAIL"]) ?>"
                                <?= $arResult["EMAIL_REQUIRED"] ? "required" : "" ?>>
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="LOGIN" id="LOGIN"
                               placeholder="Логин"
                               value="<?= htmlspecialcharsbx($arUser["LOGIN"]) ?>"
                               maxlength="50" required>
                    </div>
                </div>

                <?php if ($arResult["PHONE_REGISTRATION"]): ?>
                    <div class="mu-input">
                        <div class="mu-input__wrap">
                            <input class="mu-input__input" type="text" name="PHONE_NUMBER" id="PHONE_NUMBER"
                                   placeholder="<?= GetMessage("main_profile_phone_number") ?>"
                                   value="<?= htmlspecialcharsbx($arUser["PHONE_NUMBER"]) ?>"
                                    <?= $arResult["PHONE_REQUIRED"] ? "required" : "" ?>>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ФИО -->
            <div class="mu-personal__block">
                <span class="mu-personal__block-title">ФИО</span>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="LAST_NAME" id="LAST_NAME"
                               placeholder="Фамилия"
                               value="<?= htmlspecialcharsbx($arUser["LAST_NAME"]) ?>"
                               maxlength="50">
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="NAME" id="NAME"
                               placeholder="Имя"
                               value="<?= htmlspecialcharsbx($arUser["NAME"]) ?>"
                               maxlength="50">
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="SECOND_NAME" id="SECOND_NAME"
                               placeholder="Отчество"
                               value="<?= htmlspecialcharsbx($arUser["SECOND_NAME"]) ?>"
                               maxlength="50">
                    </div>
                </div>
            </div>

            <!-- Личные данные -->
            <div class="mu-personal__block">
                <span class="mu-personal__block-title">Личные данные</span>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="PERSONAL_PHONE" id="PERSONAL_PHONE"
                               placeholder="Телефон"
                               value="<?= htmlspecialcharsbx($arUser["PERSONAL_PHONE"]) ?>"
                               maxlength="255">
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="PERSONAL_MOBILE" id="PERSONAL_MOBILE"
                               placeholder="Мобильный телефон"
                               value="<?= htmlspecialcharsbx($arUser["PERSONAL_MOBILE"]) ?>"
                               maxlength="255">
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="PERSONAL_CITY" id="PERSONAL_CITY"
                               placeholder="Город"
                               value="<?= htmlspecialcharsbx($arUser["PERSONAL_CITY"]) ?>"
                               maxlength="255">
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="PERSONAL_STREET" id="PERSONAL_STREET"
                               placeholder="Адрес"
                               value="<?= htmlspecialcharsbx($arUser["PERSONAL_STREET"]) ?>"
                               maxlength="255">
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <div class="mu-input__inner-date">
                            <input class="mu-input__input" type="text" name="PERSONAL_BIRTHDAY" id="PERSONAL_BIRTHDAY"
                                   placeholder="Дата рождения (<?= htmlspecialcharsbx($arResult["DATE_FORMAT"]) ?>)"
                                   value="<?= htmlspecialcharsbx($arUser["PERSONAL_BIRTHDAY"]) ?>">
                            <span class="mu-input__date-icon"></span>
                        </div>
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <select class="mu-input__input" name="PERSONAL_GENDER" id="PERSONAL_GENDER">
                            <option value=""><?= GetMessage("USER_DONT_KNOW") ?></option>
                            <option value="M"<?= $arUser["PERSONAL_GENDER"] == "M" ? " selected" : "" ?>><?= GetMessage("USER_MALE") ?></option>
                            <option value="F"<?= $arUser["PERSONAL_GENDER"] == "F" ? " selected" : "" ?>><?= GetMessage("USER_FEMALE") ?></option>
                        </select>
                    </div>
                </div>

                <div class="mu-input">
                    <div class="mu-input__wrap">
                        <input class="mu-input__input" type="text" name="PERSONAL_PROFESSION" id="PERSONAL_PROFESSION"
                               placeholder="<?= GetMessage('USER_PROFESSION') ?>"
                               value="<?= htmlspecialcharsbx($arUser["PERSONAL_PROFESSION"]) ?>"
                               maxlength="255">
                    </div>
                </div>
            </div>

            <!-- Пароль -->
            <div class="mu-personal__block-group">
                <div class="mu-personal__block">
                    <span class="mu-personal__block-title">Смена пароля</span>

                    <div class="mu-input">
                        <div class="mu-input__wrap">
                            <div class="mu-input__inner-password js-input-password">
                                <input class="mu-input__input" type="password" name="NEW_PASSWORD" id="NEW_PASSWORD"
                                       placeholder="Новый пароль"
                                       value="" autocomplete="off" maxlength="50">
                                <span class="mu-input__password-icon"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mu-input">
                        <div class="mu-input__wrap">
                            <div class="mu-input__inner-password js-input-password">
                                <input class="mu-input__input" type="password" name="NEW_PASSWORD_CONFIRM" id="NEW_PASSWORD_CONFIRM"
                                       placeholder="Подтверждение пароля"
                                       value="" autocomplete="off" maxlength="50">
                                <span class="mu-input__password-icon"></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($arResult["TIME_ZONE_ENABLED"]): ?>
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <select class="mu-input__input" name="TIME_ZONE" id="TIME_ZONE">
                                    <?php foreach ($arResult["TIME_ZONE_LIST"] as $tz => $tz_name): ?>
                                        <option value="<?= htmlspecialcharsbx($tz) ?>"<?= ($arUser["TIME_ZONE"] == $tz ? " selected" : "") ?>>
                                            <?= htmlspecialcharsbx($tz_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mu-personal__block">
                    <span class="mu-personal__block-title">Фотография</span>
                    <?php if ($arUser["PERSONAL_PHOTO"]): ?>
                        <div class="mu-personal__photo mb-2">
                            <?= $arUser["PERSONAL_PHOTO_HTML"] ?? "" ?>
                            <label class="form-check-label">
                                <input type="checkbox" name="PERSONAL_PHOTO_del" value="Y"> Удалить фото
                            </label>
                        </div>
                    <?php endif; ?>
                    <div class="mu-file-input">
                        <div class="mu-file-input__wrap">
                            <svg class="file mu-file-input__icon">
                                <use xlink:href="#file"></use>
                            </svg>
                            <label class="mu-file-input__label" for="PERSONAL_PHOTO">Загрузить фото</label>
                            <input class="mu-file-input__input" type="file" name="PERSONAL_PHOTO" id="PERSONAL_PHOTO">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Заметки -->
            <div class="mu-personal__panel-column" style="grid-column: 1 / -1;">
                <div class="mu-textarea">
                    <label class="mu-textarea__label" for="PERSONAL_NOTES">Дополнительная информация</label>
                    <textarea class="mu-textarea__input" name="PERSONAL_NOTES" id="PERSONAL_NOTES"
                              placeholder="Введите текст"><?= htmlspecialcharsbx($arUser["PERSONAL_NOTES"]) ?></textarea>
                </div>
            </div>

            <?php // Пользовательские свойства (UF_*) ?>
            <?php if (!empty($arResult["USER_PROPERTIES"]["DATA"])): ?>
                <div class="mu-personal__block" style="grid-column: 1 / -1;">
				<span class="mu-personal__block-title">
					<?= htmlspecialcharsbx(strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")) ?>
				</span>
                    <?php foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField): ?>
                        <div class="mu-input mb-2">
                            <label class="mu-textarea__label" for="<?= $FIELD_NAME ?>">
                                <?= htmlspecialcharsbx($arUserField["EDIT_FORM_LABEL"]) ?>
                                <?php if ($arUserField["MANDATORY"] == "Y"): ?><span class="starrequired">*</span><?php endif; ?>
                            </label>
                            <div class="mu-input__wrap">
                                <?php
                                $APPLICATION->IncludeComponent(
                                        "bitrix:system.field.edit",
                                        $arUserField["USER_TYPE"]["USER_TYPE_ID"],
                                        [
                                                "bVarsFromForm" => $arResult["bVarsFromForm"],
                                                "arUserField" => $arUserField,
                                        ],
                                        null,
                                        ["HIDE_ICONS" => "Y"]
                                );
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="mu-personal__actions mt-4">
            <button type="submit" name="save" class="mu-btn mu-btn--round mu-btn--filled" value="<?= GetMessage("MAIN_SAVE") ?>">
                <span><?= GetMessage("MAIN_SAVE") ?: "Сохранить" ?></span>
            </button>
            &nbsp;
            <button type="submit" name="apply" class="mu-btn mu-btn--round" value="<?= GetMessage("MAIN_APPLY") ?>">
                <span><?= GetMessage("MAIN_APPLY") ?: "Применить" ?></span>
            </button>
            &nbsp;
            <button type="reset" class="mu-btn mu-btn--round">
                <span><?= GetMessage("MAIN_RESET") ?: "Сбросить" ?></span>
            </button>
        </div>
    </form>

<?php endif; ?>
