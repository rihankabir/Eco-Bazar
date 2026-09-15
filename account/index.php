<?php

$page_title = 'My Account';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '../../includes/auth-check.php';

?>

<div class="container py-5">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-md-3 mb-4">

            <div class="list-group">

                <a
                    href="/Ecomart/account/index.php?tab=dashboard"
                    class="list-group-item list-group-item-action active"
                >
                    Dashboard
                </a>

                <a
                    href="/Ecomart/account/index.php?tab=orders"
                    class="list-group-item list-group-item-action"
                >
                    Order History
                </a>

                <a
                    href="/Ecomart/account/index.php?tab=wishlist"
                    class="list-group-item list-group-item-action"
                >
                    Wishlist
                </a>

                <a
                    href="/Ecomart/cart.php"
                    class="list-group-item list-group-item-action"
                >
                    Shopping Cart
                </a>

                <a
                    href="/Ecomart/account/index.php?tab=settings"
                    class="list-group-item list-group-item-action"
                >
                    Settings
                </a>

                <a
                    href="/Ecomart/auth/logout.php"
                    class="list-group-item list-group-item-action text-danger"
                >
                    Logout
                </a>

            </div>

        </div>


        <!-- Main Content -->

        <div class="col-md-9">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h2 class="mb-3">
                        Welcome,
                        <?= e($_SESSION['user_first_name']); ?>!
                    </h2>

                    <p class="text-muted">
                        Welcome to your EcoBazar account dashboard.
                    </p>

                    <div class="row g-4 mt-2">

                        <div class="col-md-4">

                            <div class="border rounded p-4 text-center">

                                <h5>
                                    Orders
                                </h5>

                                <h3>
                                    0
                                </h3>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="border rounded p-4 text-center">

                                <h5>
                                    Wishlist
                                </h5>

                                <h3>
                                    0
                                </h3>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="border rounded p-4 text-center">

                                <h5>
                                    Cart Items
                                </h5>

                                <h3>
                                    0
                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>