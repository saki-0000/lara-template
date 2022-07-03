<?php

namespace Tests\Permissions;

use App\Actions\ActivityType;
use App\Actions\Comment;
use App\Auth\Role;
use App\Auth\User;
use App\Entities\Models\Book;
use App\Entities\Models\Bookshelf;
use App\Entities\Models\Chapter;
use App\Entities\Models\Entity;
use App\Entities\Models\Page;
use App\Uploads\Image;
use Tests\TestCase;
use Tests\TestResponse;

class RolesTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->getViewer();
    }

    public function test_admin_can_see_settings()
    {
        $this->asAdmin()->get('/settings/features')->assertSee('Settings');
    }

    // public function test_cannot_delete_admin_role()
    // {
    //     $adminRole = Role::getRole('admin');
    //     $deletePageUrl = '/settings/roles/delete/' . $adminRole->id;

    //     $this->asAdmin()->get($deletePageUrl);
    //     $this->delete($deletePageUrl)->assertRedirect($deletePageUrl);
    //     $this->get($deletePageUrl)->assertSee('cannot be deleted');
    // }

    // public function test_role_cannot_be_deleted_if_default()
    // {
    //     $newRole = $this->createNewRole();
    //     $this->setSettings(['registration-role' => $newRole->id]);

    //     $deletePageUrl = '/settings/roles/delete/' . $newRole->id;
    //     $this->asAdmin()->get($deletePageUrl);
    //     $this->delete($deletePageUrl)->assertRedirect($deletePageUrl);
    //     $this->get($deletePageUrl)->assertSee('cannot be deleted');
    // }

    public function test_role_create_update_delete_flow()
    {
        $testRoleName = 'Test Role';
        $testRoleDesc = 'a little test description';
        $testRoleUpdateName = 'An Super Updated role';

        // Creation
        $resp = $this->asAdmin()->get('/settings/features');
        $resp->assertElementContains('a[href="' . url('/settings/roles') . '"]', 'Roles');

        $resp = $this->get('/settings/roles');
        $resp->assertElementContains('a[href="' . url('/settings/roles/new') . '"]', 'Create New Role');

        $resp = $this->get('/settings/roles/new');
        $resp->assertElementContains('form[action="' . url('/settings/roles/new') . '"]', 'Save Role');

        $resp = $this->post('/settings/roles/new', [
            'display_name' => $testRoleName,
            'description'  => $testRoleDesc,
        ]);
        $resp->assertRedirect('/settings/roles');

        $resp = $this->get('/settings/roles');
        $resp->assertSee($testRoleName);
        $resp->assertSee($testRoleDesc);
        $this->assertDatabaseHas('roles', [
            'display_name' => $testRoleName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => false,
        ]);

        /** @var Role $role */
        $role = Role::query()->where('display_name', '=', $testRoleName)->first();

        // Updating
        $resp = $this->get('/settings/roles/' . $role->id);
        $resp->assertSee($testRoleName);
        $resp->assertSee($testRoleDesc);
        $resp->assertElementContains('form[action="' . url('/settings/roles/' . $role->id) . '"]', 'Save Role');

        $resp = $this->put('/settings/roles/' . $role->id, [
            'display_name' => $testRoleUpdateName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => 'true',
        ]);
        $resp->assertRedirect('/settings/roles');
        $this->assertDatabaseHas('roles', [
            'display_name' => $testRoleUpdateName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => true,
        ]);

        // Deleting
        $resp = $this->get('/settings/roles/' . $role->id);
        $resp->assertElementContains('a[href="' . url("/settings/roles/delete/$role->id") . '"]', 'Delete Role');

        $resp = $this->get("/settings/roles/delete/$role->id");
        $resp->assertSee($testRoleUpdateName);
        $resp->assertElementContains('form[action="' . url("/settings/roles/delete/$role->id") . '"]', 'Confirm');

        $resp = $this->delete("/settings/roles/delete/$role->id");
        $resp->assertRedirect('/settings/roles');
        $this->get('/settings/roles')->assertSee('Role successfully deleted');
        $this->assertActivityExists(ActivityType::ROLE_DELETE);
    }

    /**
     * Check a standard entity access permission.
     */
    private function checkAccessPermission(string $permission, array $accessUrls = [], array $visibles = [])
    {
        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->get($url)
                ->assertElementNotContains('.action-buttons', $text);
        }

        $this->giveUserPermissions($this->user, [$permission]);

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertOk();
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->get($url)->assertSee($text);
        }
    }

    public function test_bookshelves_create_all_permissions()
    {
        $this->checkAccessPermission('bookshelf-create-all', [
            '/create-shelf',
        ], [
            '/shelves' => 'New Shelf',
        ]);

        $this->post('/shelves', [
            'name'        => 'test shelf',
            'description' => 'shelf desc',
        ])->assertRedirect('/shelves/test-shelf');
    }

    // テストデータ（前提条件）がよくわからず一旦保留
    // public function test_bookshelves_create_all_permissions()
    // {
    //     $this->checkAccessPermission('bookshelf-create-all', [
    //         '/create-shelf',
    //     ], [
    //         '/shelves' => 'New Shelf',
    //     ]);

    //     $this->post('/shelves', [
    //         'name'        => 'test shelf',
    //         'description' => 'shelf desc',
    //     ])->assertRedirect('/shelves/test-shelf');
    // }

    // テストデータ（前提条件）がよくわからず一旦保留
    // public function test_bookshelves_edit_own_permission()
    // {
    //     /** @var Bookshelf $otherShelf */
    //     $otherShelf = Bookshelf::query()->first();
    //     $ownShelf = $this->newShelf(['name' => 'test-shelf', 'slug' => 'test-shelf']);
    //     $ownShelf->forceFill(['owned_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
    //     $this->regenEntityPermissions($ownShelf);

    //     $this->checkAccessPermission('bookshelf-update-own', [
    //         $ownShelf->getUrl('/edit'),
    //     ], [
    //         $ownShelf->getUrl() => 'Edit',
    //     ]);

    //     $this->get($otherShelf->getUrl())->assertElementNotContains('.action-buttons', 'Edit');
    //     $this->get($otherShelf->getUrl('/edit'))->assertRedirect('/');
    // }

    // テストデータ（前提条件）がよくわからず一旦保留
    // public function test_bookshelves_edit_all_permission()
    // {
    //     /** @var Bookshelf $otherShelf */
    //     $otherShelf = Bookshelf::query()->first();
    //     $this->checkAccessPermission('bookshelf-update-all', [
    //         $otherShelf->getUrl('/edit'),
    //     ], [
    //         $otherShelf->getUrl() => 'Edit',
    //     ]);
    // }
}
