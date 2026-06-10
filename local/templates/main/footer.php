<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

    <?if($curDir != "/"){ ?>
        </div><!--end .page__main-->
    <?}?>

    </div><!--end .mu_container-->

	<footer class="py-5">
			<div class="mu-container">
				<div class="row mb-5">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="row pr-md-2">
                            <div class="col-12 col-md-4">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_menu_1.php"
                                    ),
                                    false
                                );?>
                            </div>
                            <div class="col-12 col-md-3">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_menu_2.php"
                                    ),
                                    false
                                );?>
                            </div>
                            <div class="col-12 col-md-5">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_menu_3.php"
                                    ),
                                    false
                                );?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="row px-md-3">
                            <div class="col-12 col-md-6">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_menu_4.php"
                                    ),
                                    false
                                );?>
                            </div>
                            <div class="col-12 col-md-6">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_menu_5.php"
                                    ),
                                    false
                                );?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="row pl-md-2">
                            <div class="col-12 col-md-6">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_social.php"
                                    ),
                                    false
                                );?>
                            </div>
                            <div class="col-12 col-md-6">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR."include/footer_contacts.php"
                                    ),
                                    false
                                );?>
                            </div>
                        </div>
                    </div>
				</div>

				<div class="row mb-5">
                    <div class="col-12 col-md-4">
                        <div class="pr-md-2">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/footer_text_1.php"
                                ),
                                false
                            );?>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="px-md-3">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/footer_text_2.php"
                                ),
                                false
                            );?>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="pl-md-2">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/footer_text_3.php"
                                ),
                                false
                            );?>
                        </div>
                    </div>
				</div>

                <div class="text-center">
                    GTU GROUP, 2022
                </div>
			</div>
	</footer>
</div> <!-- //bx-wrapper -->



<div class="mu-modal js-modal js-login-modal mu-modal--default">
    <div class="mu-modal__wrap">
        <div class="mu-modal__close js-modal-close"></div>
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:system.auth.authorize",
            "",
            Array(),
            false
        );
        ?>
    </div>
</div>
<div class="mu-modal js-modal js-registration-modal mu-modal--default">
    <div class="mu-modal__wrap">
        <div class="mu-modal__close js-modal-close"></div>
        <div class="mu-registration">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:system.auth.registration",
                "",
                Array(),
                false
            );
            ?>
        </div>
    </div>
