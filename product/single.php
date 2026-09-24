<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Get product slug
|--------------------------------------------------------------------------
*/

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: ../products.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Get product
|--------------------------------------------------------------------------
*/

$productSql = "
    SELECT
        p.id,
        p.category_id,
        p.name,
        p.slug,
        p.sku,
        p.brand,
        p.short_description,
        p.description,
        p.price,
        p.discount_price,
        p.stock,
        p.status,
        p.featured,
        p.created_at,

        c.name AS category_name,
        c.slug AS category_slug

    FROM products p

    INNER JOIN categories c
        ON c.id = p.category_id

    WHERE p.slug = :slug
      AND p.status = 1

    LIMIT 1
";

$productStmt = $pdo->prepare($productSql);
$productStmt->execute([
    ':slug' => $slug
]);

$product = $productStmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    http_response_code(404);
    echo 'Product not found.';
    exit;
}

$productId = (int) $product['id'];

/*
|--------------------------------------------------------------------------
| Product images
|--------------------------------------------------------------------------
*/

$imageSql = "
    SELECT
        id,
        image,
        sort_order,
        is_primary
    FROM product_images
    WHERE product_id = :product_id
    ORDER BY is_primary DESC, sort_order ASC, id ASC
";

$imageStmt = $pdo->prepare($imageSql);
$imageStmt->execute([
    ':product_id' => $productId
]);

$productImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Product information
|--------------------------------------------------------------------------
*/

$informationSql = "
    SELECT
        information_name,
        information_value
    FROM product_information
    WHERE product_id = :product_id
    ORDER BY sort_order ASC, id ASC
";

$informationStmt = $pdo->prepare($informationSql);
$informationStmt->execute([
    ':product_id' => $productId
]);

$productInformation = $informationStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Product tags
|--------------------------------------------------------------------------
*/

$tagsSql = "
    SELECT
        t.name,
        t.slug
    FROM product_tags pt

    INNER JOIN tags t
        ON t.id = pt.tag_id

    WHERE pt.product_id = :product_id
      AND t.status = 'active'

    ORDER BY t.name ASC
";

$tagsStmt = $pdo->prepare($tagsSql);
$tagsStmt->execute([
    ':product_id' => $productId
]);

$productTags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Reviews
|--------------------------------------------------------------------------
*/

$reviewSql = "
    SELECT
        COUNT(*) AS review_count,
        COALESCE(AVG(rating), 0) AS average_rating
    FROM product_reviews
    WHERE product_id = :product_id
      AND status = 'approved'
";

$reviewStmt = $pdo->prepare($reviewSql);
$reviewStmt->execute([
    ':product_id' => $productId
]);

$reviewSummary = $reviewStmt->fetch(PDO::FETCH_ASSOC);

$reviewCount = (int) ($reviewSummary['review_count'] ?? 0);
$averageRating = (float) ($reviewSummary['average_rating'] ?? 0);

/*
|--------------------------------------------------------------------------
| Customer reviews
|--------------------------------------------------------------------------
*/

$reviewsSql = "
    SELECT
        pr.id,
        pr.rating,
        pr.review,
        pr.created_at,

        u.first_name,
        u.last_name,
        u.profile_image

    FROM product_reviews pr

    LEFT JOIN users u
        ON u.id = pr.user_id

    WHERE pr.product_id = :product_id
      AND pr.status = 'approved'

    ORDER BY pr.created_at DESC
";

$reviewsStmt = $pdo->prepare($reviewsSql);
$reviewsStmt->execute([
    ':product_id' => $productId
]);

$productReviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Active offer
|--------------------------------------------------------------------------
*/

$offerSql = "
    SELECT
        id,
        offer_name,
        offer_price,
        starts_at,
        ends_at,
        badge_text
    FROM product_offers
    WHERE product_id = :product_id
      AND status = 'active'
      AND starts_at <= NOW()
      AND ends_at > NOW()
    ORDER BY offer_price ASC, id DESC
    LIMIT 1
";

$offerStmt = $pdo->prepare($offerSql);
$offerStmt->execute([
    ':product_id' => $productId
]);

$productOffer = $offerStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Price
|--------------------------------------------------------------------------
*/

$normalPrice = (float) $product['price'];

