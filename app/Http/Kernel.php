<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        // Other middleware entries...
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'canUpdateUser' => \App\Http\Middleware\CanUpdateUser::class,
        // 'cors' => \App\Http\Middleware\Cors::class,
    ];
}
