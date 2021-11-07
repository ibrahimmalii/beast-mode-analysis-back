<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search($key){

        if ($key) {
            $companies = DB::table('symbols')->where('Name' , 'LIKE', '%' . $key . "%")->orWhere('symbols' , 'LIKE', '%' . $key . "%")->paginate(100);
            if ($companies) {
                return $companies;
            }
        }
    }
}
