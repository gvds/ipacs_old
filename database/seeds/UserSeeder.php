<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\facades\DB;

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
                'email' => 'gvds@sun.ac.za',
                'password' => bcrypt('*baGGins0!'),
            ]);
        }
        factory(App\User::class, 5)->create();
    }
}
