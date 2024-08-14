<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserAuthorResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthorController extends Controller
{

    public function storeUserAuthorRole(Request $request) {
        $author_id = Auth::user()->id;
        $user_author = new UserAuthor();
        $user_author->author_id = $author_id;
        $user_author->user_id = $request->user_id;
        $user_author->updated_at = now();
        $user_author->created_at = now();
        $user_author->save();
        /*  */
        $user = User::find($request->user_id);
        $user->role_level = $request->role_level;
        $user->save();       
        return response()->json([
            'status' => 1,
            'message' => 'Data saved succesfully'
        ]);
    }

    public function updateUserAuthorRole(Request $request, $id) {
        /*  */
        $user = User::find($id);
        $user->role_level = $request->role_level;
        $user->save();       
        return response()->json([
            'status' => 1,
            'message' => 'Data saved succesfully'
        ]);
    }

    public function indexUserByAuthor(Request $request) {
        $author_id = Auth::user()->id;
        if(!empty($request->search)) {
            $user_ids = UserAuthor::where('author_id', $author_id)->pluck('user_id');
            $data = User::with(['role'])->whereIn('id', $user_ids)
                    ->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orderBy('updated_at', 'desc')
                    ->paginate(12);
            return UserResource::collection($data);
        }
        $user_ids = UserAuthor::where('author_id', $author_id)->pluck('user_id');
        $data = User::with(['role', 'user_author'])->whereIn('id', $user_ids)
                ->orderBy('updated_at', 'desc')
                ->paginate(12);
        return UserResource::collection($data);
    }
    
    public function index(){
        $data = UserAuthor::with(['author', 'user'])->orderBy('updated_at', 'desc')->paginate(12);
        return UserAuthorResource::collection($data);
    }

    public function view($id){
        $data = UserAuthor::with(['author', 'user'])->find($id);
        return new UserAuthorResource($data);
    }

    public function store(Request $request){
        $author_id = Auth::user()->id;
        $data = new UserAuthor();
        $data->author_id = $author_id;
        $data->user_id = $request->user_id;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Data saved successfully.',
            'data' => new UserAuthorResource($data),
        ]);
    }

    public function update(Request $request, $id){
        $author_id = Auth::user()->id;
        $data = UserAuthor::find($id);
        $data->author_id = $author_id;
        $data->user_id = $request->user_id;
        $data->created_at = now();
        $data->updated_at = now();
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Data saved successfully.',
            'data' => new UserAuthorResource($data),
        ]);
    }

    public function delete($id) {
        UserAuthor::find($id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Data deleted successfully.',
        ]);
    }


}
