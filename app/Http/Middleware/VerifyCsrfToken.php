<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'http://localhost:3005',
        'http://localhost:3005/*',
        'http://127.0.0.1:8005/login',
        'http://127.0.0.1:8005/login/*',
        'http://127.0.0.1:8005/register',
        'http://127.0.0.1:8005/register/*',
        /* BOT */
        'http://127.0.0.1:8005/bot-register',
        'http://127.0.0.1:8005/bot-register/*',
        'http://localhost:3005/bot-campaign-store-by-points',
        'http://localhost:3005//bot-voucher-reward-used',
    ];
}
