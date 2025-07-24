<?php

declare(strict_types=1);

use App\Actions\User\ListUserAction;
use App\Models\User;
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\Role;

describe('ListUserAction (Feature)', function () {
    beforeEach(function () {
        User::query()->delete();
        Role::query()->delete();

        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $editorRole = Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $viewerRole = Role::create(['name' => 'Viewer', 'slug' => 'viewer']);

        User::factory()->create([
            'name' => 'Alice Admin',
            'email' => 'alice@example.com',
        ])->assignRole($adminRole);

        User::factory()->create([
            'name' => 'Eddie Editor',
            'email' => 'eddie@example.com',
        ])->assignRole($editorRole);

        User::factory()->create([
            'name' => 'Vera Viewer',
            'email' => 'vera@example.com',
        ])->assignRole($viewerRole);
    });

    it('returns all users without pagination', function () {
        $params = new Fluent([
            'paginated' => false,
        ]);

        $result = app(ListUserAction::class)->execute($params);

        expect($result)->toHaveCount(3);
        expect($result->first())->toHaveKeys(['id', 'name', 'email', 'role']);
    });

    it('returns paginated users', function () {
        $params = new Fluent([
            'paginated' => true,
            'limit' => 2,
        ]);

        $result = app(ListUserAction::class)->execute($params);

        expect($result->total())->toBe(3);
        expect($result->perPage())->toBe(2);
        expect($result->items())->toHaveCount(2);
    });

    it(
        'filters users by search term (name/email/role)',
        function () {
            $params = new Fluent([
                'search' => 'Alice',
                'paginated' => false,
            ]);

            $result = app(ListUserAction::class)->execute($params);

            expect($result)->toHaveCount(1);
            expect($result->first()->name)->toBe('Alice Admin');
        }
    );

    it('orders users by name descending', function () {
        $params = new Fluent([
            'order' => 'desc',
            'column' => 'name',
            'paginated' => false,
        ]);

        $result = app(ListUserAction::class)->execute($params);

        expect($result->first()->name)->toBe('Vera Viewer');
    });

    it('orders users by role ascending', function () {
        $params = new Fluent([
            'order' => 'asc',
            'column' => 'role',
            'paginated' => false,
        ]);

        $result = app(ListUserAction::class)->execute($params);

        expect($result->pluck('role')->values()->all())->toMatchArray([
            'Admin',
            'Editor',
            'Viewer',
        ]);
    });

    it('orders users by setSituation (users.active)', function () {
        $params = new Fluent([
            'order' => 'asc',
            'column' => 'setSituation',
            'paginated' => false,
        ]);

        User::factory()->create(['name' => 'Active User', 'active' => true]);
        User::factory()->create(['name' => 'Inactive User', 'active' => false]);

        $result = app(ListUserAction::class)->execute($params);

        expect($result->first()->active)->toBeFalse();
    });

    it('orders users by email ascending', function () {
        $params = new Fluent([
            'order' => 'asc',
            'column' => 'email',
            'paginated' => false,
        ]);

        User::factory()->create(['name' => 'User B', 'email' => 'beta@example.com']);
        User::factory()->create(['name' => 'User A', 'email' => 'alpha@example.com']);

        $result = app(ListUserAction::class)->execute($params);

        expect($result->first()->email)->toBe('alice@example.com');
    });
})->group('feature', 'user');
