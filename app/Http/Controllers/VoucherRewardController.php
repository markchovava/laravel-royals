<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherRewardResource;
use App\Models\VoucherReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherRewardController extends Controller
{

    public function searchByCode(Request $request){
        if(!empty($request->search)){
            $data = VoucherReward::with(['user', 'campaign_managed', 'reward'])
                    ->where('code', $request->search)->first();
            if(isset($data)){
                return response()->json([
                    'status' => 1,
                    'data' => new VoucherRewardResource($data),
                ]);
            }
            return response()->json([
                'status' => 0,
                'data' => [],
                'message' => 'Sorry, not found.',
            ]);
        }
        return response()->json([
            'status' => 2,
            'data' => [],
            'message' => 'Something went wrong.',
        ]);
    }
    
    public function indexByCampaign(Request $request, $id){
        if(!empty($request->search)) {
            $data = VoucherReward::with(['user', 'campaign_managed', 'reward'])
                    ->where('campaign_id', $id)
                    ->where('code', $request->search)->paginate(12);
            return VoucherRewardResource::collection($data);
        }
        $data = VoucherReward::with(['user', 'campaign_managed', 'reward'])
                ->where('campaign_id', $id)->paginate(12);
        return VoucherRewardResource::collection($data);
    }

    public function view($id){
        $data = VoucherReward::with(['user', 'campaign_managed', 'reward'])
                ->find($id);
        return new VoucherRewardResource($data);
    }

}
