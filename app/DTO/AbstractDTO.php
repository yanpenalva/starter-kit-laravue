<?php

declare(strict_types = 1);

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract class AbstractDTO implements Arrayable, JsonSerializable {
    /**
     * @return array<string, mixed>
     */
    final public function toArray(): array {
        $recursiveConversion = function ($item) use (&$recursiveConversion): mixed {
            if (is_object($item)) {
                $item = get_object_vars($item);
            }

            if (is_array($item)) {
                /** @var array<string, mixed> $mapped */
                $mapped = array_map($recursiveConversion, $item);

                return $mapped;
            }

            return $item;
        };

        /** @var array<string, mixed> $result */
        $result = $recursiveConversion($this);

        return $result;
    }

    final public function toJson(int $options = 0): string {
        return (string) json_encode($this->toArray(), $options);
    }

    /**
     * @return array<string, mixed>
     */
    final public function jsonSerialize(): array {
        return $this->toArray();
    }
}
