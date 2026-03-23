<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
  protected $listen = [
    Login::class => [
      LogSuccessfulLogin::class,
    ],
  ];

  public function boot(): void
{
    \Illuminate\Support\Facades\URL::forceScheme('https');
    \Illuminate\Support\Facades\Request::setTrustedProxies(
        ['*'],
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
    );
}
}
