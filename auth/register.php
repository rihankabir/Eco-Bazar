<?php

$page_title = 'Create Account';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-6">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4">
                        Create Account
                    </h2>

                    <form method="POST" action="">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="first_name" class="form-label">
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="first_name"
                                    id="first_name"
                                    class="form-control"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="last_name" class="form-label">
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    name="last_name"
                                    id="last_name"
                                    class="form-control"
                                    required
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label for="email" class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label for="confirm_password" class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                id="confirm_password"
                                class="form-control"
                                required
                            >

                        </div>

                        <button
                            type="submit"
                            name="register"
                            class="btn btn-primary w-100"
                        >
                            Create Account
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">

                        Already have an account?

                        <a href="/ecommerce/auth/login.php">
                            Sign In
                        </a>

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>