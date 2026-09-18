<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';


$action = $_POST['action'] ?? '';

$user_id = $_SESSION['user_id'] ?? null;


/*
|--------------------------------------------------------------------------
| GET / CREATE ACTIVE CART
|--------------------------------------------------------------------------
*/

function get_active_cart(PDO $pdo, $user_id)
{
    /*
    |--------------------------------------------------------------------------
    | Logged-in User
    |--------------------------------------------------------------------------
    */

    if ($user_id !== null) {

        $stmt = $pdo->prepare("
            SELECT id

            FROM carts

            WHERE user_id = ?

            AND status = 'active'

            LIMIT 1
        ");

        $stmt->execute([
            $user_id
        ]);

        $cart = $stmt->fetch();


        if ($cart) {

            return (int) $cart['id'];
        }


        /*
        | Create User Cart
        */

        $stmt = $pdo->prepare("
            INSERT INTO carts
            (
                user_id,
                cart_token,
                status
            )

            VALUES
            (
                ?,
                NULL,
                'active'
            )
        ");

        $stmt->execute([
            $user_id
        ]);


        return (int) $pdo->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | Guest Cart Token
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_SESSION['cart_token']) ||
        $_SESSION['cart_token'] === ''
    ) {

        $_SESSION['cart_token'] =
            bin2hex(
                random_bytes(32)
            );
    }


    $cart_token =
        $_SESSION['cart_token'];


    /*
    |--------------------------------------------------------------------------
    | Find Guest Cart
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id

        FROM carts

        WHERE cart_token = ?

        AND status = 'active'

        LIMIT 1
    ");

    $stmt->execute([
        $cart_token
    ]);


    $cart = $stmt->fetch();


    if ($cart) {

        return (int) $cart['id'];
    }


    /*
    |--------------------------------------------------------------------------
    | Create Guest Cart
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO carts
        (
            user_id,
            cart_token,
            status
        )

        VALUES
        (
            NULL,
            ?,
            'active'
        )
    ");

    $stmt->execute([
        $cart_token
    ]);


    return (int) $pdo->lastInsertId();
}


/*
|--------------------------------------------------------------------------
| GET CART COUNT
|--------------------------------------------------------------------------
*/

function get_cart_count(PDO $pdo, int $cart_id)
{
    $stmt = $pdo->prepare("
        SELECT
            COALESCE(
                SUM(quantity),
                0
            )

        FROM cart_items

        WHERE cart_id = ?
    ");

    $stmt->execute([
        $cart_id
    ]);


    return (int) $stmt->fetchColumn();
}


/*
|--------------------------------------------------------------------------
| CART COUNT
|--------------------------------------------------------------------------
*/

if ($action === 'count') {

    $cart_id =
        get_active_cart(
            $pdo,
            $user_id
        );


    $count =
        get_cart_count(
            $pdo,
            $cart_id
        );


    json_response(
        true,
        '',
        [
            'count' => $count
        ]
    );
}


/*
|--------------------------------------------------------------------------
| ADD TO CART
|--------------------------------------------------------------------------
*/

if ($action === 'add') {

    $product_id =
        (int) (
            $_POST['product_id'] ?? 0
        );


    $quantity =
        (int) (
            $_POST['quantity'] ?? 1
        );


    /*
    |--------------------------------------------------------------------------
    | Validate Product ID
    |--------------------------------------------------------------------------
    */

    if ($product_id <= 0) {

        json_response(
            false,
            'Invalid product.'
        );
    }


    if ($quantity <= 0) {

        $quantity = 1;
    }


    /*
    |--------------------------------------------------------------------------
    | Find Product
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            stock,
            status

        FROM products

        WHERE id = ?

        LIMIT 1
    ");

    $stmt->execute([
        $product_id
    ]);


    $product =
        $stmt->fetch();


    if (!$product) {

        json_response(
            false,
            'Product not found.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Check Product Status
    |--------------------------------------------------------------------------
    */

    if ($product['status'] !== 'active') {

        json_response(
            false,
            'This product is not available.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Check Stock
    |--------------------------------------------------------------------------
    */

    if ((int) $product['stock'] <= 0) {

        json_response(
            false,
            'This product is out of stock.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Cart
    |--------------------------------------------------------------------------
    */

    $cart_id =
        get_active_cart(
            $pdo,
            $user_id
        );


    /*
    |--------------------------------------------------------------------------
    | Check Existing Product
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            quantity

        FROM cart_items

        WHERE cart_id = ?

        AND product_id = ?

        LIMIT 1
    ");

    $stmt->execute([
        $cart_id,
        $product_id
    ]);


    $existing_item =
        $stmt->fetch();


    /*
    |--------------------------------------------------------------------------
    | Existing Product
    |--------------------------------------------------------------------------
    */

    if ($existing_item) {

        $new_quantity =
            (int) $existing_item['quantity']
            + $quantity;


        /*
        | Don't exceed stock
        */

        if (
            $new_quantity >
            (int) $product['stock']
        ) {

            $new_quantity =
                (int) $product['stock'];
        }


        $stmt = $pdo->prepare("
            UPDATE cart_items

            SET quantity = ?,
                updated_at = NOW()

            WHERE id = ?
        ");

        $stmt->execute([
            $new_quantity,
            $existing_item['id']
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | New Product
    |--------------------------------------------------------------------------
    */

    else {

        if (
            $quantity >
            (int) $product['stock']
        ) {

            $quantity =
                (int) $product['stock'];
        }


        $stmt = $pdo->prepare("
            INSERT INTO cart_items
            (
                cart_id,
                product_id,
                quantity
            )

            VALUES
            (
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $cart_id,
            $product_id,
            $quantity
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Updated Cart Count
    |--------------------------------------------------------------------------
    */

    $count =
        get_cart_count(
            $pdo,
            $cart_id
        );


    json_response(
        true,
        'Product added to cart.',
        [
            'count' => $count
        ]
    );
}


/*
|--------------------------------------------------------------------------
| Invalid Action
|--------------------------------------------------------------------------
*/

json_response(
    false,
    'Invalid cart action.'
);