<?php
// Ma CSRF sinh ngau nhien cho tung phien; dung cho form va fetch cung nguon.
declare(strict_types=1);

function csrfToken(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function csrfHopLe(): bool
{
    $gui = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($gui) && hash_equals(csrfToken(), $gui);
}
