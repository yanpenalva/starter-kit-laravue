<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

// @codeCoverageIgnoreStart
/**
 * BaseResource
 *
 * Class that all resources must extend to get the pagination information
 * and rewrite the collection method.
 *
 * @property mixed $preserveKeys
 */
class BaseResource extends JsonResource
{
    /**
     * Adds pagination information to the response.
     *
     * @param  array<string, mixed>  $paginated
     * @param  array<string, mixed>  $default
     * @return array<string, mixed>
     */
    public function paginationInformation(
        \Illuminate\Http\Request $request,
        array $paginated,
        array $default
    ): array {
        $meta = collect($default['meta']);

        return [
            'pagination' => [
                'page' => $meta->get('current_page'),
                'rowsNumber' => $meta->get('total'),
                'rowsPerPage' => $meta->get('per_page'),
            ],
        ];
    }
    /**
     * Transforms the keys of the resource data based on allowed keys.
     *
     * @param  array<string>  $allowedKeys
     * @return array<string, mixed>
     */
    protected function transformKeys(array $allowedKeys, ?\Illuminate\Http\Request $request = null): array
    {
        $resource = is_array($parentArray = parent::toArray($request)) ? $parentArray : $parentArray->toArray();

        return collect($allowedKeys)->mapWithKeys(function (string $allowedKey) use ($resource) {
            return [Str::camel($allowedKey) => $resource[$allowedKey] ?? null];
        })->toArray();
    }
}

// @codeCoverageIgnoreEnd
