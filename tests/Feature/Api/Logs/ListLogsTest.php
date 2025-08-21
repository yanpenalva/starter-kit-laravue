<?php

declare(strict_types = 1);

namespace Tests\Feature\Api\Logs;

use App\Enums\PermissionEnum;
use App\Models\User;
use Database\Factories\ActivityFactory;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();
    Permission::findOrCreate(PermissionEnum::ACTIVITY_LOGS_LIST->value);
    $this->user->givePermissionTo(PermissionEnum::ACTIVITY_LOGS_LIST->value);
    $this->actingAs($this->user);
});

describe('List Logs API', function () {
    it('returns paginated logs with default params', function () {
        ActivityFactory::new()->count(3)->create([
            'causer_id' => $this->user->id,
            'log_name' => 'system',
            'event' => 'create',
            'description' => 'Created record',
        ]);

        $response = $this->getJson(route('activity_logs.list'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'logName',
                    'event',
                    'eventPt',
                    'description',
                    'causer',
                    'subjectId',
                    'subject',
                    'properties',
                    'createdAt',
                    'updatedAt',
                    'deletedAt',
                ],
            ],
            'links',
            'meta',
        ]);
    });

    it('applies search filter by description', function () {
        ActivityFactory::new()->create(['description' => 'Special log']);
        ActivityFactory::new()->create(['description' => 'Other log']);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Special']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.description'))->toBe('Special log');
    });

    it('applies search filter by event in portuguese', function () {
        ActivityFactory::new()->create(['event' => 'delete', 'description' => 'Deleted']);
        ActivityFactory::new()->create(['event' => 'create', 'description' => 'Created']);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Excluir']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.event'))->toBe('delete');
    });

    it('applies ordering by description asc', function () {
        ActivityFactory::new()->create(['description' => 'B log']);
        ActivityFactory::new()->create(['description' => 'A log']);

        $response = $this->getJson(route('activity_logs.list', [
            'column' => 'description',
            'order' => 'asc',
        ]));

        $descriptions = array_column($response->json('data'), 'description');
        expect($descriptions)->toBe(['A log', 'B log']);
    });

    it('validates request rules', function () {
        $response = $this->getJson(route('activity_logs.list', ['limit' => 200]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['limit']);
    });
})->group('feature', 'logs');
