<?php

namespace Tests\Feature;

use App\Actions\ActivityType;
use App\Auth\Role;
use App\Auth\User;
use Tests\TestCase;

class UsersApiTest extends TestCase
{
    use TestsFeature;

    protected $baseEndpoint = '/api/users';

    protected $endpointMap = [
        ['get', '/api/users'],
        ['post', '/api/users'],
        ['get', '/api/users/1'],
        ['put', '/api/users/1'],
        ['delete', '/api/users/1'],
    ];

    public function test_create_endpoint()
    {
        // $this->withoutExceptionHandling();
        $this->actingAsApiAdmin();
        /** @var Role $role */
        $role = Role::query()->first();

        $resp = $this->postJson($this->baseEndpoint, [
            'name'        => 'Benny Boris',
            'email'       => 'bboris@example.com',
            'password'    => 'mysuperpass',
            'language'    => 'it',
            'roles'       => [$role->id],
            'send_invite' => false,
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'name'             => 'Benny Boris',
            'email'            => 'bboris@example.com',
            'external_auth_id' => '',
            'roles'            => [
                [
                    'id'           => $role->id,
                    'display_name' => $role->display_name,
                ],
            ],
        ]);
        $this->assertDatabaseHas('users', ['email' => 'bboris@example.com']);

        /** @var User $user */
        $user = User::query()->where('email', '=', 'bboris@example.com')->first();
        // $this->assertActivityExists(ActivityType::USER_CREATE, null, $user->logDescriptor());
        $this->assertActivityExists(ActivityType::USER_CREATE, null);
        $this->assertEquals(1, $user->roles()->count());
        $this->assertEquals('it', setting()->getUser($user, 'language'));
    }
}
