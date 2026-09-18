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