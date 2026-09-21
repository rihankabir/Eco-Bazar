<!-- =========================================
     FOOTER
========================================= -->

<footer class="site-footer">


    <!-- =====================================
         NEWSLETTER SECTION
         Newsletter belongs to footer
    ====================================== -->

    <section class="footer-newsletter">

        <div class="container">

            <div class="row align-items-center">


                <!-- =================================
                     NEWSLETTER TEXT
                ================================== -->

                <div class="col-lg-4 col-md-5">

                    <div class="footer-newsletter-content">

                        <h2>
                            Subscribe to our Newsletter
                        </h2>

                        <p>
                            Subscribe to our newsletter and get updates
                            about our latest products, offers and news.
                        </p>

                    </div>

                </div>


                <!-- =================================
                     NEWSLETTER FORM
                ================================== -->

                <div class="col-lg-5 col-md-5">

                    <form
                        action="#"
                        method="post"
                        class="footer-newsletter-form"
                    >

                        <div class="footer-subscribe-box">


                            <!-- Email Icon -->

                            <i
                                class="bi bi-envelope footer-email-icon"
                            ></i>


                            <!-- Email Input -->

                            <input
                                type="email"
                                name="email"
                                class="footer-email-input"
                                placeholder="Your email address"
                                required
                            >


                            <!-- Subscribe Button -->

                            <button
                                type="submit"
                                class="footer-subscribe-button"
                            >
                                Subscribe
                            </button>

                        </div>

                    </form>

                </div>


                <!-- =================================
                     SOCIAL ICONS
                ================================== -->

                <div class="col-lg-3 col-md-2">

                    <div class="footer-social">


                        <!-- Facebook -->

                        <a
                            href="#"
                            class="footer-social-link"
                            aria-label="Facebook"
                        >
                            <i class="bi bi-facebook"></i>
                        </a>


                        <!-- Twitter -->

                        <a
                            href="#"
                            class="footer-social-link"
                            aria-label="Twitter"
                        >
                            <i class="bi bi-twitter-x"></i>
                        </a>


                        <!-- Pinterest -->

                        <a
                            href="#"
                            class="footer-social-link"
                            aria-label="Pinterest"
                        >
                            <i class="bi bi-pinterest"></i>
                        </a>


                        <!-- Instagram -->

                        <a
                            href="#"
                            class="footer-social-link"
                            aria-label="Instagram"
                        >
                            <i class="bi bi-instagram"></i>
                        </a>


                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =====================================
         DARK FOOTER MAIN
    ====================================== -->

    <section class="footer-main">

        <div class="container">

            <div class="row">


                <!-- =================================
                     ECOMART INFORMATION
                ================================== -->

                <div class="col-lg-4 col-md-12">

                    <div class="footer-about">


                        <!-- Logo -->

                        <a
                            href="/Ecomart/"
                            class="footer-logo"
                        >
                            <?= e(APP_NAME); ?>
                        </a>


                        <!-- Description -->

                        <p class="footer-description">

                           

                        </p>


                        <!-- Contact -->

                        <div class="footer-contact">


                            <div class="footer-contact-item">

                                <strong>
                                    +880 0000-000000
                                </strong>

                            </div>


                            <div class="footer-contact-or">
                                or
                            </div>


                            <div class="footer-contact-item">

                                <strong>
                                    info@ecomart.com
                                </strong>

                            </div>


                        </div>


                    </div>

                </div>



                <!-- =================================
                     MY ACCOUNT
                ================================== -->

                <div class="col-lg-2 col-md-3 col-6">

                    <div class="footer-widget">

                        <h3>
                            My Account
                        </h3>


                        <ul>

                            <li>
                                <a href="/Ecomart/account/">
                                    My Account
                                </a>
                            </li>

                            <li>
                                <a
                                    href="/Ecomart/account/index.php?tab=orders"
                                >
                                    Order History
                                </a>
                            </li>

                            <li>
                                <a href="/Ecomart/cart.php">
                                    Shopping Cart
                                </a>
                            </li>

                            <li>
                                <a
                                    href="/Ecomart/account/index.php?tab=wishlist"
                                >
                                    Wishlist
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>



                <!-- =================================
                     HELPS
                ================================== -->

                <div class="col-lg-2 col-md-3 col-6">

                    <div class="footer-widget">

                        <h3>
                            Helps
                        </h3>


                        <ul>

                            <li>
                                <a href="/Ecomart/contact.php">
                                    Contact
                                </a>
                            </li>

                            <li>
                                <a href="#">
                                    Faqs
                                </a>
                            </li>

                            <li>
                                <a href="#">
                                    Terms &amp; Conditions
                                </a>
                            </li>

                            <li>
                                <a href="#">
                                    Privacy Policy
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>



                <!-- =================================
                     PROXY
                ================================== -->

                <div class="col-lg-2 col-md-3 col-6">

                    <div class="footer-widget">

                        <h3>
                            Proxy
                        </h3>


                        <ul>

                            <li>
                                <a href="#">
                                    About
                                </a>
                            </li>

                            <li>
                                <a href="/Ecomart/products.php">
                                    Shop
                                </a>
                            </li>

                            <li>
                                <a href="/Ecomart/product/single.php">
                                    Product
                                </a>
                            </li>

                            <li>
                                <a href="#">
                                    Track Order
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>



                <!-- =================================
                     CATEGORIES
                ================================== -->

                <div class="col-lg-2 col-md-3 col-6">

                    <div class="footer-widget">

                        <h3>
                            Categories
                        </h3>


                        <ul>

                            <li>
                                <a
                                    href="/Ecomart/products.php?category=fresh%20fruit"
                                >
                                    Fruits &amp; Vegetables
                                </a>
                            </li>

                            <li>
                                <a
                                    href="/Ecomart/products.php?category=meat-fish"
                                >
                                    Meats &amp; Fish
                                </a>
                            </li>

                            <li>
                                <a
                                    href="/Ecomart/products.php?category=bread-bakery"
                                >
                                    Bread &amp; Bakery
                                </a>
                            </li>

                            <li>
                                <a
                                    href="/Ecomart/products.php?category=beauty-health"
                                >
                                    Beauty &amp; Health
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>


            </div>

        </div>

    </section>



    <!-- =====================================
         FOOTER BOTTOM
    ====================================== -->

    <section class="footer-bottom">

        <div class="container">

            <div class="row align-items-center">


                <!-- Copyright -->

                <div class="col-md-6">

                    <p class="footer-copyright">

                        <?= e(APP_NAME); ?> eCommerce
                        &copy; <?= date('Y'); ?>.
                        All Rights Reserved.

                    </p>

                </div>


                <!-- Payment Methods -->

                <div class="col-md-6">

                    <div class="footer-payment">


                        <div class="footer-payment-icons">

                            <span class="payment-card">
                                <img
                    src="/Ecomart/assets/images/payment/applepay.png"
                    alt="Ecomart apple pay"
                >
                            </span>

                            <span class="payment-card">
                                <img
                    src="/Ecomart/assets/images/payment/visapay.png"
                    alt="Ecomart visapay"
                >
                            </span>

                            <span class="payment-card">
                                <img
                    src="/Ecomart/assets/images/payment/discoverpay.png"
                    alt="Ecomart discoverpay"
                >
                            </span>

                            <span class="payment-card">
                                <img
                    src="/Ecomart/assets/images/payment/mastercardpay.png"
                    alt="Ecomart mastercardpay"
                >
                            </span>
