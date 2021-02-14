<?php

namespace App\Http\Controllers\API;

use Response;
use Storage;
use Log;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
class RolesController extends Controller
{
	public function index(Request $request){ 
		return $this->list($request,$request->input('limit')?$request->input('limit'):10);
	}
	public function list(Request $request,$limit=10){ 
		$list = Role::paginate($limit);
		return response(['data'=>$list]);
	}
	public function show($id){ 
		$check = validator([
			"id"=>$id
		],[
			'id'  =>'required|exists:roles,id'
		]);
		if($check->passes()){ 
			$data = Role::where(['id'=>$request->input('id')])->first();
			return response(['data'=>$data]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function store(Request $request){ 
		$check = validator($request->all(),[ 
			'name'=>'required|unique:roles,name',
			'guard_name'=>'required|in:api'
		]);
		if($check->passes()){ 
			$data = Role::create([ 
				'name'=>$request->input('name'),
				'guard_name'=>$request->input('guard_name'),
			]);
			return response(['message'=>"数据已保存！",'data'=>$data]);
		}else{ 
			return response(['check_errors'=>$check->errors()],400);
		}
	}
	public function update($id,Request $request){ 
		$request->offsetSet("id",$id);
		$check = validator($request->all(),[ 
			'id'  =>'required|exists:roles,id',
			'name'=>'required|unique:roles,name,'.$request->input('id'),
			'guard_name'=>'required|in:api'
		]);
		if($check->passes()){ 
			$data = Role::where(['id'=>$request->input('id')])
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
		$count = Role::whereIn('id',explode(",",$ids))->delete();
		return response(['message'=>"已删除".$count."条记录！"]);
	}
}