<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePolymorphicTypes extends Migration
{
    /**
     * Mapping of old polymorphic types to new simpler values.
     */
    protected $changeMap = [
        'App\\Bookshelf' => 'bookshelf',
        'App\\Book'      => 'book',
        'App\\Chapter'   => 'chapter',
        'App\\Page'      => 'page',
    ];

    /**
     * Mapping of tables and columns that contain polymorphic types.
     */
    protected $columnsByTable = [
        'activities'         => 'entity_type',
        'comments'           => 'entity_type',
        'deletions'          => 'deletable_type',
        'entity_permissions' => 'restrictable_type',
        'favourites'         => 'favouritable_type',
        'joint_permissions'  => 'entity_type',
        'search_terms'       => 'entity_type',
        'tags'               => 'entity_type',
        'views'              => 'viewable_type',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->columnsByTable as $table => $column) {
            foreach ($this->changeMap as $oldVal => $newVal) {
                DB::table($table)
                    ->where([$column => $oldVal])
                    ->update([$column => $newVal]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->columnsByTable as $table => $column) {
            foreach ($this->changeMap as $oldVal => $newVal) {
                DB::table($table)
                    ->where([$column => $newVal])
                    ->update([$column => $oldVal]);
            }
        }
    }
}
