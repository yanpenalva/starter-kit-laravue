<?php

namespace App\Actions\Log;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Fluent;
use Spatie\Activitylog\Models\Activity;

final readonly class ListLogAction {
    public function execute(Fluent $params): LengthAwarePaginator|Collection {
        $query = Activity::query()->with(['causer', 'subject']);

        $search = $params->get('search');
        if (is_string($search) && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereLike('id', "%{$search}%")
                    ->orWhereLike('log_name', "%{$search}%")
                    ->orWhereLike('description', "%{$search}%")
                    ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        $order = $params->get('order');
        $column = $params->get('column', 'id');

        if (is_string($order)) {
            $query->orderBy(
                match ($column) {
                    'logName' => 'log_name',
                    'description' => 'description',
                    'causer' => 'causer_id',
                    'subject' => 'subject_id',
                    'createdAt' => 'created_at',
                    default => 'id',
                },
                $order
            );
        }

        $paginated = (bool) $params->get('paginated', false);
        $limit = is_numeric($params->get('limit')) ? (int) $params->get('limit') : 10;

        return $paginated ? $query->paginate($limit) : $query->get();
    }
}
