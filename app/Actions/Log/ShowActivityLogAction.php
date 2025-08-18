<?php

declare(strict_types = 1);

namespace App\Actions\Log;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;

final readonly class ShowActivityLogAction
{
    public function execute(int $id): Activity
    {
        $activity = Activity::with(['causer', 'subject'])->find($id);

        throw_if(
            !$activity,
            ModelNotFoundException::class,
            'Registro não encontrado',
            Response::HTTP_NOT_FOUND
        );

        return $activity;
    }
}
