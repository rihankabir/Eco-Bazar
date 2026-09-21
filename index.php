<?php
$page_title = 'Home';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';


//for categories//
$stmt = $pdo->prepare("
    SELECT
        id,
        name,
        slug,
        image
    FROM categories
    WHERE status = 'active'
    ORDER BY id ASC
");

$stmt->execute();

$categories = $stmt->fetchAll();
// =========================
// POPULAR PRODUCTS
// =========================

$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.name,
        p.slug,
        p.price,
        p.discount_price,
        p.stock,

        (
            SELECT pi.image
            FROM product_images pi
            WHERE pi.product_id = p.id
            ORDER BY
                pi.is_primary DESC,
                pi.sort_order ASC,
                pi.id ASC
            LIMIT 1
        ) AS product_image,

        COALESCE(
            (
                SELECT AVG(pr.rating)
                FROM product_reviews pr
                WHERE pr.product_id = p.id
                AND pr.status = 'approved'
            ),
            0
        ) AS average_rating,

        (
            SELECT COUNT(*)
            FROM product_reviews pr
            WHERE pr.product_id = p.id
            AND pr.status = 'approved'
        ) AS review_count

    FROM products p

    INNER JOIN categories c
        ON c.id = p.category_id

    WHERE p.status = 'active'
    AND c.status = 'active'

    ORDER BY
        p.featured DESC,
        p.created_at DESC

    LIMIT 10
");

$stmt->execute();

$popular_products = $stmt->fetchAll();
/*
|--------------------------------------------------------------------------
| ACTIVE CATEGORY PROMOTIONS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        category_promotions.id,
        category_promotions.category_id,
        category_promotions.title,
        category_promotions.image,
        category_promotions.starts_at,
        category_promotions.ends_at,

        categories.name AS category_name,
        categories.slug AS category_slug

    FROM category_promotions

    INNER JOIN categories
        ON categories.id = category_promotions.category_id

    WHERE category_promotions.status = 'active'

    AND category_promotions.starts_at <= NOW()

    AND category_promotions.ends_at > NOW()

    AND categories.status = 'active'

    ORDER BY
        category_promotions.sort_order ASC,
        category_promotions.id ASC

    LIMIT 3
");

$stmt->execute();

$promotions = $stmt->fetchAll();
/*
|--------------------------------------------------------------------------
| HOT SALE PRODUCTS
|--------------------------------------------------------------------------
*/
$hot_sale_names = [
    'Green Apple',
    'Chinese Cabbage',
    'Green Lettuce',
    'Eggplant',
    'Fresh Cauliflower',
    'Green Capsicum',
    'Green Chili',
    'Big Potatoes',
    'Corn',
    'Red Chili',
    'Red Tomatos',
    'Surjapur Mango'
];


/*
|--------------------------------------------------------------------------
| Create placeholders
|--------------------------------------------------------------------------
*/

$placeholders = implode(
    ',',
    array_fill(
        0,
        count($hot_sale_names),
        '?'
    )
);


/*
|--------------------------------------------------------------------------
| Product Query
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT

        products.id,
        products.name,
        products.slug,
        products.sku,
        products.brand,
        products.price,
        products.discount_price,
        products.stock,
        products.featured,
        products.created_at,

        /*
        |--------------------------------------------------------------------------
        | Primary Image
        |--------------------------------------------------------------------------
        */

        (
            SELECT product_images.image

            FROM product_images

            WHERE product_images.product_id = products.id

            ORDER BY
                product_images.is_primary DESC,
                product_images.sort_order ASC,
                product_images.id ASC

            LIMIT 1

        ) AS product_image,


        /*
        |--------------------------------------------------------------------------
        | Average Rating
        |--------------------------------------------------------------------------
        */

        (
            SELECT ROUND(
                AVG(product_reviews.rating),
                1
            )

            FROM product_reviews

            WHERE product_reviews.product_id = products.id

            AND product_reviews.status = 'approved'

        ) AS average_rating,


        /*
        |--------------------------------------------------------------------------
        | Review Count
        |--------------------------------------------------------------------------
        */

        (
            SELECT COUNT(*)

            FROM product_reviews

            WHERE product_reviews.product_id = products.id

            AND product_reviews.status = 'approved'

        ) AS review_count


    FROM products


    WHERE products.status = 'active'

    AND products.name IN ($placeholders)


    ORDER BY CASE products.name

        WHEN 'Green Apple' THEN 1
        WHEN 'Chinese Cabbage' THEN 2
        WHEN 'Green Lettuce' THEN 3
        WHEN 'Eggplant' THEN 4
        WHEN 'Fresh Cauliflower' THEN 5
        WHEN 'Green Capsicum' THEN 6
        WHEN 'Green Chili' THEN 7
        WHEN 'Big Potatoes' THEN 8
        WHEN 'Corn' THEN 9
        WHEN 'Red Chili' THEN 10
        WHEN 'Red Tomatos' THEN 11
        WHEN 'Surjapur Mango' THEN 12

    END

");

$stmt->execute($hot_sale_names);

$hot_sale_products = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Safety Check
|--------------------------------------------------------------------------
|
| We expect exactly 12 products.
|
*/

$hot_sale_products = array_values(
    $hot_sale_products
);
/* festured product query */


$featuredProductsStmt = $pdo->prepare("
    SELECT
        products.id,
        products.name,
        products.slug,
        products.price,
        products.discount_price,
        products.stock,

        (
            SELECT product_images.image
            FROM product_images
            WHERE product_images.product_id = products.id
            ORDER BY
                product_images.is_primary DESC,
                product_images.sort_order ASC,
                product_images.id ASC
            LIMIT 1
        ) AS product_image,

        (
            SELECT COALESCE(AVG(product_reviews.rating), 0)
            FROM product_reviews
            WHERE product_reviews.product_id = products.id
            AND product_reviews.status = 'approved'
        ) AS average_rating,

        (
            SELECT COUNT(*)
            FROM product_reviews
            WHERE product_reviews.product_id = products.id
            AND product_reviews.status = 'approved'
        ) AS review_count

    FROM products

    WHERE products.status = 'active'
    AND products.featured = 1

    ORDER BY products.created_at DESC, products.id ASC

    LIMIT 5
");

$featuredProductsStmt->execute();

$featuredProducts = $featuredProductsStmt->fetchAll();

/*latest news*/

$latestNewsStmt = $pdo->prepare("
    SELECT
        blog_posts.id,
        blog_posts.title,
        blog_posts.slug,
        blog_posts.excerpt,
        blog_posts.featured_image,
        blog_posts.author_name,
        blog_posts.published_at,
        blog_posts.created_at,

        blog_categories.name AS category_name

    FROM blog_posts

    LEFT JOIN blog_categories
        ON blog_categories.id = blog_posts.category_id

    WHERE blog_posts.status = 'published'

    AND (
        blog_categories.status = 'active'
        OR blog_categories.id IS NULL
    )

    ORDER BY
        blog_posts.published_at DESC,
        blog_posts.id ASC

    LIMIT 3
");

$latestNewsStmt->execute();

$latestNews = $latestNewsStmt->fetchAll();
?>
<!-- =========================
     HERO / BANNER SECTION
========================= -->
<section class="hero-section">
    <div class="container px-0">
        <div class="row g-4">

            <!-- LEFT MAIN BANNER -->
            <div class="col-lg-8">
                <div class="main-banner">

                    <!-- Banner Text -->
                    <div class="main-banner-content">

                        <h1>
                            Fresh &amp; Healthy<br>
                            Organic Food
                        </h1>

                        <div class="sale-info">
                            <div class="sale-text">
                                Sale up to
                                <span>30% OFF</span>
                            </div>

                            <p>Free shipping on all your order.</p>
                        </div>

                        <a href="#" class="shop-btn">
                            Shop now
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                    <!-- Woman Image -->
                    <img
                        src="assets/images/hero/hero-banner-image.png"
                        alt="Fresh Organic Food"
                        class="main-banner-image"
                    >

                </div>
            </div>


            <!-- RIGHT BANNERS -->
            <div class="col-lg-4">

                <div class="right-banners">

                    <!-- TOP RIGHT BANNER -->
                    <div class="small-banner summer-banner">

                        <div class="summer-content">

                            <span class="small-title">
                                SUMMER SALE
                            </span>

                            <h2>75% OFF</h2>

                            <p>
                                Only Fruit &amp; Vegetable
                            </p>

                            <a href="#">
                                Shop Now
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>

                        

                    </div>


                    <!-- BOTTOM RIGHT BANNER -->
                    <div class="small-banner deal-banner">

                        <div class="deal-overlay"></div>

                        <div class="deal-content">

                            <span class="small-title">
                                BEST DEAL
                            </span>

                            <h2>
                                Special Products<br>
                                Deal of the Month
                            </h2>

                            <a href="#">
                                Shop Now
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</section>
<section class="brand"> 
    <div class="container"> 
        <div class="row"> 
            <div class="brand-sec shadow row g-4"> 

                <div class="col-lg-3 col-12 col-sm-6 col-md-6"> 
                    <div class="box d-flex justify-content-center align-items-center"> 
                        <div class="box-icon me-3"> 
                            <img src="assets/images/logo/truck.png" class="box-image img-fluid"> 
                        </div> 
                        <div class="box-text"> 
                            <p class="main-text p-0 m-0">Free Shipping</p> 
                            <p class="sub-text p-0 m-0">Free shipping on all your order</p> 
                        </div> 
                    </div> 
                </div> 


                <!----> 
                <!--second box--> 

                <div class="col-lg-3 col-12 col-sm-6 col-md-6"> 
                    <div class="box d-flex justify-content-center align-items-center"> 
                        <div class="box-icon me-3"> 
                            <img src="assets/images/logo/headphone.png" class="box-image img-fluid"> 
                        </div> 
                        <div class="box-text"> 
                            <p class="main-text p-0 m-0">Customer Support 24/7</p> 
                            <p class="sub-text p-0 m-0">Instant access to Support</p> 
                        </div> 
                    </div> 
                </div> 


                <!----> 
                <!--third box--> 

                <div class="col-lg-3 col-12 col-sm-6 col-md-6"> 
                    <div class="box d-flex justify-content-center align-items-center"> 
                        <div class="box-icon me-3"> 
                            <img src="assets/images/logo/pay.png" class="box-image img-fluid"> 
                        </div> 
                        <div class="box-text"> 
                            <p class="main-text p-0 m-0">100% Secure Payment</p> 
                            <p class="sub-text p-0 m-0">We ensure your money is safe</p> 
                        </div> 
                    </div> 
                </div> 


                <!----> 
                <!--fourth box--> 

                <div class="col-lg-3 col-12 col-sm-6 col-md-6"> 
                    <div class="box d-flex justify-content-center align-items-center"> 
                        <div class="box-icon me-3"> 
                            <img src="assets/images/logo/package.png" class="box-image img-fluid"> 
                        </div> 
                        <div class="box-text"> 
                            <p class="main-text p-0 m-0">Money-Back Guarantee</p> 
                            <p class="sub-text p-0 m-0">30 Days Money-Back Guarantee</p> 
                        </div> 
                    </div> 
                </div> 

            </div> 
        </div> 
    </div> 
</section>
<!--categories section-->
<div class="container py-5">

    <div class="text-center d-flex justify-content-between mb-5">

        <h1>
            Popular Categories
        </h1>

        
        <a href="/Ecomart/products.php" class="btn category-btn">View All<i class="bi bi-arrow-right"></i></a>
        

    </div>


    <div class="row g-4">

        <?php if (!empty($categories)): ?>

            <?php foreach ($categories as $category): ?>

                <div class="col-6 col-md-4 col-lg-2">

                    <a
                        href="/Ecomart/products.php?category=<?= urlencode($category['slug']); ?>"
                        class="text-decoration-none text-dark"
                    >

                        <div class="card cat-card  text-center">

                            <?php if (!empty($category['image'])): ?>

                                <img
                                    src="/Ecomart/assets/uploads/categories/<?= e($category['image']); ?>"
                                    class="card-img-top"
                                    alt="<?= e($category['name']); ?>"
                                >

                            <?php endif; ?>

                            <div class="card-body cat-body">

                                <h5 class="card-title cat-title mb-0">
                                    <?= e($category['name']); ?>
                                </h5>

                            </div>

                        </div>

                    </a>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="col-12">

                <div class="alert alert-info text-center">
                    No categories found.
                </div>

            </div>

        <?php endif; ?>

    </div>

</div>
<!-- =========================================================
     POPULAR PRODUCTS
========================================================= -->

<section class="popular-products-section">

    <div class="container">

        <!-- SECTION TITLE -->

        <div class="section-heading">

            <h2>
                Popular Products
            </h2>

            <a
                href="/Ecomart/products.php"
                class="view-all-link"
            >
                View All
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>


        <!-- PRODUCTS GRID -->

        <div class="popular-products-grid">

            <?php if (!empty($popular_products)): ?>

                <?php foreach ($popular_products as $product): ?>

                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | Discount
                    |--------------------------------------------------------------------------
                    */

                    $has_discount =
                        !empty($product['discount_price']) &&
                        $product['discount_price'] > 0 &&
                        $product['discount_price'] < $product['price'];


                    if ($has_discount) {

                        $current_price =
                            $product['discount_price'];

                        $discount_percent =
                            round(
                                (
                                    ($product['price'] - $product['discount_price'])
                                    / $product['price']
                                ) * 100
                            );

                    } else {

                        $current_price =
                            $product['price'];

                        $discount_percent = 0;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Rating
                    |--------------------------------------------------------------------------
                    */

                    $rating =
                        (int) round(
                            (float) $product['average_rating']
                        );

                    ?>

                    <!-- PRODUCT ITEM -->

                    <div class="popular-product-item">

                        <div
                            class="home-product-card"
                            data-product-id="<?= (int) $product['id']; ?>"
                        >

                            <!-- SALE BADGE -->

                            <?php if ($has_discount): ?>

                                <span class="home-sale-badge">

                                    Sale <?= $discount_percent; ?>%

                                </span>

                            <?php endif; ?>


                            <!-- PRODUCT ACTIONS -->

                            <div class="home-product-actions">

                                <!-- WISHLIST -->

                                <button
                                    type="button"
                                    class="home-action-btn wishlist-btn"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Wishlist"
                                >

                                    <i class="bi bi-heart"></i>

                                </button>


                                <!-- QUICK VIEW -->

                                <button
                                    type="button"
                                    class="home-action-btn quick-view"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Quick View"
                                >

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>


                            <!-- PRODUCT IMAGE -->

                            <a
                                href="/Ecomart/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                                class="home-product-image-link"
                            >

                                <?php if (!empty($product['product_image'])): ?>

                                    <img
                                        src="/Ecomart/assets/uploads/products/<?= e($product['product_image']); ?>"
                                        alt="<?= e($product['name']); ?>"
                                        class="home-product-image"
                                    >

                                <?php else: ?>

                                    <div class="home-product-no-image">
                                        No Image
                                    </div>

                                <?php endif; ?>

                            </a>


                            <!-- PRODUCT INFORMATION -->

                            <div class="home-product-info">

                                <!-- PRODUCT NAME -->

                                <a
                                    href="/Ecomart/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                                    class="home-product-name"
                                >

                                    <?= e($product['name']); ?>

                                </a>


                                <!-- PRICE -->

                                <div class="home-product-price">

                                    <span class="current-price">

                                        $
                                        <?= number_format(
                                            $current_price,
                                            2
                                        ); ?>

                                    </span>


                                    <?php if ($has_discount): ?>

                                        <del class="old-price">

                                            $
                                            <?= number_format(
                                                $product['price'],
                                                2
                                            ); ?>

                                        </del>

                                    <?php endif; ?>

                                </div>


                                <!-- RATING -->

                                <div class="home-product-rating">

                                    <span class="rating-stars">

                                        <?php for ($i = 1; $i <= 5; $i++): ?>

                                            <?php if ($i <= $rating): ?>

                                                <i class="bi bi-star-fill"></i>

                                            <?php else: ?>

                                                <i class="bi bi-star"></i>

                                            <?php endif; ?>

                                        <?php endfor; ?>

                                    </span>


                                    <span class="review-count">

                                        (<?= (int) $product['review_count']; ?>)

                                    </span>

                                </div>


                                <!-- ADD TO CART -->

                                <button
                                    type="button"
                                    class="home-cart-btn add-to-cart"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Add to Cart"
                                    <?= (int) $product['stock'] <= 0 ? 'disabled' : ''; ?>
                                >

                                    <?php if ((int) $product['stock'] > 0): ?>

                                        <i class="bi bi-bag"></i>

                                    <?php else: ?>

                                        <i class="bi bi-x-circle"></i>

                                    <?php endif; ?>

                                </button>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="no-products-message">

                    No popular products found.

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>
<!-- =========================================================
     PROMOTIONAL CARDS
========================================================= -->

<section class="promo-section">

    <div class="container">

        <div class="row g-4">

            <?php if (!empty($promotions)): ?>

                <?php foreach ($promotions as $promotion): ?>

                    <div class="col-lg-4 col-md-6">

                        <div
                            class="promo-card"
                            style="
                                background-image:
                                url('/Ecomart/assets/uploads/categories/<?= e($promotion['image']); ?>');
                            "
                        >

                            <div class="promo-overlay"></div>


                            <div class="promo-content">

                                <span class="promo-label">
                                    SPECIAL OFFER
                                </span>


                                <h3>
                                    <?= e($promotion['title']); ?>
                                </h3>


                                <!-- DYNAMIC COUNTDOWN -->

                                <div
                                    class="promo-countdown"
                                    data-countdown="<?= e($promotion['ends_at']); ?>"
                                >

                                    <div class="countdown-item">

                                        <span
                                            class="countdown-value"
                                            data-days
                                        >
                                            00
                                        </span>

                                        <small>
                                            Days
                                        </small>

                                    </div>


                                    <div class="countdown-separator">
                                        :
                                    </div>


                                    <div class="countdown-item">

                                        <span
                                            class="countdown-value"
                                            data-hours
                                        >
                                            00
                                        </span>

                                        <small>
                                            Hours
                                        </small>

                                    </div>


                                    <div class="countdown-separator">
                                        :
                                    </div>


                                    <div class="countdown-item">

                                        <span
                                            class="countdown-value"
                                            data-minutes
                                        >
                                            00
                                        </span>

                                        <small>
                                            Minutes
                                        </small>

                                    </div>


                                    <div class="countdown-separator">
                                        :
                                    </div>


                                    <div class="countdown-item">

                                        <span
                                            class="countdown-value"
                                            data-seconds
                                        >
                                            00
                                        </span>

                                        <small>
                                            Seconds
                                        </small>

                                    </div>

                                </div>


                                <!-- SHOP NOW -->

                                <a
                                    href="/Ecomart/products.php?category=<?= urlencode($promotion['category_slug']); ?>"
                                    class="promo-btn"
                                >

                                    Shop Now

                                    <i class="bi bi-arrow-right"></i>

                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>


            <?php else: ?>

                <div class="col-12">

                    <div class="alert alert-info text-center">

                        No active promotions available.

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>
<!-- =========================================================
     HOT SALE SECTION
========================================================= -->

<!-- =========================================================
     HOT DEALS
========================================================= -->
<!-- =========================================================
     HOT DEALS SECTION
========================================================= -->

<section class="hot-deals-section">

    <div class="container">

        <!-- =====================================================
             SECTION HEADER
        ====================================================== -->

        <div class="hot-deals-heading">

            <h2>
                Hot Deals
            </h2>

            <a
                href="/Ecomart/products.php"
                class="hot-deals-view-all"
            >
                View All
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>


        <?php if (count($hot_sale_products) === 12): ?>


            <!-- =================================================
                 MAIN HOT DEALS GRID

                 5 COLUMNS
                 3 ROWS

                 FEATURED CARD:
                 2 COLUMNS × 2 ROWS

                 SMALL PRODUCTS:
                 6 ON RIGHT
                 5 UNDERNEATH
            ================================================== -->

            <div class="hot-deals-layout">


                <!-- =================================================
                     FEATURED PRODUCT
                ================================================== -->

                <div class="hot-deals-featured">


                    <!-- =================================================
                         FEATURED IMAGE AREA
                    ================================================== -->

                    <div class="hot-deals-featured-image">


                        <!-- SALE BADGE -->

                        <span class="hot-deals-sale-badge">
                            Sale 50%
                        </span>


                        <!-- BEST SALE BADGE -->

                        <span class="hot-deals-best-badge">
                            Best Sale
                        </span>


                        <!-- ACTION BUTTONS -->

                        <div class="hot-deals-featured-actions">

                            <button
                                type="button"
                                class="home-action-btn wishlist-btn"
                                data-product-id="<?= (int) $hot_sale_products[0]['id']; ?>"
                                title="Add to Wishlist"
                            >

                                <i class="bi bi-heart"></i>

                            </button>


                            <button
                                type="button"
                                class="home-action-btn quick-view"
                                data-product-id="<?= (int) $hot_sale_products[0]['id']; ?>"
                                title="Quick View"
                            >

                                <i class="bi bi-eye"></i>

                            </button>

                        </div>


                        <!-- FEATURED PRODUCT IMAGE -->

                        <?php if (
                            !empty(
                                $hot_sale_products[0]['product_image']
                            )
                        ): ?>

                            <img
                                src="/Ecomart/assets/uploads/products/<?= e(
                                    $hot_sale_products[0]['product_image']
                                ); ?>"
                                alt="<?= e(
                                    $hot_sale_products[0]['name']
                                ); ?>"
                                class="hot-deals-featured-img"
                            >

                        <?php else: ?>

                            <div class="hot-deals-image-placeholder">

                                <i class="bi bi-image"></i>

                            </div>

                        <?php endif; ?>


                    </div>


                    <!-- =================================================
                         FEATURED PRODUCT CONTENT
                    ================================================== -->

                    <div class="hot-deals-featured-content">


                        <!-- ADD TO CART -->

                        <button
                            type="button"
                            class="hot-deals-featured-cart add-to-cart"
                            data-product-id="<?= (int) $hot_sale_products[0]['id']; ?>"
                        >

                            Add to Cart

                            <i class="bi bi-bag"></i>

                        </button>


                        <!-- PRODUCT NAME -->

                        <h3 class="hot-deals-featured-name">

                            <a
                                href="/Ecomart/product/single.php?slug=<?= urlencode(
                                    $hot_sale_products[0]['slug']
                                ); ?>"
                            >

                                <?= e(
                                    $hot_sale_products[0]['name']
                                ); ?>

                            </a>

                        </h3>


                        <!-- PRICE -->

                        <div class="hot-deals-featured-price">

                            <span class="hot-deals-current-price">

                                $<?= number_format(
                                    (
                                        !empty(
                                            $hot_sale_products[0]['discount_price']
                                        )
                                        &&
                                        $hot_sale_products[0]['discount_price'] > 0
                                    )
                                    ?
                                    $hot_sale_products[0]['discount_price']
                                    :
                                    $hot_sale_products[0]['price'],
                                    2
                                ); ?>

                            </span>


                            <?php if (
                                !empty(
                                    $hot_sale_products[0]['discount_price']
                                )
                                &&
                                $hot_sale_products[0]['discount_price'] > 0
                                &&
                                $hot_sale_products[0]['discount_price']
                                <
                                $hot_sale_products[0]['price']
                            ): ?>

                                <del class="hot-deals-old-price">

                                    $<?= number_format(
                                        $hot_sale_products[0]['price'],
                                        2
                                    ); ?>

                                </del>

                            <?php endif; ?>

                        </div>


                        <!-- RATING -->

                        <div class="hot-deals-featured-rating">

                            <div class="hot-deals-stars">

                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>

                            </div>


                            <span>

                                (
                                <?= (int) (
                                    $hot_sale_products[0]['review_count']
                                    ?: 524
                                ); ?>
                                Feedback
                                )

                            </span>

                        </div>


                        <!-- COUNTDOWN TEXT -->

                        <div class="hot-deals-countdown-title">

                            Hurry up! Offer ends in:

                        </div>


                        <!-- COUNTDOWN -->

                        <div
                            class="hot-deals-countdown"
                            data-countdown="2026-09-30 23:59:59"
                        >


                            <!-- DAYS -->

                            <div class="hot-deals-countdown-item">

                                <strong data-days>
                                    00
                                </strong>

                                <span>
                                    DAYS
                                </span>

                            </div>


                            <div class="hot-deals-countdown-separator">
                                :
                            </div>


                            <!-- HOURS -->

                            <div class="hot-deals-countdown-item">

                                <strong data-hours>
                                    00
                                </strong>

                                <span>
                                    HOURS
                                </span>

                            </div>


                            <div class="hot-deals-countdown-separator">
                                :
                            </div>


                            <!-- MINUTES -->

                            <div class="hot-deals-countdown-item">

                                <strong data-minutes>
                                    00
                                </strong>

                                <span>
                                    MINS
                                </span>

                            </div>


                            <div class="hot-deals-countdown-separator">
                                :
                            </div>


                            <!-- SECONDS -->

                            <div class="hot-deals-countdown-item">

                                <strong data-seconds>
                                    00
                                </strong>

                                <span>
                                    SECS
                                </span>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- =================================================
                     PRODUCTS 2 - 12
                     
                     IMPORTANT:
                     display: contents in CSS makes these 11 cards
                     participate directly in the 5-column main grid.
                     
                     Therefore:
                     
                     Product 2  Product 3  Product 4
                     Product 5  Product 6  Product 7
                     
                     Product 8  Product 9  Product 10
                     Product 11 Product 12
                     
                     The final 5 appear underneath the big card.
                ================================================== -->

                <div class="hot-deals-products">


                    <?php

                    $small_hot_products = array_slice(
                        $hot_sale_products,
                        1
                    );

                    ?>


                    <?php foreach (
                        $small_hot_products
                        as $product
                    ): ?>


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | DISCOUNT CHECK
                        |--------------------------------------------------------------------------
                        */

                        $has_discount =
                            !empty(
                                $product['discount_price']
                            )
                            &&
                            $product['discount_price'] > 0
                            &&
                            $product['discount_price']
                            <
                            $product['price'];


                        /*
                        |--------------------------------------------------------------------------
                        | DISPLAY PRICE
                        |--------------------------------------------------------------------------
                        */

                        $display_price =
                            $has_discount
                            ?
                            $product['discount_price']
                            :
                            $product['price'];


                        /*
                        |--------------------------------------------------------------------------
                        | DISCOUNT PERCENTAGE
                        |--------------------------------------------------------------------------
                        */

                        $discount = 0;

                        if ($has_discount) {

                            $discount = round(
                                (
                                    (
                                        $product['price']
                                        -
                                        $product['discount_price']
                                    )
                                    /
                                    $product['price']
                                ) * 100
                            );

                        }

                        ?>


                        <!-- =================================================
                             SAME PRODUCT CARD AS POPULAR PRODUCTS
                        ================================================== -->

                        <div class="home-product-card hot-deals-product-card">


                            <!-- =================================================
                                 PRODUCT IMAGE AREA
                            ================================================== -->

                            <div class="home-product-image-wrapper">


                                <!-- SALE BADGE -->

                                <?php if ($has_discount): ?>

                                    <span class="home-sale-badge">

                                        Sale <?= $discount; ?>%

                                    </span>

                                <?php endif; ?>


                                <!-- PRODUCT ACTIONS -->

                                <div class="home-product-actions">


                                    <!-- WISHLIST -->

                                    <button
                                        type="button"
                                        class="home-action-btn wishlist-btn"
                                        data-product-id="<?= (int) $product['id']; ?>"
                                        title="Add to Wishlist"
                                    >

                                        <i class="bi bi-heart"></i>

                                    </button>


                                    <!-- QUICK VIEW -->

                                    <button
                                        type="button"
                                        class="home-action-btn quick-view"
                                        data-product-id="<?= (int) $product['id']; ?>"
                                        title="Quick View"
                                    >

                                        <i class="bi bi-eye"></i>

                                    </button>


                                </div>


                                <!-- PRODUCT IMAGE -->

                                <a
                                    href="/Ecomart/product/single.php?slug=<?= urlencode(
                                        $product['slug']
                                    ); ?>"
                                    class="home-product-image-link"
                                >


                                    <?php if (
                                        !empty(
                                            $product['product_image']
                                        )
                                    ): ?>

                                        <img
                                            src="/Ecomart/assets/uploads/products/<?= e(
                                                $product['product_image']
                                            ); ?>"
                                            alt="<?= e(
                                                $product['name']
                                            ); ?>"
                                            class="home-product-image"
                                        >

                                    <?php else: ?>

                                        <div class="hot-deals-small-placeholder">

                                            <i class="bi bi-image"></i>

                                        </div>

                                    <?php endif; ?>


                                </a>

                            </div>


                            <!-- =================================================
                                 PRODUCT INFORMATION
                            ================================================== -->

                            <div class="home-product-info">


                                <!-- PRODUCT NAME -->

                                <h3 class="home-product-name">

                                    <a
                                        href="/Ecomart/product/single.php?slug=<?= urlencode(
                                            $product['slug']
                                        ); ?>"
                                    >

                                        <?= e(
                                            $product['name']
                                        ); ?>

                                    </a>

                                </h3>


                                <!-- PRICE -->

                                <div class="home-product-price">


                                    <span class="current-price">

                                        $<?= number_format(
                                            $display_price,
                                            2
                                        ); ?>

                                    </span>


                                    <?php if ($has_discount): ?>

                                        <span class="old-price">

                                            $<?= number_format(
                                                $product['price'],
                                                2
                                            ); ?>

                                        </span>

                                    <?php endif; ?>


                                </div>


                                <!-- =================================================
                                     RATING UNDER PRICE
                                ================================================== -->

                                <div class="home-product-rating">


                                    <div class="rating-stars">

                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-half"></i>

                                    </div>


                                    <?php if (
                                        isset(
                                            $product['review_count']
                                        )
                                    ): ?>

                                        <span class="review-count">

                                            (
                                            <?= (int) $product['review_count']; ?>
                                            )

                                        </span>

                                    <?php endif; ?>


                                </div>


                                <!-- =================================================
                                     ADD TO CART
                                ================================================== -->

                                <button
                                    type="button"
                                    class="home-cart-btn add-to-cart"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Add to Cart"
                                >

                                    <i class="bi bi-bag"></i>

                                </button>


                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>


        <?php else: ?>


            <!-- =================================================
                 PRODUCT COUNT ERROR
            ================================================== -->

            <div class="alert alert-warning">

                Hot Deals requires exactly 12 active products.

                Currently available:

                <?= count($hot_sale_products); ?>

            </div>


        <?php endif; ?>


    </div>

