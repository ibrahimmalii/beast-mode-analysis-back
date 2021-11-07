<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockProperty;

class StockPropertieController extends Controller
{
    public function index(){
        $properties = StockProperty::all();

        return $properties;
    }

    public function create(Request $request){
        // dd($request);
        // Angular
        $data = StockProperty::create([
            'title' => $request->title,
            'comment' =>$request->comment
        ]);


        return $data;
    }

    public function update(Request $request ,$id){

        $updatedProperty = StockProperty::find($id)->update([
            'title' => $request->title,
            'comment' =>$request->comment
        ]);

        $property = StockProperty::find($id);


        return $property;
    }


    public function delete(){

    }
}
