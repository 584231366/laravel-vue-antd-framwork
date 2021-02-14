<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function(){
	Route::get('/user', function (Request $request) {
		$user = $request->user();
		$permissions = $user->getAllPermissions()->pluck("name")->toArray();
		return response(["data"=>[
			'name'=> $user->name,
			'email'=> $user->email,
			'mobile'=> $user->mobile,
			'permissions'=>$permissions
		]]);
	});
	Route::put('/user/password', function (Request $request) {
		$user = $request->user();
		$check = validator($request->all(),[ 
			'password'=>'required|min:6|max:255|confirmed',
			'password_confirmation'=>'required|min:6'
		]);
		if($check->passes()){ 
			$user->update([ 
				"password"=>Hash::make($request->input('password')),
			]);
			return response(['message'=>"数据已保存！"]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	});

	Route::group(['prefix'=>'/roles'],function(){
		Route::get('/',['middleware' => ['permission:roles-select|roles-edit'],'uses'=>'\App\Http\Controllers\API\RolesController@index']);
		Route::get('/{id}',['middleware' => ['permission:roles-select|roles-edit'],'uses'=>'\App\Http\Controllers\API\RolesController@index']);
		Route::post('/',['middleware' => ['permission:roles-edit'],'uses'=>'\App\Http\Controllers\API\RolesController@store']);
		Route::put('/{id}',['middleware' => ['permission:roles-edit'],'uses'=>'\App\Http\Controllers\API\RolesController@update']);
		Route::delete('/{ids}',['middleware' => ['permission:roles-edit'],'uses'=>'\App\Http\Controllers\API\RolesController@destory']);

		Route::get('/{id}/permissions',['middleware' => ['permission:roles-select|roles-edit'],'uses'=>'\App\Http\Controllers\API\RolePermissionsController@index']);
		Route::post('/{id}/permissions',['middleware' => ['permission:roles-edit'],'uses'=>'\App\Http\Controllers\API\RolePermissionsController@store']);
	});

	Route::group(['prefix'=>'/permissions'],function(){
		Route::get('/',['middleware' => ['permission:permissions-select|permissions-edit'],'uses'=>'\App\Http\Controllers\API\PermissionsController@index']);
		Route::get('/{id}',['middleware' => ['permission:permissions-select|permissions-edit'],'uses'=>'\App\Http\Controllers\API\PermissionsController@index']);
		Route::post('/',['middleware' => ['permission:permissions-edit'],'uses'=>'\App\Http\Controllers\API\PermissionsController@store']);
		Route::put('/{id}',['middleware' => ['permission:permissions-edit'],'uses'=>'\App\Http\Controllers\API\PermissionsController@update']);
		Route::delete('/{ids}',['middleware' => ['permission:permissions-edit'],'uses'=>'\App\Http\Controllers\API\PermissionsController@destory']);
	});
	Route::group(['prefix'=>'/users'],function(){
		Route::get('/',['middleware' => ['permission:users-select|users-edit'],'uses'=>'\App\Http\Controllers\API\UsersController@index']);
		Route::get('/{id}',['middleware' => ['permission:users-select|users-edit'],'uses'=>'\App\Http\Controllers\API\UsersController@index']);
		Route::post('/',['middleware' => ['permission:users-edit'],'uses'=>'\App\Http\Controllers\API\UsersController@store']);
		Route::put('/{id}',['middleware' => ['permission:users-edit'],'uses'=>'\App\Http\Controllers\API\UsersController@update']);
		Route::delete('/{ids}',['middleware' => ['permission:users-edit'],'uses'=>'\App\Http\Controllers\API\UsersController@destory']);
	});
});