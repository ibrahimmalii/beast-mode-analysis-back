<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Mockery\Undefined;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->NotFoundError();
        };

        $tierData = Tier::find($user->tier_id);

        $data = [
            'user' => $user,
            'request_limit' => $tierData->request_limit
        ];

        return $data;
    }

    public function isExist(Request $request)
    {
        if ($request->email) {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                return 'invalid data';
            }

            return;
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'subscriptionID' => ['required'],
            'expire_date' => ['required']
        ]);


        if ($validator->fails()) {
            return 'invalid data';
        }

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->subscriptionID = $request->subscriptionID;
        $user->expire_date = $request->expire_date;
        $user->monthly_number_of_requests = 0;
        $user->tier_id = 1;
        $user->subscribe_status = 'completed';
        $user->current_logged_status = 'true';


        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        $tierData = Tier::find($user->tier_id);


        $data = [
            'access_token' => $token,
            'user' => $user,
        ];


        return $this->apiResponse($data, 'User registered successfully');
    }


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 200);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $tierData = Tier::find($user->tier_id);
        $data = [
            'access_token' => $token,
            'user' => $user,
            'request_limit' => $tierData->request_limit
        ];

        if ($user->current_logged_status === 'false') {
            $user->current_logged_status = 'true';
            $user->save();
            return $this->apiResponse($data, 'User logged successfully');
        }


        throw ValidationException::withMessages([
            'msg' => ['User already logged.'],
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->NotFoundError();
        };

        $user->current_logged_status = 'false';

        DB::table('users')->where('id', $user->id)->update(['current_logged_status' => 'false']);

        return;
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->NotFoundError();
        }


        $user->subscribe_status = $request->subscribe_status;

        if ($request->subscriptionID) {
            $user->subscriptionID = $request->subscriptionID;
            $user->expire_date = $request->expire_date;
            $user->current_logged_status = 'true';
        }else{
            $user->current_logged_status = 'false';
        }

        $user->save();

        if ($user) {
            return $this->apiResponse($user, '', 201);
        }

        return  $this->UnknownError();
    }

    //***************** About reset password */
    public function validatePasswordRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        //Check if the user exists
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 200);
        }
        $user = User::where('email', $request->email)->first();

        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

        // dd($tokenData);
        Mail::to($user->email)->send(new ForgotPasswordMail($user->name, $tokenData->token));

        // if ($this->getResetPassword($request->email, $tokenData->token)) {
        if ($this->getResetPassword($tokenData->token)) {

            $data = ['msg' => 'message has been send'];

            return $data;
        } else {
            return $this->apiResponse(null, $validator->errors(), 200);
        }
    }

    public function getResetPassword($token)
    {
        $password_reset_data = DB::table('password_resets')->where('token', $token)->first();


        if (!$password_reset_data->token || Carbon::now()->subMinutes(10) > $password_reset_data->created_at) {
            $msg = 'Invalid password reset link or link expired';
            return $msg;
        }

        $confirm_token = compact('token');
        $url = 'https://beastmodeanalysis.com/auth/reset-password/' . $confirm_token['token'];
        return redirect()->away($url);
    }


    public function resetPassword($token, Request $request)
    {
        $password_reset_data = DB::table('password_resets')->where('token', $token)->first();
        if (!$password_reset_data->token || Carbon::now()->subMinutes(10) > $password_reset_data->created_at) {
            $data = ['msg' => 'Invalid token'];
            return $data;
        } else {

            $user = User::where('email', $password_reset_data->email)->first();
            if ($user->email !== $request->email) {
                $data = ['msg' => 'Enter Correct Email'];
                return $data;
            }

            $user->update([
                'password' => bcrypt($request->password)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            $tierData = Tier::find($user->tier_id);
            $data = [
                'access_token' => $token,
                'user' => $user,
                'request_limit' => $tierData->request_limit
            ];

            DB::table('password_resets')->where('email', $request->email)->delete();

            return $data;
        }
    }


    public $data;
    public function patreonRegister(Request $request)
    {
        // dd($request->query()['code']);
        $code = $request->query()['code'];
        if ($code) {
            // $response = Http::post('https://www.patreon.com/api/oauth2/token?code=' . $code . '&grant_type=authorization_code&client_id=8BNNHk815QuQxUu7du7TiB7AmRfmAt2kIUy3x4N8Tc5fN3tFtImZf4S1ORIbx5Cp&client_secret=V-kazaMie8BPvRsoB6kT2CeIJ4vjRezlkkoNBcyd3AE_81yUcUATrdawIV_WDdC5&redirect_uri=https://jerryromine.com/admin/public/admin.php/api/patreon-register&Content-Type=application/x-www-form-urlencoded');
            $response = Http::post('https://www.patreon.com/api/oauth2/token?code=' . $code . '&grant_type=authorization_code&client_id=jQxXFMw3h584iK72GHSg1fDYCZcLfGRj3dduXJQUUJzA-xpLwyyMGAh5cdFKF8b0&client_secret=QTyGLq_x7iayp9S-XbNqfBuMmUHTNWbufly5R_WvMCa8v8orvCY7sGHzMiw-ZFQi&redirect_uri=http://localhost:8000/api/patreon-register&Content-Type=application/x-www-form-urlencoded');
            $response->json();
            $responseData = $response->json();

            if ($responseData['access_token']) {
                $token = $responseData['access_token'];

                $responseUserData = Http::withToken($token)->get('https://www.patreon.com/api/oauth2/api/current_user');
                $responseUserData->json();
                $responseAllUserData = $responseUserData->json();
                dd($responseAllUserData);

                // Start Test Response
                // dd($responseAllUserData['errors'][0]['status'] == '403');
                if (Arr::has($responseAllUserData, 'errors')) {
                    $responseUserData = Http::withToken($token)->get('https://www.patreon.com/api/oauth2/api/current_user');
                    $responseUserData->json();
                    $responseAllUserData = $responseUserData->json();
                    dd($responseAllUserData);
                } else {
                    // dd('no error');
                    $responseUserData = Http::withToken($token)->get('https://www.patreon.com/api/oauth2/api/current_user');
                    $responseUserData->json();
                    $responseAllUserData = $responseUserData->json();
                    dd($responseAllUserData);
                }


                //End Test
                if ($responseAllUserData['data']) {
                    $data = $responseAllUserData['data'];
                    $isSuspended = $data['attributes']['is_suspended'];
                    $full_name = $data['attributes']['full_name'];
                    $email = $data['attributes']['email'];
                    // session(['isSuspended' => $this->data['attributes']['can_see_nsfw']]);
                    // session(['full_name'=> $this->data['attributes']['full_name']]);
                    // session(['email'=> $this->data['attributes']['email']]);
                    // $data = session()->all();

                    if ($isSuspended != null) {
                        $url = 'https://beastmodeanalysis.com/auth/register/?done=true&name=' . $full_name . '&email=' . $email;
                        return redirect()->away($url);
                    }

                    $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
                    return redirect()->away($url);
                }

                $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
                return redirect()->away($url);
            }

            $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
            return redirect()->away($url);
        }

        $url = 'https://beastmodeanalysis.com/auth/login/';
        return redirect()->away($url);
    }


    public function patreonLogin(Request $request)
    {
        $code = $request->query()['code'];
        if ($code) {
            $response = Http::post('https://www.patreon.com/api/oauth2/token?code=' . $code . '&grant_type=authorization_code&client_id=l_C9HqfSV57DMgkpqvUvatGVHe2xBZechBUN0AbQ1I6Tnfu6U5R90gbkVjvVWuef&client_secret=xjOP-aqVwvTfDbTrtZ5v-nACsc91rs2-qR2H5S-V31hvW8NbvBasOi03zEDRVTQF&redirect_uri=https://jerryromine.com/admin/public/admin.php/api/patreon-login&Content-Type=application/x-www-form-urlencoded');
            $response->json();
            $responseData = $response->json();

            if ($responseData['access_token']) {
                $token = $responseData['access_token'];

                $responseUserData = Http::withToken($token)->get('https://www.patreon.com/api/oauth2/api/current_user');
                $responseUserData->json();
                $responseAllUserData = $responseUserData->json();

                if ($responseAllUserData['data']) {
                    $data = $responseAllUserData['data'];
                    $is_suspended = $data['attributes']['is_suspended'];
                    $email = $data['attributes']['email'];
                    // session(['is_suspended' => $this->data['attributes']['can_see_nsfw']]);
                    // session(['full_name'=> $this->data['attributes']['full_name']]);
                    // session(['email'=> $this->data['attributes']['email']]);
                    // $data = session()->all();

                    if ($is_suspended != null) {
                        $url = 'https://beastmodeanalysis.com/auth/login/?done=true&email=' . $email;
                        return redirect()->away($url);
                    }

                    // $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
                    $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
                    return redirect()->away($url);
                }

                $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
                return redirect()->away($url);
            }

            $url = 'https://beastmodeanalysis.com/auth/login/?done=false';
            return redirect()->away($url);
        }

        $url = 'https://beastmodeanalysis.com/auth/login/';
        return redirect()->away($url);
    }


    public function patreonData()
    {
        dd(session()->all());
        $full_name = session()->get('full_name');
        $email = session()->get('email');
        $is_suspended = session()->get('is_suspended');

        $data = [
            'full_name' => $full_name,
            'email' => $email,
            'is_suspended' => $is_suspended
        ];

        return $data;
    }
}
