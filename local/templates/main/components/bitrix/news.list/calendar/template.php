<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div>
    <div id="calendar_dates" class="owl-carousel my-4"></div>

    <script>
      let calendarDates = <?=CUtil::PhpToJSObject($arResult["CALENDAR_DATES"])?>;
      let firstDate = new Date(calendarDates[0].split('.'))
      if(firstDate > new Date()){
        firstDate = new Date()
      }

      firstDate.setDate(1)

      let lastDate = new Date(calendarDates[calendarDates.length - 1].split('.'))
      if(lastDate < new Date()){
        lastDate = new Date()
      }
      lastDate = new Date(lastDate.getFullYear(), lastDate.getMonth() + 1, 0)

      let days = ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб']
      let months = [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь']

      let monthContainer, monthInnerContainer
      let calendarContainer = document.getElementById('calendar_dates')
      let todayDate = new Date()
      let todayString = [
        todayDate.getFullYear(),
        (todayDate.getMonth() + 1).toString().length < 2 ? '0' + (todayDate.getMonth() + 1) : todayDate.getMonth() + 1,
        todayDate.getDate().toString().length < 2 ? '0' + todayDate.getDate() : todayDate.getDate(),
      ].join('.')

      let startPosition = 0
      let j = 0
      for (let curDate = firstDate; curDate <= lastDate; curDate.setDate(curDate.getDate() + 1)) {
        let curDateString = [
          curDate.getFullYear(),
          (curDate.getMonth() + 1).toString().length < 2 ? '0' + (curDate.getMonth() + 1) : curDate.getMonth() + 1,
          curDate.getDate().toString().length < 2 ? '0' + curDate.getDate() : curDate.getDate(),
        ].join('.')

        if (curDate.getDate() == 1) {
          if(todayDate.getMonth() == curDate.getMonth()){
            startPosition = j
          }
          j++;

          monthContainer = document.createElement('div')
          monthContainer.classList.add('calendar_dates_month', 'mx-5')

          let monthName = document.createElement('div')
          monthName.innerHTML = months[curDate.getMonth()] + ' ' + curDate.getFullYear()
          monthContainer.append(monthName)

          monthInnerContainer = document.createElement('div')
          monthInnerContainer.classList.add('d-flex', 'justify-content-between')
          monthContainer.append(monthInnerContainer)
          monthContainer.setAttribute('data-month', curDate.getMonth() + 1)
        }

        let dayContainer = document.createElement('div')

        dayContainer.classList.add('calendar_dates_date', 'd-flex', 'flex-column', 'align-items-center', 'p-1',
          'element-bg')

        if (todayString == curDateString) {
          dayContainer.classList.remove('element-bg')
          dayContainer.classList.add('bg-light')
          dayContainer.classList.add('text-dark')
        }

        if (calendarDates.includes(curDateString)) {
          dayContainer.classList.remove('bg-light')
          dayContainer.classList.remove('element-bg')
          dayContainer.classList.add('bg-golden')
          dayContainer.setAttribute('role', 'button')
          dayContainer.onclick = function () {
            let elements = document.getElementsByClassName('calendar-item')
            for (let el of elements) {
              el.setAttribute('style', 'display:none!important')
            }
            let visElements = document.querySelectorAll(
              '[data-date="' + curDateString.split('.').reverse().join('.') + '"]')
            for (let el of visElements) {
              el.removeAttribute('style')
            }
          }
        }

        let dateRow = document.createElement('span')
        let dayRow = document.createElement('span')
        dateRow.innerHTML = curDate.getDate()
        dayRow.innerHTML = days[curDate.getDay()]

        dayContainer.append(dateRow)
        dayContainer.append(dayRow)

        monthInnerContainer.append(dayContainer)

        if (curDate.getDate() == new Date(curDate.getFullYear(), curDate.getMonth() + 1, 0).getDate()) {
          calendarContainer.append(monthContainer)
        }
      }



      $(document).ready(function () {
        $('#calendar_dates').owlCarousel({
          center: true,
          items: 1,
          nav: true,
          navText: '',
          dots: false,
          startPosition: startPosition,
        })
      })
    </script>

    <div class="row">
        <?

        $eventCount = 0;
        foreach ($arResult["ITEMS"] as $key => $arItem): ?>
            <?
            $this->AddEditAction(
                $arItem['ID'],
                $arItem['EDIT_LINK'],
                CIBlock::GetArrayByID(
                    $arItem["IBLOCK_ID"],
                    "ELEMENT_EDIT"
                )
            );
            $this->AddDeleteAction(
                $arItem['ID'],
                $arItem['DELETE_LINK'],
                CIBlock::GetArrayByID(
                    $arItem["IBLOCK_ID"],
                    "ELEMENT_DELETE"
                ),
                array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
            );
            ?>



            <?
            $closestEvent = false;
            if($eventCount < 8 && date("Y.m.d") < implode(".", array_reverse( explode(".", $arItem["ACTIVE_FROM"])))){
                $closestEvent = true;
                $eventCount++;
            }
            ?>
            <div class="calendar-item d-flex mb-4 col-12 col-md-6 col-lg-3" id="<?= $this->GetEditAreaId(
                $arItem['ID']
            ); ?>" data-date="<?= $arItem["ACTIVE_FROM"] ?>" <?=$closestEvent?"":"style='display:none!important'"?>>
                <div class="card">

                    <button class="favor <?= in_array(
                        $arItem['ID'],
                        unserialize($_COOKIE['favorites'])
                    ) ? "active" : "" ?>" data-item="<?= $arItem['ID'] ?>"></button>

                    <?php
                    $type = $arItem["PROPERTIES"]["TYPE"]["VALUE_XML_ID"];
                    ?>
                    <span class="event_type <?= $type ?>"></span>

                    <div class="card-img-container">
                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"> <img
                                    class=""
                                    src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                    alt="<?= $arItem["PREVIEW_PICTURE"]["ALT"] ?>"
                                    title="<?= $arItem["PREVIEW_PICTURE"]["TITLE"] ?>"
                            /> </a> <a class="btn btn-primary" href="<? echo $arItem["DETAIL_PAGE_URL"] ?>">Купить
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/ticket.svg" alt=""></a>
                    </div>


                    <div class="card-body d-flex flex-column">
                        <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                            <div class="card-title">
                                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                                    <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>"><? echo $arItem["NAME"] ?></a>
                                <? else: ?>
                                    <? echo $arItem["NAME"] ?>
                                <? endif; ?>
                            </div>
                        <? endif; ?>

                        <? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
                            <p class="card-text"><? echo $arItem["PREVIEW_TEXT"]; ?></p>
                        <? endif; ?>

                        <div class="date-published mt-auto"><?= $arItem["ACTIVE_FROM"]?:$arItem["TIMESTAMP_X"] ?></div>
                    </div>
                </div>
            </div>
        <? endforeach; ?>
    </div>

    <? if (count(explode("/", $APPLICATION->GetCurDir())) <= 3 && $APPLICATION->GetCurDir(
        ) != $arResult["LIST_PAGE_URL"]) { ?>
        <div class="text-center mt-4">
            <a href="<?= $arResult["LIST_PAGE_URL"] ?>" class="btn btn-secondary py-2 px-4">Показать больше</a>
        </div>
    <? } ?>
</div>