</section>
<!--discount banner-->
<section class="discount-banner">

    <div class="container">

        <div class="row">

            <div class="col-12 px-0">

                <div class="maindis-banner">

                    <!-- Banner Image -->
                    <img
                        src="/Ecomart/assets/uploads/discountbanner/discountbanner.png"
                        alt="Summer Sale"
                        class="maindis-banner-image"
                    >

                    <!-- Banner Content -->
                    <div class="maindis-content">

                        <p class="primarytext">
                            Summer Sale
                        </p>

                        <h1 class="maindis-title">
                            <span class="mainsail">37%</span>
                            <span class="lastsail-title">OFF</span>
                        </h1>

                        <p class="maindis-description">
                            Free on all your order, Free Shipping and 30 days
                            money-back guarantee
                        </p>

                        <a
                            href="/Ecomart/products.php"
                            class="shop-btn"
                        >
                            Shop Now
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!--discount banner end-->

<!-- =========================================
     FEATURED PRODUCTS
========================================= -->

<section class="featured-products-section">

    <div class="container">

        <div class="section-heading-row">

            <h2 class="section-title">
                Featured Products
            </h2>

            <a
                href="/Ecomart/products.php?featured=1"
                class="section-view-all"
            >
                View All
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>


        <?php if (!empty($featuredProducts)): ?>

            <div class="featured-products-grid">

                <?php foreach ($featuredProducts as $product): ?>

                    <?php

                    $hasDiscount =
                        !empty($product['discount_price']) &&
                        (float) $product['discount_price'] > 0 &&
                        (float) $product['discount_price'] < (float) $product['price'];

                    $currentPrice = $hasDiscount
                        ? (float) $product['discount_price']
                        : (float) $product['price'];

                    $oldPrice = (float) $product['price'];

                    $discountPercent = 0;

                    if ($hasDiscount && $oldPrice > 0) {

                        $discountPercent = round(
                            (($oldPrice - $currentPrice) / $oldPrice) * 100
                        );

                    }

                    $averageRating = (float) $product['average_rating'];
                    $reviewCount = (int) $product['review_count'];

                    ?>

                    <!-- SAME PRODUCT CARD DESIGN -->
                    <div class="home-product-card">

                        <!-- Product Image -->
                        <div class="home-product-image-wrap">

                            <?php if ($hasDiscount): ?>

                                <span class="home-sale-badge">
                                    Sale <?= $discountPercent; ?>%
                                </span>

                            <?php endif; ?>


                            <!-- Product Actions -->
                            <div class="home-product-actions">

                                <button
                                    type="button"
                                    class="home-action-btn wishlist-btn"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Add to Wishlist"
                                >
                                    <i class="bi bi-heart"></i>
                                </button>


                                <button
                                    type="button"
                                    class="home-action-btn quick-view"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Quick View"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>


                            <!-- Product Image -->
                            <a
                                href="/Ecomart/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                                class="home-product-image-link"
                            >

                                <?php if (!empty($product['product_image'])): ?>

                                    <img
                                        src="/Ecomart/assets/uploads/products/<?= e($product['product_image']); ?>"
                                        alt="<?= e($product['name']); ?>"
                                        class="home-product-image"
                                    >

                                <?php else: ?>

                                    <img
                                        src="/Ecomart/assets/images/products/no-image.png"
                                        alt="<?= e($product['name']); ?>"
                                        class="home-product-image"
                                    >

                                <?php endif; ?>

                            </a>

                        </div>


                        <!-- Product Information -->
                        <div class="home-product-info">

                            <div class="home-product-info-row">

                                <div>

                                    <h3 class="home-product-name">

                                        <a
                                            href="/Ecomart/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                                        >
                                            <?= e($product['name']); ?>
                                        </a>

                                    </h3>


                                    <!-- Price -->
                                    <div class="home-product-price">

                                        <span class="current-price">
                                            $<?= number_format($currentPrice, 2); ?>
                                        </span>


                                        <?php if ($hasDiscount): ?>

                                            <span class="old-price">
                                                $<?= number_format($oldPrice, 2); ?>
                                            </span>

                                        <?php endif; ?>

                                    </div>


                                    <!-- Rating -->
                                    <div class="home-product-rating">

                                        <span class="rating-stars">

                                            <?php for ($star = 1; $star <= 5; $star++): ?>

                                                <?php if ($averageRating >= $star): ?>

                                                    <i class="bi bi-star-fill"></i>

                                                <?php elseif ($averageRating >= ($star - 0.5)): ?>

                                                    <i class="bi bi-star-half"></i>

                                                <?php else: ?>

                                                    <i class="bi bi-star"></i>

                                                <?php endif; ?>

                                            <?php endfor; ?>

                                        </span>


                                        <span class="review-count">
                                            (<?= $reviewCount; ?>)
                                        </span>

                                    </div>

                                </div>


                                <!-- Add To Cart -->
                                <button
                                    type="button"
                                    class="home-cart-btn add-to-cart"
                                    data-product-id="<?= (int) $product['id']; ?>"
                                    title="Add to Cart"
                                    <?= ((int) $product['stock'] <= 0) ? 'disabled' : ''; ?>
                                >
                                    <i class="bi bi-bag"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="text-center py-4">
                <p class="text-muted mb-0">
                    No featured products available.
                </p>
            </div>

        <?php endif; ?>

    </div>

