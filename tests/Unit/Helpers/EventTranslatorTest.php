<?php

declare(strict_types = 1);

namespace Tests\Unit\Helpers;

use App\Helpers\EventTranslator;
use ReflectionClass;

describe('EventTranslator', function () {
    it('translates known events correctly', function (string $input, string $expected) {
        $result = EventTranslator::translateEvent($input);

        expect($result)->toBe($expected);
    })->with([
        ['view', 'Visualizar'],
        ['create', 'Criar'],
        ['update', 'Atualizar'],
        ['delete', 'Excluir'],
    ]);

    it('returns ucfirst of unknown event', function () {
        $result = EventTranslator::translateEvent('custom');

        expect($result)->toBe('Custom');
    });

    it('returns default string when null passed', function () {
        $result = EventTranslator::translateEvent(null);

        expect($result)->toBe('Evento');
    });

    it('returns default string when empty string passed', function () {
        $result = EventTranslator::translateEvent('');

        expect($result)->toBe('Evento');
    });

    it('translates known labels back to keys in translateSearchEvent', function (string $input, ?string $expected) {
        $result = EventTranslator::translateSearchEvent($input);

        expect($result)->toBe($expected);
    })->with([
        ['visualizar', 'view'],
        ['criar', 'create'],
        ['atualizar', 'update'],
        ['excluir', 'delete'],
    ]);

    it('returns null for unknown label in translateSearchEvent', function () {
        $result = EventTranslator::translateSearchEvent('desconhecido');

        expect($result)->toBeNull();
    });

    it('verifies EventTranslator class structure', function () {
        $reflection = new ReflectionClass(EventTranslator::class);

        expect($reflection->hasMethod('translateEvent'))->toBeTrue();
        expect($reflection->hasMethod('translateSearchEvent'))->toBeTrue();

        $translateEventParams = $reflection->getMethod('translateEvent')->getParameters();
        expect($translateEventParams[0]->allowsNull())->toBeTrue();

        $translateSearchEventParams = $reflection->getMethod('translateSearchEvent')->getParameters();
        expect($translateSearchEventParams[0]->getType()->getName())->toBe('string');
    });
})->group('helpers', 'unit');
