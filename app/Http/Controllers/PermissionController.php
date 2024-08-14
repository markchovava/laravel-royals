<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    
    public function index(Request $request){
        if(!empty($request->search)){
            $data = Permission::where('name', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            return PermissionResource::collection($data);
        }
        $data = Permission::orderBy('updated_at', 'desc')
                ->paginate(12);
        return PermissionResource::collection($data);
    }
    
    public function store(Request $request){
        $data = new Permission();
        $data->name = $request->name;
        $data->slug = $request->slug;
        $data->priority = $request->priority;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Data saved successfully.'
        ]);
    }
    
    public function view($id){
        $data = Permission::find($id);
        return new PermissionResource($data);
    }

    public function update(Request $request, $id){
        $data = Permission::find($id);
        $data->name = $request->name;
        $data->slug = $request->slug;
        $data->priority = $request->priority;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Data saved successfully.'
        ]);
    }

    public function delete($id){
        Permission::find($id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Data deleted successfully.',
        ]);
    }


}
