<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingList;

class shoppingListsController extends Controller
{
    public function store(Request $request_info){
        try{
            $user = Auth::user();
            $validated_data = $this->validate($request_info, [
                'name' => ['required', 'string'],
                'quantity' => ['string', 'numeric'],
            ]); 

            $newItem = ShoppingList::create([
                'name' => $validated_data['name'],
                'quantity' => $validated_data['quantity'],
                'user_id' => $user->id,
            ]);

            return $this->customResponse($newItem, 'newItem Created Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAll()
    {
        try {
            $user = Auth::user();
            $recipes = ShoppingList::where('user_id', $user->id)->get();
    
            return $this->customResponse($recipes);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }

    public function destroy($id){
        try{
            $item = ShoppingList::find($id);
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
