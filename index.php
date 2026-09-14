<?php
$page_title = 'Home';

require_once __DIR__ . '/includes/header.php';
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


<?php

require_once __DIR__ . '/includes/footer.php';
?>