<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriesRresource;
use App\Models\Category;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function category_show(int $id){
   
        return Category::where('id',$id)
        ->select(
           'id',
           'name',
           'max_amount',
           'current_amount',
           'type',
           'user_id'
        )->get();
      
       }
    public function categories_show(int $user_id){
   
        return Category::where('User_id',$user_id)
        ->select(
           'id',
           'name',
           'max_amount',
           'current_amount',
           'type',
           'user_id'
        )->get();
      
       }
    
    public function max_amount_update(int $id,int $user_id,Request $request){
     Category::where('id',$id+(24*($user_id-1)))
                ->update([
                    'max_amount'=> $request->max_amount

                ]);

     
        return $this->category_show($id+(24*($user_id-1)));
      
    }   

   public function category_store(String $name, int $user_id,int $type){
     $category=Category::Create([
      'name'=>$name,
      'max_amount'=>0,
      'current_amount'=>0,
      'user_id'=>$user_id,
      'type'=>$type,
      
     ]);
     return new CategoriesRresource($category);
   }
}