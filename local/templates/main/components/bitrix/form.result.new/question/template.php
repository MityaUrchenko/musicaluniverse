<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="question-container">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="question-form">
                    <?=$arResult["FORM_HEADER"]?>
                    <div class="header"><?=$arResult["FORM_TITLE"]?></div>
                    <div class="text-danger error-msg mb-4"></div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <?=$arResult['funcGetInputHtml']($arResult["QUESTIONS"]["NAME"], $arResult["arrVALUES"])?>
                            </div>
                            <div class="form-group">
                                <?=$arResult['funcGetInputHtml']($arResult["QUESTIONS"]["PHONE"], $arResult["arrVALUES"])?>
                            </div>
                            <div class="form-group">
                                <?=$arResult['funcGetInputHtml']($arResult["QUESTIONS"]["EMAIL"], $arResult["arrVALUES"])?>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <?=$arResult['funcGetInputHtml']($arResult["QUESTIONS"]['QUESTION'], $arResult["arrVALUES"])?>
                            </div>
                            <div class="d-flex justify-content-between align-items-start">
                                    <div class="form-group d-flex align-items-start">
                                        <div class="mr-2">
                                            <?=$arResult['funcGetInputHtml']($arResult["QUESTIONS"]['APPROVAL'], $arResult["arrVALUES"])?>
                                        </div>
                                        <label for="question-form-approval" class="question-form-approval">
                                            <?=$arResult["QUESTIONS"]["APPROVAL"]["CAPTION"]?>
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>
                                    </button>
                            </div>
                        </div>
                    </div>
                    <?=$arResult["FORM_FOOTER"]?>
                </div>

                <script>
                    ajaxForm(document.getElementsByName('<?=$arResult['arForm']['SID']?>')[0])
                </script>
            </div>
        </div>
    </div>
</div>