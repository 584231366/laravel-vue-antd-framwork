<?php

namespace App\Http\Controllers\API;

use Response;
use Storage;
use Log;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
class PermissionsController extends Controller
{
	public function index(Request $request){ 
		return $this->list($request);
	}
	public function list(Request $request,$limit=10){ 
		$list = Permission::paginate($limit);
		return response(['data'=>$list]);
	}
	public function show($id){ 
		$check = validator([
			"id"=>$id
		],[
			'id'  =>'required|exists:permissions,id'
		]);
		if($check->passes()){ 
			$data = Permission::where(['id'=>$request->input('id')])->first();
			return response(['data'=>$data]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function store(Request $request){ 
		app()['cache']->forget('spatie.permission.cache');
		$check = validator($request->all(),[ 
			'name'=>'required|unique:permissions,name',
			'guard_name'=>'required|in:api'
		]);
		if($check->passes()){ 
			$data = Permission::create([ 
				'name'=>$request->input('name'),
				'guard_name'=>$request->input('guard_name'),
			]);
			return response(['message'=>"数据已保存！",'data'=>$data]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function update($id,Request $request){ 
		app()['cache']->forget('spatie.permission.cache');
		$request->offsetSet("id",$id);
		$check = validator($request->all(),[ 
			'id'  =>'required|exists:permissions,id',
			'name'=>'required|unique:permissions,name,'.$request->input('id'),
			'guard_name'=>'required|in:api'
		]);
		if($check->passes()){ 
			$data = Permission::where(['id'=>$request->input('id')])
			->update([ 
				'name'=>$request->input('name'),
				'guard_name'=>$request->input('guard_name'),
			]);
			return response(['message'=>"数据已保存！"]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function destory($ids){ 
		$count = Permission::whereIn('id',explode(",",$ids))->delete();
		app()['cache']->forget('spatie.permission.cache');
		return response(['message'=>"已删除".$count."条记录！"]);
	}
}