</section>

<!-- =========================================
     LATEST NEWS
========================================= -->

<!-- =========================================
     LATEST NEWS
========================================= -->

<section class="latest-news-section">

    <div class="container">

        <!-- Centered Heading -->
        <div class="latest-news-heading">

            <h2>
                Latest News
            </h2>

        </div>


        <?php if (!empty($latestNews)): ?>

            <div class="latest-news-grid">

                <?php foreach ($latestNews as $post): ?>

                    <?php

                    /*
                     * Use published_at when available.
                     * Otherwise use created_at.
                     */
                    $postDate = !empty($post['published_at'])
                        ? strtotime($post['published_at'])
                        : strtotime($post['created_at']);

                    $day = date('d', $postDate);
                    $month = strtoupper(date('M', $postDate));

                    ?>

                    <article class="latest-news-card">


                        <!-- =================================
                             IMAGE
                        ================================== -->

                        <div class="latest-news-image">

                            <a
                                href="/Ecomart/blog/single.php?slug=<?= urlencode($post['slug']); ?>"
                            >

                                <?php if (!empty($post['featured_image'])): ?>

                                    <img
                                        src="/Ecomart/assets/uploads/blog/<?= e($post['featured_image']); ?>"
                                        alt="<?= e($post['title']); ?>"
                                    >

                                <?php else: ?>

                                    <img
                                        src="/Ecomart/assets/images/blog/no-image.png"
                                        alt="<?= e($post['title']); ?>"
                                    >

                                <?php endif; ?>

                            </a>


                            <!-- =================================
                                 DATE
                            ================================== -->

                            <div class="latest-news-date">

                                <span class="latest-news-day">
                                    <?= e($day); ?>
                                </span>

                                <span class="latest-news-month">
                                    <?= e($month); ?>
                                </span>

                            </div>

                        </div>


                        <!-- =================================
                             CONTENT
                        ================================== -->

                        <div class="latest-news-content">


                            <!-- Metadata -->

                            <div class="latest-news-meta">

                                <!-- Category -->

                                <span class="latest-news-meta-item">

                                    <i class="bi bi-tag"></i>

                                    <?= !empty($post['category_name'])
                                        ? e($post['category_name'])
                                        : 'Food';
                                    ?>

                                </span>


                                <!-- Author -->

                                <span class="latest-news-meta-item">

                                    <i class="bi bi-person"></i>

                                    By
                                    <?= !empty($post['author_name'])
                                        ? e($post['author_name'])
                                        : 'Admin';
                                    ?>

                                </span>


                                <!-- Comments -->

                                <span class="latest-news-meta-item">

                                    <i class="bi bi-chat-dots"></i>

                                    0 Comments

                                </span>

                            </div>


                            <!-- Title -->

                            <h3 class="latest-news-title">

                                <a
                                    href="/Ecomart/blog/single.php?slug=<?= urlencode($post['slug']); ?>"
                                >
                                    <?= e($post['title']); ?>
                                </a>

                            </h3>


                            <!-- Excerpt -->

                            <?php if (!empty($post['excerpt'])): ?>

                                <p class="latest-news-excerpt">
                                    <?= e($post['excerpt']); ?>
                                </p>

                            <?php endif; ?>


                            <!-- Read More -->

                            <a
                                href="/Ecomart/blog/single.php?slug=<?= urlencode($post['slug']); ?>"
                                class="latest-news-read-more"
                            >
                                Read More
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="text-center">

                <p class="text-muted mb-0">
                    No latest news available.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
