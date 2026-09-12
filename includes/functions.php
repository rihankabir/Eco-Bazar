<?php

require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}