<?php

namespace App\Http\Controllers;

use App\Http\Resources\CampaignManagedResource;
use App\Http\Resources\RewardResource;
use App\Models\CampaignManaged;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CampaignManagedController extends Controller
{

    public function indexAll(){
        $data = CampaignManaged::with(['user', 'reward'])->get();
        return CampaignManagedResource::collection($data);
    }


    public function statusUpdate(Request $request, $id){
        $data = CampaignManaged::find($id);
        $data->status = $request->status;
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved successfully.'
        ]);
    }

    public function durationUpdate(Request $request, $id){
        $end_date = date('Y-m-d', strtotime($request->start_date . ' + ' . $request->num_of_days . ' days'));
        $data = CampaignManaged::find($id);
        $data->start_date = $request->start_date;
        $data->end_date = $end_date;
        $data->num_of_days = $request->num_of_days;
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved successfully.'
        ]);
    }

    public function indexByAuthorUser(Request $request){
        $user_id = Auth::user()->id;
        $authorIds = UserAuthor::where('user_id', $user_id)->pluck('author_id');
        $combinedIds = $authorIds;
        $combinedIds[] = $user_id;
        if(!empty($authorIds)) {
            if(!empty($request->search)){
                $data = CampaignManaged::whereIn('user_id', $combinedIds)
                        ->where('name', 'LIKE', '%' . $request->search . '%')
                        ->orderBy('updated_at', 'desc')
                        ->paginate(12);
                return CampaignManagedResource::collection($data);
            }
            $data = CampaignManaged::whereIn('user_id', $combinedIds)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            return CampaignManagedResource::collection($data);
        }
        if(!empty($request->search)){
            $data = CampaignManaged::where('user_id', $user_id)
                    ->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            return CampaignManagedResource::collection($data);
        }
        $data = CampaignManaged::where('user_id', $user_id)
                ->orderBy('updated_at', 'desc')
                ->paginate(12);
        Log::info('no $authorIds');
        Log::info($data);
        return CampaignManagedResource::collection($data);
    }

    public function indexByUserActive(){
        $user_id = Auth::user()->id;
        $data = CampaignManaged::where('user_id', $user_id)
                ->where('status', 'Active')->get();
        return CampaignManagedResource::collection($data);
    }

    public function indexByUser(Request $request){
        $user_id = Auth::user()->id;
        if(!empty($request->search)){
            $data = CampaignManaged::with(['user', 'reward'])
                    ->where('user_id', $user_id)
                    ->where('name', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = CampaignManaged::with(['user', 'reward'])
                    ->where('user_id', $user_id)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
        }
        return CampaignManagedResource::collection($data);
    }

    public function index(Request $request){
        if(!empty($request->search)){
            $data = CampaignManaged::with(['user'])->where('name', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = CampaignManaged::with(['user'])->orderBy('updated_at', 'desc')
                    ->paginate(5);
        }
        return CampaignManagedResource::collection($data);
    }

    public function store(Request $request){
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        if($user->role_level > 2){
            $user->role_level = 2;
            $user->save();
        }
        $data = new CampaignManaged();
        $data->status = 'Processing';
        $data->user_id = $user_id;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->quantity = $request->quantity;
        $data->quantity_remaining = $request->quantity;
        $data->total = $request->total;
        //$data->start_date = $request->start_date;
        //$data->end_date = $request->end_date;
        $data->num_of_days = $request->num_of_days;
        $data->company_name = $request->company_name;
        $data->company_phone = $request->company_phone;
        $data->company_address = $request->company_address;
        $data->company_email = $request->company_email;
        $data->company_website = $request->company_website;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        /* REWARD */
        $reward = new Reward();
        $reward->user_id = $user_id;
        $reward->name = $request->reward_name;
        $reward->target_points = $request->target_points;
        $reward->points_per_voucher = $request->points_per_voucher;
        $reward->price_per_voucher = $request->price_per_voucher;
        $reward->campaign_managed_id = $data->id;
        $reward->updated_at = now();
        $reward->created_at = now();
        $reward->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new CampaignManagedResource($data),
            'reward' => new RewardResource($reward),
        ]);
    }

    public function update(Request $request, $id){
        $user_id = Auth::user()->id;
        $data = CampaignManaged::find($id);
        $data->user_id = $user_id;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->quantity = $request->quantity;
        $data->quantity_remaining = $request->quantity;
        $data->total = $request->total;
        //$data->start_date = $request->start_date;
        //$data->end_date = $request->end_date;
        $data->num_of_days = $request->num_of_days;
        $data->company_name = $request->company_name;
        $data->company_phone = $request->company_phone;
        $data->company_address = $request->company_address;
        $data->company_email = $request->company_email;
        $data->company_website = $request->company_website;
        $data->updated_at = now();
        $data->save();
        $reward = Reward::where('campaign_managed_id', $data->id)->first();
        $reward->user_id = $user_id;
        $reward->name = $request->reward_name;
        $reward->target_points = $request->target_points;
        $reward->points_per_voucher = $request->points_per_voucher;
        $reward->price_per_voucher = $request->price_per_voucher;
        $reward->campaign_managed_id = $data->id;
        $reward->updated_at = now();
        $reward->save();
        return response()->json([
            'status' => 1,
            'message' => 'Saved Successfully.',
            'data' => new CampaignManagedResource($data)
        ]);
    }

    public function view($id){
        $data = CampaignManaged::with(['user', 'reward'])->find($id);
        return new CampaignManagedResource($data);
    }

    public function delete($id){
        CampaignManaged::find($id)->delete();
        Reward::where('campaign_managed_id', $id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Deleted Successfully.'
        ]);
    }

}
