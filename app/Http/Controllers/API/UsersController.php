<?php

namespace App\Http\Controllers\API;

use Response;
use Storage;
use Log;
use Cache;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UsersController extends Controller
{
	public function index(Request $request){ 
		return $this->list($request,$request->input('limit')?$request->input('limit'):10);
	}
	public function list(Request $request,$limit=10){ 
		$list = User::paginate($limit);
		$list->each(function($item){
			$item->roles = $item->roles()->pluck("name")->all();
		});
		return response(['data'=>$list]);
	}
	public function show($id){ 
		$check = validator([
			"id"=>$id
		],[
			'id'  =>'required|exists:users,id'
		]);
		if($check->passes()){ 
			$data = User::where(['id'=>$request->input('id')])->first();
			return response(['data'=>$data]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function store(Request $request){ 
		$check = validator($request->all(),[ 
			'name'=>'required',
			'email'=>'required|email|unique:users,email',
			'roles'=>'nullable|array',
			'password'=>'required|min:6|max:255|confirmed',
			'password_confirmation'=>'required|min:6'
		]);
		if($check->passes()){ 
			$user = User::create([ 
				'name'=>$request->input('name'),
				'email'=>$request->input('email'),
				"password"=>Hash::make($request->input('password')),
			]);
			$this->assignRoles($user,$request->input('roles'));
			return response(['message'=>"数据已保存！",'data'=>$user]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function update($id,Request $request){ 
		$request->offsetSet("id",$id);
		$rules = [ 
			'name'=>'required',
			'email'=>'required|email|unique:users,email,'.$request->input('id'),
			'roles'=>'nullable|array',
		];
		if ($request->input('password')) {
			$rules['password']='required|min:6|max:255|confirmed';
			$rules['password_confirmation']='required|min:6';
		}
		$check = validator($request->all(),$rules);
		if($check->passes()){ 
			$data = User::where(['id'=>$request->input('id')])
			->update([ 
				'name'=>$request->input('name'),
				'email'=>$request->input('email'),
				"password"=>Hash::make($request->input('password')),
			]);
			if($request->input('password')){
				User::where(['id'=>$request->input('id')])
				->update([
					"password"=>Hash::make($request->input('password')),
				]);
			}
			$this->assignRoles(User::where(['id'=>$request->input('id')])->first(),$request->input('roles'));
			return response(['message'=>"数据已保存！"]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function destory($ids){ 
		$count = User::whereIn('id',explode(",",$ids))->delete();
		return response(['message'=>"已删除".$count."条记录！"]);
	}

	public function assignRoles($user,$roles){
		// 获取所有角色
		foreach($user->roles()->pluck("name")->all() as $role){
			if(!in_array($role, $roles)){
				$user->removeRole($role);
			}
		}
		$user->assignRole($roles);
		app()['cache']->forget('spatie.permission.cache');
	}
}