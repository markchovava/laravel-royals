<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCampaignManagedResource;
use App\Models\CampaignManaged;
use App\Models\User;
use App\Models\UserAuthor;
use App\Models\UserCampaignManaged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCampaignManagedController extends Controller
{
    
    public function indexAllByUser(){
        $user_id = Auth::user()->id;
        $data = UserCampaignManaged::with(['campaign_managed', 'user'])
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'desc')
                ->get();
        return UserCampaignManagedResource::collection($data);
    } 

    public function index(){
        $data = UserCampaignManaged::with(['campaign_managed', 'user'])
                ->orderBy('updated_at', 'desc')
                ->paginate(12);
        return UserCampaignManagedResource::collection($data);
    }  

    public function store(Request $request) {
        $user_id = Auth::user()->id;
        $data = new UserCampaignManaged();
        $data->user_id = $user_id;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Data saved successfully',
            'data' => new UserCampaignManagedResource($data),
        ]);
    }

    public function update(Request $request, $id) {
        $user_id = Auth::user()->id;
        $data = UserCampaignManaged::find($id);
        $data->user_id = $user_id;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Data saved successfully',
            'data' => new UserCampaignManagedResource($data),
        ]);
    }


    public function view($id) {
        $data = UserCampaignManaged::with(['campaign_managed', 'user'])->find($id);
        return new UserCampaignManagedResource($data);
    }

    public function delete($id){
        UserCampaignManaged::find($id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Deleted successfully.'
        ]);
    }


}
