<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource {
    public function __construct($resource) {
        parent::__construct($resource);
    }
    public function toArray(Request $request): array {
        return is_array(parent::toArray($request))
            ? parent::toArray($request)
            : (array) parent::toArray($request);
    }
}
