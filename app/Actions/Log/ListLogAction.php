<?php

declare(strict_types=1);

namespace App\Actions\Log;

use App\Helpers\EventTranslator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Fluent;
use Spatie\Activitylog\Models\Activity;

final readonly class ListLogAction {
    /**
     * @param Fluent<string, mixed> $params
     * @return LengthAwarePaginator<int, Activity>|Collection<int, Activity>
     */
    public function execute(Fluent $params): LengthAwarePaginator|Collection {
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->selectRaw($this->subjectFallbackSelect())
            ->leftJoin('users as causer_users', 'activity_log.causer_id', '=', 'causer_users.id')
            ->leftJoin('users as subject_users', 'activity_log.subject_id', '=', 'subject_users.id');

        /** @var string|null $search */
        $search = $params->get('search');

        $query->when(
            is_string($search) && $search !== '',
            function ($query) use ($search) {
                $eventSearch = EventTranslator::translateSearchEvent((string) $search);

                $query->when(
                    $eventSearch !== null,
                    fn($query) => $query->where('activity_log.event', $eventSearch)
                );

                $query->when(
                    $eventSearch === null,
                    function ($query) use ($search) {
                        $query->where(function ($query) use ($search) {
                            $like = "%{$search}%";

                            $query->where('activity_log.description', 'ILIKE', $like)
                                ->orWhere('causer_users.name', 'ILIKE', $like)
                                ->orWhere('subject_users.name', 'ILIKE', $like)
                                ->orWhereRaw("(activity_log.properties->'attributes'->>'name') ILIKE ?", [$like])
                                ->orWhereRaw("(activity_log.properties->'attributes'->'before'->>'name') ILIKE ?", [$like]);

                            $date = \DateTime::createFromFormat('d/m/Y', (string) $search) ?: null;

                            $query->when(
                                $date instanceof \DateTimeInterface,
                                fn($query) => $query->orWhereDate('activity_log.created_at', $date?->format('Y-m-d'))
                            );
                        });
                    }
                );
            }
        );

        /** @var string|null $order */
        $order = $params->get('order');
        $order = is_string($order) ? $order : 'desc';

        $column = is_string($params->get('column')) ? $params->get('column') : 'createdAt';

        $query->orderBy(
            match ($column) {
                'description' => 'activity_log.description',
                'causer'      => 'causer_users.name',
                'subject'     => 'subject_fallback',
                'createdAt'   => 'activity_log.created_at',
                default       => 'activity_log.created_at',
            },
            $order
        );

        $paginated = (bool) $params->get('paginated', false);
        $limit = is_numeric($params->get('limit')) ? (int) $params->get('limit') : 10;

        return $paginated
            ? $query->paginate($limit)
            : $query->get();
    }

    private function subjectFallbackSelect(): string {
        return "
            activity_log.*,
            COALESCE(
                subject_users.name,
                (activity_log.properties->'attributes'->>'name'),
                (activity_log.properties->'attributes'->'before'->>'name')
            ) as subject_fallback
        ";
    }
}