if ($productOffer) {
    $salePrice = (float) $productOffer['offer_price'];
} elseif (
    $product['discount_price'] !== null &&
    (float) $product['discount_price'] > 0
) {
    $salePrice = (float) $product['discount_price'];
} else {
    $salePrice = $normalPrice;
}

$discountPercent = 0;

if ($normalPrice > 0 && $salePrice < $normalPrice) {
    $discountPercent = round(
        (($normalPrice - $salePrice) / $normalPrice) * 100
    );
}

/*
|--------------------------------------------------------------------------
| Main product image
|--------------------------------------------------------------------------
*/

$mainImage = '';

if (!empty($productImages)) {
    $mainImage = $productImages[0]['image'];
}

/*
|--------------------------------------------------------------------------
| Image helper
|--------------------------------------------------------------------------
*/

function productImageUrl($image)
{
    if (!$image) {
        return '../assets/images/products/default-product.png';
    }

    /*
     * If database already contains a complete path
     */
    if (
        str_starts_with($image, 'http://') ||
        str_starts_with($image, 'https://') ||
        str_starts_with($image, '/')
    ) {
        return $image;
    }

    return '../assets/uploads/products/' . $image;
}

/*
|--------------------------------------------------------------------------
| Related products
|--------------------------------------------------------------------------
*/

$relatedSql = "
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
            ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
            LIMIT 1
        ) AS product_image,

        (
            SELECT COALESCE(AVG(pr.rating), 0)
            FROM product_reviews pr
            WHERE pr.product_id = p.id
              AND pr.status = 'approved'
        ) AS average_rating,

        (
            SELECT COUNT(*)
            FROM product_reviews pr2
            WHERE pr2.product_id = p.id
              AND pr2.status = 'approved'
        ) AS review_count

    FROM products p

    WHERE p.category_id = :category_id
      AND p.id != :product_id
      AND p.status = 1

    ORDER BY p.featured DESC, p.created_at DESC

    LIMIT 4
";

$relatedStmt = $pdo->prepare($relatedSql);

$relatedStmt->execute([
    ':category_id' => $product['category_id'],
    ':product_id' => $productId
]);

$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Header
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

?>

<!-- =========================================================
     PRODUCT BREADCRUMB
========================================================= -->

<section class="product-breadcrumb">
    <div class="container">

        <div class="product-breadcrumb-inner">

            <a href="../index.php">
                <i class="fa-solid fa-house"></i>
            </a>

            <span>
                <i class="fa-solid fa-angle-right"></i>
            </span>

            <span>Category</span>

            <span>
                <i class="fa-solid fa-angle-right"></i>
            </span>

            <a href="../products.php?category=<?= urlencode($product['category_slug']) ?>">
                <?= htmlspecialchars($product['category_name']) ?>
            </a>

            <span>
                <i class="fa-solid fa-angle-right"></i>
            </span>

            <strong>
                <?= htmlspecialchars($product['name']) ?>
            </strong>

        </div>

    </div>
</section>


<!-- =========================================================
     PRODUCT DETAILS
========================================================= -->

