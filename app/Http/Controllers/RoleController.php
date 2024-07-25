<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    
    public function indexAll(){
        $data = Role::with(['user'])->get();
        return RoleResource::collection($data);
    }

    public function index(Request $request){
        if(!empty($request->search)){
            $data = Role::with(['user'])->where('name', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = Role::with(['user'])->orderBy('level', 'asc')
                    ->paginate(5);
        }
        return RoleResource::collection($data);
    }

    public function store(Request $request){
        $user_id = Auth::user()->id;
        $data = new Role();
        $data->user_id = $user_id;
        $data->name = $request->name;
        $data->level = $request->level;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new RoleResource($data)
        ]);
    }

    public function update(Request $request, $id){
        $data = Role::find($id);
        $data->user_id = Auth::user()->id;
        $data->name = $request->name;
        $data->level = $request->level;
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new RoleResource($data)
        ]);
    }

    public function view($id){
        $data = Role::with(['user'])->find($id);
        return new RoleResource($data);
    }

    public function delete($id){
        $data = Role::find($id);
        $data->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Deleted Successfully.'
        ]);
    }
}
