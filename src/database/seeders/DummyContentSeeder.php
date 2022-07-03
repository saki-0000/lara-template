<?php

namespace Database\Seeders;

use App\Api\ApiToken;
use App\Auth\Permissions\PermissionService;
use App\Auth\Permissions\RolePermission;
use App\Auth\Role;
use App\Auth\User;
use App\Entities\Models\Bookshelf;
use App\Entities\Models\Chapter;
use App\Entities\Models\Page;
use App\Entities\Tools\SearchIndex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an editor user
        $editorUser = User::factory()->create();
        $editorRole = Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        // Create a viewer user
        $viewerUser = User::factory()->create();
        $role = Role::getRole('viewer');
        $viewerUser->attachRole($role);

        $byData = ['created_by' => $editorUser->id, 'updated_by' => $editorUser->id, 'owned_by' => $editorUser->id];

        \App\Entities\Models\Book::factory()->count(5)->create($byData)
            ->each(function ($book) use ($byData) {
                $chapters = Chapter::factory()->count(3)->create($byData)
                    ->each(function ($chapter) use ($book, $byData) {
                        $pages = Page::factory()->count(3)->make(array_merge($byData, ['book_id' => $book->id]));
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = Page::factory()->count(3)->make($byData);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });

        $largeBook = \App\Entities\Models\Book::factory()->create(array_merge($byData, ['name' => 'Large book' . Str::random(10)]));
        $pages = Page::factory()->count(200)->make($byData);
        $chapters = Chapter::factory()->count(50)->make($byData);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);

        $shelves = Bookshelf::factory()->count(10)->create($byData);
        $largeBook->shelves()->attach($shelves->pluck('id'));

        // Assign API permission to editor role and create an API key
        $apiPermission = RolePermission::getByName('access-api');
        $editorRole->attachPermission($apiPermission);
        // $token = (new ApiToken())->forceFill([
        //     'user_id'    => $editorUser->id,
        //     'name'       => 'Testing API key',
        //     'expires_at' => ApiToken::defaultExpiry(),
        //     'secret'     => Hash::make('password'),
        //     'token_id'   => 'apitoken',
        // ]);
        // $token->save();

        app(PermissionService::class)->buildJointPermissions();
        // app(SearchIndex::class)->indexAllEntities();
    }
}
