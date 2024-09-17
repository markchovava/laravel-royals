<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherGeneratedResource;
use App\Models\CampaignManaged;
use App\Models\Reward;
use App\Models\VoucherGenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VoucherGeneratedController extends Controller
{

    public function generateRandomText($length = 9) {
        $date = date('Ymdhis');
        $characters = $date . '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $shuffled = str_shuffle($characters);
        return substr($shuffled, 0, $length);
    }

    public function bot_searchByCode(Request $request){
        if(!empty($request->search)){
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('code', $request->search)
                    ->first();
            if(!isset($data)){
                return response()->json([
                    'status' => 0,
                    'data' => [],
                    'message' => 'Voucher is not available.'
                ]);
            }
            return response()->json([
                'status' => 1,
                'data' => new VoucherGeneratedResource($data),
            ]);
        }
        return response()->json([
            'status' => 0,
            'data' => [],
            'message' => 'Something went wrong.'
        ]);
    }

    public function searchByCode(Request $request){
        if(!empty($request->search)){
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('code', $request->search)
                    ->first();
            if(!isset($data)){
                return response()->json([
                    'status' => 0,
                    'data' => [],
                    'message' => 'Voucher not found.'
                ]);
            }
            return response()->json([
                'status' => 1,
                'data' => new VoucherGeneratedResource($data),
            ]);
        }
    }

    public function checkVoucherByCampaignManagedId(Request $request){
        Log::info('$request->campaign_managed_id');
        Log::info($request->campaign_managed_id);
        $data = VoucherGenerated::where('campaign_managed_id', $request->campaign_managed_id)->first();
        if(isset($data)) {
            Log::info($data);
            return response()->json([
                'status' => 1,
                'data' => new VoucherGeneratedResource($data),
            ]);
        }
        return response()->json([
            'status' => 0,
            'data' => [],
        ]);
    }

    public function storeAll(Request $request){
        $user_id = Auth::user()->id;
        for ($i = 0; $i < $request->quantity; $i++) {
            $code = $this->generateRandomText(12);
            $data = new VoucherGenerated();
            $data->user_id = $user_id;
            $data->code = $code;
            $data->campaign_managed_id = $request->campaign_managed_id;
            $data->points = $request->points;
            //$data->receipt_no = $request->receipt_no;
            //$data->phone = $request->phone;
            $data->amount = $request->amount;
            $data->status = 0;
            $data->updated_at = now();
            $data->created_at = now();
            $data->save();
        }
        return response()->json([
            'status' => 1,
            'message' => 'Vouchers created successfully.'
        ]);
    }

    public function indexByUser(Request $request){
        $user_id = Auth::user()->id;
        if(!empty($request->search)){
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('user_id', $user_id)
                    ->where('code', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('user_id', $user_id)
                    ->orderBy('updated', 'desc')
                    ->paginate(12);
        }
        return VoucherGeneratedResource::collection($data);
    }

    public function indexByCampaignCSV(Request $request, $id){
        if(!empty($request->search)){
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('campaign_managed_id', $id)
                    ->where('code', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->get();
        } else{
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('campaign_managed_id', $id)
                    ->orderBy('updated_at', 'desc')
                    ->get();
        }
        return VoucherGeneratedResource::collection($data);
    }
    public function indexByCampaign(Request $request, $id){
        if(!empty($request->search)){
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('campaign_managed_id', $id)
                    ->where('code', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
        } else{
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('campaign_managed_id', $id)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
        }
        return VoucherGeneratedResource::collection($data);
    }

    public function deleteAllByCampaign(Request $request){
        VoucherGenerated::where('campaign_managed_id', $request->campaign_managed_id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Vouchers deleted successfully.',
        ]);
    }

    public function store(Request $request){
        $voucher = VoucherGenerated::where('campaign_managed_id', $request->campaign_managed_id)->first();
        if(!isset($voucher)) {
            $voucher->delete();
        }
        $reward = Reward::where('campaign_managed_id', $request->campaign_managed_id)->first();
        if( (int)$reward->price_per_voucher > (int)$request->amount ){
            return response()->json([
                'status' => 0,
                'message' => 'Voucher cannot be issued when amount is below Minimum amount required.',
                'price_per_voucher' => (int)$reward->price_per_voucher,
            ]);
        }
        $points = ((int)$request->amount / $reward->price_per_voucher) * $reward->points_per_voucher;
        $code = $this->generateRandomText(12);
        $user_id = Auth::user()->id;
        /*  */
        $data = new VoucherGenerated();
        $data->user_id = $user_id;
        $data->code = $code;
        $data->campaign_managed_id = $request->campaign_managed_id;
        $data->receipt_no = $request->receipt_no;
        $data->phone = $request->phone;
        $data->points = floor($points);
        $data->amount = $request->amount;
        $data->status = 1;
        $data->updated_at = now();
        $data->created_at = now();
        $data->save();

        return response()->json([
            'status' => 1,
            'message' => 'Voucher created successfully.',
            'data' => new VoucherGeneratedResource($data),
        ]);
    }

    public function index(Request $request){
        if(!empty($request->search)){
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->where('code', 'LIKE', '%' . $request->search . '%')
                    ->paginate(12);
        } else{
            $data = VoucherGenerated::with(['user', 'campaign_managed'])
                    ->orderBy('updated', 'desc')
                    ->paginate(12);
        }
        return VoucherGeneratedResource::collection($data);
    }

    public function view($id){
        $data = VoucherGenerated::find($id);
        return new VoucherGeneratedResource($data);
    }

    public function delete($id){
        VoucherGenerated::find($id)->delete();
        return response([
            'status' => 1,
            'message' => 'Deleted successfully.',
        ]);
    }
}
