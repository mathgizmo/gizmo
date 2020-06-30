<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App\User::count() < 1) {
             DB::table('users')->insert([
                'name' => 'admin',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
                'role' => 'superadmin'
            ]);
        }
    }
}
