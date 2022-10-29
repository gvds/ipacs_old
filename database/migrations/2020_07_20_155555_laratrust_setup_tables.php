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
        DB::unprepared("INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `restricted`, `created_at`, `updated_at`) VALUES
            (1, 'sysadmin', 'System Administrator', 'This user has full access to all functions', 1, NOW(), NOW()),
            (2, 'admin', 'IPACS Administrator', 'Administer non-system components', 1, NOW(), NOW()),
            (3, 'freezer_admin', 'Freezer Manager', 'Create and manage freezers and definitions', 1, NOW(), NOW())");

        // Create table for storing permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('scope')->default('system');
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        DB::unprepared("INSERT INTO `permissions` (`id`, `name`, `scope`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
            (1, 'manage-freezers', 'system', 'Manage Freezers', 'Create and manage physical freezers', '2020-07-20 17:01:07', '2020-07-21 09:44:57'),
            (2, 'manage-storage', 'system', 'Manage Storage', 'Create and manage virtual freezers', '2020-07-20 17:04:55', '2020-07-21 15:11:40'),
            (3, 'manage-users', 'system', 'Manage Users', 'Create, update & delete users and their permissions', '2020-07-20 17:06:05', '2020-07-21 09:45:46'),
            (4, 'manage-teams', 'project', 'Manage Project Team', 'Add or remove users in project team and assign permissions', '2020-07-21 09:43:23', '2020-07-28 11:40:44'),
            (5, 'manage-subjects', 'project', 'Manage Subjects', NULL, '2020-07-21 14:29:25', '2020-07-22 14:17:01'),
            (6, 'log-samples', 'project', 'Log Samples', 'Log primary and derivative samples', '2020-07-21 14:30:04', '2020-08-13 12:15:01'),
            (7, 'manage-samples', 'project', 'Manage Samples', NULL, '2020-07-21 14:30:28', '2020-07-22 14:16:52'),
            (8, 'administer-project', 'project', 'Administer Project', NULL, '2020-07-21 14:31:06', '2020-08-19 09:32:55'),
            (9, 'store-samples', 'project', 'Store/Retrieve Samples', NULL, '2020-07-21 15:13:28', '2020-07-22 14:17:20'),
            (10, 'register-samples', 'project', 'Register Primary Samples', 'Register (and optionally log) primary samples', '2020-08-13 12:13:55', '2020-08-17 11:46:14'),
            (11, 'manage-projects', 'system', 'Manage Projects', 'Create and manage projects', '2020-09-01 11:50:24', '2020-09-01 11:53:22'),
            (12, 'manage-datafiles', 'project', 'Manage Data-Files', 'Upload and manage project data files', '2020-09-02 12:40:51', '2020-09-02 12:40:51'),
            (13, 'monitor-progress', 'project', 'Monitor Progress', 'Access progress reporting', '2020-11-05 06:12:18', '2020-11-05 06:12:18');");

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
        DB::unprepared("INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
            (1, 1),
            (1, 3),
            (2, 1),
            (2, 2),
            (2, 3),
            (3, 1),
            (3, 2),
            (11, 1),
            (11, 2);");
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
