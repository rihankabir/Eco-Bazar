$(document).ready(function () {

    const minPrice = $('#minPrice');
    const maxPrice = $('#maxPrice');

    const minPriceValue = $('#minPriceValue');
    const maxPriceValue = $('#maxPriceValue');

    const priceRange = $('#priceSliderRange');

    const priceDisplay = $('#priceDisplay');

    const priceFilterForm = $('#priceFilterForm');


    /*
    |--------------------------------------------------------------------------
    | UPDATE SLIDER
    |--------------------------------------------------------------------------
    */

    function updatePriceSlider() {

        let minValue = parseInt(minPrice.val());

        let maxValue = parseInt(maxPrice.val());


        /*
        |--------------------------------------------------------------------------
        | Prevent minimum from going above maximum
        |--------------------------------------------------------------------------
        */

        if (minValue > maxValue) {

            minValue = maxValue;

            minPrice.val(minValue);

        }


        /*
        |--------------------------------------------------------------------------
        | Calculate slider positions
        |--------------------------------------------------------------------------
        */

        const minPercent =
            (minValue / 1500) * 100;

        const maxPercent =
            (maxValue / 1500) * 100;


        /*
        |--------------------------------------------------------------------------
        | Green range
        |--------------------------------------------------------------------------
        */

        priceRange.css({

            left: minPercent + '%',

            right: (100 - maxPercent) + '%'

        });


        /*
        |--------------------------------------------------------------------------
        | Update visible price
        |--------------------------------------------------------------------------
        */

        priceDisplay.text(

            '$' +
            minValue.toLocaleString() +
            ' - $' +
            maxValue.toLocaleString()

        );


        /*
        |--------------------------------------------------------------------------
        | Update hidden form values
        |--------------------------------------------------------------------------
        */

        minPriceValue.val(minValue);

        maxPriceValue.val(maxValue);

    }


    /*
    |--------------------------------------------------------------------------
    | MIN PRICE SLIDER
    |--------------------------------------------------------------------------
    */

    minPrice.on('input', function () {

        let minValue = parseInt(minPrice.val());

        let maxValue = parseInt(maxPrice.val());


        if (minValue > maxValue) {

            minPrice.val(maxValue);

        }


        updatePriceSlider();

    });


    /*
    |--------------------------------------------------------------------------
    | MAX PRICE SLIDER
    |--------------------------------------------------------------------------
    */

    maxPrice.on('input', function () {

        let minValue = parseInt(minPrice.val());

        let maxValue = parseInt(maxPrice.val());


        if (maxValue < minValue) {

            maxPrice.val(minValue);

        }


        updatePriceSlider();

    });


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FILTER
    |--------------------------------------------------------------------------
    |
    | When the user finishes moving the slider,
    | send the selected values to PHP.
    |
    */

    minPrice.on('change', function () {

        updatePriceSlider();

        priceFilterForm.submit();

    });


    maxPrice.on('change', function () {

        updatePriceSlider();

        priceFilterForm.submit();

    });


    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    updatePriceSlider();

});