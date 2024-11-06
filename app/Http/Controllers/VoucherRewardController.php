<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherRewardResource;
use App\Models\User;
use App\Models\VoucherReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoucherRewardController extends Controller
{

    public function bot_indexByUser(Request $request){
        if(!empty($request->phone)) {
            $user = User::where('phone', $request->phone)->first();
            if(!isset($user)) {
                return response()->json([
                    'status' => -3,
                    'message' => 'User not registered, you are required to register.'
                ]);
            }
            $data = VoucherReward::with(['reward', 'campaign_managed', 'campaign'])->where('user_id', $user->id)->get();
            if(!isset($data)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Reward vouchers not available.'
                ]);
            }
            return response()->json([
                'status' => 1,
                'data' => VoucherRewardResource::collection($data),
            ]);
        }
        return response()->json([
            'status' => -1,
            'message' => 'Phone number is required.'
        ]);
    
    }


    public function bot_searchByCode(Request $request){
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
