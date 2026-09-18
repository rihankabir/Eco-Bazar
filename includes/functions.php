<?php

require_once __DIR__ . '/../config/app.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function json_response(
    bool $success,
    string $message = '',
    array $data = []
) {

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);

    exit;
}