<section class="single-product-section">

    <div class="container">

        <div class="row">

            <!-- =============================================
                 LEFT SIDE
            ============================================== -->

            <div class="col-lg-6">

                <div class="product-gallery">

                    <!-- Thumbnail navigation -->

                    <div class="product-gallery-thumbnails">

                        <?php if (count($productImages) > 4): ?>

                            <button
                                type="button"
                                class="gallery-arrow gallery-prev"
                            >
                                <i class="fa-solid fa-chevron-up"></i>
                            </button>

                        <?php endif; ?>


                        <div
                            class="product-thumbnail-list"
                            id="productThumbnailList"
                        >

                            <?php foreach ($productImages as $index => $image): ?>

                                <button
                                    type="button"
                                    class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                    data-image="<?= htmlspecialchars(productImageUrl($image['image'])) ?>"
                                >

                                    <img
                                        src="<?= htmlspecialchars(productImageUrl($image['image'])) ?>"
                                        alt="<?= htmlspecialchars($product['name']) ?>"
                                    >

                                </button>

                            <?php endforeach; ?>

                        </div>


                        <?php if (count($productImages) > 4): ?>

                            <button
                                type="button"
                                class="gallery-arrow gallery-next"
                            >
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                        <?php endif; ?>

                    </div>


                    <!-- Main image -->

                    <div class="product-main-image-wrapper">

                        <img
                            id="productMainImage"
                            class="product-main-image"
                            src="<?= htmlspecialchars(productImageUrl($mainImage)) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                        >

                    </div>

                </div>

            </div>


            <!-- =============================================
                 RIGHT SIDE
            ============================================== -->

            <div class="col-lg-6">

                <div class="single-product-content">

                    <!-- Product title -->

                    <div class="product-title-row">

                        <h1>
                            <?= htmlspecialchars($product['name']) ?>
                        </h1>

                        <?php if ((int) $product['stock'] > 0): ?>

                            <span class="stock-badge">
                                In Stock
                            </span>

                        <?php else: ?>

                            <span class="stock-badge out-of-stock">
                                Out of Stock
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- Rating -->

                    <div class="product-meta">

                        <div class="product-rating">

                            <div class="stars">

                                <?php

                                for ($i = 1; $i <= 5; $i++) {

                                    if ($averageRating >= $i) {
                                        echo '<i class="fa-solid fa-star"></i>';
                                    } elseif ($averageRating >= ($i - 0.5)) {
                                        echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                    } else {
                                        echo '<i class="fa-regular fa-star"></i>';
                                    }

                                }

                                ?>

                            </div>

                            <span>
                                <?= $reviewCount ?> Review<?= $reviewCount !== 1 ? 's' : '' ?>
                            </span>

                        </div>

                        <span class="meta-divider">•</span>

                        <span>
                            SKU:
                            <strong>
                                <?= htmlspecialchars($product['sku']) ?>
                            </strong>
                        </span>

                    </div>


                    <!-- Price -->

                    <div class="product-price-row">

                        <?php if ($salePrice < $normalPrice): ?>

                            <span class="old-price">
                                $<?= number_format($normalPrice, 2) ?>
                            </span>

                            <span class="current-price">
                                $<?= number_format($salePrice, 2) ?>
                            </span>

                            <span class="discount-badge">
                                <?= $discountPercent ?>% Off
                            </span>

                        <?php else: ?>

                            <span class="current-price">
                                $<?= number_format($normalPrice, 2) ?>
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- Brand / Share -->

                    <div class="brand-share-row">

                        <div class="brand-area">

                            <span class="brand-label">
                                Brand:
                            </span>

                            <span class="brand-name">
                                <?= htmlspecialchars($product['brand'] ?: 'Ecomart') ?>
                            </span>

                        </div>


                        <div class="share-area">

                            <span>
                                Share item:
                            </span>

                            <a href="#" class="share-facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>

                            <a href="#">
                                <i class="fa-brands fa-twitter"></i>
                            </a>

                            <a href="#">
                                <i class="fa-brands fa-pinterest-p"></i>
                            </a>

                            <a href="#">
                                <i class="fa-brands fa-instagram"></i>
                            </a>

                        </div>

                    </div>


                    <!-- Short description -->

                    <div class="product-short-description">

                        <?= nl2br(
                            htmlspecialchars(
                                $product['short_description'] ?? ''
                            )
                        ) ?>

                    </div>


                    <!-- Cart -->

                    <div class="product-cart-row">

                        <div class="quantity-control">

                            <button
                                type="button"
                                id="quantityMinus"
                            >
                                <i class="fa-solid fa-minus"></i>
                            </button>

                            <input
                                type="number"
                                id="productQuantity"
                                value="1"
                                min="1"
                                max="<?= max(1, (int) $product['stock']) ?>"
                            >

                            <button
                                type="button"
                                id="quantityPlus"
                            >
                                <i class="fa-solid fa-plus"></i>
                            </button>

                        </div>


                        <button
                            type="button"
                            class="single-add-cart"
                            data-product-id="<?= $productId ?>"
                            <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>
                        >

                            Add to Cart

                            <i class="fa-solid fa-bag-shopping"></i>

                        </button>


                        <button
                            type="button"
                            class="single-wishlist"
                            data-product-id="<?= $productId ?>"
                        >

                            <i class="fa-regular fa-heart"></i>

                        </button>

                    </div>


                    <!-- Category -->

                    <div class="product-category-tags">

                        <div>

                            <strong>
                                Category:
                            </strong>

                            <a href="../products.php?category=<?= urlencode($product['category_slug']) ?>">
                                <?= htmlspecialchars($product['category_name']) ?>
                            </a>

                        </div>


                        <?php if (!empty($productTags)): ?>

                            <div>

                                <strong>
                                    Tag:
                                </strong>

                                <?php foreach ($productTags as $tagIndex => $tag): ?>

                                    <a href="../products.php?tag=<?= urlencode($tag['slug']) ?>">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </a>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     PRODUCT TABS
