<?php

declare(strict_types = 1);

namespace App\Http\Resources;

use App\Helpers\EventTranslator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

final class LogResource extends JsonResource {
    public function __construct(Activity $resource) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array {
        $activity = $this->resource;

        $causerName = $activity->causer
            ? ($activity->causer->name ?? ($activity->causer->email ?? null))
            : null;

        $subjectName = $activity->subject
            ? ($activity->subject->name ?? ($activity->subject->title ?? null))
            : null;

        $props = $activity->properties instanceof Collection
            ? $activity->properties->toArray()
            : (is_string($activity->properties) ? json_decode($activity->properties, true) ?? [] : (array) $activity->properties);

        return [
            'id' => $activity->getKey(),
            'logName' => $activity->log_name,
            'event' => $activity->event ?? null,
            'eventPt' => EventTranslator::translateEvent($activity->event ?? null),
            'description' => $activity->description,
            'causer' => $causerName,
            'subjectId' => $activity->subject_id,
            'subject' => $subjectName
                ?? ($props['attributes']['name'] ?? $props['attributes']['before']['name'] ?? null),
            'properties' => $props,
            'createdAt' => $activity->created_at?->translatedFormat('d/m/Y H\hi\m\i\n'),
            'updatedAt' => $activity->updated_at?->translatedFormat('d/m/Y H\hi\m\i\n'),
            'deletedAt' => $props['attributes']['before']['deleted_at'] ?? null,

        ];
    }
}
