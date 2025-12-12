
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // Trust all (or specify the IPs of your proxy)
    protected $proxies = '*';

    // Trust standard X-Forwarded-* headers
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
