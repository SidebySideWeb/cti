/**
 * jQuery Countdown plugin 1.0.0
 *
 * @author UIXThemes
 */
(function ($) {
    var countdownIntervals = {};

    $.fn.glozin_countdown = function () {
        return this.each(function (index) {
            var $this = $(this),
                diff = $this.attr('data-expire'),
                icon = $this.data('icon');

            var updateClock = function (distance) {
                var days = Math.floor(distance / (60 * 60 * 24));
                var hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));
                var minutes = Math.floor((distance % (60 * 60)) / (60));
                var seconds = Math.floor(distance % 60);
                var texts = $this.data('text');
                var icons = icon ? icon : '';
                var daySimple = texts.day ? texts.day : texts.days;
                var hourSimple = texts.hour ? texts.hour : texts.hours;
                var minuteSimple = texts.minute ? texts.minute : texts.minutes;
                var secondSimple = texts.second ? texts.second : texts.seconds;
                var days_class = days > 0 ? ' d-flex' : ' d-none';
                $this.html(
                    icons +
                    '<span class="days timer align-items-end text-inherit'+ days_class +'"><span class="digits fs-inherit fw-inherit text-transform-inherit m-0">' + (days < 10 ? '0' : '') + days + '</span><span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-4">' + (days == 1 ? daySimple : texts.days) + '</span><span class="divider d-inline fs-inherit fw-normal text-transform-inherit m-0 ps-4 pe-5">:</span></span>' +
                    '<span class="hours timer d-flex align-items-end text-inherit"><span class="digits fs-inherit fw-inherit text-transform-inherit m-0">' + (hours < 10 ? '0' : '') + hours + '</span><span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-4">' + (hours == 1 ? hourSimple : texts.hours) + '</span><span class="divider d-inline fs-inherit fw-normal text-transform-inherit m-0 ps-4 pe-5">:</span></span>' +
                    '<span class="minutes timer d-flex align-items-end text-inherit"><span class="digits fs-inherit fw-inherit text-transform-inherit m-0">' + (minutes < 10 ? '0' : '') + minutes + '</span><span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-4">' + (minutes == 1 ? minuteSimple : texts.minutes) + '</span><span class="divider d-inline fs-inherit fw-normal text-transform-inherit m-0 ps-4 pe-5">:</span></span>' +
                    '<span class="seconds timer d-flex align-items-end text-inherit"><span class="digits fs-inherit fw-inherit text-transform-inherit m-0">' + (seconds < 10 ? '0' : '') + seconds + '</span><span class="text fs-14 fw-inherit text-transform-inherit m-0 ps-4">' + (seconds == 1 ? secondSimple : texts.seconds) + '</span></span>'
                );

            };

            updateClock(diff);

            var countdown = setInterval(function () {
                diff = diff - 1;
                var new_diff = diff < 0 ? 0 : diff;
                updateClock(new_diff);

                if (diff < 0) {
                    clearInterval(countdown);
                }
            }, 1000);

            $this.data('countdown-interval', countdown);
        });
    };

    /* Init tabs */
    $(function () {
        $(document.body).find('.glozin-countdown').glozin_countdown();

        $(document.body).on('glozin_countdown', function (e, el) {
            clearCountdownInterval(el);
            $(el).glozin_countdown();
        });
    });

    window.clearCountdownInterval = function (element) {
        // Find the interval from the data attribute and clear it
        var countdown = $(element).data('countdown-interval');
        if (countdown) {
            clearInterval(countdown);
            $(element).removeData('countdown-interval'); // Remove the stored interval reference
        }
    };
})(jQuery);