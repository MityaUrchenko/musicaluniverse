<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Поиск"); ?>

<?php
// === Обработка фильтров из GET ===
$GLOBALS['arrFilter'] = [];

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
	// без модуля инфоблоков фильтры по свойствам/датам недоступны
}

$selectedIblocks = [];
if (!empty($_REQUEST['filter_iblock']) && is_array($_REQUEST['filter_iblock'])) {
	$selectedIblocks = array_filter(array_map('intval', $_REQUEST['filter_iblock']));
	if (!empty($selectedIblocks)) {
		$GLOBALS['arrFilter']['PARAM2'] = $selectedIblocks;
	}
}

// Список инфоблоков, по которым ищем ID элементов (для свойств и дат)
$iblocksForIdFilter = $selectedIblocks;
if (empty($iblocksForIdFilter) && !empty($arParams["arrFILTER_iblock_content"])) {
	// fallback — не используем, т.к. $arParams здесь ещё нет; берём из запроса или все из component params ниже
}
// ID инфоблоков из параметров компонента (жёстко, как в IncludeComponent)
$defaultIblockIds = [1, 2, 3, 4, 5, 6, 7, 8, 9];
if (empty($iblocksForIdFilter)) {
	$iblocksForIdFilter = $defaultIblockIds;
}

/**
 * Собираем ID элементов по фильтру инфоблока
 */
$collectElementIds = function (array $filter) {
	$ids = [];
	$rs = \CIBlockElement::GetList([], $filter, false, false, ['ID']);
	while ($row = $rs->Fetch()) {
		$ids[] = (int)$row['ID'];
	}
	return $ids;
};

$matchedItemIds = null; // null = нет ограничения по ITEM_ID

// --- Фильтр по свойствам ---
if (!empty($_REQUEST['filter_prop']) && is_array($_REQUEST['filter_prop'])) {
	$propMatched = [];
	$hasPropFilter = false;

	foreach ($_REQUEST['filter_prop'] as $iblockId => $props) {
		$iblockId = (int)$iblockId;
		if ($iblockId <= 0 || !is_array($props)) {
			continue;
		}
		if (!empty($selectedIblocks) && !in_array($iblockId, $selectedIblocks, true)) {
			continue;
		}

		$iblockPropFilter = ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'];
		$hasAnyProp = false;

		foreach ($props as $propCode => $value) {
			$propCode = trim($propCode);
			if ($propCode === '') {
				continue;
			}

			if (substr($propCode, -5) === '_from') {
				$realCode = substr($propCode, 0, -5);
				if ($value !== '' && $value !== null) {
					$iblockPropFilter['>=PROPERTY_' . $realCode] = $value;
					$hasAnyProp = true;
				}
				continue;
			}
			if (substr($propCode, -3) === '_to') {
				$realCode = substr($propCode, 0, -3);
				if ($value !== '' && $value !== null) {
					$iblockPropFilter['<=PROPERTY_' . $realCode] = $value;
					$hasAnyProp = true;
				}
				continue;
			}

			if ($value === '' || $value === null) {
				continue;
			}

			if (is_array($value)) {
				$value = array_filter($value, function ($v) {
					return $v !== '' && $v !== null;
				});
				if (empty($value)) {
					continue;
				}
				$iblockPropFilter['PROPERTY_' . $propCode] = $value;
			} else {
				$iblockPropFilter['PROPERTY_' . $propCode] = $value;
			}
			$hasAnyProp = true;
		}

		if ($hasAnyProp) {
			$hasPropFilter = true;
			$propMatched = array_merge($propMatched, $collectElementIds($iblockPropFilter));
		}
	}

	if ($hasPropFilter) {
		$matchedItemIds = array_unique($propMatched);
		if (empty($matchedItemIds)) {
			$matchedItemIds = [0];
		}
	}
}

