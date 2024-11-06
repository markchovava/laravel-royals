<?php

namespace App\Http\Controllers;

use App\Http\Resources\CampaignManagedResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\RewardResource;
use App\Http\Resources\VoucherGeneratedResource;
use App\Http\Resources\VoucherRewardResource;
use App\Http\Resources\VoucherUsedResource;
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


    public function bot_addPointsToCampaign(Request $request) {
        if(!empty($request->code) && !empty($request->phone)){
            $voucher = VoucherGenerated::where('code', $request->code)->first();
            if(!isset($voucher)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Voucher is not available, try a different one.',
                ]);
            }
            $user = User::where('phone', $request->phone)->first();
            if(!isset($user)) {
                return response()->json([
                    'status' => -3,
                    'message' => 'User not registered, please register before adding points.',
                ]);
            }
            $reward = Reward::where('campaign_managed_id', $voucher->campaign_managed_id)->first();
            if(!isset($reward)){
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong on rewards.'
                ]);
            }
            $campaign_managed = CampaignManaged::find($voucher->campaign_managed_id);
            if($campaign_managed->status != 'Active') {
                return response()->json([
                    'status' => -4,
                    'message' => 'The Campaign is not Acctive.'
                ]);
            }
            $campaign = Campaign::where('user_id', $user->id)
                        ->where('campaign_managed_id', $voucher->campaign_managed_id)
                        ->first();
            if(!isset($campaign)) {
                $campaign = new Campaign();
                $campaign->campaign_managed_id = $voucher->campaign_managed_id;
                $campaign->user_id = $user->id;
                $campaign->current_points = $voucher->points;
                $campaign->current_quantity = 1;
                $campaign->reward_id = $reward->id;
                $campaign->updated_at = now();
                $campaign->created_at = now();
                $campaign->save();
                /* VOUCHER */
                $voucher_used = new VoucherUsed();
                $voucher_used->voucher_generated_id = $voucher->id;
                $voucher_used->code = $request->code;
                $voucher_used->points = $voucher->points;
                $voucher_used->campaign_managed_id = $voucher->campaign_managed_id;
                $voucher_used->updated_at = now();
                $voucher_used->created_at = now();
                $voucher_used->save();
                VoucherGenerated::find($voucher->id)->delete();
                /* Subtract one */
                $campaign_managed = CampaignManaged::find($voucher->campaign_managed_id);
                $campaign_managed->quantity_remaining -= 1;
                $campaign_managed->updated_at = now();
                $campaign_managed->save();
                $data = Campaign::with(['user', 'campaign_managed', 'reward'])->find($campaign->id);
                return response()->json([
                    'status' => 1,
                    'message' => 'Campaign created successfully.',
                    'data' => new CampaignResource($data),
                    'voucher' => new VoucherUsedResource($voucher_used),
                ]);
            }
            $campaign->current_points += $voucher->points;
            $campaign->current_quantity += 1;
            $campaign->reward_id = $reward->id;
            $campaign->updated_at = now();
            $campaign->save();
            /* VOUCHER */
            $voucher_used = new VoucherUsed();
            $voucher_used->voucher_generated_id = $voucher->id;
            $voucher_used->code = $request->code;
            $voucher_used->points = $voucher->points;
            $voucher_used->campaign_managed_id = $voucher->campaign_managed_id;
            $voucher_used->updated_at = now();
            $voucher_used->created_at = now();
            $voucher_used->save();
            VoucherGenerated::find($voucher->id)->delete();
             /* Subtract one */
            $campaign_managed = CampaignManaged::find($voucher->campaign_managed_id);
            $campaign_managed->quantity_remaining -= 1;
            $campaign_managed->updated_at = now();
            $campaign_managed->save();
            $data = Campaign::with(['user', 'campaign_managed', 'reward'])->find($campaign->id);
            /**
             * 
             *  CHECK IF TARGET REACHED,
             *  In Campaign
             *  
             **/
            if($campaign->current_points >= $reward->target_points){
                $campaign->current_points -= $reward->target_points;
                $campaign->updated_at = now();
                $campaign->save();
                /* REWARD VOUCHER */
                $voucher = new VoucherReward();
                $voucher->user_id = $user->id;
                $voucher->code = $this->generateRandomText(8);
                $voucher->status = 'Active';
                $voucher->campaign_managed_id = $campaign_managed->id;
                $voucher->campaign_id = $campaign->id;
                $voucher->reward_id = $reward->id;
                $voucher->created_at = now();
                $voucher->updated_at = now();
                $voucher->save();
                $data = Campaign::with(['user', 'campaign_managed', 'reward'])->find($campaign->id);
                $voucher_reward = VoucherReward::with(['campaign_managed', 'campaign', 'reward'])->find($voucher->id);

                return response()->json([
                    'status' => 2,
                    'data' => new CampaignResource($data),
                    'voucher_reward' => new VoucherRewardResource($voucher_reward),
                    'voucher' => new VoucherUsedResource($voucher_used),
                ]);

            }
            return response()->json([
                'status' => 1,
                'message' => 'Campaign updated successfully.',
                'data' => new CampaignResource($data),
                'voucher' => new VoucherUsedResource($voucher_used),
            ]);
        }
        return response()->json([
            'status' => -1,
            'message' => 'QR Code and phone number is required.'
        ]);

    }


    public function bot_indexByUser(Request $request){
        if(!empty($request->phone)) {
            $user = User::where('phone', $request->phone)->first();
            if(!isset($user)) {
                return response()->json([
                    'status' => -3,
                    'message' => 'User not registered, you are required to register.',
                ]);
            }
            $data = Campaign::with(['user', 'campaign_managed', 'reward'])
                    ->where('user_id', $user->id)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            if(!isset($data)) {
                return response()->json([
                    'status' => 0,
                    'data' => [],
                    'message' => 'Phone number does not exist.'
                ]);
            }

            return response()->json([
                'status' => 1,
                'message' => 'User Campaigns found.',
                'data' => CampaignResource::collection($data),
            ]);
        }
        return response()->json([
            'status' => -1,
            'message' => 'Phone number is required.'
        ]);
    }


    public function indexAll(){
        $data = Campaign::with(['user', 'campaign_managed', 'reward'])->get();
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
