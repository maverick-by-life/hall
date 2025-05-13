jQuery(document).ready(function ($) {
    //маска телефона
    $("input[name*=-phone]").mask("+7 (999) 999-9999");

    // Настройка Datepicker
    var blockedDates = JSON.parse($('#blocked-dates').val() || '[]');

    $("#booking-date").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        beforeShow: function () {
            setTimeout(function () {
                addCustomHeader();
            }, 10)
        },
        onChangeMonthYear: function (year, month, inst) {
            setTimeout(function () {
                addCustomHeader();
            }, 10);
        },
        beforeShowDay: function (date) {
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);

            if (blockedDates.indexOf(dateString) !== -1) {
                return [false, "blocked-date", "Эта дата занята"];
            }
            return [true];
        }
    });

    // Функция для добавления блока
    function addCustomHeader() {
        $(".custom-row").remove();
        $(".ui-datepicker-header").after('<ul' +
            ' class="custom-row"><li>Выбранные</li><li>Свободные</li><li>Занятые</li></ul>');
    }
});