// --- Фильтр по дате начала активности (ACTIVE_FROM / DATE_ACTIVE_FROM) ---
$dateFrom = trim((string)($_REQUEST['filter_date_from'] ?? ''));
$dateTo   = trim((string)($_REQUEST['filter_date_to'] ?? ''));

if ($dateFrom !== '' || $dateTo !== '') {
	// HTML input type="date" отдаёт Y-m-d — конвертируем в формат Битрикс
	$bitrixFrom = $dateFrom !== '' ? ConvertTimeStamp(strtotime($dateFrom . ' 00:00:00'), 'FULL') : false;
	$bitrixTo   = $dateTo !== ''   ? ConvertTimeStamp(strtotime($dateTo . ' 23:59:59'), 'FULL') : false;

	$dateMatched = [];
	foreach ($iblocksForIdFilter as $iblockId) {
		$dateFilter = [
			'IBLOCK_ID' => (int)$iblockId,
			'ACTIVE'    => 'Y',
		];
		// Дата начала активности элемента
		if ($bitrixFrom) {
			$dateFilter['>=DATE_ACTIVE_FROM'] = $bitrixFrom;
		}
		if ($bitrixTo) {
			$dateFilter['<=DATE_ACTIVE_FROM'] = $bitrixTo;
		}

		$dateMatched = array_merge($dateMatched, $collectElementIds($dateFilter));
	}
	$dateMatched = array_unique($dateMatched);

	if ($matchedItemIds === null) {
		$matchedItemIds = $dateMatched;
	} else {
		// пересечение с уже найденными по свойствам
		$matchedItemIds = array_values(array_intersect($matchedItemIds, $dateMatched));
	}

	if (empty($matchedItemIds)) {
		$matchedItemIds = [0];
	}
}

if ($matchedItemIds !== null) {
	$GLOBALS['arrFilter']['ITEM_ID'] = $matchedItemIds;
}
?>

<? $APPLICATION->IncludeComponent(
	"bitrix:search.page",
	"main",
	array(
		"USE_TITLE_RANK" => "Y",
		"CHECK_DATES" => "Y",
		"arrWHERE" => array(
			0 => "iblock_content",
		),
		"arrFILTER" => array(
			0 => "iblock_content",
		),
		"SHOW_WHERE" => "Y",
		"PAGE_RESULT_COUNT" => "50",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"FILTER_NAME" => "arrFilter",
		"COMPONENT_TEMPLATE" => "main",
		"RESTART" => "N",
		"NO_WORD_LOGIC" => "N",
		"DEFAULT_SORT" => "rank",
		"arrFILTER_iblock_content" => array(
			0 => "1",
			1 => "2",
			2 => "3",
			3 => "4",
			4 => "5",
			5 => "6",
			6 => "7",
			7 => "8",
			8 => "9",
			9 => "all",
		),
		"SHOW_WHEN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"USE_LANGUAGE_GUESS" => "Y",
		"USE_SUGGEST" => "Y",
		"SHOW_RATING" => "",
		"RATING_TYPE" => "",
		"PATH_TO_USER_PROFILE" => "",
		"DISPLAY_TOP_PAGER" => "Y",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"TAGS_SORT" => "NAME",
		"TAGS_PAGE_ELEMENTS" => "150",
		"TAGS_PERIOD" => "",
		"TAGS_URL_SEARCH" => "",
		"TAGS_INHERIT" => "Y",
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "000000",
		"COLOR_OLD" => "C8C8C8",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "Y",
		"COLOR_TYPE" => "Y",
		"WIDTH" => "100%",
		"STRUCTURE_FILTER" => "structure",
		"NAME_TEMPLATE" => "",
		"SHOW_LOGIN" => "Y",
		"SHOW_ITEM_TAGS" => "Y",
		"SHOW_ITEM_DATE_CHANGE" => "Y",
		"SHOW_ORDER_BY" => "Y",
		"SHOW_TAGS_CLOUD" => "N"
	),
	false
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
