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
                'image' => ['required', 'image'],
            ]); 

            $fileNameExt = $request_info->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameExt, PATHINFO_FILENAME);
            $fileExt = $request_info->file('image')->getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.time().'.'.$fileExt;
            $pathToStore = $request_info->file('image')->storeAs('public/recipe_images',$fileNameToStore);
            
            $recipe = Recipe::create([
                'name' => $validated_data['name'],
                'cuisine' => $validated_data['cuisine'],
                'ingredients' => $validated_data['ingredients'],
                'image_url' => $fileNameToStore,
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

            // $recipes->image_url = asset('storage/app/public/recipe_images' . $this->image->name);

            foreach ($recipes as $recipe) {
                // $recipe->image_url = asset('storage/' . $this->recipe_images->name);
                // $recipe->image = asset('storage/' . $recipe->image_url);
                $recipe->new_image_url = asset('storage/recipe_images/' . $recipe->image_url);
            }
            
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
            foreach ($myRecipes as $myRecipe){
                $myRecipe->new_image_url = asset('storage/recipe_images/' . $myRecipe->image_url);
            }
            return $this->customResponse($myRecipes);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }

    public function getById(Recipe $recipe){
        try{
            $recipe->load('user');
            $recipe->new_image_url = asset('storage/recipe_images/' . $recipe->image_url);
            return $this->customResponse($recipe);
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

            $recipe->calendar()->delete();

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

            foreach ($recipes as $recipe) {
                $recipe->new_image_url = asset('storage/recipe_images/' . $recipe->image_url);
            }

            return $this->customResponse($recipes);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        } 
    }

    public function hasThisRecipe($id){
        try{
            $user = Auth::user();
            $recipe = Recipe::find($id);

            if ($recipe && $recipe->user_id === $user->id) {
                return $this->customResponse(true, 'User has this recipe');
            } else {
                return $this->customResponse(false, 'User does not have this recipe');
            }
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
