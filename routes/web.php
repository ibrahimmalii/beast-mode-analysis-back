<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// $data = {};

// Route::get('/', function (Request $request) {
//     // dd($request->query()['code']);
//     $code = $request->query()['code'];
//     // dd('https://www.patreon.com/api/oauth2/token?code='.$code.'&grant_type=authorization_code&client_id=s_ccKDJuAaiVRv6UHhmQ27a0CvUYF1x0LiK8r7wiybkrnxcwl9JBvPj2KMsXiMBV&client_secret=5hgUtooHEMVk8t53jFy-ZsoOKea712csujK2Au_YzUmt_A-WsdmiLZUFj-gnk0ii&redirect_uri=http://localhost:8000/&Content-Type=application/x-www-form-urlencoded');
//     // dd($code);
//     if ($code) {
//         $response = Http::post('https://www.patreon.com/api/oauth2/token?code=' . $code . '&grant_type=authorization_code&client_id=s_ccKDJuAaiVRv6UHhmQ27a0CvUYF1x0LiK8r7wiybkrnxcwl9JBvPj2KMsXiMBV&client_secret=5hgUtooHEMVk8t53jFy-ZsoOKea712csujK2Au_YzUmt_A-WsdmiLZUFj-gnk0ii&redirect_uri=http://localhost:8000/&Content-Type=application/x-www-form-urlencoded');
//         $response->json();
//         $responseData = $response->json();
//         $token = $responseData['access_token'];

//         $responseUserData = Http::withToken($token)->get('https://www.patreon.com/api/oauth2/api/current_user');
//         $responseUserData->json();
//         $responseAllUserData = $responseUserData->json();
//         // dd($responseAllUserData['data']);

//         if ($responseAllUserData['data']) {
//             $url = 'http://localhost:4200/auth/register/?done=true';
//             return redirect()->away($url);
//         }

//         $url = 'http://localhost:4200/auth/login/?done=false';
//         return redirect()->away($url);
//         // return view('welcome');
//     }
// });

Route::get('/', function () {

    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
