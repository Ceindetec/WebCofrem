<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $userId = DB::table('users')->insertGetId([
            'name' => 'cofremadmin',
            'email' => 'cofremadmin@cofrem.com.co',
            'password' => bcrypt('123456789'),
        ]);

        $roleId = DB::table('roles')->insertGetId([
            "name" => "Administrador",
            "slug" => "admin",
            "Description" => "permite modificar crear y editar usuario, crear nuevos tipos de vinculacion y manipular roles"
        ]);

        DB::table('role_user')->insert([
            "role_id"=>$roleId,
            "user_id"=>$userId
        ]);
    }
}
