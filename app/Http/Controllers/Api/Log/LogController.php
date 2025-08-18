<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Log;

use App\Actions\Log\{ListActivityLogAction, ShowActivityLogAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Log\{IndexActivityLogRequest, ShowActivityLogRequest};
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Activitylog\Models\Activity;

final class LogController extends Controller
{
    public function index(IndexActivityLogRequest $request): JsonResource
    {
        $this->authorize('index', Activity::class);

        $params = $request->validated();
        $fluent = new \Illuminate\Support\Fluent($params);

        $logs = app(ListActivityLogAction::class)->execute($fluent);

        return ActivityLogResource::collection($logs);
    }

    public function show(ShowActivityLogRequest $request, int $id): JsonResource
    {
        $activity = app(ShowActivityLogAction::class)->execute($id);

        $this->authorize('view', $activity);

        return new ActivityLogResource($activity);
    }
}
