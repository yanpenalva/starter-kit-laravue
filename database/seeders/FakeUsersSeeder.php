<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class FakeUsersSeeder extends Seeder {
    public function run(): void {
        DB::transaction(function () {
            User::factory()
                ->count(15)
                ->create()
                ->each(fn(User $user) => $user->assignRole(RolesEnum::GUEST->label()));
        });
    }
}
