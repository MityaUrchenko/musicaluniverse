<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
Loader::includeModule("iblock");

use Bitrix\Iblock\IblockTable,
    Bitrix\Iblock\PropertyTable;

$json = file_get_contents('php://input');
$arData = json_decode($json, true);





$obProperties = PropertyTable::getList(
    [
        'filter' => ['IBLOCK_ID' => $arData['iblockId']],
        'order' => [],
        'select' => ['*']
    ]
);
$arProperties = [];
while($property = $obProperties->fetch()) {
    $arResult['PROPERTIES'][$property['ID']] = $property;
}

foreach($arResult['PROPERTIES'] as $property) { ?>
    <div class="mu-content__row">
        <div class="mu-input">
            <div class="mu-input__wrap">
                <label class="mu-input__label"
                       for="<?=$property['CODE']?>"><?=$property['NAME']?></label>
                <input class="mu-input__input"
                       name="PROPERTY_VALUES[<?=$property['CODE']?>]"
                       id="property_<?=$property['CODE']?>"
                       value="<?=implode(',', $property['VALUE'])?>"
                    <?=$property['IS_REQUIRED']?"requaired":"";?>
                >
            </div>
        </div>
    </div>
<?}
