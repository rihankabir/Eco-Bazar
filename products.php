<?php

$page_title = 'Products';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';


/*
|--------------------------------------------------------------------------
| Category Filter
|--------------------------------------------------------------------------
*/

$category_slug = trim($_GET['category'] ?? '');

$selected_category = null;


/*
|--------------------------------------------------------------------------
| Find Selected Category
|--------------------------------------------------------------------------
*/

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
| Categories With Product Counts
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
| Get Products
|--------------------------------------------------------------------------
*/

if ($selected_category) {

    $stmt = $pdo->prepare("
        SELECT

            products.id,
            products.category_id,
            products.name,
            products.slug,
            products.sku,
            products.brand,
            products.short_description,
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
                SELECT ROUND(AVG(product_reviews.rating), 1)

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

        AND products.category_id = ?

        ORDER BY products.created_at DESC
    ");

    $stmt->execute([
        $selected_category['id']
    ]);

} else {

    $stmt = $pdo->prepare("
        SELECT

            products.id,
            products.category_id,
            products.name,
            products.slug,
            products.sku,
            products.brand,
            products.short_description,
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
                SELECT ROUND(AVG(product_reviews.rating), 1)

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

        ORDER BY products.created_at DESC
    ");

    $stmt->execute();
}


$products = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Total Product Count
|--------------------------------------------------------------------------
*/

$total_products = 0;

foreach ($categories as $category) {

    $total_products += (int) $category['product_count'];

}

?>

<!-- =========================================================
     BREADCRUMB / BANNER
========================================================= -->

<section class="products-banner">

    <div class="container">

        <div class="products-breadcrumb">

            <a href="/ecommerce/">

                <i class="fa-solid fa-house"></i>

                Home

            </a>

            <i class="fa-solid fa-chevron-right"></i>

            <span>Categories</span>

            <i class="fa-solid fa-chevron-right"></i>

            <span class="active">

                <?= $selected_category
                    ? e($selected_category['name'])
                    : 'All Products';
                ?>

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
                 LEFT SIDEBAR
            ================================================= -->

            <div class="col-lg-3">

                <aside class="products-sidebar">


                    <!-- Filter Button -->

                    <button
                        type="button"
                        class="products-filter-button"
                    >

                        Filter

                        <i class="fa-solid fa-sliders"></i>

                    </button>


                    <!-- Categories -->

                    <div class="filter-section">

                        <div class="filter-title">

                            <h4>
                                All Categories
                            </h4>

                            <i class="fa-solid fa-chevron-up"></i>

                        </div>


                        <div class="category-filter-list">


                            <!-- All Categories -->

                            <a
                                href="/ecommerce/products.php"
                                class="category-filter-item
                                <?= $selected_category === null
                                    ? 'selected'
                                    : '';
                                ?>"
                            >

                                <span class="category-radio"></span>

                                <span class="category-filter-name">
                                    All Categories
                                </span>

                                <span class="category-count">
                                    <?= $total_products; ?>
                                </span>

                            </a>


                            <!-- Dynamic Categories -->

                            <?php foreach ($categories as $category): ?>

                                <a
                                    href="/ecommerce/products.php?category=<?= urlencode($category['slug']); ?>"
                                    class="category-filter-item
                                    <?= (
                                        $selected_category
                                        &&
                                        $selected_category['id'] == $category['id']
                                    )
                                    ? 'selected'
                                    : '';
                                    ?>"
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


                    <!-- Price -->

                    <div class="filter-section">

                        <div class="filter-title">

                            <h4>
                                Price
                            </h4>

                            <i class="fa-solid fa-chevron-up"></i>

                        </div>


                        <div class="price-slider">

                            <div class="price-track">

                                <span class="price-dot left"></span>

                                <span class="price-dot right"></span>

                            </div>

                        </div>


                        <div class="price-values">

                            <span>
                                Price:
                            </span>

                            <strong>
                                $0 - $1,500
                            </strong>

                        </div>

                    </div>


                    <!-- Rating -->

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


                    <!-- Popular Tags -->

                    <div class="filter-section">

                        <div class="filter-title">

                            <h4>
                                Popular Tag
                            </h4>

                            <i class="fa-solid fa-chevron-up"></i>

                        </div>


                        <div class="popular-tags">

                            <span>Healthy</span>
                            <span class="active">Low fat</span>
                            <span>Vegetarian</span>
                            <span>Kid foods</span>
                            <span>Vitamins</span>
                            <span>Bread</span>
                            <span>Meat</span>
                            <span>Snacks</span>
                            <span>Tiffin</span>
                            <span>Lunch</span>
                            <span>Dinner</span>
                            <span>Breakfast</span>
                            <span>Fruit</span>

                        </div>

                    </div>


                    <!-- Promotion -->

                    <div class="sidebar-promotion">

                        <div class="promotion-content">

                            <span>
                                79%
                            </span>

                            Discount

                            <small>
                                on your first order
                            </small>

                            <a href="/ecommerce/products.php">

                                Shop Now

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>

                        </div>

                    </div>


                    <!-- Sale Products -->

                    <div class="sale-products">

                        <h4>
                            Sale Products
                        </h4>


                        <div class="sale-product-item">

                            <div class="sale-product-image">

                                <img
                                    src="/ecommerce/assets/uploads/products/green-capsicum.jpg"
                                    alt="Green Capsicum"
                                >

                            </div>

                            <div>

                                <small>
                                    Green Capsicum
                                </small>

                                <strong>
                                    $9.00
                                </strong>

                                <del>
                                    $20.99
                                </del>

                                <div class="mini-stars">
                                    ★★★★★
                                </div>

                            </div>

                        </div>


                        <div class="sale-product-item active">

                            <div class="sale-product-image">

                                <img
                                    src="/ecommerce/assets/uploads/products/fresh-mango.jpg"
                                    alt="Surjapur Mango"
                                >

                            </div>

                            <div>

                                <small>
                                    Surjapur Mango
                                </small>

                                <strong>
                                    $34.00
                                </strong>

                                <div class="mini-stars">
                                    ★★★★★
                                </div>

                            </div>

                        </div>


                        <div class="sale-product-item">

                            <div class="sale-product-image">

                                <img
                                    src="/ecommerce/assets/uploads/products/fresh-capsicum.jpg"
                                    alt="Capsicum"
                                >

                            </div>

                            <div>

                                <small>
                                    Green Capsicum
                                </small>

                                <strong>
                                    $9.00
                                </strong>

                                <del>
                                    $20.99
                                </del>

                                <div class="mini-stars">
                                    ★★★★★
                                </div>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>


            <!-- =================================================
                 RIGHT CONTENT
            ================================================= -->

            <div class="col-lg-9">


                <!-- Top Controls -->

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


                <!-- Product Grid -->

                <div class="row g-3">


                    <?php if (empty($products)): ?>

                        <div class="col-12">

                            <div class="no-products">

                                <i class="fa-solid fa-box-open"></i>

                                <h3>
                                    No Products Found
                                </h3>

                                <p>
                                    There are no products in this category yet.
                                </p>

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
                                        $product['price']
                                        -
                                        $product['discount_price']
                                    )
                                    /
                                    $product['price']
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


                                <!-- Image Area -->

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


                                    <?php if (!empty($product['product_image'])): ?>

                                        <img
                                            src="/ecommerce/assets/uploads/products/<?= e($product['product_image']); ?>"
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


                                <!-- Product Information -->

                                <div class="product-card-body">


                                    <a
                                        href="/ecommerce/product/single.php?slug=<?= urlencode($product['slug']); ?>"
                                        class="product-name"
                                    >

                                        <?= e($product['name']); ?>

                                    </a>


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


                                        <?php if ((int) $product['review_count'] > 0): ?>

                                            <small>
                                                (<?= (int) $product['review_count']; ?>)
                                            </small>

                                        <?php else: ?>

                                            <small>
                                                (0)
                                            </small>

                                        <?php endif; ?>

                                    </div>


                                </div>

                            </div>

                        </div>


                    <?php endforeach; ?>

                </div>


                <!-- Pagination -->

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