<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaratrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('restricted')->default(0);
            $table->timestamps();
        });
        // DB::unprepared("INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `restricted`, `created_at`, `updated_at`) VALUES
        // (1, 'sysadmin', 'System Administrator', 'This user has full access to all functions', 1, NOW(), NOW()),
        // (2, 'admin', NULL, NULL, 0, NOW(), NOW()),
        // (3, 'freezer_admin', NULL, NULL, 0, NOW(), NOW())");

        // Create table for storing permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('scope')->default('system');
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        // DB::unprepared("INSERT INTO `permissions` (`id`, `name`, `scope`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
        // (1, 'manage-freezers', 'system', 'Manage Freezers', 'Create and manage physical freezers', NOW(), NOW()),
        // (2, 'manage-storage', 'system', 'Manage Storage', 'Create and manage virtual freezers', NOW(), NOW()),
        // (3, 'manage-users', 'system', 'Manage Users', 'Create, update & delete users and their permissions', NOW(), NOW()),
        // (4, 'manage-teams', 'project', 'Manage Project Team', 'Add or remove users in project team and assign permissions', NOW(), NOW()),
        // (5, 'manage-subjects', 'project', 'Manage Subjects', NULL, NOW(), NOW()),
        // (6, 'log-samples', 'project', 'Log Samples', NULL, NOW(), NOW()),
        // (7, 'manage-samples', 'project', 'Manage Samples', NULL, NOW(), NOW()),
        // (8, 'administer-projects', 'project', 'Administer Projects', NULL, NOW(), NOW()),
        // (9, 'store-samples', 'project', 'Store/Retrieve Samples', NULL, NOW(), NOW())");

        // Create table for storing teams
        Schema::create('teams', function (Blueprint $table) {
            // $table->unsignedBigInteger('id');
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating roles to users and teams (Many To Many Polymorphic)
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->unsignedBigInteger('team_id')->nullable();

            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['user_id', 'role_id', 'user_type', 'team_id']);
        });

        // Create table for associating permissions to users (Many To Many Polymorphic)
        Schema::create('permission_user', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->unsignedBigInteger('team_id')->nullable();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['user_id', 'permission_id', 'user_type', 'team_id']);
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
        // DB::unprepared("INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
        // (1, 1),
        // (1, 3),
        // (2, 1),
        // (2, 2),
        // (2, 3),
        // (3, 1),
        // (3, 2)");
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('teams');
    }
}
