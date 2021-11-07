<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Symbol;

class SymbolController extends Controller
{
    public function index(){
        $symbols = Symbol::get();
        return $symbols[0];
    }

    public function create(Request $request){
        $data = Symbol::create([
            'title' => $request->title,
            'keys' =>$request->keys
        ]);


        return $data;
    }
}
