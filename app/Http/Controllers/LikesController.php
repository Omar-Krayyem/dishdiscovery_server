<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function store(Request $request_info){
        try{
            $user = Auth::user();
            $validated_data = $this->validate($request_info, [
                'recipe_id' => ['required', 'numeric'],
            ]); 

            $recipe = Like::create([
                'recipe_id' => $validated_data['recipe_id'],
                'user_id' => $user->id,
            ]);

            return $this->customResponse($recipe, 'recipe Created Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function destroy($id){
        try{
            $user = Auth::user();
            $like = Like::where('recipe_id', $id)->where('user_id', $user->id);
            $like->delete();
            return $this->customResponse($like, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function countLikes($id){
        try {
            $recipeLikes =Like::where('recipe_id', $id)->count();

            return $this->customResponse($recipeLikes);
        } catch(Exception $e) {
            return $this->customResponse($e->getMessage(), 'error', 500);
        }
    }

    public function isLiked($id){
        try{
            $user = Auth::user();
            $liked = Like::where('user_id', $user->id)
                     ->where('recipe_id', $id)
                     ->exists();

            return $this->customResponse(['is_liked' => $liked], 'Success');
        } catch(Exception $e) {
            return $this->customResponse($e->getMessage(), 'error', 500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
