<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Type\DateTime;
$date = "2026.01.30";
$date = new DateTime($date, 'Y.m.d H:i:s');
echo "<pre style='background:#222;color:#ddd;padding:20px;font:14px monospace'>";
print_r($date->format('d-m-Y H:i:s'));
echo "</pre>";
?>
<div id="calendar-component">
    <div>
        <div id="calendar_dates" class="owl-carousel my-4"></div>
        <script>
            let calendarDates = <?=CUtil::PhpToJSObject($arResult["CALENDAR_DATES"])?>;
            let firstDate = new Date(calendarDates[0].split('.'))
            if (firstDate > new Date()) {
                firstDate = new Date()
            }

            firstDate.setDate(1)

            let lastDate = new Date(calendarDates[calendarDates.length - 1].split('.'))
            if (lastDate < new Date()) {
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
                    if (todayDate.getMonth() == curDate.getMonth()) {
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
                    dayContainer.setAttribute('data-date', curDateString)
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

        <div id="calendar-news-list" class="row">

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        document.querySelectorAll('.calendar_dates_date').forEach(function (el) {
            el.addEventListener('click', function () {
                let dateStr = this.getAttribute('data-date'); // должен быть в формате dd.mm.yyyy

                if (!dateStr) return;

                $('#calendar-news-list').html('<div class="col-12 text-center"><p>Загрузка...</p></div>');
                BX.ajax({
                    url: '<?= $templateFolder ?>/ajax.php',   // ← важно!
                    method: 'POST',
                    data: {
                        calendar_date: dateStr,
                        sessid: BX.bitrix_sessid()
                    },
                    dataType: 'html',
                    onsuccess: function (html) {
                        $('#calendar-news-list').html(html);
                    },
                    onfailure: function () {
                        $('#calendar-news-list').html('<p class="text-danger">Ошибка загрузки</p>');
                    }
                });
            });
        });
    });
</script>
