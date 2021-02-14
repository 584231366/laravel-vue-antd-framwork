<?php

namespace App\Http\Controllers\API;

use Response;
use Storage;
use Log;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
class RolePermissionsController extends Controller
{
	public function index($id){
		$role = Role::where(['id'=>$id])->first();
		if($role){
			$permissions = $role->permissions()->pluck("name")->all();
		}else{
			$permissions = [];
		}
		return response(["data"=>$permissions]);
	}
	public function store($id,Request $request){
		$request->offsetSet("id",$id);
		$rules = [ 
			'id'=>'required|exists:roles,id',
			'permissions'=>'nullable|array'
		];
		$check = validator($request->all(),$rules);
		if($check->passes()){
			$role = Role::where(["id"=>$request->input("id")])->first();
			foreach($role->permissions()->pluck("name")->all() as $p){
				if(!in_array($p, $request->input('permissions'))){
					$role->revokePermissionTo($p);
				}
			}
			foreach ($request->input('permissions') as $p) {
				$role->givePermissionTo($p);
			}
			app()['cache']->forget('spatie.permission.cache');
			return response(['message'=>"数据已保存！"]);
		}else{ 
			return response(['message'=>$check->errors()->first()],400);
		}
	}
}