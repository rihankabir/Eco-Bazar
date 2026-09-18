<?php

require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? APP_NAME;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= e($page_title); ?>
    </title>

    <link rel="stylesheet" href="/Ecomart/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
>
    <link rel="stylesheet" href="/Ecomart/assets/css/style.css">

</head>

<body>
    <?php require_once __DIR__ . '/navbar.php'; ?>