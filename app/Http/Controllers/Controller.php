<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// @codeCoverageIgnoreStart

class Controller extends BaseController {
    use AuthorizesRequests;
    use ValidatesRequests;
}

// @codeCoverageIgnoreEnd
