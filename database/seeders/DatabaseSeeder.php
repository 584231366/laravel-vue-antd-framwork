<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{

		// $user = \App\Models\User::create([
		// 	"name"=>"管理员",
		// 	"email"=>"admin@qq.com",
		// 	"password"=>Hash::make("123456"),
		// ]);

		$this->call([
			RolesAndPermissionsSeeder::class
		]);
	}
}