<!-- =========================================
     CLIENT TESTIMONIALS
========================================= -->

<section class="client-testimonials">

    <div class="container">

        <!-- Section Header -->

        <div class="testimonial-heading-row">

            <h2 class="testimonial-title">
                Client Testimonials
            </h2>


            <div class="testimonial-navigation">

                <button
                    type="button"
                    class="testimonial-prev"
                    aria-label="Previous testimonial"
                >
                    <i class="bi bi-arrow-left"></i>
                </button>


                <button
                    type="button"
                    class="testimonial-next"
                    aria-label="Next testimonial"
                >
                    <i class="bi bi-arrow-right"></i>
                </button>

            </div>

        </div>


        <!-- =================================
             SWIPER
        ================================== -->

        <div class="swiper testimonial-swiper">

            <div class="swiper-wrapper">


                <!-- =============================
                     TESTIMONIAL 1
                ============================== -->

                <div class="swiper-slide">

                    <div class="testimonial-card">

                        <div class="testimonial-quote-icon">
                            <i class="bi bi-quote"></i>
                        </div>


                        <p class="testimonial-text">
                            Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget
                        </p>


                        <div class="testimonial-bottom">

                            <div class="testimonial-customer">

                                <img
                                    src="/Ecomart/assets/images/testimonials/customer-1.png"
                                    alt="Robert Fox"
                                    class="testimonial-avatar"
                                >

                                <div class="testimonial-customer-info">

                                    <h4>
                                        Robert Fox
                                    </h4>

                                    <span>
                                        Customer
                                    </span>

                                </div>

                            </div>


                            <div class="testimonial-stars">

                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =============================
                     TESTIMONIAL 2
                ============================== -->

                <div class="swiper-slide">

                    <div class="testimonial-card">

                        <div class="testimonial-quote-icon">
                            <i class="bi bi-quote"></i>
                        </div>


                        <p class="testimonial-text">
                           Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget
                        </p>


                        <div class="testimonial-bottom">

                            <div class="testimonial-customer">

                                <img
                                    src="/Ecomart/assets/images/testimonials/customer-2.png"
                                    alt="Jane Cooper"
                                    class="testimonial-avatar"
                                >

                                <div class="testimonial-customer-info">

                                    <h4>
                                        Jane Cooper
                                    </h4>

                                    <span>
                                        Customer
                                    </span>

                                </div>

                            </div>


                            <div class="testimonial-stars">

                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =============================
                     TESTIMONIAL 3
                ============================== -->

                <div class="swiper-slide">

                    <div class="testimonial-card">

                        <div class="testimonial-quote-icon">
                            <i class="bi bi-quote"></i>
                        </div>


                        <p class="testimonial-text">
                           Pellentesque eu nibh eget mauris congue mattis mattis nec tellus. Phasellus imperdiet elit eu magna dictum, bibendum cursus velit sodales. Donec sed neque eget
                        </p>


                        <div class="testimonial-bottom">

                            <div class="testimonial-customer">

                                <img
                                    src="/Ecomart/assets/images/testimonials/customer-3.png"
                                    alt="Eleanor Pena"
                                    class="testimonial-avatar"
                                >

                                <div class="testimonial-customer-info">

                                    <h4>
                                        Eleanor Pena
                                    </h4>

                                    <span>
                                        Customer
                                    </span>

                                </div>

                            </div>


                            <div class="testimonial-stars">

                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =============================
                     TESTIMONIAL 4
                ============================== -->

                <div class="swiper-slide">

                    <div class="testimonial-card">

                        <div class="testimonial-quote-icon">
                            <i class="bi bi-quote"></i>
                        </div>


                        <p class="testimonial-text">
                            Pellentesque eu nibh eget mauris congue
                            mattis mattis nec tellus. Phasellus imperdiet
                            elit eu magna dictum, bibendum cursus velit
                            sodales.
                        </p>


                        <div class="testimonial-bottom">

                            <div class="testimonial-customer">

                                <img
                                    src="/Ecomart/assets/images/testimonials/customer-4.png"
                                    alt="Guy Hawkins"
                                    class="testimonial-avatar"
                                >

                                <div class="testimonial-customer-info">

                                    <h4>
                                        Guy Hawkins
                                    </h4>

                                    <span>
                                        Customer
                                    </span>

                                </div>

                            </div>


                            <div class="testimonial-stars">

                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
