<?php
$page_title = 'Home';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">

    <div class="p-5 mb-4 bg-light rounded-3">

        <div class="container-fluid py-5">

            <h1 class="display-5 fw-bold">
                Welcome to EcoBazar
            </h1>

            <p class="col-md-8 fs-4">
                Your production ecommerce website is working.
            </p>

            <button
                type="button"
                class="btn btn-primary btn-lg"
                id="testButton"
            >
                Test JavaScript
            </button>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        Bootstrap
                    </h5>

                    <p class="card-text">
                        If this card is styled correctly, Bootstrap CSS is working.
                    </p>

                    <span class="badge bg-success">
                        CSS Working
                    </span>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        PHP
                    </h5>

                    <p class="card-text">
                        <?= e(APP_NAME); ?> PHP configuration is working.
                    </p>

                    <span class="badge bg-success">
                        PHP Working
                    </span>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <h5 class="card-title">
                        JavaScript
                    </h5>

                    <p class="card-text" id="jsMessage">
                        Click the button above to test JavaScript.
                    </p>

                    <span class="badge bg-secondary">
                        Waiting
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once __DIR__ . '/includes/footer.php';
?>