<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

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

            let days = [
                'вс',
                'пн',
                'вт',
                'ср',
                'чт',
                'пт',
                'сб'
            ]
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
            let selectedDates = []
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
                    monthName.classList.add("month-name")
                    monthName.innerHTML = months[curDate.getMonth()] + ' ' + curDate.getFullYear()
                    monthContainer.append(monthName)

                    monthInnerContainer = document.createElement('div')
                    monthInnerContainer.classList.add('d-flex', 'justify-content-between')
                    monthContainer.append(monthInnerContainer)
                    monthContainer.setAttribute('data-month', curDate.getMonth() + 1)
                }

                let dayBtn = document.createElement('div')

                dayBtn.classList.add('calendar_dates_date')
                dayBtn.setAttribute('role', 'button')
                dayBtn.setAttribute('data-date', curDateString)

                if (todayString == curDateString) {
                    dayBtn.classList.add('current_date')
                }

                if (calendarDates.includes(curDateString)) {
                    dayBtn.classList.add('event_date')
                }

                dayBtn.onclick = function (e) {
                    selectedDates.push(dayBtn);
                    dayBtn.classList.add('chosen_date')

                    if (selectedDates.length > 1) {
                        if (selectedDates.length > 2) {
                            selectedDates.forEach((item) => {
                                item.classList.remove('chosen_date')
                                item.classList.remove('chosen_date_alt')
                            })
                            selectedDates = [dayBtn]
                            dayBtn.classList.add('chosen_date')
                            loadNewsByDate(selectedDates[0].dataset.date)
                            return
                        }

                        selectedDates = selectedDates.sort((a, b) => new Date(a.dataset.date) - new Date(b.dataset.date))
                        let firstChoosenRange = selectedDates[0];
                        let lastChoosenRange = selectedDates[1];
                        selectedDates = []
                        for (let curBtn = firstChoosenRange; curBtn.dataset.date < lastChoosenRange.dataset.date; curBtn = curBtn.nextSibling){
                            selectedDates.push(curBtn)
                            curBtn.classList.add('chosen_date_alt')
                        }
                        selectedDates.push(lastChoosenRange)
                        loadNewsRange(selectedDates[0].dataset.date, selectedDates[selectedDates.length-1].dataset.date)
                        return
                    }
                    loadNewsByDate(selectedDates[0].dataset.date);
                };

                let dateRow = document.createElement('span')
                let dayRow = document.createElement('span')
                dateRow.innerHTML = curDate.getDate()
                dayRow.innerHTML = days[curDate.getDay()]

                dayBtn.append(dateRow)
                dayBtn.append(dayRow)

                monthInnerContainer.append(dayBtn)

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
                    mouseDrag: false,
                    touchDrag: false,
                })
            })
            $('#calendar_dates').on('initialized.owl.carousel', function(event) {
                selectedDates.forEach((item) => {
                    item.classList.remove('chosen_date')
                    item.classList.remove('chosen_date_alt')
                })
                selectedDates = []
                selectedDates = event.currentTarget.querySelectorAll(`#calendar_dates .owl-item.active .calendar_dates_month .calendar_dates_date`);
                loadNewsRange(selectedDates[0].dataset.date, selectedDates[selectedDates.length-1].dataset.date)
                selectedDates = []

            })
            $('#calendar_dates').on('translated.owl.carousel', function(event) {
                selectedDates.forEach((item) => {
                    item.classList.remove('chosen_date')
                    item.classList.remove('chosen_date_alt')
                })
                selectedDates = []
                selectedDates = event.currentTarget.querySelectorAll(`#calendar_dates .owl-item.active .calendar_dates_month .calendar_dates_date`);
                loadNewsRange(selectedDates[0].dataset.date, selectedDates[selectedDates.length-1].dataset.date)
                selectedDates = []

            })


            function loadNewsByDate(dateStr) {
                $('#calendar-news-list').html('<div class="col-12 text-center"><p>Загрузка...</p></div>');

                BX.ajax({
                    url: '<?= $templateFolder ?>/ajax.php',
                    method: 'POST',
                    data: {
                        calendar_date: dateStr,
                        sessid: BX.bitrix_sessid()
                    },
                    dataType: 'html',
                    onsuccess: function (html) {
                        if(html.trim() != ""){
                            $('#calendar-news-list').show()
                            $('#calendar-news-list').html(html);
                            document.querySelector("#calendar-news-list__not-found").style.display = "none"
                        }else{
                            $('#calendar-news-list').hide()
                            document.querySelector("#calendar-news-list__not-found").style.display = "block"
                        }
                    }
                });
            }

            function loadNewsRange(dateFrom, dateTo) {
                $('#calendar-news-list').html('<div class="col-12 text-center"><p>Загрузка диапазона...</p></div>');

                BX.ajax({
                    url: '<?= $templateFolder ?>/ajax.php',
                    method: 'POST',
                    data: {
                        date_from: dateFrom,
                        date_to: dateTo,
                        sessid: BX.bitrix_sessid()
                    },
                    dataType: 'html',
                    onsuccess: function (html) {
                        if(html.trim() != ""){
                            $('#calendar-news-list').show()
                            $('#calendar-news-list').html(html);
                            document.querySelector("#calendar-news-list__not-found").style.display = "none"
                        }else{
                            $('#calendar-news-list').hide()
                            document.querySelector("#calendar-news-list__not-found").style.display = "block"
                        }
                    }
                });
            }
        </script>

        <div id="calendar-news-list" class="row">

        </div>
        <div  id="calendar-news-list__not-found" style="display: none">
            <img style="display:block;margin:auto" src="<?= $templateFolder ?>/images/not_found.png" alt="">
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
