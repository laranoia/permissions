<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaranoiaPermissionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permissions.tables');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permissions.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['abilities'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique()->index();
            $table->string('display_name')->unique();
            $table->bigInteger('priority')->unique();
            $table->timestamps();
        });

        Schema::create($tableNames['roles_abilities'], function (Blueprint $table) use ($tableNames) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id')->index();
            $table->unsignedBigInteger('ability_id')->index();

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->foreign('ability_id')
                ->references('id')
                ->on($tableNames['abilities'])
                ->onDelete('cascade');

            $table->unique(['role_id', 'ability_id']);
        });

        Schema::create($tableNames['permission_types'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->unique()->index();
            $table->timestamps();
        });

        Schema::create($tableNames['permissions'], function (Blueprint $table) use ($tableNames) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_type_id');
            $table->unsignedBigInteger('granted_for_id');
            $table->timestampTz('valid_from')->nullable();
            $table->timestampTz('valid_until')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on($tableNames['users']);

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['permissions']);

            $table->foreign('permission_type_id')
                ->references('id')
                ->on($tableNames['permission_types']);

            $table->foreign('granted_for_id')
                ->references('id')
                ->on($tableNames['abilities']);
        });

        if(Schema::hasTable($tableNames['users'])){
            Schema::table($tableNames['users'], function (Blueprint $table) {
                $table->text('last_role')->nullable();
                $table->timestampTz('last_login')->nullable();
                $table->timestampTz('valid_from')->nullable();
                $table->timestampTz('valid_until')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permissions.tables');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permissions.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['abilities']);
        Schema::drop($tableNames['roles_abilities']);
        Schema::drop($tableNames['permission_types']);
        Schema::drop($tableNames['permissions']);
    }
}