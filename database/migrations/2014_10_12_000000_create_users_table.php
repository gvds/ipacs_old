<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username',20)->unique();
            $table->string('firstname',50);
            $table->string('surname',50);
            $table->string('email',50)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('site',20);
            $table->rememberToken();
            $table->timestamps();
        });
        DB::unprepared("INSERT INTO `users` (`id`, `username`, `firstname`, `surname`, `email`, `email_verified_at`, `telephone`, `password`, `homesite`, `remember_token`, `created_at`, `updated_at`) VALUES
        (1, 'gvds', 'Gian', 'van der Spuy', 'gvds@sun.ac.za', NULL, '084 553-1355', '$2y$10$GgWB7N7gChIy.Fia0a6r.ugLB/MzyVGh8gq0.acPzfyT6pv2FZga2', 'SU_MBHG', NULL, NOW(), NOW())");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