</div>
<div class="mu-modal js-modal js-author-modal">
    <div class="mu-modal__wrap">
        <div class="mu-modal__close js-modal-close"></div>
        <div class="mu-author">
            <div class="mu-author__main"><span class="mu-author__title">Стать Автором</span>
                <form class="mu-author__form">
                    <div class="mu-author__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="login" id="login"
                                                               placeholder="Введите логин" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="last-name"
                                                               id="last-name" placeholder="Фамилия" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Код подразделение" required></div>
                        </div>
                    </div>
                    <div class="mu-author__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <div class="mu-input__inner-password js-input-password"><input
                                            class="mu-input__input" type="password" name="password" id="password"
                                            placeholder="Пароль" required><span class="mu-input__password-icon"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="name" id="name"
                                                               placeholder="Имя" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Когда выдан" required></div>
                        </div>
                    </div>
                    <div class="mu-author__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="phone" id="phone"
                                                               placeholder="Телефон" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="middle-name"
                                                               id="middle-name" placeholder="Отчество" required>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Прописка" required></div>
                        </div>
                    </div>
                    <div class="mu-author__panel-row">
                        <div class="mu-file-input">
                            <div class="mu-file-input__result js-file-input-result"></div>
                            <div class="mu-file-input__wrap">
                                <svg class="file mu-file-input__icon">
                                    <use xlink:href="#file"></use>
                                </svg>
                                <label class="mu-file-input__label">Фото профиля</label><input
                                        class="mu-file-input__input js-file-input" type="file"></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Серия и номер паспорта" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Место рождения" required></div>
                        </div>
                    </div>
                    <div class="mu-author__panel-row">
                        <div class="mu-select js-mu-select js-select-single mu-select_single mu-select_round">
                            <div class="mu-select__toggle-wrap">
                                <div class="mu-select__toggle" data-type="toggle"
                                     data-default-text="Предпочитаемый язык">Предпочитаемый язык
                                </div>
                            </div>
                            <div class="mu-select__drop"><label class="mu-select__item" data-type="item"
                                                                data-id="lang-1" for="lang-1"><input
                                            class="mu-select__item-input js-select-input" type="radio" id="lang-1"
                                            name="lang" data-text="Язык 1"><span class="mu-select__item-text">Язык 1</span></label><label
                                        class="mu-select__item" data-type="item" data-id="lang-2" for="lang-2"><input
                                            class="mu-select__item-input js-select-input" type="radio" id="lang-2"
                                            name="lang" data-text="Язык 2"><span class="mu-select__item-text">Язык 2</span></label>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Кем выдан" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="" id=""
                                                               placeholder="Дата рождения" required></div>
                        </div>
                    </div>
                    <div class="mu-author__panel-row">
                        <div class="mu-file-input">
                            <div class="mu-file-input__result js-file-input-result"></div>
                            <div class="mu-file-input__wrap">
                                <svg class="file mu-file-input__icon">
                                    <use xlink:href="#file"></use>
                                </svg>
                                <label class="mu-file-input__label">Загрузить документы</label><input
                                        class="mu-file-input__input js-file-input" type="file"></div>
                        </div>
                    </div>
                    <div class="mu-author__panel-textarea">
                        <div class="mu-textarea"><label class="mu-textarea__label" for="text">Рассказ о себе</label><textarea
                                    name="text"
                                    placeholder="Введите текст или перетащите изображение в поле"></textarea></div>
                    </div>
                    <div class="mu-author__panel-button">
                        <button class="mu-btn mu-btn--round mu-btn--filled"><span>Зарегистрироваться</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="mu-modal js-modal js-member-modal mu-modal--margin">
    <div class="mu-modal__wrap">
        <div class="mu-modal__close js-modal-close"></div>
        <div class="mu-member">
            <div class="mu-member__main"><span class="mu-member__title">Стать Представителем</span>
                <form class="mu-member__form">
                    <div class="mu-member__panel-row mu-member__panel-row--full">
                        <div class="mu-input">
                            <div class="mu-input__wrap"><label class="mu-input__label" for="login">Страница для
                                    управления</label><input class="mu-input__input" name="login" id="login"
                                                             placeholder="Введите адрес" required></div>
                        </div>
                    </div>
                    <div class="mu-member__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="registration"
                                                               id="registration" placeholder="Логин (почта)"
                                                               value="" required></div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="last-name"
                                                               id="last-name" placeholder="Фамилия" required></div>
                        </div>
                    </div>
                    <div class="mu-member__panel-row">
                        <div class="mu-input">
                            <div class="mu-input__wrap">
                                <div class="mu-input__inner-password js-input-password"><input
                                            class="mu-input__input" type="password" name="password" id="password"
                                            placeholder="Пароль" required><span class="mu-input__password-icon"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mu-input">
                            <div class="mu-input__wrap"><input class="mu-input__input" name="name" id="name"
                                                               placeholder="Имя" required></div>
                        </div>
                    </div>
                    <div class="mu-member__panel-textarea">
                        <div class="mu-textarea"><label class="mu-textarea__label" for="text">Основания для
                                предоставления права управления страницей</label><textarea name="text"
                                                                                           placeholder="Введите текст или перетащите изображение в поле"></textarea>
                        </div>
                    </div>
                    <div class="mu-member__panel-column"><span class="mu-member__panel-column-title">Документы, подтверждающие право на представительское управление</span>
                        <div class="mu-file-input">
                            <div class="mu-file-input__result js-file-input-result"></div>
                            <div class="mu-file-input__wrap">
                                <svg class="file mu-file-input__icon">
                                    <use xlink:href="#file"></use>
                                </svg>
                                <label class="mu-file-input__label">Загрузить документы</label><input
                                        class="mu-file-input__input js-file-input" type="file"></div>
                        </div>
                        <div class="mu-file-input">
                            <div class="mu-file-input__result js-file-input-result"></div>
                            <div class="mu-file-input__wrap">
                                <svg class="file mu-file-input__icon">
                                    <use xlink:href="#file"></use>
                                </svg>
                                <label class="mu-file-input__label">Фото профиля</label><input
                                        class="mu-file-input__input js-file-input" type="file"></div>
                        </div>
                    </div>
                    <div class="mu-member__panel-textarea">
                        <div class="mu-textarea"><label class="mu-textarea__label" for="text">Основания для
                                предоставления права управления страницей</label><textarea name="text"
                                                                                           placeholder="Введите текст или перетащите изображение в поле"></textarea>
                        </div>
                    </div>
                    <div class="mu-member__panel-button">
                        <button class="mu-btn mu-btn--round mu-btn--filled"><span>Зарегистрироваться</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>









<?if(explode("/",$curDir)[1] != "personal" && count(explode("/",$curDir))>3){?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
<!--<script src="--><?//= SITE_TEMPLATE_PATH?><!--/assets/js/vendors.js"></script>-->
<!--<script src="--><?//= SITE_TEMPLATE_PATH?><!--/assets/js/app.js"></script>-->
<?}?>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
<script src="<?= SITE_TEMPLATE_PATH?>/assets/js/vendors.js"></script>
<script src="<?= SITE_TEMPLATE_PATH?>/assets/js/app.js"></script>

<script>
	BX.ready(function(){
		var upButton = document.querySelector('[data-role="eshopUpButton"]');
		BX.bind(upButton, "click", function(){
			var windowScroll = BX.GetWindowScrollPos();
			(new BX.easing({
				duration : 500,
				start : { scroll : windowScroll.scrollTop },
				finish : { scroll : 0 },
				transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
				step : function(state){
					window.scrollTo(0, state.scroll);
				},
				complete: function() {
				}
			})).animate();
		})
	});
</script>
</body>
</html>
