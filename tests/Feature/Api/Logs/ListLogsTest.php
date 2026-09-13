<?php

declare(strict_types = 1);

namespace Tests\Feature\Api\Logs;

use App\Enums\PermissionEnum;
use App\Models\User;
use Database\Factories\ActivityFactory;
use Spatie\Permission\Models\Permission;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    Permission::findOrCreate(PermissionEnum::ACTIVITY_LOGS_LIST->value);
    $this->user->givePermissionTo(PermissionEnum::ACTIVITY_LOGS_LIST->value);

    $this->actingAs($this->user);
});

describe('List Logs API', function () {
    it('returns paginated logs with default params', function (): void {
        ActivityFactory::new()
            ->count(3)
            ->withCauser($this->user)
            ->create([
                'log_name' => 'system',
                'event' => 'create',
                'description' => 'Created record',
                'created_at' => now()->subDays(5),
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

    it('applies search filter by description', function (): void {
        ActivityFactory::new()->withCauser($this->user)->create([
            'description' => 'Special log',
            'created_at' => now(),
        ]);

        ActivityFactory::new()->withCauser($this->user)->create([
            'description' => 'Other log',
            'created_at' => now(),
        ]);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Special']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.description'))->toBe('Special log');
    });

    it('applies search filter by event in portuguese', function (): void {
        ActivityFactory::new()->withCauser($this->user)->create([
            'event' => 'delete',
            'description' => 'Deleted',
            'created_at' => now(),
        ]);

        ActivityFactory::new()->withCauser($this->user)->create([
            'event' => 'create',
            'description' => 'Created',
            'created_at' => now(),
        ]);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Excluir']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.event'))->toBe('delete');
    });

    it('does not return logs older than 30 days', function (): void {
        ActivityFactory::new()
            ->withCauser($this->user)
            ->create([
                'description' => 'Old log',
                'created_at' => now()->subDays(45),
            ]);

        $recent = ActivityFactory::new()
            ->withCauser($this->user)
            ->create([
                'description' => 'Recent log',
                'created_at' => now()->subDays(5),
            ]);

        $response = $this->getJson(route('activity_logs.list'));

        $response->assertOk();
        $response->assertJsonMissing(['description' => 'Old log']);
        $response->assertJsonFragment(['description' => 'Recent log']);
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.id'))->toBe($recent->id);
    });

    it('applies ordering by description asc', function (): void {
        ActivityFactory::new()->withCauser($this->user)->create([
            'description' => 'B log',
            'created_at' => now(),
        ]);

        ActivityFactory::new()->withCauser($this->user)->create([
            'description' => 'A log',
            'created_at' => now(),
        ]);

        $response = $this->getJson(route('activity_logs.list', [
            'column' => 'description',
            'order' => 'asc',
        ]));

        $descriptions = array_column($response->json('data'), 'description');
        expect($descriptions)->toBe(['A log', 'B log']);
    });

    it('validates request rules', function (): void {
        $response = $this->getJson(route('activity_logs.list', ['limit' => 200]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['limit']);
    });

    it('applies search filter by date (d/m/Y)', function (): void {
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

    it('applies search filter by causer name', function (): void {
        $user = User::factory()->create(['name' => 'Special Causer']);
        ActivityFactory::new()->withCauser($user)->create(['created_at' => now()]);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Special Causer']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.causer'))->toBe('Special Causer');
    });

    it('applies search filter by subject name', function (): void {
        $subject = User::factory()->create(['name' => 'Subject X']);
        ActivityFactory::new()
            ->withCauser($this->user)
            ->withSubject($subject)
            ->create(['created_at' => now()]);

        $response = $this->getJson(route('activity_logs.list', ['search' => 'Subject X']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        expect($response->json('data.0.subject'))->toBe('Subject X');
    });

    it('applies ordering by causer desc', function (): void {
        $userA = User::factory()->create(['name' => 'Ana']);
        $userB = User::factory()->create(['name' => 'Zeca']);

        ActivityFactory::new()->withCauser($userA)->create(['created_at' => now()]);
        ActivityFactory::new()->withCauser($userB)->create(['created_at' => now()]);

        $response = $this->getJson(route('activity_logs.list', [
            'column' => 'causer',
            'order' => 'desc',
        ]));

        $causers = array_column($response->json('data'), 'causer');
        expect($causers)->toBe(['Zeca', 'Ana']);
    });

    it('applies ordering by subject asc', function (): void {
        $subjectA = User::factory()->create(['name' => 'Alpha subject']);
        $subjectB = User::factory()->create(['name' => 'Beta subject']);

        ActivityFactory::new()
            ->withCauser($this->user)
            ->withSubject($subjectB)
            ->create(['created_at' => now()]);
        ActivityFactory::new()
            ->withCauser($this->user)
            ->withSubject($subjectA)
            ->create(['created_at' => now()]);

        $response = $this->getJson(route('activity_logs.list', [
            'column' => 'subject',
            'order' => 'asc',
        ]));

        $subjects = array_column($response->json('data'), 'subject');
        expect($subjects)->toBe(['Alpha subject', 'Beta subject']);
    });

    it('returns unpaginated logs when paginated = false', function (): void {
        ActivityFactory::new()
            ->count(2)
            ->withCauser($this->user)
            ->create(['created_at' => now()]);

        $response = $this->getJson(route('activity_logs.list', ['paginated' => false]));

        $response->assertOk();
        $this->assertArrayNotHasKey('links', $response->json());
        $this->assertArrayNotHasKey('meta', $response->json());
    });
})->group('feature', 'logs');
