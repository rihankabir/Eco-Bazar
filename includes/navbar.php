<nav class="navbar navbar-expand-lg bg-light">

    <div class="container">

        <a class="navbar-brand" href="/Ecomart/">
            <?= e(APP_NAME); ?>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/ecommerce/"
                    >
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/ecommerce/products.php"
                    >
                        Products
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/ecommerce/blog/"
                    >
                        Blog
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="/ecommerce/contact.php"
                    >
                        Contact
                    </a>
                </li>

            </ul>

            <div class="d-flex align-items-center gap-3">

                <a
                    href="/ecommerce/cart.php"
                    class="text-decoration-none"
                >
                    Cart

                    <span class="badge bg-primary">
                        0
                    </span>

                </a>

                <a
                    href="/ecommerce/auth/login.php"
                    class="btn btn-outline-primary"
                >
                    Sign In
                </a>

            </div>

        </div>

    </div>

</nav>