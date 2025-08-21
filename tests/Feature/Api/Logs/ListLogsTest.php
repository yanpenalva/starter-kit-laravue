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
        ActivityFactory::new()->count(3)->withCauser($this->user)->create([
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
        ActivityFactory::new()->withCauser($this->user)->create(['description' => 'Special log']);
        ActivityFactory::new()->withCauser($this->user)->create(['description' => 'Other log']);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Special']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.description'))->toBe('Special log');
    });

    it('applies search filter by event in portuguese', function () {
        ActivityFactory::new()->withCauser($this->user)->create(['event' => 'delete', 'description' => 'Deleted']);
        ActivityFactory::new()->withCauser($this->user)->create(['event' => 'create', 'description' => 'Created']);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Excluir']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.event'))->toBe('delete');
    });

    it('applies ordering by description asc', function () {
        ActivityFactory::new()->withCauser($this->user)->create(['description' => 'B log']);
        ActivityFactory::new()->withCauser($this->user)->create(['description' => 'A log']);

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

    it('applies search filter by date (d/m/Y)', function () {
        $activity = ActivityFactory::new()->withCauser($this->user)->create([
            'created_at' => now()->startOfDay(),
            'description' => 'Log date test',
        ]);

        $date = now()->format('d/m/Y');

        $response = $this->getJson(route('activity_logs.list', ['search' => $date]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.id'))->toBe($activity->id);
    });

    it('applies search filter by causer name', function () {
        $user = User::factory()->create(['name' => 'Special Causer']);
        ActivityFactory::new()->withCauser($user)->create();

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Special Causer']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.causer'))->toBe('Special Causer');
    });

    it('applies search filter by subject name', function () {
        $subject = User::factory()->create(['name' => 'Subject X']);
        ActivityFactory::new()->withCauser($this->user)->withSubject($subject)->create();

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Subject X']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.subject'))->toBe('Subject X');
    });

    it('applies ordering by causer desc', function () {
        $userA = User::factory()->create(['name' => 'Ana']);
        $userB = User::factory()->create(['name' => 'Zeca']);
        ActivityFactory::new()->withCauser($userA)->create();
        ActivityFactory::new()->withCauser($userB)->create();

        $response = $this->getJson(route('activity_logs.list', [
            'column' => 'causer',
            'order' => 'desc',
        ]));

        $causers = array_column($response->json('data'), 'causer');
        expect($causers)->toBe(['Zeca', 'Ana']);
    });

    it('returns unpaginated logs when paginated = false', function () {
        ActivityFactory::new()->count(2)->withCauser($this->user)->create();

        $response = $this->getJson(route('activity_logs.list', ['paginated' => false]));

        $response->assertOk();
        $this->assertArrayNotHasKey('links', $response->json());
        $this->assertArrayNotHasKey('meta', $response->json());
    });
})->group('feature', 'logs');