========================================================= -->

<section class="product-tabs-section">

    <div class="container">

        <ul
            class="nav product-tabs"
            id="productTabs"
            role="tablist"
        >

            <li class="nav-item">

                <button
                    class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#description-tab"
                    type="button"
                >
                    Descriptions
                </button>

            </li>

            <li class="nav-item">

                <button
                    class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#information-tab"
                    type="button"
                >
                    Additional Information
                </button>

            </li>

            <li class="nav-item">

                <button
                    class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#feedback-tab"
                    type="button"
                >
                    Customer Feedback
                </button>

            </li>

        </ul>


        <div class="tab-content product-tab-content">

            <!-- =========================================
                 DESCRIPTION
            ========================================== -->

            <div
                class="tab-pane fade show active"
                id="description-tab"
            >

                <div class="row align-items-start">

                    <div class="col-lg-6">

                        <div class="product-description">

                            <?= nl2br(
                                htmlspecialchars(
                                    $product['description'] ?? ''
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="col-lg-6">

                        <div class="product-feature-image">

                            <img
                                src="../assets/images/products/product-video.jpg"
                                alt="Product information"
                            >

                            <button
                                type="button"
                                class="product-video-button"
                            >
                                <i class="fa-solid fa-play"></i>
                            </button>

                        </div>


                        <div class="product-benefits">

                            <div class="benefit-item">

                                <i class="fa-regular fa-percent"></i>

                                <div>

                                    <strong>
                                        <?= $discountPercent ?>% Discount
                                    </strong>

                                    <span>
                                        Save your <?= $discountPercent ?>% money with us
                                    </span>

                                </div>

                            </div>


                            <div class="benefit-item">

                                <i class="fa-solid fa-leaf"></i>

                                <div>

                                    <strong>
                                        100% Organic
                                    </strong>

                                    <span>
                                        100% Organic Vegetables
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 ADDITIONAL INFORMATION
            ========================================== -->

            <div
                class="tab-pane fade"
                id="information-tab"
            >

                <div class="additional-information">

                    <?php if (!empty($productInformation)): ?>

                        <?php foreach ($productInformation as $information): ?>

                            <div class="information-row">

                                <div class="information-name">
                                    <?= htmlspecialchars($information['information_name']) ?>
                                </div>

                                <div class="information-value">
                                    <?= nl2br(
                                        htmlspecialchars(
                                            $information['information_value']
                                        )
                                    ) ?>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="empty-information">
                            No additional information available.
                        </p>

                    <?php endif; ?>

                </div>

            </div>


            <!-- =========================================
                 CUSTOMER FEEDBACK
            ========================================== -->

            <div
                class="tab-pane fade"
                id="feedback-tab"
            >

                <div class="customer-feedback">

                    <div class="feedback-summary">

                        <h3>
                            Customer Feedback
                        </h3>

                        <div class="feedback-rating">

                            <strong>
                                <?= number_format($averageRating, 1) ?>
                            </strong>

                            <div>

                                <div class="stars">

                                    <?php

                                    for ($i = 1; $i <= 5; $i++) {

                                        echo $i <= round($averageRating)
                                            ? '<i class="fa-solid fa-star"></i>'
                                            : '<i class="fa-regular fa-star"></i>';

                                    }

                                    ?>

                                </div>

                                <span>
                                    <?= $reviewCount ?> reviews
                                </span>

                            </div>

                        </div>

                    </div>


                    <?php if (!empty($productReviews)): ?>

                        <div class="review-list">

                            <?php foreach ($productReviews as $review): ?>

                                <?php

                                $reviewerName = trim(
                                    ($review['first_name'] ?? '') . ' ' .
                                    ($review['last_name'] ?? '')
                                );

                                if ($reviewerName === '') {
                                    $reviewerName = 'Customer';
                                }

                                ?>

                                <div class="single-review">

                                    <div class="reviewer-image">

                                        <?php if (!empty($review['profile_image'])): ?>

                                            <img
                                                src="../assets/uploads/users/<?= htmlspecialchars($review['profile_image']) ?>"
                                                alt="<?= htmlspecialchars($reviewerName) ?>"
                                            >

                                        <?php else: ?>

                                            <div class="reviewer-placeholder">
                                                <i class="fa-solid fa-user"></i>
                                            </div>

                                        <?php endif; ?>

                                    </div>


                                    <div class="review-content">

                                        <div class="review-top">

                                            <strong>
                                                <?= htmlspecialchars($reviewerName) ?>
                                            </strong>

                                            <div class="stars">

                                                <?php

                                                for ($i = 1; $i <= 5; $i++) {

                                                    echo $i <= (int) $review['rating']
                                                        ? '<i class="fa-solid fa-star"></i>'
                                                        : '<i class="fa-regular fa-star"></i>';

                                                }

                                                ?>

                                            </div>

                                        </div>

                                        <p>
                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $review['review']
                                                )
                                            ) ?>
                                        </p>

                                        <small>
                                            <?= date(
                                                'M d, Y',
                                                strtotime($review['created_at'])
                                            ) ?>
                                        </small>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <p class="no-reviews">
                            No customer reviews yet.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     RELATED PRODUCTS
