<?php

$page_title = 'Create Account';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        $first_name === '' ||
        $last_name === '' ||
        $email === '' ||
        $password === '' ||
        $confirm_password === ''
    ) {

        $error = 'All fields are required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    } elseif ($password !== $confirm_password) {

        $error = 'Passwords do not match.';

    } elseif (strlen($password) < 8) {

        $error = 'Password must be at least 8 characters.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check existing email
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $existing_user = $stmt->fetch();

        if ($existing_user) {

            $error = 'An account with this email already exists.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | Hash password
            |--------------------------------------------------------------------------
            */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            /*
            |--------------------------------------------------------------------------
            | Create user
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO users
                (
                    first_name,
                    last_name,
                    email,
                    password
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                $hashed_password
            ]);

            $success = 'Account created successfully. You can now sign in.';
        }
    }
}

?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-6">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h2 class="text-center mb-4">
                        Create Account
                    </h2>

                    <?php if ($error !== ''): ?>

                        <div class="alert alert-danger">
                            <?= e($error); ?>
                        </div>

                    <?php endif; ?>

                    <?php if ($success !== ''): ?>

                        <div class="alert alert-success">
                            <?= e($success); ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST" action="">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="first_name"
                                    class="form-label"
                                >
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="first_name"
                                    id="first_name"
                                    class="form-control"
                                    value="<?= e($_POST['first_name'] ?? ''); ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label
                                    for="last_name"
                                    class="form-label"
                                >
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    name="last_name"
                                    id="last_name"
                                    class="form-control"
                                    value="<?= e($_POST['last_name'] ?? ''); ?>"
                                    required
                                >

                            </div>

                        </div>

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

                        <div class="mb-3">

                            <label
                                for="confirm_password"
                                class="form-label"
                            >
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
                            class="btn btn-primary w-100"
                        >
                            Create Account
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">

                        Already have an account?

                        <a href="../../Ecomart/auth/login.php">
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