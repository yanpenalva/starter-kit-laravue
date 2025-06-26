<?php declare(strict_types = 1);

namespace Tests\Feature\Api\Permission;

use App\Models\User;
use Illuminate\Http\Response;

beforeEach(function () {
    $this->userAuth = User::factory()->create();
});

describe('PermissionTest', function () {
    describe('Listing Permissions', function () {
        it('should return a 200 status code and proper JSON structure', function () {
            $this->actingAs($this->userAuth);
            $response = $this->get(route('permissions.list'));
            $response->assertStatus(Response::HTTP_OK);
            $response->assertJsonStructure([
                'data',
            ]);
        });
    });
})->group('PermissionTest');
