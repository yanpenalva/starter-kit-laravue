<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Log;

use App\Actions\Log\{ListLogAction, ShowLogAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Log\IndexLogRequest;
use App\Http\Resources\LogResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Activitylog\Models\Activity;

final class LogController extends Controller {
    public function index(IndexLogRequest $request): JsonResource {
        $this->authorize('index', Activity::class);

        $logs = app(ListLogAction::class)->execute($request->fluent());

        return LogResource::collection($logs);
    }

    public function show(int $id): JsonResource {
        $log = app(ShowLogAction::class)->execute($id);

        $this->authorize('show', $log);

        return new LogResource($log);
    }
}
