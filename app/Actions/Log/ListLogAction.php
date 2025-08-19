<?php

declare(strict_types=1);

namespace App\Actions\Log;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Fluent;
use Spatie\Activitylog\Models\Activity;

final readonly class ListLogAction {
    public function execute(Fluent $params): LengthAwarePaginator|Collection {
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->select('activity_log.*')
            ->leftJoin('users as causer_users', 'activity_log.causer_id', '=', 'causer_users.id')
            ->leftJoin('users as subject_users', 'activity_log.subject_id', '=', 'subject_users.id');

        $search = $params->get('search');
        if (is_string($search) && $search !== '') {
            $query->where(function ($query) use ($search) {
                $query->whereLike('activity_log.id', "%{$search}%")
                    ->orWhereLike('activity_log.log_name', "%{$search}%")
                    ->orWhereLike('activity_log.description', "%{$search}%")
                    ->orWhere('activity_log.properties', 'like', "%{$search}%")
                    ->orWhereLike('causer_users.name', "%{$search}%")
                    ->orWhereLike('subject_users.name', "%{$search}%");
            });
        }

        $order = $params->get('order');
        $column = $params->get('column', 'id');

        if (is_string($order)) {
            $query->orderBy(
                match ($column) {
                    'logName'      => 'activity_log.log_name',
                    'description'  => 'activity_log.description',
                    'event'        => 'activity_log.event',
                    'causer'       => 'causer_users.name',
                    'subject'      => 'subject_users.name',
                    'created_at',
                    'createdAt'    => 'activity_log.created_at',
                    default        => 'activity_log.id',
                },
                $order
            );
        }

        $paginated = (bool) $params->get('paginated', false);
        $limit = is_numeric($params->get('limit')) ? (int) $params->get('limit') : 10;

        return $paginated
            ? $query->paginate($limit)
            : $query->get();
    }
}
