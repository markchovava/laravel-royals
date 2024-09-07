<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherRewardUsedResource;
use App\Models\User;
use App\Models\VoucherReward;
use App\Models\VoucherRewardUsed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherRewardUsedController extends Controller
{

    public function bot_store(Request $request){
        $user = User::where('phone', $request->phone)->first();
        $data = new VoucherRewardUsed();
        $data->user_id = $user->id;
        $data->code = $request->code;
        $data->status = 'Used';
        $data->campaign_id = $request->campaign_id;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->reward_id = $request->reward_id;
        $data->updated_at = now();
        $data->created_at = now();
        $data->save();
        VoucherReward::where('id', $request->voucher_reward_id)->delete();
        return response()->json([
            'status' => 1, 
            'message' => 'Saved successfully.',
            'data' => new VoucherRewardUsedResource($data),
        ]);
    }
    
    public function indexByUser(Request $request){
        $user_id = Auth::user()->id;
        if(!empty($request->search)){
            $data = VoucherRewardUsed::with(['user', 'campaign', 'campaign_managed', 'reward'])
                    ->where('user_id', $user_id)
                    ->where('code', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            return VoucherRewardUsedResource::collection($data);
        }
        $data = VoucherRewardUsed::with(['user', 'campaign', 'campaign_managed', 'reward'])
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'desc')
                ->paginate(12);
        return VoucherRewardUsedResource::collection($data);
    }

    public function viewByUser($id){
        $user_id = Auth::user()->id;
        $data = VoucherRewardUsed::with(['user', 'campaign', 'campaign_managed', 'reward'])
                ->where('user_id', $user_id)
                ->where('id', $id)
                ->first();
        return new VoucherRewardUsedResource($data);
    }

    public function index(Request $request){
        if(!empty($request->search)){
            $data = VoucherRewardUsed::with(['user', 'campaign', 'campaign_managed', 'reward'])
                    ->where('code', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            return VoucherRewardUsedResource::collection($data);
        }
        $data = VoucherRewardUsed::with(['user', 'campaign', 'campaign_managed', 'reward'])
                ->orderBy('updated_at', 'desc')
                ->paginate(12);
        return VoucherRewardUsedResource::collection($data);
    }

    public function store(Request $request){
        $user_id = Auth::user()->id;
        $data = new VoucherRewardUsed();
        $data->user_id = $user_id;
        $data->code = $request->code;
        $data->status = 'Used';
        $data->campaign_id = $request->campaign_id;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->reward_id = $request->reward_id;
        $data->updated_at = now();
        $data->created_at = now();
        $data->save();
        VoucherReward::where('id', $request->voucher_reward_id)->delete();
        return response()->json([
            'status' => 1, 
            'message' => 'Saved successfully.',
            'data' => new VoucherRewardUsedResource($data),
        ]);
    }

    public function view($id){
        $data = VoucherRewardUsed::with(['user', 'campaign', 'campaign_managed', 'reward'])
                ->find($id);
        return new VoucherRewardUsedResource($data);
    }

    public function delete($id){
        VoucherRewardUsed::where('id', $id)->delete();
        return response()->json([
            'status' => 1, 
            'message' => 'Deleted successfully.',
        ]);
    }
}
