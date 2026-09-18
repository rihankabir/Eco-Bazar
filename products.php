<?php

$page_title = 'Products';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';


/*
|--------------------------------------------------------------------------
| CATEGORY FILTER
|--------------------------------------------------------------------------
*/

$category_slug = trim($_GET['category'] ?? '');


/*
|--------------------------------------------------------------------------
| PRICE FILTER
|--------------------------------------------------------------------------
*/

$min_price = isset($_GET['min_price'])
    ? (float) $_GET['min_price']
    : 0;

$max_price = isset($_GET['max_price'])
    ? (float) $_GET['max_price']
    : 1500;


/*
|--------------------------------------------------------------------------
| KEEP PRICE VALUES SAFE
|--------------------------------------------------------------------------
*/

if ($min_price < 0) {
    $min_price = 0;
}

if ($max_price > 1500) {
    $max_price = 1500;
}

if ($min_price > $max_price) {

    $temporary_price = $min_price;

    $min_price = $max_price;

    $max_price = $temporary_price;
}


/*
|--------------------------------------------------------------------------
| SELECTED CATEGORY
|--------------------------------------------------------------------------
*/

$selected_category = null;


if ($category_slug !== '') {

    $stmt = $pdo->prepare("
        SELECT
            id,
            parent_id,
            name,
            slug,
            image,
            description,
            status,
            sort_order
        FROM categories
        WHERE slug = ?
        AND status = 'active'
        LIMIT 1
    ");

    $stmt->execute([
        $category_slug
    ]);

    $selected_category = $stmt->fetch();
}


/*
|--------------------------------------------------------------------------
| GET ACTIVE CATEGORIES + PRODUCT COUNTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT

        categories.id,
        categories.name,
        categories.slug,
        categories.image,
        categories.description,
        categories.sort_order,

        COUNT(products.id) AS product_count

    FROM categories

    LEFT JOIN products
        ON products.category_id = categories.id
        AND products.status = 'active'

    WHERE categories.status = 'active'

    GROUP BY
        categories.id,
        categories.name,
        categories.slug,
        categories.image,
        categories.description,
        categories.sort_order

    ORDER BY
        categories.sort_order ASC,
        categories.name ASC
");

$stmt->execute();

$categories = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| TOTAL PRODUCTS
|--------------------------------------------------------------------------
*/

$total_products = 0;


foreach ($categories as $category) {

    $total_products += (int) $category['product_count'];

}


/*
|--------------------------------------------------------------------------
| PRODUCT QUERY
|--------------------------------------------------------------------------
*/

$product_sql = "
    SELECT

        products.id,
        products.category_id,
        products.name,
        products.slug,
        products.sku,
        products.brand,
        products.short_description,
        products.description,
        products.price,
        products.discount_price,
        products.stock,
        products.status,
        products.featured,
        products.created_at,

        categories.name AS category_name,

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
            SELECT ROUND(
                AVG(product_reviews.rating),
                1
            )

            FROM product_reviews

            WHERE product_reviews.product_id = products.id

            AND product_reviews.status = 'approved'

        ) AS average_rating,

        (
            SELECT COUNT(product_reviews.id)

            FROM product_reviews

            WHERE product_reviews.product_id = products.id

            AND product_reviews.status = 'approved'

        ) AS review_count

    FROM products

    INNER JOIN categories
        ON categories.id = products.category_id

    WHERE products.status = 'active'

    AND categories.status = 'active'

    AND products.price >= ?

    AND products.price <= ?
";


/*
|--------------------------------------------------------------------------
| CATEGORY CONDITION
|--------------------------------------------------------------------------
*/

if ($selected_category) {

    $product_sql .= "
        AND products.category_id = ?
    ";

}


/*
|--------------------------------------------------------------------------
| PRODUCT ORDER
|--------------------------------------------------------------------------
*/

$product_sql .= "
    ORDER BY products.created_at DESC
";


