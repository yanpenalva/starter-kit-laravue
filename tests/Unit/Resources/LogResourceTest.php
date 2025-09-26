<?php

declare(strict_types = 1);

namespace Tests\Unit\Resources;

use App\Helpers\EventTranslator;
use App\Http\Resources\LogResource;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

describe('LogResource', function () {
    it('transforms activity with causer and subject correctly', function () {
        $causer = new User();
        $causer->id = 1;
        $causer->name = 'Causer Name';

        $subject = new User();
        $subject->id = 2;
        $subject->name = 'Subject Name';

        $activity = new Activity([
            'id' => 10,
            'log_name' => 'system',
            'event' => 'create',
            'description' => 'Created something',
            'causer_id' => $causer->id,
            'subject_id' => $subject->id,
            'properties' => ['attributes' => ['foo' => 'bar']],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $activity->setRelation('causer', $causer);
        $activity->setRelation('subject', $subject);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['id'])->toBe(10)
            ->and($resource['logName'])->toBe('system')
            ->and($resource['event'])->toBe('create')
            ->and($resource['eventPt'])->toBe(EventTranslator::translateEvent('create'))
            ->and($resource['description'])->toBe('Created something')
            ->and($resource['causer'])->toBe('Causer Name')
            ->and($resource['subject'])->toBe('Subject Name')
            ->and($resource['properties'])->toBe(['attributes' => ['foo' => 'bar']])
            ->and($resource['createdAt'])->not->toBeNull()
            ->and($resource['updatedAt'])->not->toBeNull()
            ->and($resource['deletedAt'])->toBeNull();
    });

    it('falls back to causer email when name is null', function () {
        $causer = new User();
        $causer->id = 1;
        $causer->email = 'test@example.com';

        $activity = new Activity([
            'causer_id' => $causer->id,
            'properties' => [],
        ]);
        $activity->setRelation('causer', $causer);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['causer'])->toBe('test@example.com');
    });

    it('returns null causer when no causer exists', function () {
        $activity = new Activity(['causer_id' => null]);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['causer'])->toBeNull();
    });

    it('falls back to subject title when name is null', function () {
        $subject = new User();
        $subject->id = 2;
        $subject->name = null;
        $subject->title = 'Custom Title';

        $activity = new Activity(['subject_id' => $subject->id]);
        $activity->setRelation('subject', $subject);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['subject'])->toBe('Custom Title');
    });

    it('falls back to properties name when subject is null', function () {
        $activity = new Activity([
            'subject_id' => null,
            'properties' => [
                'attributes' => ['name' => 'Prop Subject'],
            ],
        ]);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['subject'])->toBe('Prop Subject');
    });

    it('falls back to properties before.name when subject and attributes.name are missing', function () {
        $activity = new Activity([
            'properties' => [
                'attributes' => [
                    'before' => ['name' => 'Old Name'],
                ],
            ],
        ]);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['subject'])->toBe('Old Name');
    });

    it('handles string properties (json encoded)', function () {
        $jsonProps = json_encode(['attributes' => ['foo' => 'bar']]);

        $activity = new Activity([
            'properties' => collect(json_decode($jsonProps, true)),
        ]);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['properties'])->toBe(['attributes' => ['foo' => 'bar']]);
    });

    it('handles properties as Collection', function () {
        $props = new Collection(['attributes' => ['foo' => 'baz']]);

        $activity = new Activity(['properties' => $props]);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['properties'])->toBe(['attributes' => ['foo' => 'baz']]);
    });

    it('returns deletedAt from before.deleted_at in properties', function () {
        $activity = new Activity([
            'properties' => [
                'attributes' => [
                    'before' => ['deleted_at' => '2025-08-20 10:00:00'],
                ],
            ],
        ]);

        $resource = (new LogResource($activity))->toArray(request());

        expect($resource['deletedAt'])->toBe('2025-08-20 10:00:00');
    });
})->group('unit', 'resources');
