<?php

require __DIR__ . '/../vendor/autoload.php';

function laravel_version(): array {
    return explode('.', app()->version());
}
