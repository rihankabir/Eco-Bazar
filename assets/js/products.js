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
    $(function () {


    /*
    |--------------------------------------------------------------------------
    | QUICK VIEW
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.quick-view',
        function () {


            const productId =
                parseInt(
                    $(this).attr(
                        'data-product-id'
                    ),
                    10
                );


            if (!productId) {

                console.log(
                    'Invalid product ID'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            $('#quickViewContent').html(

                '<div class="text-center py-5">' +

                '<div class="spinner-border text-success"></div>' +

                '<p class="mt-3 mb-0">' +

                'Loading product...' +

                '</p>' +

                '</div>'

            );


            /*
            |--------------------------------------------------------------------------
            | Bootstrap Modal
            |--------------------------------------------------------------------------
            */

            const modalElement =
                document.getElementById(
                    'quickViewModal'
                );


            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );


            modal.show();


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: '/Ecomart/ajax/products.php',

                type: 'POST',

                dataType: 'json',

                data: {

                    action:
                        'quick_view',

                    product_id:
                        productId

                },


                success: function (
                    response
                ) {


                    console.log(
                        'Quick View:',
                        response
                    );


                    if (
                        response.success
                    ) {

                        $('#quickViewContent')
                            .html(
                                response.data.html
                            );

                    } else {

                        $('#quickViewContent')
                            .html(

                                '<div class="alert alert-danger">' +

                                response.message +

                                '</div>'

                            );

                    }

                },


                error: function (
                    xhr
                ) {


                    console.log(
                        'QUICK VIEW ERROR:',
                        xhr.responseText
                    );


                    $('#quickViewContent')
                        .html(

                            '<div class="alert alert-danger">' +

                            'Unable to load product.' +

                            '</div>'

                        );

                }

            });

        }
    );

});

});