<span class="payment-card">
                                <img
                    src="/Ecomart/assets/images/payment/securepay.png"
                    alt="Ecomart secure payment"
                >
                            </span>
                        </div>

                    </div>

                </div>


            </div>

        </div>

    </section>


</footer>



<!-- =========================================
     JAVASCRIPT
========================================= -->




<script src="/Ecomart/assets/js/jquery-3.7.1.min.js"></script>

<script src="/Ecomart/assets/js/bootstrap.bundle.min.js"></script>
<script src="/Ecomart/assets/js/main.js"></script>
<script src="/Ecomart/assets/js/products.js"></script>
<!-- Checkout -->

<script
    src="/Ecomart/assets/js/checkout.js"
></script>


<!-- Countdown -->

<script
    src="/Ecomart/assets/js/countdown.js"
></script>


<!-- Dashboard -->

<script
    src="/Ecomart/assets/js/dashboard.js"
></script>
<!-- Wishlist -->

<script
    src="/Ecomart/assets/js/wishlist.js"
></script>


<!-- Account -->

<script
    src="/Ecomart/assets/js/account.js"
></script>
<!-- Cart -->
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
<script src="/Ecomart/assets/js/testimonials.js"></script>
<script
    src="/Ecomart/assets/js/cart.js"
></script>

</body>

</html>