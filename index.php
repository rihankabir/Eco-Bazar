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

        
        <a href="#" class="btn category-btn">View All<i class="bi bi-arrow-right"></i></a>
        

    </div>


    <div class="row g-4">

        <?php if (!empty($categories)): ?>

            <?php foreach ($categories as $category): ?>

                <div class="col-6 col-md-4 col-lg-3">

                    <a
                        href="/ecommerce/products.php?category=<?= urlencode($category['slug']); ?>"
                        class="text-decoration-none text-dark"
                    >

                        <div class="card h-100 text-center">

                            <?php if (!empty($category['image'])): ?>

                                <img
                                    src="/Ec/assets/uploads/categories/<?= e($category['image']); ?>"
                                    class="card-img-top"
                                    alt="<?= e($category['name']); ?>"
                                >

                            <?php endif; ?>

                            <div class="card-body">

                                <h5 class="card-title mb-0">
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


<?php

require_once __DIR__ . '/includes/footer.php';
?>