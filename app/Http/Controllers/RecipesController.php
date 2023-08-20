<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\User;
use App\Models\Recipe;

class RecipesController extends Controller
{
    public function store(Request $request_info){
        try{
            $user = Auth::user();
            $validated_data = $this->validate($request_info, [
                'name' => ['required', 'string'],
                'cuisine' => ['string'],
                'ingredients' => ['string'],
                'image' => ['required', 'string'],
            ]); 

            $base64Image = $request_info->image;
            echo $base64Image;

            $decodedImage = base64_decode($base64Image);
            $filename = 'recipe_' . time() . '.jpg';
            $path = Storage::disk('public')->put('recipe_images/' . $filename, $decodedImage);

            $recipe = Recipe::create([
                'name' => $validated_data['name'],
                'cuisine' => $validated_data['cuisine'],
                'ingredients' => $validated_data['ingredients'],
                'image_url' => 'recipe_images/' . $filename,
                'user_id' => $user->id,
            ]);

            return $this->customResponse($recipe, 'recipe Created Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAll()
    {
        try {
            $recipes = Recipe::with('user')
                ->orderBy('created_at', 'desc')
                ->take(24)
                ->get();
    
            return $this->customResponse($recipes);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }

    public function getMyRecipes()
    {
        try {
            $user = Auth::user();
            $myRecipes = Recipe::with('user')->where('user_id', $user->id)->get();
    
            return $this->customResponse($myRecipes);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }

    public function getById(Recipe $recipe){
        try{
            return $this->customResponse($recipe->load('user'));
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function destroy($id){
        try{
            $recipe = Recipe::find($id);

            if (!$recipe) {
                return $this->customResponse('recipe not found', 'error', 404);
            }

            $recipe->delete();
            return $this->customResponse($recipe, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function searchRecipes($searchRecipes) {
        try{
            $recipes = Recipe::where('name', 'LIKE', "%$searchRecipes%")
            ->orWhere('cuisine', 'LIKE', "%$searchRecipes%")
            ->orWhere('ingredients', 'LIKE', "%$searchRecipes%")->get();

            return $this->customResponse($recipes);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        } 
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
