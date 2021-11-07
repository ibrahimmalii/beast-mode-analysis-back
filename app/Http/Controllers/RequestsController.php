<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;

class RequestsController extends Controller
{
    public function index(){
        $requests = Requests::all();

        return $requests;
    }

    public function create(Request $request){
        $newRequest = Requests::create([
            'number_of_requests' => $request->number_of_requests,
        ]);

        return $newRequest;
    }


    public function update($id){
        $currentRequest = Requests::find($id);

        if($currentRequest){
            $currentRequest->number_of_requests += 1;
        };

        return $currentRequest;

    }
}
