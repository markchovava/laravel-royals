<?php

namespace App\Http\Controllers;

use App\Http\Resources\CampaignManagedResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\RewardResource;
use App\Http\Resources\VoucherRewardResource;
use App\Models\Campaign;
use App\Models\CampaignManaged;
use App\Models\Reward;
use App\Models\User;
use App\Models\VoucherGenerated;
use App\Models\VoucherReward;
use App\Models\VoucherUsed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{

    public function generateRandomText($length = 9) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $shuffled = str_shuffle($characters);
        return substr($shuffled, 0, $length);
    }


    public function bot_storeByPoints(Request $request){
        $user = User::where('phone', $request->phone)->first();
        $reward = Reward::where('campaign_managed_id', $request->campaign_managed_id)->first();
        if(!isset($reward)){
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong on rewards.'
            ]);
        }
        if(!isset($user)){
            /* USER */
            $code  = date('Ymd');
            $user = new User();
            $user->phone = $request->phone;
            $user->email = $request->phone;
            $user->code = $code;
            $user->password = Hash::make($code);
            $user->save();
            /* CAMAPAIGN */
            $campaign = new Campaign();
            $campaign->campaign_managed_id = $request->campaign_managed_id;
            $campaign->user_id = $user->id;
            $campaign->current_points = $request->points;
            $campaign->current_quantity = $request->quantity;
            $campaign->reward_id = $reward->id;
            $campaign->save();
            /* VOUCHER */
            $voucher = new VoucherUsed();
            $voucher->voucher_generated_id = $request->voucher_generated_id;
            $voucher->code = $request->code;
            $voucher->points = $request->points;
            $voucher->campaign_managed_id = $request->campaign_managed_id;
            $voucher->save();
            VoucherGenerated::find($request->voucher_generated_id)->delete();
            /* Subtract one */
            $campaign_managed = CampaignManaged::find($request->campaign_managed_id);
            $campaign_managed->quantity_remaining -= 1;
            $campaign_managed->save();
            return response()->json([
                'status' => 1,
                'message' => 'New User and Campaign created successfully.',
                'data' => new CampaignResource($campaign),
            ]);
        }
        /*  */
        $campaign = Campaign::where('user_id', $user->id)
                    ->where('campaign_managed_id', $request->campaign_managed_id)
                    ->first();
        if(!isset($campaign)){
            /* CAMAPAIGN */
            $campaign = new Campaign();
            $campaign->campaign_managed_id = $request->campaign_managed_id;
            $campaign->user_id = $user->id;
            $campaign->current_points = $request->points;
            $campaign->current_quantity = $request->quantity;
            $campaign->reward_id = $reward->id;
            $campaign->save();
            /* VOUCHER */
            $voucher = new VoucherUsed();
            $voucher->voucher_generated_id = $request->voucher_generated_id;
            $voucher->code = $request->code;
            $voucher->points = $request->points;
            $voucher->campaign_managed_id = $request->campaign_managed_id;
            $voucher->save();
            VoucherGenerated::find($request->voucher_generated_id)->delete();
             /* Subtract one */
             $campaign_managed = CampaignManaged::find($request->campaign_managed_id);
             $campaign_managed->quantity_remaining -= 1;
             $campaign_managed->save();
            return response()->json([
                'status' => 1,
                'message' => 'Campaign created successfully.',
                'data' => new CampaignResource($campaign),
            ]);
        }
        /*  */
        $campaign->current_points += $request->points;
        $campaign->current_quantity += (int)$request->quantity;
        $campaign->save();
        /* VOUCHER */
        $voucher = new VoucherUsed();
        $voucher->voucher_generated_id = $request->voucher_generated_id;
        $voucher->code = $request->code;
        $voucher->points = (int)$request->points;
        $voucher->campaign_managed_id = $request->campaign_managed_id;
        $voucher->save();
        VoucherGenerated::find($request->voucher_generated_id)->delete();
         /* Subtract one */
         $campaign_managed = CampaignManaged::find($request->campaign_managed_id);
         $campaign_managed->quantity_remaining -= 1;
         $campaign_managed->save();
        return response()->json([
            'status' => 1,
            'message' => 'Campaign saved successfully.',
            'data' => new CampaignResource($campaign)
        ]);


    }

    public function indexAll(){
        $data = Campaign::with(['user', 'campaign_managed', 'reward'])->get();
        return CampaignResource::collection($data);
    }


    public function bot_indexByUser(Request $request){
        $user = User::where('phone', $request->phone)->first();
        $data = Campaign::with(['user', 'campaign_managed', 'reward'])
                ->where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->paginate(12);
        
        return CampaignResource::collection($data);
    }


    public function indexByUser(Request $request){
        $user_id = Auth::user()->id;
        if(!empty($request->search)){
            $data = Campaign::with(['user', 'campaign_managed', 'reward'])
                    ->where('user_id', $user_id)
                    ->where('name', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = Campaign::with(['user', 'campaign_managed', 'reward'])
                    ->where('user_id', $user_id)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
        }
        return CampaignResource::collection($data);
    }

    public function storeByPoints(Request $request){
        $user = User::where('phone', $request->phone)->first();
        $reward = Reward::where('campaign_managed_id', $request->campaign_managed_id)->first();
        if(!isset($reward)){
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong on rewards.'
            ]);
        }
        if(!isset($user)){
            /* USER */
            $code  = date('Ymd');
            $user = new User();
            $user->phone = $request->phone;
            $user->email = $request->phone;
            $user->code = $code;
            $user->password = Hash::make($code);
            $user->save();
            /* CAMAPAIGN */
            $campaign = new Campaign();
            $campaign->campaign_managed_id = $request->campaign_managed_id;
            $campaign->user_id = $user->id;
            $campaign->current_points = $request->points;
            $campaign->current_quantity = $request->quantity;
            $campaign->reward_id = $reward->id;
            $campaign->save();
            /* VOUCHER */
            $voucher = new VoucherUsed();
            $voucher->voucher_generated_id = $request->voucher_generated_id;
            $voucher->code = $request->code;
            $voucher->points = $request->points;
            $voucher->campaign_managed_id = $request->campaign_managed_id;
            $voucher->save();
            VoucherGenerated::find($request->voucher_generated_id)->delete();
            /* Subtract one */
            $campaign_managed = CampaignManaged::find($request->campaign_managed_id);
            $campaign_managed->quantity_remaining -= 1;
            $campaign_managed->save();
            return response()->json([
                'status' => 1,
                'message' => 'New User and Campaign created successfully.',
                'data' => new CampaignResource($campaign),
            ]);
        }
        /*  */
        $campaign = Campaign::where('user_id', $user->id)
                    ->where('campaign_managed_id', $request->campaign_managed_id)
                    ->first();
        if(!isset($campaign)){
            /* CAMAPAIGN */
            $campaign = new Campaign();
            $campaign->campaign_managed_id = $request->campaign_managed_id;
            $campaign->user_id = $user->id;
            $campaign->current_points = $request->points;
            $campaign->current_quantity = $request->quantity;
            $campaign->reward_id = $reward->id;
            $campaign->save();
            /* VOUCHER */
            $voucher = new VoucherUsed();
            $voucher->voucher_generated_id = $request->voucher_generated_id;
            $voucher->code = $request->code;
            $voucher->points = $request->points;
            $voucher->campaign_managed_id = $request->campaign_managed_id;
            $voucher->save();
            VoucherGenerated::find($request->voucher_generated_id)->delete();
             /* Subtract one */
             $campaign_managed = CampaignManaged::find($request->campaign_managed_id);
             $campaign_managed->quantity_remaining -= 1;
             $campaign_managed->save();
            return response()->json([
                'status' => 1,
                'message' => 'Campaign created successfully.',
                'data' => new CampaignResource($campaign),
            ]);
        }
        /*  */
        $campaign->current_points += $request->points;
        $campaign->current_quantity += (int)$request->quantity;
        $campaign->save();
        /* VOUCHER */
        $voucher = new VoucherUsed();
        $voucher->voucher_generated_id = $request->voucher_generated_id;
        $voucher->code = $request->code;
        $voucher->points = (int)$request->points;
        $voucher->campaign_managed_id = $request->campaign_managed_id;
        $voucher->save();
        VoucherGenerated::find($request->voucher_generated_id)->delete();
         /* Subtract one */
         $campaign_managed = CampaignManaged::find($request->campaign_managed_id);
         $campaign_managed->quantity_remaining -= 1;
         $campaign_managed->save();
        return response()->json([
            'status' => 1,
            'message' => 'Campaign saved successfully.',
            'data' => new CampaignResource($campaign)
        ]);


    }

    public function view($id){
        $code = $this->generateRandomText(10);
        $user_id = Auth::user()->id;
        $data = Campaign::with(['user', 'campaign_managed', 'reward'])->find($id);
        $campaign_managed = CampaignManaged::where('id', $data->campaign_managed_id)->first();
        $reward = Reward::where('campaign_managed_id', $campaign_managed->id)->first();
        if($data->current_points >= $reward->target_points) {
            $data->current_points -= $reward->target_points;
            $data->save();
            $voucher = new VoucherReward();
            $voucher->user_id = $user_id;
            $voucher->code = $code;
            $voucher->status = 'Active';
            $voucher->campaign_managed_id = $campaign_managed->id;
            $voucher->campaign_id = $data->id;
            $voucher->reward_id = $reward->id;
            $voucher->created_at = now();
            $voucher->updated_at = now();
            $voucher->save();
            //  REWARD VOUCHERS 
            $vouchers = VoucherReward::where('campaign_id', $data->id)->where('status', 'Active')->get();
            if(isset($vouchers)){
                return response()->json([
                    'status' => 1,
                    'data' => new CampaignResource($data),
                    'campaign_managed' => new CampaignManagedResource($campaign_managed),
                    'reward' => new RewardResource($reward),
                    'vouchers' => VoucherRewardResource::collection($vouchers),
                    'message' => 'Reward voucher added.'
                ]) ;
            }
            return response()->json([
                'status' => 1,
                'data' => new CampaignResource($data),
                'campaign_managed' => new CampaignManagedResource($campaign_managed),
                'reward' => new RewardResource($reward),
                'vouchers' => [],
                'message' => 'Reward voucher added.'
            ]);
        }
        //  REWARD VOUCHERS 
        $vouchers = VoucherReward::where('campaign_id', $data->id)->where('status', 'Active')->get();
        if(isset($vouchers)){
            return response()->json([
                'status' => 1,
                'data' => new CampaignResource($data),
                'campaign_managed' => new CampaignManagedResource($campaign_managed),
                'reward' => new RewardResource($reward),
                'vouchers' => VoucherRewardResource::collection($vouchers),
            ]) ;
        }
        return response()->json([
            'status' => 1,
            'data' => new CampaignResource($data),
            'campaign_managed' => new CampaignManagedResource($campaign_managed),
            'reward' => new RewardResource($reward),
            'vouchers' => [],
        ]) ;
    }
    

    public function index(Request $request){
        if(!empty($request->search)){
            $data = Campaign::with(['user', 'campaign_managed', 'reward'])->where('name', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = Campaign::with(['user', 'campaign_managed', 'reward'])->orderBy('updated_at', 'desc')
                    ->paginate(12);
        }
        return CampaignResource::collection($data);
    }

    public function store(Request $request){
        $user_id = Auth::user()->id;
        $data = new Campaign();
        $data->user_id = $user_id;
        $data->current_quantity = $request->current_quantity;
        $data->current_points = $request->current_points;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->reward_id = $request->reward_id;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new CampaignResource($data)
        ]);
    }

    public function update(Request $request, $id){
        $user_id = Auth::user()->id;
        $data = Campaign::find($id);
        $data->user_id = $user_id;
        $data->current_quantity = $request->current_quantity;
        $data->current_points = $request->current_points;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->reward_id = $request->reward_id;
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new CampaignResource($data)
        ]);
    }

    public function delete($id){
        $data = Campaign::find($id);
        $data->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Deleted Successfully.'
        ]);
    }


}
