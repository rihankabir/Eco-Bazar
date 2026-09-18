<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';


$action =
    $_POST['action'] ?? '';


/*
|--------------------------------------------------------------------------
| QUICK VIEW
|--------------------------------------------------------------------------
*/

if ($action === 'quick_view') {

    $product_id =
        (int) (
            $_POST['product_id'] ?? 0
        );


    if ($product_id <= 0) {

        json_response(
            false,
            'Invalid product.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Product
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT

            products.id,

            products.name,

            products.slug,

            products.sku,

            products.brand,

            products.short_description,

            products.description,

            products.price,

            products.discount_price,

            products.stock,

            (
                SELECT product_images.image

                FROM product_images

                WHERE product_images.product_id =
                    products.id

                ORDER BY

                    product_images.is_primary DESC,

                    product_images.sort_order ASC,

                    product_images.id ASC

                LIMIT 1

            ) AS product_image,

            (
                SELECT AVG(product_reviews.rating)

                FROM product_reviews

                WHERE product_reviews.product_id =
                    products.id

                AND product_reviews.status =
                    'approved'

            ) AS average_rating,

            (
                SELECT COUNT(*)

                FROM product_reviews

                WHERE product_reviews.product_id =
                    products.id

                AND product_reviews.status =
                    'approved'

            ) AS review_count

        FROM products

        WHERE products.id = ?

        AND products.status = 'active'

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
    | Price
    |--------------------------------------------------------------------------
    */

    $has_discount =
        $product['discount_price'] > 0 &&
        $product['discount_price'] <
        $product['price'];


    $current_price =
        $has_discount
            ? $product['discount_price']
            : $product['price'];


    /*
    |--------------------------------------------------------------------------
    | Rating
    |--------------------------------------------------------------------------
    */

    $rating =
        (int) round(
            (float) $product['average_rating']
        );


    /*
    |--------------------------------------------------------------------------
    | Build HTML
    |--------------------------------------------------------------------------
    */

    ob_start();

    ?>

    <div class="row g-4">


        <!-- IMAGE -->

        <div class="col-md-6">

            <div class="quick-view-image">

                <?php if (!empty($product['product_image'])): ?>

                    <img
                        src="/Ecomart/assets/uploads/products/<?= e($product['product_image']); ?>"
                        alt="<?= e($product['name']); ?>"
                    >

                <?php else: ?>

                    <div class="text-muted">
                        No image available
                    </div>

                <?php endif; ?>

            </div>

        </div>


        <!-- INFORMATION -->

        <div class="col-md-6">


            <!-- NAME -->

            <h3 class="mb-2">

                <?= e($product['name']); ?>

            </h3>


            <!-- RATING -->

            <div class="mb-3">

                <span
                    class="text-warning"
                >

                    <?php for (
                        $i = 1;
                        $i <= 5;
                        $i++
                    ): ?>

                        <?php if ($i <= $rating): ?>

                            <i class="bi bi-star-fill"></i>

                        <?php else: ?>

                            <i class="bi bi-star"></i>

                        <?php endif; ?>

                    <?php endfor; ?>

                </span>


                <span class="text-muted ms-2">

                    <?= (int) $product['review_count']; ?>
                    Reviews

                </span>

            </div>


            <!-- PRICE -->

            <div class="mb-3">

                <span
                    class="fs-4 fw-bold text-success"
                >

                    $
                    <?= number_format(
                        $current_price,
                        2
                    ); ?>

                </span>


                <?php if ($has_discount): ?>

                    <del class="text-muted ms-2">

                        $
                        <?= number_format(
                            $product['price'],
                            2
                        ); ?>

                    </del>

                <?php endif; ?>

            </div>


            <!-- STOCK -->

            <?php if (
                (int) $product['stock'] > 0
            ): ?>

                <span
                    class="badge bg-success mb-3"
                >
                    In Stock
                </span>

            <?php else: ?>

                <span
                    class="badge bg-danger mb-3"
                >
                    Out of Stock
                </span>

            <?php endif; ?>


            <!-- DESCRIPTION -->

            <?php if (
                !empty(
                    $product['short_description']
                )
            ): ?>

                <p class="text-muted">

                    <?= e(
                        $product['short_description']
                    ); ?>

                </p>

            <?php endif; ?>


            <!-- SKU -->

            <p class="mb-2">

                <strong>
                    SKU:
                </strong>

                <?= e($product['sku']); ?>

            </p>


            <!-- BRAND -->

            <?php if (
                !empty($product['brand'])
            ): ?>

                <p class="mb-3">

                    <strong>
                        Brand:
                    </strong>

                    <?= e($product['brand']); ?>

                </p>

            <?php endif; ?>


            <!-- BUTTONS -->

            <?php if (
                (int) $product['stock'] > 0
            ): ?>

                <div
                    class="d-flex flex-wrap gap-2"
                >

                    <button
                        type="button"
                        class="btn btn-success add-to-cart"
                        data-product-id="<?= (int) $product['id']; ?>"
                    >

                        <i class="bi bi-bag me-1"></i>

                        Add to Cart

                    </button>


                    <a
                        href="/Ecomart/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                        class="btn btn-outline-success"
                    >

                        View Details

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <?php


    $html =
        ob_get_clean();


    /*
    |--------------------------------------------------------------------------
    | JSON
    |--------------------------------------------------------------------------
    */

    json_response(
        true,
        '',
        [
            'html' => $html
        ]
    );
}


/*
|--------------------------------------------------------------------------
| INVALID ACTION
|--------------------------------------------------------------------------
*/

json_response(
    false,
    'Invalid product action.'
);