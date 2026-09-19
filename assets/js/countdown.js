$(function () {

    /*
    |--------------------------------------------------------------------------
    | DYNAMIC COUNTDOWN
    |--------------------------------------------------------------------------
    */

    function updateCountdown(element) {

        const endTime =
            $(element).attr('data-countdown');


        if (!endTime) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Convert PHP MySQL Date
        |--------------------------------------------------------------------------
        */

        const endDate =
            new Date(
                endTime.replace(' ', 'T')
            );


        /*
        |--------------------------------------------------------------------------
        | Current Time
        |--------------------------------------------------------------------------
        */

        const now =
            new Date();


        /*
        |--------------------------------------------------------------------------
        | Remaining Time
        |--------------------------------------------------------------------------
        */

        let difference =
            endDate.getTime()
            -
            now.getTime();


        /*
        |--------------------------------------------------------------------------
        | Expired
        |--------------------------------------------------------------------------
        */

        if (difference <= 0) {

            $(element).find('[data-days]')
                .text('00');

            $(element).find('[data-hours]')
                .text('00');

            $(element).find('[data-minutes]')
                .text('00');

            $(element).find('[data-seconds]')
                .text('00');

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Time
        |--------------------------------------------------------------------------
        */

        const days =
            Math.floor(
                difference /
                (1000 * 60 * 60 * 24)
            );


        difference %=
            (1000 * 60 * 60 * 24);


        const hours =
            Math.floor(
                difference /
                (1000 * 60 * 60)
            );


        difference %=
            (1000 * 60 * 60);


        const minutes =
            Math.floor(
                difference /
                (1000 * 60)
            );


        difference %=
            (1000 * 60);


        const seconds =
            Math.floor(
                difference /
                1000
            );


        /*
        |--------------------------------------------------------------------------
        | Display
        |--------------------------------------------------------------------------
        */

        $(element).find('[data-days]')
            .text(
                String(days).padStart(2, '0')
            );


        $(element).find('[data-hours]')
            .text(
                String(hours).padStart(2, '0')
            );


        $(element).find('[data-minutes]')
            .text(
                String(minutes).padStart(2, '0')
            );


        $(element).find('[data-seconds]')
            .text(
                String(seconds).padStart(2, '0')
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Update All Timers
    |--------------------------------------------------------------------------
    */

    function updateAllCountdowns() {

        $('.promo-countdown').each(
            function () {

                updateCountdown(this);

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | First Update
    |--------------------------------------------------------------------------
    */

    updateAllCountdowns();


    /*
    |--------------------------------------------------------------------------
    | Update Every Second
    |--------------------------------------------------------------------------
    */

    setInterval(
        updateAllCountdowns,
        1000
    );
$(function () {


    function updateCountdown(element) {

        const endTime =
            $(element).attr('data-countdown');


        if (!endTime) {
            return;
        }


        const endDate =
            new Date(
                endTime.replace(' ', 'T')
            );


        const now =
            new Date();


        let difference =
            endDate.getTime()
            -
            now.getTime();


        if (difference <= 0) {

            $(element)
                .find('[data-days]')
                .text('00');

            $(element)
                .find('[data-hours]')
                .text('00');

            $(element)
                .find('[data-minutes]')
                .text('00');

            $(element)
                .find('[data-seconds]')
                .text('00');

            return;
        }


        const days =
            Math.floor(
                difference /
                (1000 * 60 * 60 * 24)
            );


        difference %=
            (1000 * 60 * 60 * 24);


        const hours =
            Math.floor(
                difference /
                (1000 * 60 * 60)
            );


        difference %=
            (1000 * 60 * 60);


        const minutes =
            Math.floor(
                difference /
                (1000 * 60)
            );


        difference %=
            (1000 * 60);


        const seconds =
            Math.floor(
                difference /
                1000
            );


        $(element)
            .find('[data-days]')
            .text(
                String(days).padStart(2, '0')
            );


        $(element)
            .find('[data-hours]')
            .text(
                String(hours).padStart(2, '0')
            );


        $(element)
            .find('[data-minutes]')
            .text(
                String(minutes).padStart(2, '0')
            );


        $(element)
            .find('[data-seconds]')
            .text(
                String(seconds).padStart(2, '0')
            );

    }


    function updateAllCountdowns() {


        /*
        |--------------------------------------------------------------------------
        | Promotional Cards
        |--------------------------------------------------------------------------
        */

        $('.promo-countdown').each(
            function () {

                updateCountdown(this);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Hot Deals
        |--------------------------------------------------------------------------
        */

        $('.hot-deals-countdown').each(
            function () {

                updateCountdown(this);

            }
        );

    }


    updateAllCountdowns();


    setInterval(
        updateAllCountdowns,
        1000
    );

});
});
