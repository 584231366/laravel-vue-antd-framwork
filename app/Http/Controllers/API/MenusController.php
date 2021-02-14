<?php

namespace App\Http\Controllers\API;

use Response;
use Storage;
use Log;
use Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menus;
use App\Models\MenusPermissionsGroup;
class MenusController extends Controller
{
	public function index(Request $request){ 
		return $this->list($request);
	}
	public function list(Request $request,$limit=10){ 
		$list = Menus::paginate($limit);
		$list->each(function($item){ 
			$item->pg = MenusPermissionsGroup::where(['menu_id'=>$item->id])->get(['group_id'])->pluck('group_id')->all();
			$item->show_name = $this->showName($item->id);
		});
		return $list;
	}
	public function show(Request $request){ 
		$check = validator($request->all(),[ 
			'id'  =>'required|exists:menus,id'
		]);
		if($check->passes()){ 
			$data=Menus::where(['id'=>$request->input('id')])->first();
			return response(['message'=>trans('messages.the_corresponding_data_has_been_found'),'data'=>$data]);
		}else{ 
			return response(['message'=>trans('messages.data_does_not_exist'),'errors'=>$check->errors()],400);
		}
	}
	public function all(Request $request){ 
		$data = Menus::all();
		$data->each(function($item){
			$item->show_name = $this->showName($item->id);
		});
		return ['data'=>$data];
	}
	public function store(Request $request){ 
		$check = validator($request->all(),[ 
			'name'=>'required|min:2|max:20',
        	'icon'=>'required|max:500',
        	'route'=>'required|max:500',
        	'parent_id'=>'nullable|exists:menus,id',
        	'pg'=>'nullable|array'
		]);
		if($check->passes()){ 
			$data = Menus::create([ 
				'name'=>$request->input('name'),
        		'icon'=>$request->input('icon'),
        		'route'=>$request->input('route'),
        		'parent_id'=>$request->input('parent_id')?$request->input('parent_id'):null,
			]);
			if($request->input('pg')){ 
				$pg = [];
				foreach($request->input('pg') as $group_id){ 
					$pg[] = [ 
						'menu_id'=>$data->id,
						'group_id'=>$group_id
					];
				}
				if(count($pg)){ 
					MenusPermissionsGroup::insert($pg);
				}
			}
			return response(['message'=>trans('messages.data_saved_successfully'),'data'=>$data]);
		}else{ 
			return response(['message'=>trans('messages.data_saving_failed'),'errors'=>$check->errors()],400);
		}
	}
	public function update(Request $request){ 
		$check = validator($request->all(),[ 
			'id'  =>'required|exists:menus,id',
			'name'=>'required|min:2|max:20',
        	'icon'=>'required|max:500',
        	'route'=>'required|max:500',
        	'parent_id'=>'nullable|exists:menus,id',
        	'pg'=>'nullable|array'
		]);
		if($check->passes()){ 
			$data = Menus::where(['id'=>$request->input('id')])
			->update([ 
				'name'=>$request->input('name'),
        		'icon'=>$request->input('icon'),
        		'route'=>$request->input('route'),
        		'parent_id'=>$request->input('parent_id')?$request->input('parent_id'):null,
			]);
			MenusPermissionsGroup::where(['menu_id'=>$request->input('id')])->delete();
			if($request->input('pg')){ 
				$pg = [];
				foreach($request->input('pg') as $group_id){ 
					$pg[] = [ 
						'menu_id'=>$request->input('id'),
						'group_id'=>$group_id
					];
				}
				if(count($pg)){ 
					MenusPermissionsGroup::insert($pg);
				}
			}
			return response(['message'=>trans('messages.data_saved_successfully'),'data'=>$data]);
		}else{ 
			return response(['message'=>trans('messages.data_saving_failed'),'errors'=>$check->errors()],400);
		}
	}
	public function destory(Request $request){ 
		$check = validator($request->all(),[ 
			'id'  =>'required|exists:menus,id'
		]);
		if($check->passes()){ 
			Menus::where(['id'=>$request->input('id')])->delete();
			MenusPermissionsGroup::where(['menu_id'=>$request->input('id')])->delete();
			return response(['message'=>trans('messages.data_deleted_successfully')]);
		}else{ 
			return response(['message'=>trans('messages.data_deletion_failed'),'errors'=>$check->errors()],400);
		}
	}
	private function showName($menu_id,$menus=null){
		if ($menus){
			if ($menu_id) {
				$mn = $this->showName($menus[$menu_id]['parent_id'],$menus);
				if ($mn) {
					return $this->showName($menus[$menu_id]['parent_id'],$menus).'>'.$menus[$menu_id]['name'];
				}
				return $menus[$menu_id]['name'];
			}else{
				return '';
			}
		}else{
			$menus = Menus::get(['id','name','parent_id'])->keyBy('id')->all();
			return $this->showName($menu_id,$menus);
		}
	}
}