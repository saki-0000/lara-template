<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveHiddenRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove the hidden property from roles
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('hidden');
        });

        // Add column to mark system users
        Schema::table('users', function (Blueprint $table) {
            $table->string('system_name')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('hidden')->default(false);
            $table->index('hidden');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('system_name');
        });
    }
}
