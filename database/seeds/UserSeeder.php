<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'gvds',
            'name' => 'Gian',
            'surname' => 'van der Spuy',
            'email' => 'gvds@sun.ac.za',
            'password' => Hash::make('*baGGins0!'),
        ]);
        factory(App\User::class, 5)->create();
    }
}