/*
|--------------------------------------------------------------------------
| EXECUTE PRODUCT QUERY
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare($product_sql);


if ($selected_category) {

    $stmt->execute([
        $min_price,
        $max_price,
        $selected_category['id']
    ]);

} else {

    $stmt->execute([
        $min_price,
        $max_price
    ]);

}


$products = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| SALE PRODUCTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT

        products.id,
        products.name,
        products.price,
        products.discount_price,

        (
            SELECT product_images.image

            FROM product_images

            WHERE product_images.product_id = products.id

            ORDER BY
                product_images.is_primary DESC,
                product_images.sort_order ASC,
                product_images.id ASC

            LIMIT 1

        ) AS product_image

    FROM products

    WHERE products.status = 'active'

    AND products.discount_price > 0

    AND products.discount_price < products.price

    ORDER BY products.created_at DESC

    LIMIT 3
");

$stmt->execute();

$sale_products = $stmt->fetchAll();

?>



<!-- =========================================================
     BREADCRUMB BANNER
========================================================= -->

<section class="products-banner">

    <div class="container">

        <div class="products-breadcrumb">

            <a href="/Ecomart/">

                <i class="fa-solid fa-house"></i>

                Home

            </a>

            <i class="fa-solid fa-chevron-right"></i>

            <span>
                Categories
            </span>

            <i class="fa-solid fa-chevron-right"></i>

            <span class="active">

                <?php if ($selected_category): ?>

                    <?= e($selected_category['name']); ?>

                <?php else: ?>

                    All Products

                <?php endif; ?>

            </span>

        </div>

    </div>

</section>



<!-- =========================================================
     PRODUCTS SECTION
========================================================= -->

<section class="products-section">

    <div class="container">

        <div class="row g-4">


            <!-- =================================================
                 SIDEBAR
            ================================================= -->

            <div class="col-lg-3">

                <aside class="products-sidebar">


                    <!-- FILTER BUTTON -->

                    <button
                        type="button"
                        class="products-filter-button"
                    >

                        Filter

                        <i class="fa-solid fa-sliders"></i>

                    </button>



                    <!-- =========================================
                         ALL CATEGORIES
                    ========================================== -->

                    <div class="filter-section">

                        <div class="filter-title">

                            <h4>
                                All Categories
                            </h4>

                            <i class="fa-solid fa-chevron-up"></i>

                        </div>


                        <div class="category-filter-list">


                            <!-- ALL PRODUCTS -->

                            <a
                                href="/Ecomart/products.php"
                                class="category-filter-item
                                <?= $selected_category === null ? 'selected' : ''; ?>"
                            >

                                <span class="category-radio"></span>

                                <span class="category-filter-name">
                                    All Categories
                                </span>

                                <span class="category-count">
                                    (<?= $total_products; ?>)
                                </span>

                            </a>



                            <!-- DYNAMIC CATEGORIES -->

                            <?php foreach ($categories as $category): ?>

                                <a
                                    href="/Ecomart/products.php?category=<?= urlencode($category['slug']); ?>"
                                    class="category-filter-item
                                    <?= (
                                        $selected_category
                                        &&
                                        $selected_category['id'] == $category['id']
                                    )
                                    ? 'selected'
                                    : ''; ?>"
                                >

                                    <span class="category-radio"></span>

                                    <span class="category-filter-name">

                                        <?= e($category['name']); ?>

                                    </span>

                                    <span class="category-count">

                                        (<?= (int) $category['product_count']; ?>)

                                    </span>

                                </a>

                            <?php endforeach; ?>


                        </div>

                    </div>



                   <!-- =========================================
     PRICE FILTER
========================================== -->

<div class="filter-section">

    <div class="filter-title">

        <h4>
            Price
        </h4>

        <i class="fa-solid fa-chevron-up"></i>

    </div>


    <form
        method="GET"
        action="/Ecomart/products.php"
        id="priceFilterForm"
    >

        <?php if ($selected_category): ?>

            <input
                type="hidden"
                name="category"
                value="<?= e($selected_category['slug']); ?>"
            >

        <?php endif; ?>


        <!-- Hidden values sent to PHP -->

        <input
            type="hidden"
            name="min_price"
            id="minPriceValue"
            value="<?= (int) $min_price; ?>"
        >

        <input
            type="hidden"
            name="max_price"
            id="maxPriceValue"
            value="<?= (int) $max_price; ?>"
        >


        <div class="price-filter">

            <div class="price-slider">

                <!-- Gray background line -->

                <div class="price-slider-track"></div>


                <!-- Green selected range -->

                <div
                    class="price-slider-range"
                    id="priceSliderRange"
                ></div>


                <!-- Minimum slider -->

                <input
                    type="range"
                    id="minPrice"
                    min="0"
                    max="1500"
                    value="<?= (int) $min_price; ?>"
                    step="1"
                >


                <!-- Maximum slider -->

                <input
                    type="range"
                    id="maxPrice"
                    min="0"
                    max="1500"
                    value="<?= (int) $max_price; ?>"
                    step="1"
                >

            </div>


            <div class="price-text">

                Price:

                <strong id="priceDisplay">

                    $<?= number_format($min_price, 0); ?>

                    -

                    $<?= number_format($max_price, 0); ?>

                </strong>

            </div>

        </div>

    </form>

</div>



                    <!-- =========================================
                         RATING
                    ========================================== -->

                    <div class="filter-section">

                        <div class="filter-title">

                            <h4>
                                Rating
                            </h4>

                            <i class="fa-solid fa-chevron-up"></i>

                        </div>


                        <div class="rating-filter">


                            <label>

                                <input
                                    type="checkbox"
                                    disabled
                                >

                                <span class="rating-stars">
                                    ★★★★★
                                </span>

                                <span>
                                    5.0
                                </span>

                            </label>


                            <label>

                                <input
                                    type="checkbox"
                                    disabled
                                >

                                <span class="rating-stars">
                                    ★★★★<span class="empty-star">★</span>
                                </span>

                                <span>
                                    4.0 & up
                                </span>

                            </label>


                            <label>

                                <input
                                    type="checkbox"
                                    disabled
                                >

                                <span class="rating-stars">
                                    ★★★<span class="empty-star">★★</span>
                                </span>

                                <span>
                                    3.0 & up
                                </span>

                            </label>


                            <label>

                                <input
                                    type="checkbox"
                                    disabled
                                >

                                <span class="rating-stars">
                                    ★★<span class="empty-star">★★★</span>
                                </span>

                                <span>
                                    2.0 & up
                                </span>

                            </label>


                            <label>

                                <input
                                    type="checkbox"
                                    disabled
                                >

                                <span class="rating-stars">
                                    ★<span class="empty-star">★★★★</span>
                                </span>

                                <span>
                                    1.0 & up
                                </span>

                            </label>


                        </div>

                    </div>



                    <!-- =========================================
                         POPULAR TAG
                    ========================================== -->

                    <div class="filter-section">

                        <div class="filter-title">

                            <h4>
                                Popular Tag
                            </h4>

                            <i class="fa-solid fa-chevron-up"></i>

                        </div>


                        <div class="popular-tags">

                            <span>
                                Healthy
                            </span>

                            <span class="active">
                                Low fat
                            </span>

                            <span>
                                Vegetarian
                            </span>

                            <span>
                                Kid foods
                            </span>

                            <span>
                                Vitamins
                            </span>

                            <span>
                                Bread
                            </span>

                            <span>
                                Meat
                            </span>

                            <span>
                                Snacks
                            </span>

                            <span>
                                Tiffin
                            </span>

                            <span>
                                Lunch
                            </span>

                            <span>
                                Dinner
                            </span>

                            <span>
                                Breakfast
                            </span>

                            <span>
                                Fruit
                            </span>

                        </div>

                    </div>



                    <!-- =========================================
                         PROMOTION
                    ========================================== -->

                    <div class="sidebar-promotion">

                        <div class="promotion-content">

                            <div>

                                <strong>
                                    79%
                                </strong>

                                <span>
                                    Discount
                                </span>

                            </div>

                            <small>
                                on your first order
                            </small>

                            <a href="/Ecomart/products.php">

                                Shop Now

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </div>

                    </div>



                    <!-- =========================================
                         SALE PRODUCTS
                    ========================================== -->

                    <div class="sale-products">

                        <h4>
                            Sale Products
                        </h4>


                        <?php foreach ($sale_products as $sale_product): ?>


                            <div class="sale-product-item">


                                <div class="sale-product-image">

                                    <?php if (!empty($sale_product['product_image'])): ?>

                                        <img
                                            src="/Ecomart/assets/uploads/products/<?= e($sale_product['product_image']); ?>"
                                            alt="<?= e($sale_product['name']); ?>"
                                        >

                                    <?php else: ?>

                                        <div class="sale-no-image">

                                            <i class="fa-regular fa-image"></i>

                                        </div>

                                    <?php endif; ?>

                                </div>


                                <div class="sale-product-info">

                                    <small>

                                        <?= e($sale_product['name']); ?>

                                    </small>


                                    <div>

                                        <strong>

                                            $<?= number_format(
                                                $sale_product['discount_price'],
                                                2
                                            ); ?>

                                        </strong>


                                        <del>

                                            $<?= number_format(
                                                $sale_product['price'],
                                                2
                                            ); ?>

                                        </del>

                                    </div>


                                    <div class="mini-stars">

                                        ★★★★★

                                    </div>

                                </div>


                            </div>


                        <?php endforeach; ?>


                        <?php if (empty($sale_products)): ?>

                            <p class="text-muted small">
                                No sale products available.
                            </p>

                        <?php endif; ?>


                    </div>


                </aside>

            </div>



            <!-- =================================================
                 PRODUCTS CONTENT
            ================================================= -->

            <div class="col-lg-9">


                <!-- =============================================
                     TOOLBAR
                ============================================== -->

                <div class="products-toolbar">


                    <button
                        type="button"
                        class="mobile-filter-button"
                    >

                        <i class="fa-solid fa-sliders"></i>

                        Filter

                    </button>


                    <div class="sort-box">

                        <span>
                            Sort by:
                        </span>


                        <select>

                            <option>
                                Latest
                            </option>

                            <option>
                                Price Low to High
                            </option>

                            <option>
                                Price High to Low
                            </option>

                            <option>
                                Name A-Z
                            </option>

                        </select>

                    </div>


                    <div class="results-count">

                        <strong>
                            <?= count($products); ?>
                        </strong>

                        Results Found

                    </div>


                </div>



                <!-- =============================================
                     PRODUCT GRID
                ============================================== -->

                <div class="row g-3">


                    <?php if (empty($products)): ?>

                        <div class="col-12">

                            <div class="no-products">

                                <i class="fa-solid fa-box-open"></i>

                                <h3>
                                    No Products Found
                                </h3>

                                <p>
                                    Try changing your category or price range.
                                </p>

                                <a
                                    href="/Ecomart/products.php"
                                    class="no-products-button"
                                >
                                    View All Products
                                </a>

                            </div>

                        </div>

                    <?php endif; ?>



                    <?php foreach ($products as $product): ?>


                        <?php

                        $has_discount =
                            $product['discount_price'] !== null
                            &&
                            (float) $product['discount_price'] > 0
                            &&
                            (float) $product['discount_price']
                            <
                            (float) $product['price'];


                        $discount_percent = 0;


                        if ($has_discount) {

                            $discount_percent = round(
                                (
                                    (
                                        (float) $product['price']
                                        -
                                        (float) $product['discount_price']
                                    )
                                    /
                                    (float) $product['price']
                                )
                                * 100
                            );

                        }


                        $rating = $product['average_rating']
                            ? (float) $product['average_rating']
                            : 0;

                        ?>


                        <div class="col-6 col-md-4">


                            <div class="product-card">


                                <!-- PRODUCT IMAGE -->

                                <div class="product-card-image">


                                    <?php if ($has_discount): ?>

                                        <span class="sale-badge">

                                            Sale <?= $discount_percent; ?>%

                                        </span>

                                    <?php endif; ?>


                                    <?php if ((int) $product['stock'] <= 0): ?>

                                        <span class="stock-badge">

                                            Out of Stock

                                        </span>

                                    <?php endif; ?>


                                    <!-- ACTION BUTTONS -->

                                    <div class="product-actions">


                                        <button
                                            type="button"
                                            title="Add to Wishlist"
                                        >

                                            <i class="fa-regular fa-heart"></i>

                                        </button>


                                        <button
                                            type="button"
                                            title="Quick View"
                                        >

                                            <i class="fa-regular fa-eye"></i>

                                        </button>


                                    </div>


                                    <!-- PRODUCT IMAGE -->

                                    <?php if (!empty($product['product_image'])): ?>

                                        <img
                                            src="/Ecomart/assets/uploads/products/<?= e($product['product_image']); ?>"
                                            alt="<?= e($product['name']); ?>"
                                        >

                                    <?php else: ?>

                                        <div class="product-no-image">

                                            <i class="fa-regular fa-image"></i>

                                            <span>
                                                No Image
                                            </span>

                                        </div>

                                    <?php endif; ?>


                                </div>



                                <!-- PRODUCT BODY -->

                                <div class="product-card-body">


                                    <a
                                        href="/Ecomart/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                                        class="product-name"
                                    >

                                        <?= e($product['name']); ?>

                                    </a>


                                    <!-- PRICE -->

                                    <div class="product-price">


                                        <?php if ($has_discount): ?>

                                            <strong>

                                                $<?= number_format(
                                                    $product['discount_price'],
                                                    2
                                                ); ?>

                                            </strong>


                                            <del>

                                                $<?= number_format(
                                                    $product['price'],
                                                    2
                                                ); ?>

                                            </del>


                                        <?php else: ?>

                                            <strong>

                                                $<?= number_format(
                                                    $product['price'],
                                                    2
                                                ); ?>

                                            </strong>

                                        <?php endif; ?>


                                        <button
                                            type="button"
                                            class="product-cart-button"
                                            title="Add to Cart"
                                        >

                                            <i class="fa-solid fa-bag-shopping"></i>

                                        </button>


                                    </div>



                                    <!-- RATING -->

                                    <div class="product-rating">


                                        <span class="stars">


                                            <?php for ($i = 1; $i <= 5; $i++): ?>


                                                <?php if ($rating >= $i): ?>

                                                    <i class="fa-solid fa-star"></i>


                                                <?php elseif ($rating >= ($i - 0.5)): ?>

                                                    <i class="fa-solid fa-star-half-stroke"></i>


                                                <?php else: ?>

                                                    <i class="fa-regular fa-star"></i>

                                                <?php endif; ?>


                                            <?php endfor; ?>


                                        </span>


                                        <small>

                                            (<?= (int) $product['review_count']; ?>)

                                        </small>


                                    </div>


                                </div>


                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>



                <!-- =============================================
                     PAGINATION
                ============================================== -->

                <div class="products-pagination">


                    <button disabled>

                        <i class="fa-solid fa-chevron-left"></i>

                    </button>


                    <button class="active">
                        1
                    </button>


                    <button>
                        2
                    </button>


                    <button>
                        3
                    </button>


                    <button>
                        4
                    </button>


                    <span>
                        ...
                    </span>


                    <button>
                        21
                    </button>


                    <button>

                        <i class="fa-solid fa-chevron-right"></i>

                    </button>


                </div>


            </div>

        </div>

    </div>

</section>



<?php require_once __DIR__ . '/includes/footer.php'; ?>