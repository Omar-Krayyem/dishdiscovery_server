<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Calendar;
use Exception;

class CalendarController extends Controller
{
    public function store(Request $request_info){
        try{
            $user = Auth::user();
            $validated_data = $this->validate($request_info, [
                'date' => ['required', 'date'],
                'recipe_id' => ['required', 'exists:recipes,id'],
            ]); 

            $recipe = Calendar::create([
                'date' => $validated_data['date'],
                'recipe_id' => $validated_data['recipe_id'],
                'user_id' => $user->id,
            ]);

            return $this->customResponse($recipe, 'new recipe added Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAll()
    {
        try {
            $user = Auth::user();
            $recipes = Calendar::where('user_id', $user->id)->get();
    
            return $this->customResponse($recipes);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }

    public function destroy($id){
        try{
            $item = Calendar::find($id);
            $item->delete();
            return $this->customResponse($item, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
