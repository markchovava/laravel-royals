<?php

namespace App\Http\Controllers;

use App\Http\Resources\PriceResource;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
    
    public function indexAll(){
        $data = Price::with(['user'])->get();
        return PriceResource::collection($data);
    }

    public function priorityOne(){
        $data = Price::where('priority', 1)->first();
        return new PriceResource($data);
    }
    
    public function index(Request $request){
        if(!empty($request->search)){
            $data = Price::with(['user'])->where('name', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = Price::with(['user'])->orderBy('updated_at', 'desc')
                    ->paginate(12);
        }
        return PriceResource::collection($data);
    }

    public function store(Request $request){
        $data = new Price();
        $data->user_id = Auth::user()->id;
        $data->name = $request->name;
        $data->slug = $request->slug;
        $data->priority = $request->priority;
        $data->amount = (int)$request->amount;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new PriceResource($data)
        ]);
    }

    public function update(Request $request, $id){
        $data = Price::find($id);
        $data->user_id = Auth::user()->id;
        $data->name = $request->name;
        $data->slug = $request->slug;
        $data->quantity = $request->quantity;
        $data->priority = $request->priority;
        $data->amount = (int)$request->amount;
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new PriceResource($data)
        ]);
    }

    public function view($id){
        $data = Price::with(['user'])->find($id);
        return new PriceResource($data);
    }

    public function delete($id){
        $data = Price::find($id);
        $data->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Deleted Successfully.'
        ]);
    }
}