========================================================= -->

<section class="related-products-section">

    <div class="container">

        <h2>
            Related Products
        </h2>


        <div class="row g-3">

            <?php foreach ($relatedProducts as $related): ?>

                <?php

                $relatedNormalPrice = (float) $related['price'];

                $relatedSalePrice =
                    !empty($related['discount_price']) &&
                    (float) $related['discount_price'] > 0
                        ? (float) $related['discount_price']
                        : $relatedNormalPrice;

                ?>

                <div class="col-xl-3 col-lg-3 col-md-6 col-6">

                    <div class="related-product-card">

                        <?php if ($relatedSalePrice < $relatedNormalPrice): ?>

                            <span class="related-sale-badge">
                                Sale
                                <?= round(
                                    (($relatedNormalPrice - $relatedSalePrice) /
                                    $relatedNormalPrice) * 100
                                ) ?>%
                            </span>

                        <?php endif; ?>


                        <a
                            href="single.php?slug=<?= urlencode($related['slug']) ?>"
                            class="related-product-image"
                        >

                            <img
                                src="<?= htmlspecialchars(
                                    productImageUrl(
                                        $related['product_image']
                                    )
                                ) ?>"
                                alt="<?= htmlspecialchars($related['name']) ?>"
                            >

                        </a>


                        <div class="related-product-info">

                            <a
                                href="single.php?slug=<?= urlencode($related['slug']) ?>"
                                class="related-product-name"
                            >
                                <?= htmlspecialchars($related['name']) ?>
                            </a>


                            <div class="related-price">

                                <strong>
                                    $<?= number_format($relatedSalePrice, 2) ?>
                                </strong>

                                <?php if ($relatedSalePrice < $relatedNormalPrice): ?>

                                    <del>
                                        $<?= number_format($relatedNormalPrice, 2) ?>
                                    </del>

                                <?php endif; ?>

                            </div>


                            <div class="related-bottom">

                                <div class="stars">

                                    <?php

                                    $relatedRating =
                                        (float) $related['average_rating'];

                                    for ($i = 1; $i <= 5; $i++) {

                                        echo $i <= round($relatedRating)
                                            ? '<i class="fa-solid fa-star"></i>'
                                            : '<i class="fa-regular fa-star"></i>';

                                    }

                                    ?>

                                </div>


                                <button
                                    type="button"
                                    class="related-cart-button"
                                    data-product-id="<?= (int) $related['id'] ?>"
                                >

                                    <i class="fa-solid fa-bag-shopping"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>


<?php

require_once __DIR__ . '/../includes/footer.php';

?>