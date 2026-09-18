$(function () {


    /*
    |--------------------------------------------------------------------------
    | UPDATE CART COUNT
    |--------------------------------------------------------------------------
    */

    function updateCartCount() {

        $.ajax({

            url: '/Ecomart/ajax/cart.php',

            type: 'POST',

            dataType: 'json',

            data: {

                action: 'count'

            },


            success: function (response) {

                console.log(
                    'Cart count:',
                    response
                );


                if (
                    response.success
                ) {

                    $('#cartCount').text(
                        response.data.count
                    );

                }

            },


            error: function (xhr) {

                console.log(
                    'Cart Count Error:',
                    xhr.responseText
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD CART COUNT
    |--------------------------------------------------------------------------
    */

    updateCartCount();


    /*
    |--------------------------------------------------------------------------
    | ADD TO CART
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.add-to-cart',
        function () {


            const button =
                $(this);


            const productId =
                parseInt(
                    button.attr(
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


            $.ajax({

                url: '/Ecomart/ajax/cart.php',

                type: 'POST',

                dataType: 'json',

                data: {

                    action: 'add',

                    product_id:
                        productId,

                    quantity: 1

                },


                beforeSend: function () {

                    button.prop(
                        'disabled',
                        true
                    );

                },


                success: function (
                    response
                ) {


                    console.log(
                        'Add cart response:',
                        response
                    );


                    if (
                        response.success
                    ) {


                        /*
                        | Update Navbar
                        */

                        $('#cartCount').text(
                            response.data.count
                        );


                        /*
                        | Green effect
                        */

                        button.addClass(
                            'cart-added'
                        );


                        /*
                        | Remove effect
                        */

                        setTimeout(
                            function () {

                                button.removeClass(
                                    'cart-added'
                                );

                            },
                            700
                        );


                    } else {


                        alert(
                            response.message
                        );

                    }

                },


                error: function (
                    xhr
                ) {

                    console.log(
                        'ADD CART ERROR:',
                        xhr.responseText
                    );


                    alert(
                        'Unable to add product to cart.'
                    );

                },


                complete: function () {

                    button.prop(
                        'disabled',
                        false
                    );

                }

            });

        }
    );

});