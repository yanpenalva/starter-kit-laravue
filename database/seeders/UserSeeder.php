<?php declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};

class UserSeeder extends Seeder
{
    private $adminPassword;

    private $adminEmail;

    private $adminRole;
    public function __construct()
    {
        $this->adminPassword = config('starterkit.admin.password');
        $this->adminEmail = config('starterkit.admin.email');
        $this->adminRole = config('starterkit.admin.role');
    }

    public function run()
    {
        DB::transaction(function () {
            $this->createAdminUser();
            $this->assignAdminRole();
        });
    }
    private function createAdminUser()
    {
        DB::table('users')->updateOrInsert(
            ['email' => $this->adminEmail],
            [
                'name' => 'Administrador',
                'email' => $this->adminEmail,
                'cpf' => '88251805007',
                'password' => Hash::make($this->adminPassword),
                'active' => StatusEnum::ENABLED,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
    private function assignAdminRole()
    {
        $user = User::where('email', $this->adminEmail)->first();
        $roleId = DB::table('roles')
            ->where('name', $this->adminRole)
            ->value('id');

        if ($user && $roleId) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $user->id, 'role_id' => $roleId],
                [
                    'role_id' => $roleId,
                    'model_type' => 'App\Models\User',
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
