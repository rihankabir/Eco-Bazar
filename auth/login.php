<?php

$page_title = 'Sign In';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($email === '' || $password === '') {

        $error = 'Email and password are required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | Find user
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        /*
        |--------------------------------------------------------------------------
        | Check password
        |--------------------------------------------------------------------------
        */

        if (!$user || !password_verify($password, $user['password'])) {

            $error = 'Invalid email or password.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | Create login session
            |--------------------------------------------------------------------------
            */

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];

            /*
            |--------------------------------------------------------------------------
            | Redirect to customer dashboard
            |--------------------------------------------------------------------------
            */

            header('Location: /Ecomart/account/index.php');

            exit;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6 col-lg-5">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4">
                        Sign In
                    </h2>

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= e($error); ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST" action="">

                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="<?= e($_POST['email'] ?? ''); ?>"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label
                                for="password"
                                class="form-label"
                            >
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

                        <div class="text-end mb-3">

                            <a href="/ecommerce/auth/forgot-password.php">
                                Forgot Password?
                            </a>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Sign In
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">

                        Don't have an account?

                        <a href="/Ecomart/auth/register.php">
                            Create Account
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