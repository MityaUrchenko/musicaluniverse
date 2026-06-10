<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);
?>


<div class="mu-login">
    <div class="mu-login__main"><span class="mu-login__title">Войти</span>
        <form class="mu-login__form" name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">

            <input type="hidden" name="AUTH_FORM" value="Y" />
            <input type="hidden" name="TYPE" value="AUTH" />
            <?if ($arResult["BACKURL"] <> ''):?>
                <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
            <?endif?>
            <?foreach ($arResult["POST"] as $key => $value):?>
                <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
            <?endforeach?>

            <div class="mu-input">
                <div class="mu-input__wrap">
                    <input class="mu-input__input" type="text" name="USER_LOGIN" maxlength="255" placeholder="Логин (почта)" value="<?=$arResult["LAST_LOGIN"]?>" required />
                </div>
            </div>

            <div class="mu-input">
                <div class="mu-input__wrap">
                    <div class="mu-input__inner-password js-input-password">
                        <input class="mu-input__input" type="password" name="USER_PASSWORD" maxlength="255"
                               autocomplete="off" placeholder="Пароль" required/>
                        <span class="mu-input__password-icon"></span>
                    </div>
                </div>
            </div>
            <div class="mu-login__form-controls">
                <button class="mu-btn mu-btn--filled mu-btn--round"><span>Войти</span></button>
                <a rel="nofollow" class="mu-btn mu-btn--round"><span>Не помню пароль</span></a>
            </div>
        </form>
    </div>
    <div class="mu-login__bottom">
        <a class="mu-login__link js-registration-btn" href="#">Зарегистрироваться</a>
    </div>
</div>
