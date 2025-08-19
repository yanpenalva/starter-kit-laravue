<?php

declare(strict_types=1);

namespace App\Helpers;

final class EventTranslator {
    private const EVENT_MAP = [
        'view'   => 'Visualizar',
        'create' => 'Criar',
        'update' => 'Atualizar',
        'delete' => 'Excluir',
    ];

    public static function translateEvent(?string $event): string {
        return self::EVENT_MAP[$event] ?? ucfirst((string) $event ?: 'Evento');
    }

    public static function translateSearchEvent(string $search): ?string {
        $normalized = ucfirst(mb_strtolower($search));

        $key = array_search($normalized, self::EVENT_MAP, true);

        return $key ?: null;
    }
}