<!-- =========================================
     BRAND SECTION
========================================= -->

<section class="brand-section">

    <div class="container">

        <div class="brand-list">

            <div class="brand-item">
                <img
                    src="/Ecomart/assets/images/brands/steps.png"
                    alt="Steps"
                >
            </div>

            <div class="brand-item">
                <img
                    src="/Ecomart/assets/images/brands/ringo.png"
                    alt="Ringo"
                >
            </div>

            <div class="brand-item">
                <img
                    src="/Ecomart/assets/images/brands/food.png"
                    alt="Food"
                >
            </div>

            <div class="brand-item">
                <img
                    src="/Ecomart/assets/images/brands/foods.png"
                    alt="Book Off"
                >
            </div>

            <div class="brand-item">
                <img
                    src="/Ecomart/assets/images/brands/book-of.png"
                    alt="G Series"
                >
            </div>

            <div class="brand-item">
                <img
                    src="/Ecomart/assets/images/brands/g-series.png"
                    alt="Brand"
                >
            </div>

        </div>

    </div>

</section>
<!-- =========================================
     INSTAGRAM
========================================= -->

<section class="instagram-section">

    <div class="container">

        <h2 class="instagram-title mt-2">
            Follow us on Instagram
        </h2>


        <div class="instagram-grid">

            <a
                href="#"
                class="instagram-item"
                aria-label="Instagram post"
            >

                <img
                    src="/Ecomart/assets/images/instagram/instagram-1.png"
                    alt="Ecomart Instagram"
                >

                <span class="instagram-overlay">
                    <i class="bi bi-instagram"></i>
                </span>

            </a>


            <a
                href="#"
                class="instagram-item"
                aria-label="Instagram post"
            >

                <img
                    src="/Ecomart/assets/images/instagram/instagram-2.png"
                    alt="Ecomart Instagram"
                >

                <span class="instagram-overlay">
                    <i class="bi bi-instagram"></i>
                </span>

            </a>


            <a
                href="#"
                class="instagram-item"
                aria-label="Instagram post"
            >

                <img
                    src="/Ecomart/assets/images/instagram/instagram-3.png"
                    alt="Ecomart Instagram"
                >

                <span class="instagram-overlay">
                    <i class="bi bi-instagram"></i>
                </span>

            </a>


            <a
                href="#"
                class="instagram-item"
                aria-label="Instagram post"
            >

                <img
                    src="/Ecomart/assets/images/instagram/instagram-4.png"
                    alt="Ecomart Instagram"
                >

                <span class="instagram-overlay">
                    <i class="bi bi-instagram"></i>
                </span>

            </a>


            <a
                href="#"
                class="instagram-item"
                aria-label="Instagram post"
            >

                <img
                    src="/Ecomart/assets/images/instagram/instagram-5.png"
                    alt="Ecomart Instagram"
                >

                <span class="instagram-overlay">
                    <i class="bi bi-instagram"></i>
                </span>

            </a>


            <a
                href="#"
                class="instagram-item"
                aria-label="Instagram post"
            >

                <img
                    src="/Ecomart/assets/images/instagram/instagram-6.png"
                    alt="Ecomart Instagram"
                >

                <span class="instagram-overlay">
                    <i class="bi bi-instagram"></i>
                </span>

            </a>

        </div>

    </div>

</section>


<!-- =========================================================
     QUICK VIEW MODAL
========================================================= -->

<div
    class="modal fade"
    id="quickViewModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Product Quick View
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <div
                class="modal-body"
                id="quickViewContent"
            >

                <div class="text-center py-5">

                    <div
                        class="spinner-border text-success"
                    ></div>

                    <p class="mt-3 mb-0">
                        Loading product...
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>




<?php

require_once __DIR__ . '/includes/footer.php';
?>