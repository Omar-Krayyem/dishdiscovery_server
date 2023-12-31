<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Exception;

class CommentsController extends Controller
{
    public function store(Request $request_info){
        try{
            $user = Auth::user();
            $validated_data = $this->validate($request_info, [
                'recipe_id' => ['required', 'numeric'],
                'text' => ['required' , 'string']
            ]); 

            $comment = Comment::create([
                'text' => $validated_data['text'],
                'recipe_id' => $validated_data['recipe_id'],
                'user_id' => $user->id,
            ]);

            return $this->customResponse($comment, 'comment Created Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getComments($id){
        try {
            $comments = Comment::with(['user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])->where('recipe_id', $id)->get();

            $result = $comments->map(function ($comment) {
                return [
                    'text' => $comment->text,
                    'first_name' => $comment->user->first_name,
                    'last_name' => $comment->user->last_name,
                ];
            });
    
            return $this->customResponse($result, 'All comments');
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
