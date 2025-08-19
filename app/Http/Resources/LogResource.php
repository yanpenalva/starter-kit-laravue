<?php

declare(strict_types=1);

namespace App\Http\Resources;

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

        $causer = $activity->causer ? [
            'id' => $activity->causer->getKey(),
            'type' => class_basename($activity->causer_type),
            'name' => $activity->causer->name ?? ($activity->causer->email ?? null),
            'email' => $activity->causer->email ?? null,
        ] : null;

        $subject = $activity->subject ? [
            'id' => $activity->subject->getKey(),
            'type' => class_basename($activity->subject_type),
            'name' => $activity->subject->name ?? ($activity->subject->title ?? null),
        ] : null;

        $props = $activity->properties instanceof Collection
            ? $activity->properties->toArray()
            : (is_string($activity->properties) ? json_decode($activity->properties, true) ?? [] : (array) $activity->properties);

        return [
            'id' => $activity->getKey(),
            'logName' => $activity->log_name,
            'event' => $activity->event ?? null,
            'eventPt' => $this->translateEvent($activity->event ?? null),
            'description' => $activity->description,
            'causer' => $causer,
            'subject' => $subject,
            'properties' => $props,
            'createdAt' => $activity->created_at?->translatedFormat('d/m/Y H\hi\m\i\n'),
            'updatedAt' => $activity->updated_at?->translatedFormat('d/m/Y H\hi\m\i\n'),
        ];
    }

    private function translateEvent(?string $event): string {
        return match ($event) {
            'view' => 'Visualizar',
            'create' => 'Criar',
            'update' => 'Atualizar',
            'delete' => 'Excluir',
            default => ucfirst((string) $event ?: 'Evento'),
        };
    }
}
