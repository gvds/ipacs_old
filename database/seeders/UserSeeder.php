<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! \App\User::where('username','gvds')->exists()) {
            DB::table('users')->insert([
                'username' => 'gvds',
                'firstname' => 'Gian',
                'surname' => 'van der Spuy',
                'email' => 'gvds@test.mail',
                'site' => 'SUN',
                'password' => '$2y$10$WKkqjxnKR963QGPJVllPheFXZI33Bs2lfsryRAm0k.kSBXpY6MIoq',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $user = \App\User::find(1);
            $user->syncRoles(['sysadmin']);
        }
        // factory(App\User::class, 5)->create();
    }
}
